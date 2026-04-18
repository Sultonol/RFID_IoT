<?php

$conn = new mysqli("localhost", "root", "", "iot_rfid");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$rawData = file_get_contents("php://input");

// kalau tidak ada data
if (!$rawData) {
    echo "Server aktif (tidak ada data)";
    exit;
}

$data = json_decode($rawData, true);

if ($data) {

    $uid = $conn->real_escape_string($data['uid']);
    $waktu = $conn->real_escape_string($data['time']);

    // 🔥 CARI NAMA DI TABLE USERS
    $query = "SELECT nama FROM mahasiswa WHERE uid='$uid'";
    $result = $conn->query($query);

    if ($row = $result->fetch_assoc()) {
        $nama = $row['nama'];
    } else {
        $nama = "Tidak Dikenal";
    }

    // 🔥 SIMPAN KE rfid_logs
    $sql = "INSERT INTO rfid_logs (uid, nama, waktu) VALUES ('$uid', '$nama', '$waktu')";

    if ($conn->query($sql) === TRUE) {
        echo "OK";
    } else {
        echo "Gagal simpan";
    }

} else {
    echo "JSON tidak valid";
}

?>