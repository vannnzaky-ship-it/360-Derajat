<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding-bottom: 40px; }
        .main-table { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 500px; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .logo-container { text-align: center; padding: 40px 0 20px 0; }
        .content { padding: 0 40px 40px 40px; text-align: center; }
        .otp-box { background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px; margin: 30px 0; }
        .otp-code { font-family: 'Courier New', monospace; font-size: 32px; font-weight: 700; color: #0f172a; letter-spacing: 8px; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; padding-top: 20px; }
    </style>
</head>
<body>

    <table class="wrapper" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="logo-container">
                    {{-- Pastikan folder images sudah benar --}}
                    <img src="{{ $message->embed(public_path('images/logo-polkam2.png')) }}" alt="Politeknik Kampar" width="150" style="height: auto; display: block; margin: 0 auto;">
                </div>

                <table class="main-table" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="content">
                            
                            <div style="margin-top: 30px;">
                                <img src="https://cdn-icons-png.flaticon.com/512/2889/2889676.png" width="50" alt="Security" style="opacity: 0.8;">
                            </div>

                            <h1 style="color: #1e293b; font-size: 22px; margin: 20px 0 10px 0;">Reset Password</h1>
                            
                            <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin: 0;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun <strong>Politeknik Kampar</strong> Anda.
                            </p>

                            <div class="otp-box">
                                <span class="otp-code">{{ $otp }}</span>
                            </div>

                            <p style="color: #64748b; font-size: 14px; margin-bottom: 5px;">
                                Kode ini kedaluwarsa dalam <strong>15 menit</strong>.
                            </p>
                            <p style="color: #94a3b8; font-size: 13px; margin-top: 0;">
                                Abaikan jika Anda tidak memintanya.
                            </p>

                            <div style="border-top: 1px solid #f1f5f9; margin: 30px 0;"></div>

                            {{-- UPDATE BAGIAN INI (Link WhatsApp) --}}
                            <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                                Butuh bantuan? Hubungi 
                                {{-- Ganti nomor di bawah ini dengan nomor asli --}}
                                <a href="https://wa.me/6282172782504" style="color: #2563eb; text-decoration: none; font-weight: 600;">
                                    IT Support Politeknik Kampar
                                </a>.
                            </p>

                        </td>
                    </tr>
                </table>

                <div class="footer">
                    &copy; {{ date('Y') }} Politeknik Kampar. All rights reserved.<br>
                    Jl. Tengku Muhammad KM. 2, Bangkinang
                </div>

            </td>
        </tr>
    </table>

</body>
</html>