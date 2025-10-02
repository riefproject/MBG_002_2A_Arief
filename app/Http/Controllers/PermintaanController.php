<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PermintaanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Permintaan::with(['pemohon', 'details.bahan'])
            ->orderByDesc('created_at');

        if ($request->user() && $request->user()->role === 'dapur') {
            $query->where('pemohon_id', $request->user()->id);
        }

        $permintaan = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $permintaan,
        ]);
    }

    // Simpan permintaan baru beserta detailnya.
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pemohon_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'dapur')),
            ],
            'tgl_masak' => ['required', 'date'],
            'menu_makan' => ['required', 'string', 'max:255'],
            'jumlah_porsi' => ['required', 'integer', 'min:1'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.bahan_id' => [
                'required',
                'integer',
                Rule::exists('bahan_baku', 'id'),
            ],
            'details.*.jumlah_diminta' => ['required', 'integer', 'min:1'],
        ], [
            'tgl_masak.required' => 'Tanggal masak wajib diisi.',
            'menu_makan.required' => 'Menu masakan wajib diisi.',
            'jumlah_porsi.required' => 'Jumlah porsi wajib diisi.',
            'details.required' => 'Detail permintaan wajib diisi.',
        ]);

        $pemohonId = $validated['pemohon_id'] ?? optional($request->user())->id;

        if (!$pemohonId) {
            throw ValidationException::withMessages([
                'pemohon_id' => 'Pemohon tidak ditemukan.',
            ]);
        }

        $permintaan = DB::transaction(function () use ($validated, $pemohonId) {
            $permintaan = Permintaan::create([
                'pemohon_id' => $pemohonId,
                'tgl_masak' => $validated['tgl_masak'],
                'menu_makan' => $validated['menu_makan'],
                'jumlah_porsi' => $validated['jumlah_porsi'],
                'status' => Permintaan::STATUS_MENUNGGU,
                'created_at' => now(),
            ]);

            foreach ($validated['details'] as $detail) {
                $permintaan->details()->create([
                    'bahan_id' => $detail['bahan_id'],
                    'jumlah_diminta' => $detail['jumlah_diminta'],
                ]);
            }

            return $permintaan->load(['pemohon', 'details.bahan']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Permintaan berhasil dibuat.',
            'data' => $this->formatPermintaanResource($permintaan),
        ], 201);
    }

    /**
     * Detail permintaan tertentu.
     */
    public function show(Permintaan $permintaan): JsonResponse
    {
        $permintaan->loadMissing(['pemohon', 'details.bahan']);

        return response()->json([
            'success' => true,
            'data' => $this->formatPermintaanResource($permintaan),
        ]);
    }

    /**
     * Update status permintaan.
     */
    public function update(Request $request, Permintaan $permintaan): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                Permintaan::STATUS_MENUNGGU,
                Permintaan::STATUS_DISETUJUI,
                Permintaan::STATUS_DITOLAK,
            ])],
        ]);

        $permintaan->update($validated);
        $permintaan->load(['pemohon', 'details.bahan']);

        return response()->json([
            'success' => true,
            'message' => 'Status permintaan berhasil diperbarui.',
            'data' => $this->formatPermintaanResource($permintaan),
        ]);
    }

    /**
     * Hapus permintaan jika masih menunggu.
     */
    public function destroy(Permintaan $permintaan): JsonResponse
    {
        if ($permintaan->status !== Permintaan::STATUS_MENUNGGU) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan hanya dapat dihapus ketika status masih menunggu.',
            ], 422);
        }

        $permintaan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permintaan berhasil dihapus.',
        ]);
    }

    protected function formatPermintaanResource(Permintaan $permintaan): array
    {
        return [
            'id' => $permintaan->id,
            'pemohon' => [
                'id' => optional($permintaan->pemohon)->id,
                'nama' => optional($permintaan->pemohon)->name,
                'email' => optional($permintaan->pemohon)->email,
            ],
            'tgl_masak' => optional($permintaan->tgl_masak)->format('Y-m-d'),
            'menu_makan' => $permintaan->menu_makan,
            'jumlah_porsi' => $permintaan->jumlah_porsi,
            'status' => $permintaan->status,
            'created_at' => optional($permintaan->created_at)->toDateTimeString(),
            'details' => $permintaan->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'bahan_id' => $detail->bahan_id,
                    'bahan_nama' => optional($detail->bahan)->nama,
                    'jumlah_diminta' => $detail->jumlah_diminta,
                ];
            })->all(),
            'total_jumlah_diminta' => $permintaan->totalJumlahDiminta(),
        ];
    }
}
