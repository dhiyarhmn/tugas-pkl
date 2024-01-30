<?php
session_start();
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!$koneksi) {
    echo json_encode(["error" => "Koneksi ke database gagal"]);
    exit;
}

if (isset($_POST['userID'])) {
    $userID = $_POST['userID'];
    $query = "SELECT CutiSisa FROM CutiTahunan WHERE UserID = '$userID'";
    $result = mysqli_query($koneksi, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(["cutiSisa" => $row['CutiSisa']]);
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }
}
?>