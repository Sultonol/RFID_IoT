<?php
$conn = new mysqli("localhost", "root", "", "iot_rfid");

// ambil UID terakhir dari scan
$uid_result = $conn->query("SELECT uid FROM rfid_logs ORDER BY id DESC LIMIT 1");
$uid = "";

if ($row = $uid_result->fetch_assoc()) {
    $uid = $row['uid'];
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registrasi Mahasiswa</title>
</head>
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Segoe UI', system-ui, sans-serif;
        background: #f0f4f8;
        min-height: 100vh;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 2rem 1rem;
        color: #1a1a2e;
    }

    .wrapper {
        width: 100%;
        max-width: 540px;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 1.5rem;
    }

    .brand-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #378ADD;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .brand-icon svg {
        width: 18px;
        height: 18px;
    }

    .brand-title {
        font-size: 15px;
        font-weight: 600;
        color: #1a1a2e;
    }

    .brand-sub {
        font-size: 12px;
        color: #6b7280;
    }

    .card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .card-header h2 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .card-header p {
        font-size: 13px;
        color: #6b7280;
    }

    .card-body {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

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
        padding-right: 80px;
    }

    textarea {
        resize: vertical;
        min-height: 90px;
        line-height: 1.5;
    }

    .divider {
        height: 1px;
        background: #e5e7eb;
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
</style>

<body>
    <div class="wrapper">
        <div class="brand">
            <div class="brand-icon">
                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="5" width="14" height="9" rx="2" stroke="white" stroke-width="1.4" />
                    <path d="M6 5V4a3 3 0 016 0v1" stroke="white" stroke-width="1.4" stroke-linecap="round" />
                    <circle cx="9" cy="9.5" r="1.5" fill="white" />
                </svg>
            </div>
            <div>
                <div class="brand-title">Sistem RFID Kampus</div>
                <div class="brand-sub">IoT Student Management</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Registrasi Mahasiswa</h2>
                <p>Daftarkan kartu RFID ke data mahasiswa</p>
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
                            <input type="text" name="uid" value="<?= $uid ?>" readonly>
                            <span class="uid-badge">Terdeteksi</span>
                        </div>
                    </div>

                    <div class="field" style="margin-top:1rem;">
                        <label>NIM <span class="required">*</span></label>
                        <input type="text" name="nim" placeholder="Contoh: 2024010001" required>
                    </div>

                    <div class="field" style="margin-top:1rem;">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="field" style="margin-top:1rem;">
                        <label>Alamat</label>
                        <textarea name="alamat" placeholder="Jl. ..."></textarea>
                    </div>

                    <div class="divider" style="margin:1.25rem 0;"></div>

                    <div class="actions">
                        <button type="button" class="btn-secondary" onclick="history.back()">&#8592; Kembali</button>
                        <button type="submit" class="btn-primary">Simpan Data</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>

</html>