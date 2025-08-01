<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomPasswordResetController extends Controller
{
    /**
     * Menampilkan form awal untuk meminta token.
     */
    public function showRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Memproses permintaan dari form, menyimpan ke DB, dan redirect ke halaman status.
     */
    public function handleRequest(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // Hapus permintaan lama jika ada untuk email yang sama
        PasswordResetRequest::where('email', $request->email)->delete();

        $token = Str::random(60);

        // Buat entri baru di database
        PasswordResetRequest::create([
            'email' => $request->email,
            'token' => $token,
            'status' => 'pending', // Pastikan statusnya 'pending'
        ]);

        // Arahkan user ke halaman status kustom kita
        return redirect()->route('password.status', ['token' => $token]);
    }

    /**
     * Menampilkan halaman status yang akan refresh otomatis.
     */
    public function showStatusPage(string $token): View|RedirectResponse
    {
        $resetRequest = PasswordResetRequest::where('token', $token)->first();

        if (!$resetRequest) {
            return redirect()->route('password.request')->with('error', 'Permintaan tidak valid atau telah kedaluwarsa.');
        }

        // Jika Owner sudah menyetujui, langsung arahkan ke form reset password
        if ($resetRequest->status === 'approved') {
            return redirect()->route('password.reset', ['token' => $token]);
        }

        // Jika masih pending, tampilkan halaman status "tunggu"
        return view('auth.password-status');
    }

    /**
     * Menampilkan form untuk mengisi password baru.
     */
    public function showResetForm(string $token): View|RedirectResponse
    {
        $requestData = PasswordResetRequest::where('token', $token)->where('status', 'approved')->first();

        if (!$requestData) {
            return redirect()->route('password.request')
                ->with('error', 'Token tidak valid atau permintaan Anda belum disetujui.');
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $requestData->email]);
    }

    /**
     * Memproses penyimpanan password baru.
     */
    public function handleReset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $resetRequest = PasswordResetRequest::where('email', $request->email)
            ->where('token', $request->token)
            ->where('status', 'approved')
            ->first();

        if (!$resetRequest) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah digunakan.']);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            $resetRequest->status = 'completed';
            $resetRequest->save();
            return redirect()->route('login')->with('status', 'Password Anda telah berhasil diubah! Silakan login.');
        }
        return back()->withErrors(['email' => 'User tidak ditemukan.']);
    }
}
