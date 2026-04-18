<?php
$conn = new mysqli("localhost", "root", "", "iot_rfid");
$result = $conn->query("SELECT * FROM mahasiswa");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Mahasiswa</title>
</head>
<body>

<h2>Data Mahasiswa</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>No</th>
        <th>UID</th>
        <th>NIM</th>
        <th>Nama</th>
        <th>Alamat</th>
    </tr>

    <?php
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>$no</td>
            <td>{$row['uid']}</td>
            <td>{$row['nim']}</td>
            <td>{$row['nama']}</td>
            <td>{$row['alamat']}</td>
        </tr>";
        $no++;
    }
    ?>

</table>

</body>
</html>