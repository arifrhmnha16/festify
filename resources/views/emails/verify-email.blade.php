<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Email Festify</title>
</head>
<body style="margin:0;background:#fff7ed;color:#171717;font-family:Arial,Helvetica,sans-serif;">
    <div style="display:none;max-height:0;overflow:hidden;color:transparent;opacity:0;">Aktifkan akun Festify kamu untuk mulai pesan tiket konser.</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fff7ed;padding:28px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;overflow:hidden;border-radius:14px;background:#ffffff;border:1px solid #fed7aa;">
                    <tr>
                        <td style="padding:24px 28px 10px;text-align:left;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="42" height="42" align="center" style="width:42px;height:42px;border-radius:12px;background:#5b21b6;color:#ffffff;font-size:22px;font-weight:900;line-height:42px;">F</td>
                                    <td style="padding-left:12px;font-size:28px;line-height:1;font-weight:900;color:#111827;">Festify</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 28px 30px;">
                            <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#c2410c;">Verifikasi Akun</p>
                            <h1 style="margin:0;font-size:28px;line-height:1.2;color:#111827;">Halo, {{ $user->name }}</h1>
                            <p style="margin:18px 0 0;font-size:16px;line-height:1.7;color:#525252;">Terima kasih sudah mendaftar di Festify. Klik tombol di bawah untuk mengaktifkan akun dan mulai pesan tiket konser favoritmu.</p>
                            <div style="margin:28px 0;text-align:center;">
                                <a href="{{ $url }}" style="display:inline-block;border-radius:999px;background:#ea580c;color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;padding:14px 24px;">Verifikasi Email</a>
                            </div>
                            <p style="margin:0;font-size:14px;line-height:1.7;color:#737373;">Kalau tombol tidak bisa dibuka, salin link berikut ke browser:</p>
                            <p style="margin:8px 0 0;word-break:break-all;font-size:13px;line-height:1.6;color:#9a3412;"><a href="{{ $url }}" style="color:#9a3412;">{{ $url }}</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top:1px solid #fed7aa;padding:18px 28px;background:#fffaf5;color:#78716c;font-size:13px;line-height:1.6;">
                            Jika kamu tidak membuat akun Festify, abaikan email ini.
                            <br>Salam hangat, Festify.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
