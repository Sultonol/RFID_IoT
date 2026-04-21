<?php
$conn = new mysqli("localhost", "root", "", "iot_rfid");
$result = $conn->query("SELECT * FROM mahasiswa ORDER BY id ASC");
$total = $conn->query("SELECT COUNT(*) as total FROM mahasiswa")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa - RFID Monitoring</title>
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

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal {
            background: #fff;
            border-radius: 14px;
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: #fafafa;
        }

        .modal-header h3 {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
            margin: 0;
        }

        .modal-close {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 18px;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.12s;
        }

        .modal-close:hover {
            background: #f1f5f9;
        }

        .modal-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .modal-footer {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            padding: 14px 20px;
            border-top: 1px solid #e5e7eb;
            background: #fafafa;
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

        .field input,
        .field textarea {
            padding: 9px 12px;
            font-size: 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-family: inherit;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            width: 100%;
        }

        .field input:focus,
        .field textarea:focus {
            border-color: #378ADD;
            box-shadow: 0 0 0 3px rgba(55, 138, 221, 0.12);
        }

        .field input[readonly] {
            background: #f9fafb;
            color: #6b7280;
            cursor: not-allowed;
            font-family: monospace;
        }

        .field textarea {
            resize: vertical;
            min-height: 80px;
            line-height: 1.5;
        }

        .btn-danger {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid #fca5a5;
            background: #fff;
            color: #dc2626;
            cursor: pointer;
            transition: background 0.12s;
        }

        .btn-danger:hover {
            background: #fef2f2;
        }

        .btn-save {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 16px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
            border: none;
            background: #378ADD;
            color: #fff;
            cursor: pointer;
            transition: background 0.12s;
        }

        .btn-save:hover {
            background: #185FA5;
        }

        .btn-cancel {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 16px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #374151;
            cursor: pointer;
            transition: background 0.12s;
        }

        .btn-cancel:hover {
            background: #f1f5f9;
        }

        /* HAPUS MODAL */
        .hapus-info {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 13px;
            color: #dc2626;
        }

        .hapus-nama {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
            margin-top: 8px;
        }

        .hapus-nim {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* NAVBAR */
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

        /* PAGE */
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

        /* STAT */
        .stat-row {
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

        .stat.blue .stat-value {
            color: #1d4ed8;
        }

        .stat.green .stat-value {
            color: #15803d;
        }

        /* TABLE CARD */
        .table-card {
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

        .card-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #374151;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.12s;
        }

        .btn:hover {
            background: #f1f5f9;
        }

        .btn.primary {
            background: #378ADD;
            color: #fff;
            border-color: #378ADD;
        }

        .btn.primary:hover {
            background: #185FA5;
        }

        .btn svg {
            width: 13px;
            height: 13px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-align: left;
            padding: 10px 16px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        td {
            font-size: 13px;
            color: #1a1a2e;
            padding: 10px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .uid {
            font-family: monospace;
            font-size: 12px;
            color: #1d4ed8;
            background: #eff6ff;
            padding: 2px 7px;
            border-radius: 5px;
            display: inline-block;
        }

        .empty-row td {
            text-align: center;
            padding: 2.5rem 1rem;
            color: #9ca3af;
            font-size: 13px;
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
            <a class="nav-link" href="register.php">Register Kartu</a>
            <a class="nav-link active" href="data_mahasiswa.php">Data Mahasiswa</a>
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
            <h1>Data Mahasiswa</h1>
            <p>Daftar mahasiswa yang telah mendaftarkan kartu RFID</p>
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

        <div class="stat-row">
            <div class="stat blue">
                <div class="stat-label">Total Mahasiswa</div>
                <div class="stat-value"><?= $total ?></div>
                <div class="stat-sub">Mahasiswa terdaftar</div>
            </div>
            <div class="stat green">
                <div class="stat-label">Kartu Aktif</div>
                <div class="stat-value"><?= $total ?></div>
                <div class="stat-sub">Kartu RFID terhubung</div>
            </div>
            <div class="stat">
                <div class="stat-label">Terakhir Diperbarui</div>
                <div class="stat-value" style="font-size:14px; padding-top:4px;"><?= date("d M Y") ?></div>
                <div class="stat-sub"><?= date("H:i") ?> WIB</div>
            </div>
        </div>

        <div class="table-card">
            <div class="card-head">
                <span class="card-title">Daftar Mahasiswa Terdaftar</span>
                <div class="card-actions">
                    <a class="btn primary" href="register.php">
                        <svg viewBox="0 0 13 13" fill="none">
                            <circle cx="6.5" cy="6.5" r="5.5" stroke="white" stroke-width="1.2" />
                            <path d="M6.5 4v5M4 6.5h5" stroke="white" stroke-width="1.2" stroke-linecap="round" />
                        </svg>
                        Register Kartu
                    </a>
                </div>
            </div>

            <table>
                <tr>
                    <th style="width:50px">No</th>
                    <th style="width:130px">UID Kartu</th>
                    <th style="width:120px">NIM</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th style="width:100px">Aksi</th>
                </tr>
                <?php if ($total == 0): ?>
                    <tr class="empty-row">
                        <td colspan="6">
                            <div style="font-size:22px; margin-bottom:6px;">📭</div>
                            Belum ada mahasiswa terdaftar
                        </td>
                    </tr>
                <?php else:
                    $no = 1;
                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="uid"><?= htmlspecialchars($row['uid']) ?></span></td>
                            <td><?= htmlspecialchars($row['nim']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['alamat']) ?></td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <button class="btn" onclick="openEdit(
                '<?= $row['id'] ?>',
                '<?= htmlspecialchars($row['uid'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['nim'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['nama'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['alamat'], ENT_QUOTES) ?>'
            )">
                                        <svg viewBox="0 0 13 13" fill="none" width="13" height="13">
                                            <path d="M9 2l2 2-6 6H3V8l6-6z" stroke="currentColor" stroke-width="1.2"
                                                stroke-linejoin="round" />
                                        </svg>
                                        Edit
                                    </button>
                                    <button class="btn-danger" onclick="openHapus(
                '<?= $row['id'] ?>',
                '<?= htmlspecialchars($row['nama'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['nim'], ENT_QUOTES) ?>'
            )">
                                        <svg viewBox="0 0 13 13" fill="none" width="13" height="13">
                                            <path d="M2 3h9M5 3V2h3v1M4 3l.5 7h4L9 3" stroke="currentColor" stroke-width="1.2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
            </table>
        </div>

        <div class="footer">© <?= date("Y") ?> IoT Project - Teknik Informatika</div>
    </div>
    <!-- MODAL EDIT -->
    <div class="modal-overlay" id="modal-edit">
        <div class="modal">
            <div class="modal-header">
                <h3>Edit Data Mahasiswa</h3>
                <button class="modal-close" onclick="closeModal('modal-edit')">&#x2715;</button>
            </div>
            <form method="POST" action="aksi_mahasiswa.php">
                <input type="hidden" name="aksi" value="edit">
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-body">
                    <div class="field">
                        <label>UID Kartu</label>
                        <input type="text" id="edit-uid" name="uid" readonly>
                    </div>
                    <div class="field">
                        <label>NIM</label>
                        <input type="text" id="edit-nim" name="nim" required>
                    </div>
                    <div class="field">
                        <label>Nama Lengkap</label>
                        <input type="text" id="edit-nama" name="nama" required>
                    </div>
                    <div class="field">
                        <label>Alamat</label>
                        <textarea id="edit-alamat" name="alamat"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-edit')">Batal</button>
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL HAPUS -->
    <div class="modal-overlay" id="modal-hapus">
        <div class="modal">
            <div class="modal-header">
                <h3>Hapus Data Mahasiswa</h3>
                <button class="modal-close" onclick="closeModal('modal-hapus')">&#x2715;</button>
            </div>
            <form method="POST" action="aksi_mahasiswa.php">
                <input type="hidden" name="aksi" value="hapus">
                <input type="hidden" name="id" id="hapus-id">
                <div class="modal-body">
                    <div class="hapus-info">
                        Tindakan ini tidak dapat dibatalkan. Data mahasiswa berikut akan dihapus permanen.
                    </div>
                    <div>
                        <div class="hapus-nama" id="hapus-nama"></div>
                        <div class="hapus-nim" id="hapus-nim"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-hapus')">Batal</button>
                    <button type="submit" class="btn-save" style="background:#dc2626;">Ya, Hapus</button>
                </div>
            </form>
        </div>
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
        function openEdit(id, uid, nim, nama, alamat) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-uid').value = uid;
            document.getElementById('edit-nim').value = nim;
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-alamat').value = alamat;
            document.getElementById('modal-edit').classList.add('show');
        }

        function openHapus(id, nama, nim) {
            document.getElementById('hapus-id').value = id;
            document.getElementById('hapus-nama').textContent = nama;
            document.getElementById('hapus-nim').textContent = 'NIM: ' + nim;
            document.getElementById('modal-hapus').classList.add('show');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
        }

        // Tutup modal kalau klik overlay
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function (e) {
                if (e.target === this) closeModal(this.id);
            });
        });
    </script>

</body>

</html>