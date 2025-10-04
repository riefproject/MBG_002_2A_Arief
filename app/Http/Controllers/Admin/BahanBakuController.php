<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Support\Database\SyncsPostgresSequences;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BahanBakuController extends Controller
{
    use SyncsPostgresSequences;

    // tampilin detail bahan baku per item
    public function show(BahanBaku $bahanBaku)
    {
        $resource = $this->formatBahanBakuResource($bahanBaku);

        return view('admin.bahan_baku.detail', [
            'bahanBaku' => $bahanBaku,
            'bahanBakuData' => $resource,
        ]);
    }
    // tampilkan daftar bahan baku buat dashboard gudang
    public function index(Request $request)
    {
        $bahanBakus = BahanBaku::latest()
            ->get()
            ->map(fn (BahanBaku $bb) => $this->formatBahanBakuResource($bb))
            ->values();

        return view('admin.bahan_baku.index', [
            'bahanBakus' => $bahanBakus,
        ]);
    }

    // simpen bahan baku baru dari form
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:120',
            'kategori' => 'required|string|max:60',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'tanggal_masuk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date',
        ], [
            'nama.required' => 'nama harus diisi',
            'kategori.required' => 'kategori harus diisi',
            'jumlah.required' => 'jumlah harus diisi',
            'satuan.required' => 'satuan harus diisi',
            'tanggal_masuk.required' => 'tanggal masuk harus diisi',
            'tanggal_kadaluarsa.required' => 'tanggal kadaluarsa harus diisi',
        ]);

        $status = BahanBaku::determineStatus($validated['jumlah'], $validated['tanggal_kadaluarsa']);

        $this->syncPostgresSequence('bahan_baku');

        $bb = BahanBaku::create([
            'nama' => $validated['nama'],
            'kategori' => $validated['kategori'],
            'jumlah' => $validated['jumlah'],
            'satuan' => $validated['satuan'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'],
            'status' => $status,
            'created_at' => now(),
        ]);
        
        // refresh status biar tetap konsisten
        $bb->refreshStatus(true);

        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bahan baku berhasil ditambahkan.',
                'data' => $this->formatBahanBakuResource($bb),
            ]);
        }
        return redirect()
            ->route('admin.bahan_baku.index')
            ->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    // update stok bahan baku yg udah ada
    public function update(Request $request, BahanBaku $bahanBaku)
    {
        $currentStatus = BahanBaku::determineStatus($bahanBaku->jumlah, $bahanBaku->tanggal_kadaluarsa);

        if ($currentStatus === 'kadaluarsa') {
            $message = 'Bahan baku kadaluarsa hanya bisa dihapus.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => [
                        'jumlah' => [$message],
                    ],
                ], 422);
            }

            return redirect()
                ->route('admin.bahan_baku.index')
                ->with('error', $message);
        }

        $validated = $request->validate([
            'jumlah' => 'required|integer|min:0',
        ], [
            'jumlah.required' => 'jumlah harus diisi',
            'jumlah.integer' => 'jumlah harus berupa angka bulat',
            'jumlah.min' => 'jumlah tidak boleh kurang dari 0',
        ]);

        $newStatus = BahanBaku::determineStatus($validated['jumlah'], $bahanBaku->tanggal_kadaluarsa);

        $bahanBaku->update([
            'jumlah' => $validated['jumlah'],
            'status' => $newStatus,
        ]);
        
        $bahanBaku->refresh();

        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Stok bahan baku berhasil diperbarui.',
                'data' => $this->formatBahanBakuResource($bahanBaku),
            ]);
        }

        return redirect()
            ->route('admin.bahan_baku.index')
            ->with('success', 'Stok bahan baku berhasil diperbarui.');
    }

    // hapus bahan baku kalo udah kadaluarsa
    public function destroy(Request $request, BahanBaku $bahanBaku)
    {
        $status = BahanBaku::determineStatus($bahanBaku->jumlah, $bahanBaku->tanggal_kadaluarsa);

        if ($status !== 'kadaluarsa') {
            $message = 'Bahan baku hanya dapat dihapus jika statusnya kadaluarsa.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()
                ->route('admin.bahan_baku.index')
                ->with('error', $message);
        }

        $bbName = $bahanBaku->nama;
        $bbId = $bahanBaku->id;
        $bahanBaku->delete();

        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Bahan baku {$bbName} berhasil dihapus.",
                'data' => [
                    'id' => $bbId,
                    'redirect' => route('admin.bahan_baku.index'),
                ],
            ]);
        }

        return redirect()
            ->route('admin.bahan_baku.index')
            ->with('success', 'Bahan baku berhasil dihapus.');
    }

    // format data bahan baku biar konsisten
    protected function formatBahanBakuResource(BahanBaku $bb): array
    {
        $status = BahanBaku::determineStatus($bb->jumlah, $bb->tanggal_kadaluarsa);

        $statusLabel = Str::headline($status);

        $routeParams = ['bahan_baku' => $bb->getKey()];

        return [
            'id' => $bb->id,
            'nama' => $bb->nama,
            'kategori' => $bb->kategori,
            'jumlah' => $bb->jumlah,
            'satuan' => $bb->satuan,
            'jumlah_label' => trim($bb->jumlah . ' ' . $bb->satuan),
            'tanggal_masuk' => optional($bb->tanggal_masuk)->format('d M Y'),
            'tanggal_masuk_label' => optional($bb->tanggal_masuk)->format('d M Y'),
            'tanggal_masuk_iso' => optional($bb->tanggal_masuk)->format('Y-m-d'),
            'tanggal_kadaluarsa' => optional($bb->tanggal_kadaluarsa)->format('d M Y'),
            'tanggal_kadaluarsa_label' => optional($bb->tanggal_kadaluarsa)->format('d M Y'),
            'tanggal_kadaluarsa_iso' => optional($bb->tanggal_kadaluarsa)->format('Y-m-d'),
            'status' => $status,
            'status_label' => $statusLabel,
            'can_delete' => $status === 'kadaluarsa',
            'view_url' => route('admin.bahan_baku.show', $routeParams),
            'edit_url' => route('admin.bahan_baku.update', $routeParams),
            'delete_url' => route('admin.bahan_baku.destroy', $routeParams),
        ];
    }
}
