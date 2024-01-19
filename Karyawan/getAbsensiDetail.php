<?php
// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "pengajuanabsensi3";

$koneksi = mysqli_connect($host, $username, $password, $dbname);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if(isset($_POST['id'])) {
    $absensiId = mysqli_real_escape_string($koneksi, $_POST['id']);

    // Query untuk mendapatkan detail absensi dan informasi tahapan persetujuan
    $query = "SELECT a.AbsensiID, a.TanggalPengajuan, a.NamaJenisAbsensi, a.Keterangan, 
              a.PeriodeAbsensi, a.WaktuPeriodeAbsensiMulai, a.WaktuPeriodeAbsensiSelesai, 
              pa.StatusPersetujuan, pa.TanggalPersetujuan, pa.TahapanSaatIni, 
              dap.RolePersetujuan, ap.NamaAlur
              FROM Absensi a
              LEFT JOIN PersetujuanAbsensi pa ON a.AbsensiID = pa.AbsensiID
              LEFT JOIN AlurPersetujuan ap ON pa.AlurPersetujuanID = ap.AlurPersetujuanID
              LEFT JOIN DetailAlurPersetujuan dap ON ap.AlurPersetujuanID = dap.AlurPersetujuanID AND pa.TahapanSaatIni = dap.Tahapan
              WHERE a.AbsensiID = '$absensiId'";

    $result = mysqli_query($koneksi, $query);

    if($result) {
        $data = mysqli_fetch_assoc($result);
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Tidak dapat mengambil data"]);
    }
} else {
    echo json_encode(["error" => "ID Absensi tidak diberikan"]);
}

mysqli_close($koneksi);
?>