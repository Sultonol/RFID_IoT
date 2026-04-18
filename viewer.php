<?php
$conn = new mysqli("localhost", "root", "", "iot_rfid");
$today = date("Y-m-d");

// Total scan hari ini
$r = $conn->query("SELECT COUNT(*) as total, MAX(waktu) as last FROM rfid_logs WHERE DATE(waktu) = '$today'");
$row = $r->fetch_assoc();
$total_scan = $row['total'];
$waktu_terakhir = $row['last'] ? date("H:i", strtotime($row['last'])) . " WIB" : "-";

// Total mahasiswa terdaftar
$r2 = $conn->query("SELECT COUNT(*) as total FROM mahasiswa");
$total_mahasiswa = $r2->fetch_assoc()['total'];

// Hadir hari ini (uid yang scan hari ini dan terdaftar)
$r3 = $conn->query("SELECT COUNT(DISTINCT rl.uid) as hadir FROM rfid_logs rl
                    INNER JOIN mahasiswa m ON m.uid = rl.uid
                    WHERE DATE(rl.waktu) = '$today'");
$total_hadir = $r3->fetch_assoc()['hadir'];
$total_absen = $total_mahasiswa - $total_hadir;
$belum_terdaftar = $conn->query("SELECT COUNT(*) as c FROM rfid_logs WHERE uid NOT IN (SELECT uid FROM mahasiswa)")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>RFID Monitoring System</title>

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

        .nav-link svg {
            width: 14px;
            height: 14px;
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
        .page-header {
            margin-top: 1.25rem;
            margin-bottom: 1.25rem;
            margin-left: 1.3rem;
            padding: 4px;
        }

        .page-header h1 {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .page-header p {
            font-size: 13px;
            color: #6b7280;
            margin-top: 2px;
        }

        .team-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .team-label {
            font-size: 12px;
            color: #6b7280;
        }

        .team-chip {
            font-size: 12px;
            font-weight: 500;
            background: #e0f2fe;
            color: #0369a1;
            padding: 3px 10px;
            border-radius: 20px;
        }

        /* ===== STATS ===== */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin: 1.5rem;
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

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
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

        /* .table-container {
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 20px;
        } */

        .uid {
            font-family: monospace;
            font-size: 12px;
            color: #1d4ed8;
            background: #eff6ff;
            padding: 2px 7px;
            border-radius: 5px;
            display: inline-block;
        }

        .status-hadir {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #15803d;
            background: #dcfce7;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .status-hadir::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #15803d;
        }

        .status-absen {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #b45309;
            background: #fef3c7;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .status-absen::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #b45309;
        }

        /* ===== FOOTER ===== */
        .footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            padding: 1.5rem 0;
        }

        /* ===== PAGE SECTIONS ===== */
        .section {
            display: none;
        }

        .section.active {
            display: block;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-brand">
            <div class="nav-brand-icon">
                <!-- icon SVG RFID di sini -->
            </div>
            <div>
                <div class="nav-brand-name">RFID Monitoring</div>
                <div class="nav-brand-sub">ESP32 · MQTT · HTTP</div>
            </div>
        </div>

        <div class="nav-links">
            <a class="nav-link active" href="index.php">Dashboard</a>
            <a class="nav-link" href="data_mahasiswa.php">Data Mahasiswa</a>
            <a class="nav-link" href="absensi.php">Absensi</a>
            <a class="nav-link" href="register.php">Register Kartu</a>
        </div>

        <div class="nav-right">
            <div class="live-badge">
                <div class="live-dot"></div>LIVE
            </div>
            <div class="nav-clock" id="clock"></div>
        </div>
    </nav>
    <div class="page-header">
        <h1>Dashboard Monitoring</h1>
        <p>Sistem monitoring RFID berbasis ESP32 &amp; MQTT — data realtime dari perangkat IoT</p>
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
            <div class="stat-label">Total Scan Hari Ini</div>
            <div class="stat-value">
                <?= $total_scan ?>
            </div>
            <div class="stat-sub">Terakhir:
                <?= $waktu_terakhir ?>
            </div>
        </div>
        <div class="stat blue">
            <div class="stat-label">Mahasiswa Terdaftar</div>
            <div class="stat-value">
                <?= $total_mahasiswa ?>
            </div>
            <div class="stat-sub">
                <?= $belum_terdaftar ?> belum terdaftar
            </div>
        </div>
        <div class="stat amber">
            <div class="stat-label">Hadir Hari Ini</div>
            <div class="stat-value">
                <?= $total_hadir ?>
            </div>
            <div class="stat-sub">
                <?= $total_absen ?> mahasiswa absen
            </div>
        </div>
    </div>
    <div class="table-container">
        <table>
            <tr>
                <th>No</th>
                <th>UID Kartu</th>
                <th>Nama</th>
                <th>Waktu Scan</th>
            </tr>

            <?php
            $conn = new mysqli("localhost", "root", "", "iot_rfid");

            if ($conn->connect_error) {
                echo "<tr><td colspan='3'>Koneksi database gagal</td></tr>";
            } else {

                $result = $conn->query("SELECT * FROM rfid_logs ORDER BY id DESC");

                $no = 1;

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td class='uid'>{$row['uid']}</td>";
                    echo "<td>{$row['nama']}</td>";
                    echo "<td>{$row['waktu']}</td>";
                    echo "</tr>";
                    $no++;
                }
            }
            ?>
        </table>
    </div>


    <div class="footer">
        © <?= date("Y"); ?> IoT Project - Teknik Informatika
    </div>

    </div>

    <!-- 🔥 REALTIME CLOCK -->
    <script>
        function updateClock() {
            const now = new Date();

            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };

            const date = now.toLocaleDateString('id-ID', options);
            const time = now.toLocaleTimeString('id-ID');

            document.getElementById("clock").innerHTML = date + " " + time;
        }

        // update tiap 1 detik
        setInterval(updateClock, 1000);

        // jalankan langsung saat load
        updateClock();
    </script>

</body>

</html>