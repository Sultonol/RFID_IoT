<?php
$conn = new mysqli("localhost", "root", "", "iot_rfid");
$uid = ""; // kosong saat pertama buka
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register Kartu - RFID Monitoring</title>
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

        /* ===== NAVBAR (sama persis dengan index.php) ===== */
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
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {

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
            max-width: 560px;
            margin: 2rem auto;
            padding: 0 1rem;
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

        /* FORM CARD */
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            padding: 14px 20px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h2 {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
            margin: 0 0 2px;
        }

        .card-header p {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* INFO BANNER */
        .info-row {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 10px 12px;
        }

        .info-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #3b82f6;
            flex-shrink: 0;
        }

        .info-text {
            font-size: 12px;
            color: #1d4ed8;
        }

        /* FIELDS */
        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .field label {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
        }

        .required {
            color: #ef4444;
        }

        .uid-wrap {
            position: relative;
        }

        .uid-badge {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            font-weight: 500;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 2px 8px;
            border-radius: 20px;
            pointer-events: none;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 9px 12px;
            font-size: 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-family: inherit;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: #378ADD;
            box-shadow: 0 0 0 3px rgba(55, 138, 221, 0.12);
        }

        input[readonly] {
            background: #f9fafb;
            color: #6b7280;
            cursor: not-allowed;
            font-family: monospace;
            font-size: 13px;
            padding-right: 90px;
        }

        textarea {
            resize: vertical;
            min-height: 90px;
            line-height: 1.5;
        }

        .form-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 0.25rem 0;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn-primary {
            flex: 1;
            padding: 10px;
            font-size: 14px;
            font-weight: 600;
            background: #378ADD;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.15s, transform 0.1s;
        }

        .btn-primary:hover {
            background: #185FA5;
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .btn-secondary {
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 500;
            background: #fff;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.15s;
        }

        .btn-secondary:hover {
            background: #f9fafb;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            padding: 1.5rem 0;
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
            <a class="nav-link active" href="register.php">Register Kartu</a>
            <a class="nav-link" href="data_mahasiswa.php">Data Mahasiswa</a>
            <a class="nav-link" href="scan_absensi.php">Absensi</a>
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
            <h1>Register Kartu RFID</h1>
            <p>Daftarkan kartu RFID ke data mahasiswa</p>
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

        <div class="card">
            <div class="card-header">
                <h2>Form Registrasi Mahasiswa</h2>
                <p>Pastikan kartu sudah di-scan sebelum mengisi form</p>
            </div>
            <div class="card-body">

                <div class="info-row">
                    <div class="info-dot"></div>
                    <span class="info-text">UID diambil otomatis dari scan RFID terakhir</span>
                </div>

                <form action="simpan_mahasiswa.php" method="POST">
                    <div class="field">
                        <label>UID Kartu</label>
                        <div class="uid-wrap">
                            <input type="text" name="uid" id="uid-field" value="" readonly
                                placeholder="Menunggu scan kartu...">
                            <span class="uid-badge" id="uid-badge" style="background:#f1f5f9; color:#9ca3af;">
                                Menunggu...
                            </span>
                        </div>
                    </div>

                    <div class="field" style="margin-top: 1rem;">
                        <label>NIM <span class="required">*</span></label>
                        <input type="text" name="nim" placeholder="Contoh: 2024010001" required>
                    </div>

                    <div class="field" style="margin-top: 1rem;">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="field" style="margin-top: 1rem;">
                        <label>Alamat</label>
                        <textarea name="alamat" placeholder="Jl. ..."></textarea>
                    </div>

                    <div class="form-divider" style="margin: 1.25rem 0;"></div>

                    <div class="actions">
                        <button type="button" class="btn-secondary" onclick="history.back()">&#8592; Kembali</button>
                        <button type="submit" class="btn-primary">Simpan Data</button>
                    </div>
                </form>

            </div>
        </div>

        <div class="footer">© <?= date("Y") ?> IoT Project - Teknik Informatika</div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const date = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            const time = now.toLocaleTimeString('id-ID');
            document.getElementById("clock").innerHTML = date + " " + time;
        }
        setInterval(updateClock, 1000);
        updateClock();
        let lastUid = "";

        function pollUID() {
            fetch("get_uid.php")
                .then(res => res.json())
                .then(data => {
                    const field = document.getElementById("uid-field");
                    const badge = document.getElementById("uid-badge");

                    if (data.uid && data.uid !== lastUid) {
                        // Ada UID baru dari scan
                        lastUid = data.uid;
                        field.value = data.uid;

                        // Update badge jadi hijau
                        badge.textContent = "Terdeteksi";
                        badge.style.background = "#dcfce7";
                        badge.style.color = "#15803d";

                        // Highlight field sebentar
                        field.style.borderColor = "#15803d";
                        field.style.boxShadow = "0 0 0 3px rgba(21,128,61,0.12)";
                        setTimeout(() => {
                            field.style.borderColor = "";
                            field.style.boxShadow = "";
                        }, 2000);

                    } else if (!data.uid) {
                        // Tidak ada scan dalam 10 detik terakhir
                        if (!lastUid) {
                            field.value = "";
                            field.placeholder = "Menunggu scan kartu...";
                            badge.textContent = "Menunggu...";
                            badge.style.background = "#f1f5f9";
                            badge.style.color = "#9ca3af";
                        }
                    }
                })
                .catch(err => console.error("Polling error:", err));
        }

        // Cek setiap 2 detik
        setInterval(pollUID, 2000);
        pollUID(); // langsung cek saat buka halaman
    </script>

</body>

</html>