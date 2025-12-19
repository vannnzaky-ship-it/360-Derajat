<!DOCTYPE html>
<html>
<head>
    <title>Reset Password OTP</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h3>Halo,</h3>
    <p>Anda menerima email ini karena ada permintaan reset password.</p>
    <p>Gunakan kode OTP berikut untuk mereset password Anda:</p>

    <h1 style="background: #eee; padding: 10px; display: inline-block; letter-spacing: 5px;">
        {{ $otp }}
    </h1>

    <p>Kode ini hanya berlaku selama <strong>15 menit</strong>.</p>
    <p>Jika Anda tidak meminta kode ini, abaikan saja.</p>
    <br>
    <p>Terima kasih,<br>Admin Politeknik Kampar</p>
</body>
</html>