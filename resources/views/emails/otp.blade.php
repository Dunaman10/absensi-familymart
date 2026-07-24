<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="color: #0d6efd; text-align: center;">Absensiku Pro</h2>
        <p>Halo <strong>{{ $name }}</strong>,</p>
        <p>Kami menerima permintaan untuk mereset kata sandi akun Anda. Berikut adalah kode OTP Anda:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <span style="display: inline-block; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #0d6efd; background-color: #f8f9fa; padding: 15px 30px; border-radius: 8px; border: 1px dashed #0d6efd;">
                {{ $otp }}
            </span>
        </div>
        
        <p>Kode OTP ini hanya berlaku selama <strong>15 menit</strong>. Jangan berikan kode ini kepada siapapun.</p>
        <p>Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Akun Anda tetap aman.</p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
        <p style="font-size: 12px; color: #777; text-align: center;">
            &copy; {{ date('Y') }} Absensiku Pro. All rights reserved.
        </p>
    </div>
</body>
</html>
