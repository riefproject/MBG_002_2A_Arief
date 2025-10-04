<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Permintaan;
use App\Support\Database\SyncsPostgresSequences;
use App\Support\Permintaan\PermintaanDetailNormalizer;
use App\Support\Permintaan\PermintaanFormState;
use App\Support\Permintaan\PermintaanViewPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\ViewErrorBag;

class PermintaanController extends Controller
{
    use SyncsPostgresSequences;

    // ambil data permintaan utk kebutuhan api
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

    // tampilkan dashboard permintaan versi dapur
    public function indexUser(Request $request): View
    {
        $permintaan = Permintaan::with(['details.bahan'])
            ->where('pemohon_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        $permintaanView = PermintaanViewPresenter::transformForUserIndex($permintaan);

        $bahanBaku = BahanBaku::where('jumlah', '>', 0)
            ->where('status', '!=', 'kadaluarsa')
            ->orderBy('nama')
            ->get();

        $formState = PermintaanFormState::fromRequest($request);

        $errors = $request->session()->get('errors');
        $shouldOpenCreateModal = ($errors instanceof ViewErrorBag && $errors->any())
            || $request->query('open') === 'create';

        return view('user.permintaan.index', [
            'permintaan' => $permintaanView,
            'bahanBaku' => $bahanBaku,
            'oldDetails' => $formState->details(),
            'shouldOpenCreateModal' => $shouldOpenCreateModal,
        ]);
    }

    // nyiapin form permintaan baru buat dapur
    public function create(Request $request): View
    {
        $bahanBaku = BahanBaku::where('jumlah', '>', 0)
            ->where('status', '!=', 'kadaluarsa')
            ->orderBy('nama')
            ->get();

        $formState = PermintaanFormState::fromRequest($request);

        return view('user.permintaan.create', [
            'bahanBaku' => $bahanBaku,
            'detailRows' => $formState->detailsWithFallbackRow(),
        ]);
    }

    // list permintaan yg nunggu utk tim gudang
    public function indexGudang()
    {
        $permintaan = Permintaan::with(['pemohon', 'details.bahan'])
            ->where('status', Permintaan::STATUS_MENUNGGU)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.permintaan.index', compact('permintaan'));
    }

    // simpen permintaan baru bareng detail bahan
    public function store(Request $request)
    {
        $detailRows = PermintaanDetailNormalizer::normalize(
            $request->input('details'),
            $request->input('bahan_id'),
            $request->input('jumlah_diminta')
        );

        $request->merge([
            'details' => $detailRows,
        ]);

        $validated = $request->validate([
            'pemohon_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'dapur')),
            ],
            'tgl_masak' => ['required', 'date', 'after_or_equal:today'],
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
            'tgl_masak.after_or_equal' => 'Tanggal masak ga boleh mundur dari hari ini.',
            'menu_makan.required' => 'Menu masakan wajib diisi.',
            'jumlah_porsi.required' => 'Jumlah porsi wajib diisi.',
            'details.required' => 'Detail permintaan wajib diisi.',
        ]);

        $pemohonId = $validated['pemohon_id'] ?? Auth::id();

        if (!$pemohonId) {
            throw ValidationException::withMessages([
                'pemohon_id' => 'Pemohon tidak ditemukan.',
            ]);
        }

        $this->syncPostgresSequence('permintaan');
        $this->syncPostgresSequence('permintaan_detail');

        $detailCollection = collect($validated['details']);
        $bahanMap = BahanBaku::whereIn('id', $detailCollection->pluck('bahan_id'))
            ->get()
            ->keyBy('id');

        $shouldStoreSnapshot = $this->shouldStoreDetailSnapshot();

        $permintaan = DB::transaction(function () use ($validated, $pemohonId, $bahanMap, $shouldStoreSnapshot) {
            $permintaan = Permintaan::create([
                'pemohon_id' => $pemohonId,
                'tgl_masak' => $validated['tgl_masak'],
                'menu_makan' => $validated['menu_makan'],
                'jumlah_porsi' => $validated['jumlah_porsi'],
                'status' => Permintaan::STATUS_MENUNGGU,
                'created_at' => now(),
            ]);

            foreach ($validated['details'] as $detail) {
                $snapshot = $bahanMap->get($detail['bahan_id']);

                $detailData = [
                    'bahan_id' => $detail['bahan_id'],
                    'jumlah_diminta' => $detail['jumlah_diminta'],
                ];

                if ($shouldStoreSnapshot) {
                    $detailData['bahan_nama_snapshot'] = $snapshot->nama ?? null;
                    $detailData['bahan_satuan_snapshot'] = $snapshot->satuan ?? null;
                }

                $permintaan->details()->create($detailData);
            }

            return $permintaan->load(['pemohon', 'details.bahan']);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil dibuat.',
                'data' => $this->formatPermintaanResource($permintaan),
            ], 201);
        }

