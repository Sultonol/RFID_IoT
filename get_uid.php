<?php
$conn = new mysqli("localhost", "root", "", "iot_rfid");

// Ambil UID yang scan dalam 10 detik terakhir saja
$result = $conn->query("SELECT uid FROM rfid_logs WHERE waktu >= NOW() - INTERVAL 10 SECOND ORDER BY id DESC LIMIT 1");

$uid = "";
if ($row = $result->fetch_assoc()) {
    $uid = $row['uid'];
}

echo json_encode(["uid" => $uid]);
?>