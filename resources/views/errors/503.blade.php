<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Situs Sedang Dalam Pemeliharaan — WargaDigi 21">
    <link rel="icon" type="image/png" href="{{ \App\Models\SystemSetting::getLogoUrl() }}">
    <title>Situs Sedang Dalam Pemeliharaan — WargaDigi 21</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #F0FDF4 0%, #F8FAFC 50%, #ECFDF5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #1F2937;
        }

        .maintenance-card {
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(16, 185, 129, 0.12), 0 0 15px rgba(0, 0, 0, 0.04);
            max-width: 540px;
            width: 100%;
            padding: 48px 36px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .maintenance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #16A34A 0%, #10B981 50%, #059669 100%);
        }

        .logo-wrapper {
            margin-bottom: 28px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-wrapper img {
            height: 48px;
            max-width: 180px;
            object-fit: contain;
        }

        .icon-circle {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: #DCFCE7;
            color: #15803D;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
            box-shadow: 0 8px 20px rgba(34, 197, 94, 0.18);
            animation: pulse-gear 3s ease-in-out infinite;
        }

        @keyframes pulse-gear {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #FEF3C7;
            color: #92400E;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 16px;
            border: 1px solid #FDE68A;
        }

        .badge-status::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #F59E0B;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        h1 {
            font-size: 26px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            line-height: 1.3;
        }

        p.description {
            font-size: 14.5px;
            line-height: 1.65;
            color: #4B5563;
            margin-bottom: 32px;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-reload {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #16A34A 0%, #15803D 100%);
            color: #FFFFFF;
            text-decoration: none;
            padding: 13px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
        }

        .btn-reload:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(22, 163, 74, 0.32);
        }

        .btn-admin {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #F9FAFB;
            color: #374151;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            border: 1px solid #D1D5DB;
            transition: all 0.2s ease;
        }

        .btn-admin:hover {
            background: #F3F4F6;
            color: #111827;
            border-color: #9CA3AF;
        }

        .footer-note {
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px solid #F3F4F6;
            font-size: 12px;
            color: #9CA3AF;
        }
    </style>
</head>
<body>

    <div class="maintenance-card">
        <div class="logo-wrapper">
            <img src="{{ \App\Models\SystemSetting::getLogoUrl() }}" alt="WargaDigi 21 Logo">
        </div>

        <div class="icon-circle">
            <i class="bi bi-tools"></i>
        </div>

        <div class="badge-status">
            HTTP 503 • Mode Pemeliharaan
        </div>

        <h1>Situs Sedang Dalam Pemeliharaan</h1>

        <p class="description">
            Sistem WargaDigi 21 saat ini sedang menjalani peningkatan dan pemeliharaan rutin demi kenyamanan serta keamanan layanan warga. Mohon kembali lagi dalam beberapa saat.
        </p>

        <div class="action-buttons">
            <button class="btn-reload" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise"></i> Coba Muat Ulang Halaman
            </button>
            <a href="{{ route('login') }}" class="btn-admin">
                <i class="bi bi-shield-lock"></i> Masuk Sebagai Pengurus / Admin
            </a>
        </div>

        <div class="footer-note">
            &copy; {{ date('Y') }} WargaDigi 21 Desa Tanimulya. Digital Gotong Royong.
        </div>
    </div>

</body>
</html>
