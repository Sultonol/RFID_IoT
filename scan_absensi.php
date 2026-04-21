<?php
$conn = new mysqli("localhost", "root", "", "iot_rfid");
$today = date("Y-m-d");
$total_hadir = $conn->query("SELECT COUNT(DISTINCT rl.uid) as c FROM rfid_logs rl
    INNER JOIN mahasiswa m ON m.uid = rl.uid
    WHERE DATE(rl.waktu) = '$today'")->fetch_assoc()['c'];
$total_mhs = $conn->query("SELECT COUNT(*) as c FROM mahasiswa")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi - RFID Monitoring</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f0f4f8;
            color: #1a1a2e;
            min-height: 100vh;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background: #0f1b2d;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            height: 56px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-brand-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #378ADD;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-brand-icon svg {
            width: 16px;
            height: 16px;
        }

        .nav-brand-name {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }

        .nav-brand-sub {
            font-size: 11px;
            color: #64748b;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 13px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
            color: #94a3b8;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }

        .nav-link:hover {
            background: #1e2d40;
            color: #e2e8f0;
        }

        .nav-link.active {
            background: #1e3a5f;
            color: #60a5fa;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            color: #34d399;
            background: #052e16;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .live-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #34d399;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .nav-clock {
            font-size: 12px;
            color: #64748b;
            font-family: monospace;
        }

        /* ===== PAGE ===== */
        .page {
            max-width: 960px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        /* PAGE HEADER */
        .page-header {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 1.25rem;
        }

        .page-header h1 {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a2e;
            margin: 0 0 4px;
        }

        .page-header p {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }

        .divider {
            height: 1px;
            background: #f1f5f9;
            margin: 14px 0;
        }

        .team-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .team-label {
            font-size: 12px;
            color: #9ca3af;
        }

        .team-chip {
            font-size: 12px;
            font-weight: 500;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 3px 10px;
            border-radius: 20px;
            border: 1px solid #bfdbfe;
        }

        /* STATS */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 1.25rem;
        }

        .stat {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px 16px;
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 700;
        }

        .stat-sub {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        .stat.green .stat-value {
            color: #15803d;
        }

        .stat.blue .stat-value {
            color: #1d4ed8;
        }

        .stat.amber .stat-value {
            color: #b45309;
        }

        /* MAIN GRID */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 16px;
            align-items: start;
        }

        /* CARD */
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            background: #fafafa;
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .card-sub {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        .card-body {
            padding: 20px;
        }

        .scan-hero {
            position: relative;
            overflow: hidden;
            padding: 28px;
            border-radius: 10px;
            background: #f0f4f8;
            border: 1px solid #f0f4f8;
            margin-bottom: 16px;
            min-height: 260px;
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(200px, 0.8fr);
            gap: 22px;
            align-items: center;
        }

        .scan-hero::before,
        .scan-hero::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            z-index: 0;
            opacity: 0.35;
        }

        .scan-hero::before {
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(21, 101, 192, 0.15) 0%, rgba(21, 101, 192, 0) 70%);
            top: -60px;
            right: -40px;
        }

        .scan-hero::after {
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(33, 150, 243, 0.12) 0%, rgba(33, 150, 243, 0) 72%);
            bottom: -80px;
            left: -60px;
        }

        .hero-copy,
        .hero-visual {
            position: relative;
            z-index: 1;
        }

        .hero-kicker {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            background: #dadee1;
            color: #000d1c;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .hero-title {
            font-size: 21px;
            font-weight: 600;
            color: #000d1c;
            line-height: 1.2;
            margin: 0 0 10px;
            letter-spacing: -0.3px;
        }

        .hero-text {
            margin: 0;
            color: #526071;
            font-size: 13px;
            line-height: 1.75;
            font-weight: 400;
        }

        .status-note {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
            padding: 10px 14px;
            background: #fff;
            color: #3f4e61;
            border-radius: 12px;
            border: 1px solid rgba(21, 101, 192, 0.14);
            font-weight: 600;
            font-size: 13px;
        }

        .status-note::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.12);
        }

        .visual-card {
            min-height: 210px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .visual-pulse {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(21, 101, 192, 0.16) 0%, rgba(21, 101, 192, 0.08) 40%, rgba(21, 101, 192, 0.02) 72%, rgba(21, 101, 192, 0) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .visual-pulse::before,
        .visual-pulse::after {
            content: "";
            position: absolute;
            inset: 24px;
            border-radius: 50%;
            border: 2px solid rgba(21, 101, 192, 0.18);
            animation: ripple 2.8s ease-out infinite;
        }

        .visual-pulse::after {
            animation-delay: 1.2s;
        }

        @keyframes ripple {
            0% {
                transform: scale(0.82);
                opacity: 0;
            }

            40% {
                opacity: 0.7;
            }

            100% {
                transform: scale(1.1);
                opacity: 0;
            }
        }

        .rfid-card {
            width: 120px;
            min-height: 155px;
            border-radius: 22px;
            background: linear-gradient(180deg, #1565c0 0%, #0d47a1 100%);
            box-shadow: 0 20px 30px rgba(21, 101, 192, 0.24);
            position: relative;
            display: grid;
            place-items: center;
            color: #fff;
        }

        .rfid-card::before {
            content: "";
            position: absolute;
            top: 16px;
            left: 16px;
            width: 24px;
            height: 18px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.3);
        }

        .rfid-card::after {
            content: "RFID";
            position: absolute;
            bottom: 14px;
            right: 14px;
            font-size: 12px;
            letter-spacing: 1px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.78);
        }

        .card-icon {
            font-size: 38px;
            transform: translateY(-5px);
        }

        /* ===== END SCAN HERO ===== */

        /* STEPS */
        .steps {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fafafa;
        }

        .step-num {
            width: 26px;
            height: 26px;
            flex-shrink: 0;
            border-radius: 7px;
            background: #eff6ff;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .step-title {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 2px;
        }

        .step-desc {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.5;
        }

        /* RESULT PANEL */
        .result-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 28px 16px;
            gap: 6px;
            border: 2px dashed #e5e7eb;
            border-radius: 10px;
            min-height: 200px;
        }

        .result-empty-icon {
            font-size: 28px;
            margin-bottom: 4px;
        }

        .result-empty-title {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        .result-empty-desc {
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.6;
            max-width: 200px;
        }

        .result-data {
            display: none;
            flex-direction: column;
            gap: 8px;
        }

        .result-data.show {
            display: flex;
        }

        .result-field {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fafafa;
        }

        .result-field-label {
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .result-field-value {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .result-message {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
        }

        .result-message.warning {
            border-color: #fed7aa;
            background: #fff7ed;
        }

        .result-message.danger {
            border-color: #fca5a5;
            background: #fef2f2;
        }

        .result-message-icon {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #dcfce7;
            font-size: 12px;
            font-weight: 700;
            color: #15803d;
        }

        .result-message.warning .result-message-icon {
            background: #fed7aa;
            color: #b45309;
        }

        .result-message.danger .result-message-icon {
            background: #fca5a5;
            color: #dc2626;
        }

        .result-message-text {
            font-size: 12px;
            color: #374151;
            line-height: 1.6;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .status-badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-badge.hadir {
            color: #15803d;
            background: #dcfce7;
        }

        .status-badge.telat {
            color: #b45309;
            background: #fef3c7;
        }

        .status-badge.asing {
            color: #dc2626;
            background: #fef2f2;
        }

        /* QUICK LINKS */
        .quick-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .quick-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fafafa;
            text-decoration: none;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.12s;
        }

        .quick-link:hover {
            background: #f1f5f9;
        }

        .quick-link-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            background: #eff6ff;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quick-link-icon svg {
            width: 14px;
            height: 14px;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            padding: 1.5rem 0;
        }

        @media (max-width: 768px) {
            .main-grid {
                grid-template-columns: 1fr;
            }

            .stats {
                grid-template-columns: 1fr 1fr;
            }

            .scan-hero {
                grid-template-columns: 1fr;
            }

            .visual-card {
                min-height: 180px;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="nav-brand">
            <div class="nav-brand-icon">
                <svg viewBox="0 0 16 16" fill="none">
                    <rect x="1" y="4" width="14" height="9" rx="2" stroke="white" stroke-width="1.3" />
                    <path d="M5 4V3a3 3 0 016 0v1" stroke="white" stroke-width="1.3" stroke-linecap="round" />
                    <circle cx="8" cy="8.5" r="1.5" fill="white" />
                </svg>
            </div>
            <div>
                <div class="nav-brand-name">RFID Monitoring</div>
                <div class="nav-brand-sub">ESP32 · MQTT · HTTP</div>
            </div>
        </div>
        <div class="nav-links">
            <a class="nav-link" href="viewer.php">Logs</a>
            <a class="nav-link" href="register.php">Register Kartu</a>
            <a class="nav-link" href="data_mahasiswa.php">Data Mahasiswa</a>
            <a class="nav-link active" href="absensi.php">Absensi</a>
        </div>
        <div class="nav-right">
            <div class="live-badge">
                <div class="live-dot"></div>LIVE
            </div>
            <div class="nav-clock" id="clock"></div>
        </div>
    </nav>

    <div class="page">

        <div class="page-header">
            <h1>Absensi RFID</h1>
            <p>Sistem absensi realtime berbasis kartu RFID — tempelkan kartu untuk mencatat kehadiran</p>
            <div class="divider"></div>
            <div class="team-row">
                <span class="team-label">Kelompok:</span>
                <span class="team-chip">Hanifah</span>
                <span class="team-chip">Hidayah</span>
                <span class="team-chip">Sulton</span>
                <span class="team-chip">Zaki</span>
                <span class="team-chip">Taufiq</span>
            </div>
        </div>

        <div class="stats">
            <div class="stat green">
                <div class="stat-label">Hadir Hari Ini</div>
                <div class="stat-value"><?= $total_hadir ?></div>
                <div class="stat-sub">Dari <?= $total_mhs ?> mahasiswa</div>
            </div>
            <div class="stat amber">
                <div class="stat-label">Belum Hadir</div>
                <div class="stat-value"><?= $total_mhs - $total_hadir ?></div>
                <div class="stat-sub">Mahasiswa absen</div>
            </div>
            <div class="stat blue">
                <div class="stat-label">Status Reader</div>
                <div class="stat-value" style="font-size:15px;padding-top:4px;">Aktif</div>
                <div class="stat-sub">Polling tiap 2 detik</div>
            </div>
        </div>

        <div class="main-grid">

            <!-- KIRI -->
            <div style="display:flex; flex-direction:column; gap:16px;">
                <div class="card">
                    <div class="card-head">
                        <div>
                            <div class="card-title">Area Scan Kartu</div>
                            <div class="card-sub">Tempelkan kartu RFID pada reader selama 1–2 detik</div>
                        </div>
                        <div class="live-badge">
                            <div class="live-dot"></div>Live
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="scan-hero" id="scanHero">
                            <div class="hero-copy">
                                <span class="hero-kicker">Tap kartu untuk absensi</span>
                                <h3 class="hero-title">Tempelkan kartu RFID Anda</h3>
                                <p class="hero-text" id="scanText">
                                    Dekatkan kartu ke RFID reader. Setelah kartu terbaca, data absensi terbaru akan
                                    langsung muncul pada panel hasil scan di sebelah kanan.
                                </p>
                                <div class="status-note" id="scanNote">Status: Siap menerima scan</div>
                            </div>
                            <div class="hero-visual">
                                <div class="visual-card">
                                    <div class="visual-pulse">
                                        <div class="rfid-card">
                                            <div class="card-icon">💳</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="steps">
                            <div class="step">
                                <div class="step-num">1</div>
                                <div>
                                    <div class="step-title">Siapkan kartu RFID</div>
                                    <div class="step-desc">Gunakan kartu yang sudah terdaftar agar data mahasiswa bisa
                                        dikenali sistem.</div>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-num">2</div>
                                <div>
                                    <div class="step-title">Tempelkan ke reader</div>
                                    <div class="step-desc">Tahan kartu di dekat reader hingga sistem mendeteksi UID dan
                                        mengirim data.</div>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-num">3</div>
                                <div>
                                    <div class="step-title">Verifikasi hasil</div>
                                    <div class="step-desc">Periksa nama, UID, waktu, dan status kehadiran pada panel di
                                        sebelah kanan.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KANAN -->
            <div style="display:flex; flex-direction:column; gap:16px;">

                <div class="card">
                    <div class="card-head">
                        <div>
                            <div class="card-title">Hasil Scan Terakhir</div>
                            <div class="card-sub">Diperbarui otomatis tanpa refresh</div>
                        </div>
                        <span class="status-badge hadir" id="statusBadge">Menunggu</span>
                    </div>
                    <div class="card-body">
                        <div class="result-empty" id="resultEmpty">
                            <div class="result-empty-icon">🪪</div>
                            <div class="result-empty-title">Belum ada scan</div>
                            <div class="result-empty-desc">Detail mahasiswa dan status kehadiran akan tampil di sini
                                setelah kartu dibaca.</div>
                        </div>
                        <div class="result-data" id="resultData">
                            <div class="result-field">
                                <div class="result-field-label">Nama</div>
                                <div class="result-field-value" id="resNama">-</div>
                            </div>
                            <div class="result-field">
                                <div class="result-field-label">UID Kartu</div>
                                <div class="result-field-value" id="resUid"
                                    style="font-family:monospace;font-size:13px;">-</div>
                            </div>
                            <div class="result-field">
                                <div class="result-field-label">Waktu Scan</div>
                                <div class="result-field-value" id="resWaktu">-</div>
                            </div>
                            <div class="result-message" id="resMessage">
                                <div class="result-message-icon" id="resIcon">✓</div>
                                <div class="result-message-text" id="resText">Data absensi berhasil diterima.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Navigasi Cepat</div>
                    </div>
                    <div class="card-body">
                        <div class="quick-links">
                            <a class="quick-link" href="viewer.php">
                                <div class="quick-link-icon">
                                    <svg viewBox="0 0 14 14" fill="none">
                                        <rect x="1" y="1" width="5" height="5" rx="1" fill="currentColor" />
                                        <rect x="8" y="1" width="5" height="5" rx="1" fill="currentColor" />
                                        <rect x="1" y="8" width="5" height="5" rx="1" fill="currentColor" />
                                        <rect x="8" y="8" width="5" height="5" rx="1" fill="currentColor" />
                                    </svg>
                                </div>
                                Dashboard Logs
                            </a>
                            <a class="quick-link" href="data_mahasiswa.php">
                                <div class="quick-link-icon">
                                    <svg viewBox="0 0 14 14" fill="none">
                                        <circle cx="7" cy="4" r="2.5" stroke="currentColor" stroke-width="1.2" />
                                        <path d="M2 12c0-2.76 2.24-5 5-5s5 2.24 5 5" stroke="currentColor"
                                            stroke-width="1.2" stroke-linecap="round" />
                                    </svg>
                                </div>
                                Data Mahasiswa
                            </a>
                            <a class="quick-link" href="register.php">
                                <div class="quick-link-icon">
                                    <svg viewBox="0 0 14 14" fill="none">
                                        <circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.2" />
                                        <path d="M7 4.5v5M4.5 7h5" stroke="currentColor" stroke-width="1.2"
                                            stroke-linecap="round" />
                                    </svg>
                                </div>
                                Register Kartu Baru
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="footer">© <?= date('Y') ?> IoT Project - Teknik Informatika</div>
    </div>

    <script>
        let lastId = 0;
        let initialized = false;
        let resetTimer = null;

        function updateClock() {
            const now = new Date();
            const date = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            const time = now.toLocaleTimeString('id-ID');
            document.getElementById('clock').textContent = date + ' ' + time;
        }

        function esc(text) {
            const d = document.createElement('div');
            d.textContent = text ?? '-';
            return d.innerHTML;
        }

        function setWaiting() {
            document.getElementById('scanText').textContent = 'Dekatkan kartu ke RFID reader. Setelah kartu terbaca, data absensi terbaru akan langsung muncul pada panel hasil scan di sebelah kanan.';
            document.getElementById('scanNote').textContent = 'Status: Siap menerima scan';
        }

        function showResult(data) {
            document.getElementById('scanText').textContent = 'Kartu berhasil dibaca! Pastikan data pada panel hasil scan sudah sesuai.';
            document.getElementById('scanNote').textContent = 'Status: Scan diterima';

            document.getElementById('resultEmpty').style.display = 'none';
            document.getElementById('resultData').classList.add('show');

            document.getElementById('resNama').innerHTML = esc(data.nama || 'Tidak dikenali');
            document.getElementById('resUid').innerHTML = esc(data.uid || '-');
            document.getElementById('resWaktu').innerHTML = esc(data.waktu || '-');

            const badge = document.getElementById('statusBadge');
            const msgBox = document.getElementById('resMessage');
            const msgIcon = document.getElementById('resIcon');
            const msgText = document.getElementById('resText');
            const type = data.status_type || 'hadir';

            badge.className = 'status-badge ' + type;

            if (type === 'hadir') {
                badge.textContent = 'Hadir';
                msgBox.className = 'result-message';
                msgIcon.textContent = '✓';
                msgText.textContent = data.message || 'Absensi berhasil dicatat.';
            } else if (type === 'telat') {
                badge.textContent = 'Telat';
                msgBox.className = 'result-message warning';
                msgIcon.textContent = '!';
                msgText.textContent = data.message || 'Mahasiswa hadir namun melewati batas waktu.';
            } else {
                badge.textContent = 'Tidak Dikenal';
                msgBox.className = 'result-message danger';
                msgIcon.textContent = '✕';
                msgText.textContent = data.message || 'UID tidak terdaftar di sistem.';
            }

            if (resetTimer) clearTimeout(resetTimer);
            resetTimer = setTimeout(setWaiting, 8000);
        }

        async function checkScan() {
            try {
                const res = await fetch('get_latest_scan.php?last_id=' + lastId + '&_=' + Date.now());
                const data = await res.json();

                if (!initialized) {
                    initialized = true;
                    lastId = data.latest_id || 0;
                    return;
                }

                if (data.has_new_scan) {
                    lastId = data.latest_id || lastId;
                    showResult(data.data || {});
                }
            } catch (e) {
                document.getElementById('scanNote').textContent = 'Status: Koneksi terputus';
            }
        }

        setInterval(updateClock, 1000);
        setInterval(checkScan, 2000);
        updateClock();
        setWaiting();
        checkScan();
    </script>
</body>

</html>