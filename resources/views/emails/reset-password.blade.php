<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password Festify</title>
</head>
<body style="margin:0;background:#f5f5f4;color:#171717;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f4;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;overflow:hidden;border-radius:16px;background:#ffffff;border:1px solid #e7e5e4;">
                    <tr>
                        <td style="background:#111827;padding:24px;text-align:center;">
                            <img src="{{ $logoUrl }}" alt="Festify" style="max-width:180px;height:auto;border-radius:8px;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#c2410c;">Keamanan Akun</p>
                            <h1 style="margin:0;font-size:28px;line-height:1.2;color:#111827;">Reset password Festify</h1>
                            <p style="margin:18px 0 0;font-size:16px;line-height:1.7;color:#525252;">Kami menerima permintaan reset password untuk akun {{ $user->email }}. Klik tombol di bawah untuk membuat password baru.</p>
                            <div style="margin:28px 0;text-align:center;">
                                <a href="{{ $url }}" style="display:inline-block;border-radius:999px;background:#111827;color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;padding:14px 24px;">Buat Password Baru</a>
                            </div>
                            <p style="margin:0;font-size:14px;line-height:1.7;color:#737373;">Kalau tombol tidak bisa dibuka, salin link berikut ke browser:</p>
                            <p style="margin:8px 0 0;word-break:break-all;font-size:13px;line-height:1.6;color:#9a3412;"><a href="{{ $url }}" style="color:#9a3412;">{{ $url }}</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top:1px solid #e7e5e4;padding:20px 32px;background:#fafaf9;color:#78716c;font-size:13px;line-height:1.6;">
                            Jika kamu tidak meminta reset password, abaikan email ini.
                            <br>Salam hangat, Festify.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
