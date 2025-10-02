<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    /**
     * Display the specified bahan baku.
     */
    public function show(BahanBaku $bahanBaku)
    {
        return view('admin.bahan_baku.detail', compact('bahanBaku'));
    }
    /**
     * Display a listing of bahan baku.
     */
    public function index(Request $request)
    {
        return spa_view('admin.bahan_baku.index', 'admin.bahan_baku.partial');
    }

    /**
     * Store a newly created bahan baku.
     */
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

    /**
     * Update the specified bahan baku.
     */
    public function update(Request $request, BahanBaku $bb)
    {
        $validated = $request->validate([
            'jumlah' => 'required|integer|min:0',
        ], [
            'jumlah.required' => 'jumlah harus diisi',
            'jumlah.integer' => 'jumlah harus berupa angka bulat',
            'jumlah.min' => 'jumlah tidak boleh kurang dari 0',
        ]);

        $status = BahanBaku::determineStatus($validated['jumlah'], $bb->tanggal_kadaluarsa);

        $bb->update([
            'jumlah' => $validated['jumlah'],
            'status' => $status,
        ]);
        $bb->refresh();

        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Stok bahan baku berhasil diperbarui.',
                'data' => $this->formatBahanBakuResource($bb),
            ]);
        }

        return redirect()
            ->route('admin.bahan_baku.index')
            ->with('success', 'Stok bahan baku berhasil diperbarui.');
    }

    /**
     * Remove the specified bahan baku.
     */
    public function destroy(Request $request, BahanBaku $bb)
    {
        $currentStatus = $bb->refreshStatus();

        if ($currentStatus !== 'kadaluarsa') {
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

        $bbName = $bb->nama;
        $bbId = $bb->id;
        $bb->delete();

        
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

    /**
     * Format response data untuk bahan baku.
     */
    protected function formatBahanBakuResource(BahanBaku $bb): array
    {
        return [
            'id' => $bb->id,
            'nama' => $bb->nama,
            'kategori' => $bb->kategori,
            'jumlah' => $bb->jumlah,
            'satuan' => $bb->satuan,
            'jumlah_label' => trim($bb->jumlah . ' ' . $bb->satuan),
            'tanggal_masuk' => optional($bb->tanggal_masuk)->format('d M Y'),
            'tanggal_masuk_iso' => optional($bb->tanggal_masuk)->format('Y-m-d'),
            'tanggal_kadaluarsa' => optional($bb->tanggal_kadaluarsa)->format('d M Y'),
            'tanggal_kadaluarsa_iso' => optional($bb->tanggal_kadaluarsa)->format('Y-m-d'),
            'status' => $bb->status,
            'status_label' => str_replace('_', ' ', $bb->status),
            'can_delete' => $bb->status === 'kadaluarsa',
            'view_url' => route('admin.bahan_baku.show', $bb),
            'edit_url' => route('admin.bahan_baku.update', $bb),
            'delete_url' => route('admin.bahan_baku.destroy', $bb),
        ];
    }
}
