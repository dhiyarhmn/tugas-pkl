<?php
session_start();

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Cek apakah pengguna adalah Admin
if (isset($_SESSION["Role"]) && $_SESSION["Role"] == 'Admin') {
    // Query untuk reset cuti tahunan
    $queryResetCuti = "UPDATE CutiTahunan SET JatahCuti = JatahCuti + CutiSisa, CutiSisa = CutiSisa + 12, CutiTerpakai = 0 WHERE Tahun = YEAR(CURDATE())";
    
    if ($koneksi->query($queryResetCuti) === TRUE) {
        echo "Reset cuti tahunan berhasil.";
    } else {
        echo "Error: " . $koneksi->error;
    }
} else {
    echo "Akses ditolak.";
}

$koneksi->close();
?>