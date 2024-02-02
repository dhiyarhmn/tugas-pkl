<?php
session_start();

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Direktur') {
    header("Location: /Login.php");
    exit();
}

// Fungsi untuk menangani persetujuan atau penolakan
// Fungsi untuk menangani persetujuan atau penolakan
function handleApplication($absensiID, $status) {
    global $koneksi;

    // Cek tahapan saat ini dan jumlah total tahapan
    $queryTahapan = "SELECT p.TahapanSaatIni, a.JumlahTahapan 
                    FROM PersetujuanAbsensi p
                    JOIN AlurPersetujuan a ON p.AlurPersetujuanID = a.AlurPersetujuanID 
                    WHERE p.AbsensiID = '$absensiID'";
    $resultTahapan = mysqli_query($koneksi, $queryTahapan);
    $dataTahapan = mysqli_fetch_assoc($resultTahapan);

    if ($dataTahapan) {
        $tahapanSaatIni = $dataTahapan['TahapanSaatIni'];
        $jumlahTahapan = $dataTahapan['JumlahTahapan'];

        if ($status == 'Approved') {
            // Cek apakah ini tahapan terakhir
            if ($tahapanSaatIni < $jumlahTahapan) {
                // Jika bukan tahapan terakhir, increment TahapanSaatIni
                // dan tetapkan status ke 'On Process'
                $tahapanSaatIni++;
                $queryUpdate = "UPDATE PersetujuanAbsensi 
                                SET TahapanSaatIni = '$tahapanSaatIni', StatusPersetujuan = 'On Process'
                                WHERE AbsensiID = '$absensiID'";
                mysqli_query($koneksi, $queryUpdate);
            } else {
                // Jika ini adalah tahapan terakhir, set status menjadi 'Approved'
                $queryUpdate = "UPDATE PersetujuanAbsensi 
                                SET StatusPersetujuan = 'Approved' 
                                WHERE AbsensiID = '$absensiID'";
                mysqli_query($koneksi, $queryUpdate);
            }
        } if ($status == 'Declined') {
            $queryCekJenis = "SELECT a.NamaJenisAbsensi, a.UserID, a.WaktuPeriodeAbsensiMulai, a.WaktuPeriodeAbsensiSelesai 
                              FROM Absensi a 
                              WHERE a.AbsensiID = '$absensiID'";
            $resultCekJenis = mysqli_query($koneksi, $queryCekJenis);
            $dataJenis = mysqli_fetch_assoc($resultCekJenis);
    
            if ($dataJenis && $dataJenis['NamaJenisAbsensi'] == 'AL') {
                $tanggalMulai = new DateTime($dataJenis['WaktuPeriodeAbsensiMulai']);
                $tanggalSelesai = new DateTime($dataJenis['WaktuPeriodeAbsensiSelesai']);
                $interval = $tanggalMulai->diff($tanggalSelesai);
    
                $jumlahHariCuti = 0;
                for ($i = 0; $i <= $interval->days; $i++) {
                    $tanggal = clone $tanggalMulai;
                    $tanggal->modify("+$i days");
                    if ($tanggal->format('N') < 6) {
                        $jumlahHariCuti++;
                    }
                }
    
                // Debugging: Periksa jumlah hari cuti
                echo "\n";
    
                $userID = $dataJenis['UserID'];
                $queryUpdateCuti = "UPDATE CutiTahunan 
                                    SET CutiTerpakai = CutiTerpakai - $jumlahHariCuti, 
                                        CutiSisa = CutiSisa + $jumlahHariCuti 
                                    WHERE UserID = '$userID'";
                if (mysqli_query($koneksi, $queryUpdateCuti)) {
                    // Debugging: Periksa apakah query berhasil
                    echo "\n";
                } else {
                    // Debugging: Periksa apakah ada error
                    echo "Error: " . mysqli_error($koneksi) . "\n";
                }
            }
    
            $queryUpdate = "UPDATE PersetujuanAbsensi 
                            SET StatusPersetujuan = 'Declined' 
                            WHERE AbsensiID = '$absensiID'";
            mysqli_query($koneksi, $queryUpdate);
        }
    }
}

// Cek jika ada form yang disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        handleApplication($_POST['absensiID'], 'Approved');
    } elseif (isset($_POST['decline'])) {
        handleApplication($_POST['absensiID'], 'Declined');
    }
}

