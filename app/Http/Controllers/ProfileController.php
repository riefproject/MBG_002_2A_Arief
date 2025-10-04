<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // tampilkan halaman profile
    public function show(Request $request)
    {
        return view('profile');
    }

    // update data profile user
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ], [
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        $user->update($validated);
        $user->refresh();
        Auth::setUser($user);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diupdate.',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'initial' => mb_strtoupper(mb_substr($user->name, 0, 1)),
                    'updated_at_diff' => optional($user->updated_at)->diffForHumans() ?? '-',
                    'updated_at' => optional($user->updated_at)->toIso8601String(),
                ]
            ]);
        }

        return redirect()
            ->route('profile')
            ->with('success', 'Profile berhasil diupdate.');
    }

    // ganti password user
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini harus diisi.',
            'password.required' => 'Password baru harus diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini tidak benar.',
                    'errors' => ['current_password' => ['Password saat ini tidak benar.']]
                ], 422);
            }

            return redirect()
                ->route('profile')
                ->withErrors(['current_password' => 'Password saat ini tidak benar.']);
        }

        // update password di database
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
        $user->refresh();
        Auth::setUser($user);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diupdate.'
            ]);
        }

        return redirect()
            ->route('profile')
            ->with('success', 'Password berhasil diupdate.');
    }
}
