<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judulPeristiwa }} — SPPK-UKT UNG</title>
</head>
<body style="font-family: Inter, Arial, sans-serif; background-color: #f8f8f7; color: #1C1917; padding: 24px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border-radius: 12px; padding: 28px; border: 1px solid #E2DFDC;">
        <div style="border-bottom: 3px solid #550b14; padding-bottom: 16px; margin-bottom: 20px;">
            <strong style="font-size: 16px; color: #550b14;">Universitas Negeri Gorontalo</strong>
            <div style="font-size: 13px; color: #57534E;">Sistem Peninjauan dan Pengajuan Keberatan UKT (SPPK-UKT UNG)</div>
        </div>

        <p style="font-size: 14px; line-height: 1.6;">Yth. <strong>{{ $namaPenerima }}</strong>,</p>

        <p style="font-size: 14px; line-height: 1.6;">
            Terdapat pembaruan status pada akun SPPK-UKT UNG Anda:
        </p>

        <div style="background-color: #F5E8E9; border-radius: 10px; padding: 16px; margin: 16px 0;">
            <div style="font-size: 13px; color: #57534E;">Peristiwa</div>
            <div style="font-size: 15px; font-weight: bold; color: #1C1917; margin-bottom: 8px;">{{ $judulPeristiwa }}</div>

            <div style="font-size: 13px; color: #57534E;">Status Terbaru</div>
            <div style="font-size: 15px; font-weight: bold; color: #550b14; margin-bottom: 8px;">{{ $statusBaru }}</div>

            <div style="font-size: 13px; color: #57534E;">Tanggal Pembaruan</div>
            <div style="font-size: 14px; color: #1C1917;">{{ $tanggalPerubahan }}</div>
        </div>

        <p style="font-size: 14px; line-height: 1.6;">
            Demi menjaga privasi dan keamanan data Anda, email ini tidak memuat rincian data ekonomi maupun dokumen. Silakan masuk ke sistem melalui tautan berikut untuk melihat rincian selengkapnya:
        </p>

        <p style="margin: 24px 0;">
            <a href="{{ $tautanHalaman }}" style="display: inline-block; background-color: #550b14; color: #FFFFFF; text-decoration: none; font-weight: bold; padding: 12px 20px; border-radius: 10px;">
                Buka Halaman Status di Sistem
            </a>
        </p>

        <hr style="border: none; border-top: 1px solid #DDD9D6; margin: 24px 0;">
        <p style="font-size: 12px; color: #78716C; line-height: 1.5;">
            Email otomatis dari SPPK-UKT Universitas Negeri Gorontalo. Mohon tidak membalas email ini.
        </p>
    </div>
</body>
</html>