// -------------------------------------------------------------------
// buat foto profile, nama lengkap, dan jabatan sesuai user yang login
// -------------------------------------------------------------------
// Adjust this query based on your actual database schema
$userDetailsQuery = "SELECT Direktur.NamaLengkap, Direktur.Departemen, Direktur.Jabatan, User.ProfilePhoto 
                     FROM Direktur
                     JOIN User ON Direktur.UserID = User.UserID
                     WHERE Direktur.UserID = '".$_SESSION["UserID"]."'";
$userDetailsResult = mysqli_query($koneksi, $userDetailsQuery);
$userDetails = mysqli_fetch_assoc($userDetailsResult);
$DirekturDepartemen = $userDetails['Departemen']; // Get the Direktur's department
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>PT. Daekyung Indah Heavy Industry</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/fonts.css"> 
</head>
<body>

<div class="wrapper">
<nav id="sidebar">
        <div class="sidebar-header">
            <button type="button" id="sidebarCollapse" class="btn" style="transition: 0.3s;">
                <i class="fas fa-bars"></i>
            </button>
            <div style="text-align: center; margin-top: 30px;">
                <?php
                // Contoh kode PHP untuk menampilkan foto profil
                $defaultProfilePhoto = 'ProfileDirektur/profile.jpeg'; // Lokasi foto default
                $userProfilePhoto = $userDetails['ProfilePhoto'] ?? null; // Foto profil yang diunggah oleh pengguna

                $photoToDisplay = $userProfilePhoto ? $userProfilePhoto : $defaultProfilePhoto; // Menentukan foto yang akan ditampilkan

                echo '<img src="'.htmlspecialchars($photoToDisplay).'" class="rounded-circle profile-image" style="margin-bottom: 10px;">';
                ?>
                <h3 class="profile-text" style="font-size: 16px; color:white"><?php echo $userDetails['NamaLengkap']; ?></h3>
                <h3 class="profile-text" style="font-size: 16px; color:white"><?php echo $userDetails['Departemen']; ?></h3>
                <h3 class="profile-text" style="font-size: 16px; color:white">-<?php echo $userDetails['Jabatan']; ?>-</h3>
            </div>
        </div>
        <ul class="list-unstyled components">
            <li>
                <a href="DashboardDirektur.php">
                    <i class="fas fa-tachometer-alt"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="EditProfileDirektur.php">
                    <i class="fas fa-user"></i> 
                    <span>Edit Profile</span>
                </a>
            </li>
            <li class="active">
                <a href="ApprovalDirektur.php">
                    <i class="fa fa-check-square"></i> 
                    <span>Approval</span>
                </a>
            </li>
            <li>
                <a href="ListKaryawanDirektur.php">
                    <i class="fa fa-search"></i> 
                    <span>List Pegawai</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-logout">
            <a href="/Logout.php" class="btn logout-button">
                <i class="fa fa-sign-out"></i>
                <span>Logout</span> <!-- Elemen ini akan disembunyikan ketika navbar tertutup -->
            </a>
        </div>
    </nav>
    <div id="content">
        <div class="container mt-3"> 
            <div class="row justify-content-center align-items-center">
                <div class="col-auto">
                    <img src="/Assets/img/logo3.png" class="img-fluid" style="max-width: 60px; height: auto;">  
                </div>
                <div class="col-auto">
                    <h2>PT. DAEKYUNG INDAH HEAVY INDUSTRY</h2>
                </div>
            </div>
            
            <div class="custom-table-container" style="margin-top: 30px;">
                <table class="table table-bordered" style="background-color: rgba(220, 220, 220, 0.8);" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center table-column" style="width: 30px;">No</th>
                            <th class="text-center table-column" style="width: 30px;">AbsensiID</th>
                            <th class="text-center table-column" style="width: 200px;">Nama</th>
                            <th class="text-center table-column" style="width: 100px;">NIK</th>
                            <th class="text-center table-column" style="width: 30px;">Jenis Absensi</th>
                            <th class="text-center table-column" style="width: 30px;">Tanggal Pengajuan</th>
                            <th class="text-center table-column" style="width: 50px;">Berkas</th>
                            <th class="text-center table-column" style="width: 145px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $query = "SELECT 
                                    a.AbsensiID, 
                                    COALESCE(k.NamaLengkap, m.NamaLengkap, h.NamaLengkap, d.NamaLengkap) AS NamaLengkap, 
                                    COALESCE(k.NIK, m.NIK, h.NIK, d.NIK) AS NIK, 
                                    ja.NamaJenisAbsensi, 
                                    a.TanggalPengajuan, 
                                    a.Keterangan, 
                                    pa.StatusPersetujuan,
                                    pa.TanggalPersetujuan,
                                    pa.AlurPersetujuanID,
                                    pa.TahapanSaatIni,
                                    u.Role,
                                    a.Berkas  
                                FROM Absensi a
                                JOIN PersetujuanAbsensi pa ON a.AbsensiID = pa.AbsensiID
                                LEFT JOIN User u ON a.UserID = u.UserID
                                LEFT JOIN Karyawan k ON u.UserID = k.UserID
                                LEFT JOIN Manajer m ON u.UserID = m.UserID
                                LEFT JOIN HRGA h ON u.UserID = h.UserID
                                LEFT JOIN Direktur d ON u.UserID = d.UserID
                                JOIN JenisAbsensi ja ON a.NamaJenisAbsensi = ja.NamaJenisAbsensi
                                WHERE ((pa.AlurPersetujuanID = 2 AND pa.TahapanSaatIni = 2) OR (pa.AlurPersetujuanID = 3 AND pa.TahapanSaatIni = 1))
                                AND pa.StatusPersetujuan NOT IN ('Approved', 'Declined')";

                        $result = mysqli_query($koneksi, $query);
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Tentukan folder berkas berdasarkan role
                            $folderBerkas = ''; 
                            switch ($row['Role']) {
                                case 'Admin':
                                    $folderBerkas = 'Admin/BerkasAdmin';
                                    break;
                                case 'Karyawan':
                                    $folderBerkas = 'Karyawan/BerkasKaryawan';
                                    break;
                                case 'Manajer':
                                    $folderBerkas = 'Manajer/BerkasManajer';
                                    break;
                                case 'HRGA':
                                    $folderBerkas = 'HRGA/BerkasHRGA';
                                    break;
                                case 'Direktur':
                                    $folderBerkas = 'Direktur/BerkasDirektur';
                                    break;
                            }

                            $namaBerkas = basename($row['Berkas']);
                            $berkasLink = !empty($namaBerkas) ? "<a href='/$folderBerkas/" . urlencode($namaBerkas) . "' target='_blank'>Lihat Berkas</a>" : "Tidak ada berkas";

                            echo "<tr class='text-center'>
                                    <td>{$no}</td>
                                    <td>{$row['AbsensiID']}</td>
                                    <td>{$row['NamaLengkap']}</td>
                                    <td>{$row['NIK']}</td>
                                    <td>{$row['NamaJenisAbsensi']}</td>
                                    <td>{$row['TanggalPengajuan']}</td>
                                    <td class='text-center'>{$berkasLink}</td>
                                    <td>
                                        <form method='post'>
                                            <input type='hidden' name='absensiID' value='{$row['AbsensiID']}'>
                                            <button type='submit' name='approve' class='btn custom-approval-btn-green'>Approve</button>
                                            <button type='submit' name='decline' class='btn custom-decline-btn-red'>Decline</button>
                                        </form>
                                        <div style='text-align: center; margin-top: 10px;'>
                                            <button type='button' class='btn custom-detail-btn-blue' onclick='showDetail({$row['AbsensiID']})'>Detail</button>
                                        </div>
                                    </td>
                                </tr>";
                            $no++;
                        }
                    ?>
                    </tbody>
                </table>
            </div>
    </div>
</div>
<!-- Popup card detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Pengajuan Absensi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Isi detail pengajuan absensi akan ditampilkan di sini -->
        <!-- Anda bisa menambahkan informasi absensi seperti jenis, tanggal, dan lainnya di sini -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Popup card approve -->
<div id="approveModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Approval</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        Pengajuan Absensi Berhasil Di Approve
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Popup card decline -->
<div id="declineModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Decline</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        Pengajuan Absensi Berhasil Di Decline
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap and jQuery libraries -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/datatables-demo.js"></script>
<script src="js/datatables-demo.js"></script>

<script src="./js/detailAbsensi.js"></script>
<script src="./js/script.js"></script>

</body>
</html>