<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    /**
     * Display the employee profile page with active authenticated user data.
     */
    public function index()
    {
        $user = Auth::user();
        return view('employee.profile.index', compact('user'));
    }

    /**
     * Update employee profile details (name, position, photo).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'position' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|file|mimes:jpeg,png,jpg,webp,heic|max:1024',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'profile_photo.mimes' => 'Format foto profil harus JPG, PNG, WEBP, atau HEIC.',
            'profile_photo.max' => 'Ukuran file foto profil terlalu besar. Maksimal 1MB.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if ($request->filled('position')) {
            $user->position = $validated['position'];
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'emp_avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/avatars');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            // Remove old photo if exists
            if ($user->registered_face_photo && File::exists(public_path($user->registered_face_photo))) {
                @File::delete(public_path($user->registered_face_photo));
            }

            $file->move($destinationPath, $filename);
            $user->registered_face_photo = 'uploads/avatars/' . $filename;
        }

        $user->save();

        return redirect()->route('employee.profile.index')
            ->with('success', 'Profil karyawan Anda berhasil diperbarui!');
    }

    /**
     * Update employee password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal harus 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.'])
                ->with('error_modal', 'changePasswordModal');
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->route('employee.profile.index')
            ->with('success', 'Password akun karyawan Anda berhasil diperbarui!');
    }
}
