<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordOtpMail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Show the form to request OTP.
     */
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP to the user's email.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
        ]);

        $user = User::where('nip', $request->nip)->first();

        if (!$user) {
            if ($request->wantsJson()) return response()->json(['message' => 'NIP tidak ditemukan dalam sistem.'], 404);
            return back()->withErrors(['nip' => 'NIP tidak ditemukan dalam sistem.']);
        }

        if (!$user->email) {
            if ($request->wantsJson()) return response()->json(['message' => 'Akun ini tidak memiliki email yang terdaftar. Hubungi Admin HR.'], 400);
            return back()->withErrors(['nip' => 'Akun ini tidak memiliki email yang terdaftar. Hubungi Admin HR.']);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save to password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['nip' => $user->nip],
            [
                'token' => Hash::make($otp),
                'created_at' => Carbon::now()
            ]
        );

        // Send Email
        Mail::to($user->email)->send(new ResetPasswordOtpMail($otp, $user->name));

        // Store NIP in session for the next step
        session(['reset_nip' => $user->nip]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Kode OTP telah dikirim ke email Anda.']);
        }

        return redirect()->route('password.verify-form')->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    /**
     * Show the form to verify OTP and reset password.
     */
    public function showVerifyForm()
    {
        if (!session('reset_nip')) {
            return redirect()->route('password.request');
        }
        
        return view('auth.verify-otp');
    }

    /**
     * Verify OTP ONLY.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $nip = session('reset_nip');
        if (!$nip) {
            if ($request->wantsJson()) return response()->json(['message' => 'Sesi telah habis. Silakan ulangi dari awal.'], 401);
            return back()->withErrors(['otp' => 'Sesi telah habis. Silakan ulangi dari awal.']);
        }

        $record = DB::table('password_reset_tokens')->where('nip', $nip)->first();

        if (!$record) {
            if ($request->wantsJson()) return response()->json(['message' => 'Permintaan reset password tidak valid.'], 400);
            return back()->withErrors(['otp' => 'Permintaan reset password tidak valid.']);
        }

        // Check expiration (15 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('nip', $nip)->delete();
            if ($request->wantsJson()) return response()->json(['message' => 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.'], 400);
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.']);
        }

        // Verify OTP
        if (!Hash::check($request->otp, $record->token)) {
            if ($request->wantsJson()) return response()->json(['message' => 'Kode OTP salah.'], 400);
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        // Valid! Let's flag the session
        session(['otp_verified_nip' => $nip]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'OTP Valid. Silakan buat password baru.']);
        }
        
        return back()->with('success', 'OTP Valid. Silakan buat password baru.');
    }

    /**
     * Reset Password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $nip = session('otp_verified_nip');
        if (!$nip) {
            if ($request->wantsJson()) return response()->json(['message' => 'Akses ditolak. Selesaikan verifikasi OTP terlebih dahulu.'], 401);
            return back()->withErrors(['password' => 'Akses ditolak.']);
        }

        // Update User Password
        $user = User::where('nip', $nip)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear tokens
        DB::table('password_reset_tokens')->where('nip', $nip)->delete();
        session()->forget(['reset_nip', 'otp_verified_nip']);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Password berhasil direset. Silakan login.']);
        }

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
    }
}
