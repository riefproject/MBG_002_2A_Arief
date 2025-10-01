<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class BahanBakuController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        return spa_view('admin.users.index', 'admin.users.partial');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50 ',
            'kategori' => 'required|string|max:25',
            'jumlah' => 'required|int|min:6',
            'satuan' => 'required|string|max:50',
            'status' =>['required', Rule::in(['tersedia', 'segera_kadaluarsa', 'kadaluarsa', 'habis'])],
        ], [
            'nama.required' => 'nama harus diisi',
            'kategori.required' => 'kategori harus diisi',
            'jumlah.required' => 'jumlah harus diisi',
            'satuan.required' => 'satuan harus diisi',
            'status.required' => 'status harus diisi'
        ]);

        $bb = BahanBaku::create([
            'nama' => $validated['nama'],
            'kategori' => $validated['kategori'],
            'jumlah' => $validated['jumlah'],
            'satuan' => $validated['satuan'],
            'status' => $validated['status'],
            'tanggal_masuk',
            'tanggal_keluar' 

        ]);

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'BahanBaku berhasil ditambahkan.',
                'data' => [
                    'nama' => $bb->nama,
                    'kategori' => $bb->kategori,
                    'jumlah' => $bb->jumlah,
                    'satuan' => $bb->satuan,
                    'status' => $bb->status,
                    'tanggal_masuk' => $bb->tanggal_masuk->format('d M Y'),
                    'tanggal_keluar' => $bb->tanggal_keluar->format('d M Y')
                ]
            ]);
        }
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'BahanBaku berhasil ditambahkan.');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, BahanBaku $bb)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50 ',
            'kategori' => 'required|string|max:25',
            'jumlah' => 'required|int|min:6',
            'satuan' => 'required|string|max:50',
            'status' =>['required', Rule::in(['tersedia', 'segera_kadaluarsa', 'kadaluarsa', 'habis'])],
        ], [
            'nama.required' => 'nama harus diisi',
            'kategori.required' => 'kategori harus diisi',
            'jumlah.required' => 'jumlah harus diisi',
            'satuan.required' => 'satuan harus diisi',
            'status.required' => 'status harus diisi'
        ]);

        $bb->update($validated);

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'BahanBaku berhasil diupdate.',
                'data' => [
                    'id' => $bb->id,
                    'name' => $bb->name,
                    'email' => $bb->email,
                    'role' => $bb->role,
                    'created_at' => $bb->created_at->format('d M Y'),
                    'created_at_diff' => $bb->created_at->diffForHumans(),
                    'email_verified_at' => $bb->email_verified_at,
                ]
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'BahanBaku berhasil diupdate.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, BahanBaku $bb)
    {
        $bbName = $bb->name;
        $bb->delete();

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "BahanBaku {$bbName} berhasil dihapus."
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'BahanBaku berhasil dihapus.');
    }
}