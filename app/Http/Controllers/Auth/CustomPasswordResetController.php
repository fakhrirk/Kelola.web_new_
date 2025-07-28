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
    public function showRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    // PERUBAHAN 1: Mengembalikan RedirectResponse, bukan JSON
    public function handleRequest(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        PasswordResetRequest::where('email', $request->email)->delete();

        $token = Str::random(60);

        PasswordResetRequest::create([
            'email' => $request->email,
            'token' => $token,
            'status' => 'pending',
        ]);

        // Arahkan user ke halaman status dengan membawa token
        return redirect()->route('password.status', ['token' => $token]);
    }

    // PERUBAHAN 2: Metode baru untuk halaman status
    public function showStatusPage(string $token): View|RedirectResponse
    {
        $resetRequest = PasswordResetRequest::where('token', $token)->first();

        // Jika token tidak ditemukan, kembalikan ke awal
        if (!$resetRequest) {
            return redirect()->route('password.request')->with('error', 'Permintaan tidak valid.');
        }

        // Jika sudah disetujui, langsung arahkan ke form reset
        if ($resetRequest->status === 'approved') {
            return redirect()->route('password.reset', ['token' => $token]);
        }

        // Jika masih pending atau ditolak, tampilkan halaman status
        return view('auth.password-status', ['status' => $resetRequest->status]);
    }

    public function showResetForm(string $token): View|RedirectResponse
    {
        $requestData = PasswordResetRequest::where('token', $token)->where('status', 'approved')->first();

        if (!$requestData) {
            return redirect()->route('password.request')
                ->with('error', 'Token tidak valid, kedaluwarsa, atau permintaan Anda belum disetujui.');
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $requestData->email]);
    }

    public function handleReset(Request $request): RedirectResponse
    {
        // ... (Fungsi ini tidak perlu diubah, sudah benar)
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
