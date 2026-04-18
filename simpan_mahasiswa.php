<?php
$conn = new mysqli("localhost", "root", "", "iot_rfid");

$uid = $_POST['uid'];
$nim = $_POST['nim'];
$nama = $_POST['nama'];
$alamat = $_POST['alamat'];

$sql = "INSERT INTO mahasiswa (uid, nim, nama, alamat)
        VALUES ('$uid', '$nim', '$nama', '$alamat')";

if ($conn->query($sql)) {
    echo "Berhasil disimpan!";
    header("Location: data_mahasiswa.php");
} else {
    echo "Gagal: " . $conn->error;
}
?>