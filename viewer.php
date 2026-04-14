<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RFID Monitoring System</title>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e3f2fd, #ffffff);
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }

        .header {
            background: #ffffff;
            border-radius: 18px;
            padding: 25px 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .title {
            font-size: 30px;
            font-weight: bold;
            color: #1565c0;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .team-label {
            font-weight: 600;
            margin-bottom: 10px;
            display: block;
            color: #444;
        }

        .team-members {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .team-members span {
            background: #1565c0;
            color: white;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 13px;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .live {
            color: green;
            font-weight: bold;
        }

        .time {
            font-size: 14px;
            font-weight: 500;
            color: #1565c0;
            margin-right: 10px;
        }

        .refresh-btn {
            background: #1565c0;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
        }

        .refresh-btn:hover {
            background: #0d47a1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #1565c0;
            color: white;
            padding: 12px;
        }

        td {
            padding: 11px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f1f7ff;
        }

        .uid {
            font-weight: bold;
            color: #1565c0;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div class="title">📡 RFID Monitoring System</div>
        <div class="subtitle">Sistem Monitoring IoT berbasis ESP32, MQTT, dan HTTP</div>

        <span class="team-label">Kelompok:</span>
        <div class="team-members">
            <span>Hanifah</span>
            <span>Hidayah</span>
            <span>Sulton</span>
            <span>Zaki</span>
            <span>Taufiq</span>
        </div>
    </div>

    <!-- DATA -->
    <div class="card">

        <div class="status">
            <div class="live">● LIVE DATA</div>

            <div>
                <span class="time" id="clock"></span>
                <button class="refresh-btn" onclick="location.reload()">🔄 Refresh</button>
            </div>
        </div>

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