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

    // Query untuk mendapatkan detail absensi beserta informasi pengguna (termasuk departemen dan jabatan)
    $query = "SELECT a.AbsensiID, a.TanggalPengajuan, a.NamaJenisAbsensi, a.Keterangan, 
              a.PeriodeAbsensi, a.WaktuPeriodeAbsensiMulai, a.WaktuPeriodeAbsensiSelesai, 
              pa.StatusPersetujuan, pa.TanggalPersetujuan, pa.TahapanSaatIni, 
              dap.RolePersetujuan, ap.NamaAlur,
              COALESCE(ad.Departemen, ak.Departemen, am.Departemen, ah.Departemen, adi.Departemen) AS Departemen,
              COALESCE(ad.Jabatan, ak.Jabatan, am.Jabatan, ah.Jabatan, adi.Jabatan) AS Jabatan
              FROM Absensi a
              LEFT JOIN PersetujuanAbsensi pa ON a.AbsensiID = pa.AbsensiID
              LEFT JOIN AlurPersetujuan ap ON pa.AlurPersetujuanID = ap.AlurPersetujuanID
              LEFT JOIN DetailAlurPersetujuan dap ON ap.AlurPersetujuanID = dap.AlurPersetujuanID AND pa.TahapanSaatIni = dap.Tahapan
              LEFT JOIN User u ON a.UserID = u.UserID
              LEFT JOIN Admin ad ON u.UserID = ad.UserID
              LEFT JOIN Karyawan ak ON u.UserID = ak.UserID
              LEFT JOIN Manajer am ON u.UserID = am.UserID
              LEFT JOIN HRGA ah ON u.UserID = ah.UserID
              LEFT JOIN Direktur adi ON u.UserID = adi.UserID
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