        return redirect()->route('user.permintaan.index')
            ->with('success', 'Permintaan bahan berhasil dikirim.');
    }

    // balikin detail permintaan via json
    public function show(Permintaan $permintaan): JsonResponse
    {
        $permintaan->loadMissing(['pemohon', 'details.bahan']);

        return response()->json([
            'success' => true,
            'data' => $this->formatPermintaanResource($permintaan),
        ]);
    }

    // update status permintaan dari gudang
    public function update(Request $request, Permintaan $permintaan): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                Permintaan::STATUS_MENUNGGU,
                Permintaan::STATUS_DISETUJUI,
                Permintaan::STATUS_DITOLAK,
                Permintaan::STATUS_KADALUARSA,
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

    // hapus permintaan kalo masih menunggu
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

    // setujui permintaan sekaligus potong stok
    public function setujuiPermintaan($id)
    {
        $permintaan = Permintaan::with(['details.bahan'])->findOrFail($id);

        if ($permintaan->status !== Permintaan::STATUS_MENUNGGU) {
            return redirect()->route('admin.permintaan.index')
                ->with('error', 'Status permintaan ini udah berubah jadi ga bisa disetujui.');
        }

        if ($permintaan->tgl_masak && $permintaan->tgl_masak->isBefore(today())) {
            $permintaan->status = Permintaan::STATUS_KADALUARSA;
            $permintaan->save();

            return redirect()->route('admin.permintaan.index')
                ->with('error', 'Tanggal masaknya udah lewat jadi permintaan otomatis kadaluarsa.');
        }

        DB::transaction(function () use ($permintaan) {
            foreach ($permintaan->details as $detail) {
                $bahan = $detail->bahan;

                if (!$bahan) {
                    continue;
                }

                $stokAkhir = max(0, (int) $bahan->jumlah - (int) $detail->jumlah_diminta); // turunkan stok sesuai permintaan
                $bahan->jumlah = $stokAkhir;

                if ($stokAkhir === 0) {
                    $bahan->status = 'habis';
                } else {
                    $bahan->refreshStatus(false);
                }

                $bahan->save();
            }

            $permintaan->status = Permintaan::STATUS_DISETUJUI;
            $permintaan->save();
        });

        return redirect()->route('admin.permintaan.index')
            ->with('success', 'Permintaan berhasil disetujui!');
    }

    // tandai permintaan ditolak sama gudang
    public function tolakPermintaan($id)
    {
        $permintaan = Permintaan::findOrFail($id);

        if ($permintaan->status !== Permintaan::STATUS_MENUNGGU) {
            return redirect()->route('admin.permintaan.index')
                ->with('error', 'Status permintaan ini udah bukan menunggu, cek lagi ya.');
        }

        if ($permintaan->tgl_masak && $permintaan->tgl_masak->isBefore(today())) {
            $permintaan->status = Permintaan::STATUS_KADALUARSA;
            $permintaan->save();

            return redirect()->route('admin.permintaan.index')
                ->with('error', 'Tanggal masaknya lewat jadi langsung ditandai kadaluarsa aja.');
        }

        $permintaan->status = Permintaan::STATUS_DITOLAK;
        $permintaan->save();

        return redirect()->route('admin.permintaan.index')
            ->with('success', 'Permintaan telah ditolak.');
    }

    // bentuk data permintaan biar rapi utk response
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
                    'bahan_nama' => $detail->bahan_nama_label,
                    'bahan_satuan' => $detail->bahan_satuan_label,
                    'jumlah_diminta' => $detail->jumlah_diminta,
                ];
            })->all(),
            'total_jumlah_diminta' => $permintaan->totalJumlahDiminta(),
        ];
    }

    private static ?bool $detailSnapshotAvailable = null;

    private function shouldStoreDetailSnapshot(): bool
    {
        if (self::$detailSnapshotAvailable === null) {
            self::$detailSnapshotAvailable = Schema::hasColumn('permintaan_detail', 'bahan_nama_snapshot')
                && Schema::hasColumn('permintaan_detail', 'bahan_satuan_snapshot');
        }

        return self::$detailSnapshotAvailable;
    }
}
