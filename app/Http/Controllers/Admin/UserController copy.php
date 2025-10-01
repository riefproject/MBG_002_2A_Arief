<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
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
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(),
            'nama' => $validated['nama'],
            'kategori' => $validated['kategori'],
            'jumlah' => $validated['jumlah'],
            'satuan' => $validated['satuan'],
            'status' => $validated['status'],
            'tanggal_masuk' => now();

        ]);

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan.',
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
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $bb)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($bb->id)],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ], [
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.required' => 'Role harus dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        $bb->update($validated);

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate.',
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
            ->with('success', 'User berhasil diupdate.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $bb)
    {
        // Prevent deleting self
        if ($bb->id === Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menghapus akun sendiri.'
                ], 400);
            }

            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $bbName = $bb->name;
        $bb->delete();

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "User {$bbName} berhasil dihapus."
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}