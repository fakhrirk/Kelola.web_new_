<!DOCTYPE html>
<html>
<head>
    <title>Persetujuan Reset Password</title>
</head>
<body>
    <h2>Permintaan Reset Password Anda Telah Disetujui</h2>
    <p>Halo,</p>
    <p>Permintaan Anda untuk mengatur ulang password telah disetujui oleh admin.</p>
    <p>Silakan klik link di bawah ini untuk membuat password baru Anda. Link ini hanya berlaku untuk waktu yang terbatas.</p>
    <br>
    <a href="{{ url('reset-password-custom/'.$token) }}"
       style="background-color: #4F46E5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
       Buat Password Baru
    </a>
    <br>
    <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
    <p>Terima kasih,</p>
    <p>Tim Kelola.web</p>
</body>
</html> 
