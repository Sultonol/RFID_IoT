<?php
$conn = new mysqli("localhost", "root", "", "iot_rfid");

$aksi = $_POST['aksi'] ?? '';
$id   = intval($_POST['id'] ?? 0);

if ($aksi === 'edit') {
    $nim    = $conn->real_escape_string($_POST['nim']);
    $nama   = $conn->real_escape_string($_POST['nama']);
    $alamat = $conn->real_escape_string($_POST['alamat']);
    $uid    = $conn->real_escape_string($_POST['uid']);

    $conn->query("UPDATE mahasiswa SET nim='$nim', nama='$nama', alamat='$alamat', uid='$uid' WHERE id=$id");

} elseif ($aksi === 'hapus') {
    $conn->query("DELETE FROM mahasiswa WHERE id=$id");
}

header("Location: data_mahasiswa.php");
exit;