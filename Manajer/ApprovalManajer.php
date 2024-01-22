<?php
session_start();

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Manajer') {
    header("Location: /Login.php");
    exit();
}

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

        if ($tahapanSaatIni < $jumlahTahapan) {
            $statusBaru = ($status == 'Approved') ? 'On Process' : 'Declined';
            $tahapanSelanjutnya = $tahapanSaatIni + 1;
            $queryUpdate = "UPDATE PersetujuanAbsensi 
                            SET StatusPersetujuan = '$statusBaru', TahapanSaatIni = '$tahapanSelanjutnya' 
                            WHERE AbsensiID = '$absensiID'";
        } else {
            $queryUpdate = "UPDATE PersetujuanAbsensi 
                            SET StatusPersetujuan = '$status', TahapanSaatIni = '$tahapanSaatIni' 
                            WHERE AbsensiID = '$absensiID'";
        }

        mysqli_query($koneksi, $queryUpdate);
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

// buat foto profile, nama lengkap, dan jabatan sesuai user yang login
// Adjust this query based on your actual database schema
$userDetailsQuery = "SELECT Manajer.NamaLengkap, Manajer.Jabatan, User.ProfilePhoto 
                     FROM Manajer
                     JOIN User ON Manajer.UserID = User.UserID
                     WHERE Manajer.UserID = '".$_SESSION["UserID"]."'";
$userDetailsResult = mysqli_query($koneksi, $userDetailsQuery);
$userDetails = mysqli_fetch_assoc($userDetailsResult);
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
            <button type="button" id="sidebarCollapse" class="btn">
                <i class="fas fa-bars"></i>
            </button>
            <div style="text-align: center; margin-top: 30px;">
                <img src="/Assets/img/<?php echo $userDetails['ProfilePhoto']; ?>" width="80" class="rounded-circle" style="margin-bottom: 10px;">
                <h3 class="profile-text" style="font-size: 16px; color:white"><?php echo $userDetails['NamaLengkap']; ?></h3>
                <h3 class="profile-text" style="font-size: 16px; color:white">[<?php echo $userDetails['Jabatan']; ?>]</h3>
              </div>
        </div>
        <ul class="list-unstyled components">
            <li>
                <a href="DashboardManajer.php">
                    <i class="fas fa-tachometer-alt"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="EditProfileManajer.php">
                    <i class="fas fa-user"></i> 
                    <span>Edit Profile</span>
                </a>
            </li>
            <li>
                <a href="PengajuanAbsensiManajer.php">
                    <i class="fas fa-plus"></i> 
                    <span>Pengajuan Absensi</span>
                </a>
            </li>
            <li>
                <a href="StatusPengajuanManajer.php">
                    <i class="fas fa-list-alt"></i> 
                    <span>Status Pengajuan</span>
                </a>
            </li>
            <li class="active">
                <a href="ApprovalManajer.php">
                    <i class="fa fa-check-square"></i> 
                    <span>Approval</span>
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
            
            <div class="custom-table-container">
                <table class="table table-bordered" style="background-color: rgba(220, 220, 220, 0.8);" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center table-column">No</th>
                            <th class="text-center table-column">Nama</th>
                            <th class="text-center table-column">NIK</th>
                            <th class="text-center table-column">Jenis Absensi</th>
                            <th class="text-center table-column">Tanggal Pengajuan</th>
                            <th class="text-center table-column">Berkas</th>
                            <th class="text-center table-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT a.AbsensiID, k.NamaLengkap, k.NIK, a.NamaJenisAbsensi, a.TanggalPengajuan, a.Berkas 
                                  FROM Absensi a 
                                  JOIN Karyawan k ON a.UserID = k.UserID 
                                  JOIN PersetujuanAbsensi p ON a.AbsensiID = p.AbsensiID
                                  JOIN DetailAlurPersetujuan d ON p.AlurPersetujuanID = d.AlurPersetujuanID
                                  WHERE p.StatusPersetujuan = 'On Process' 
                                    AND d.AlurPersetujuanID = 1 
                                    AND d.RolePersetujuan = 'Manajer'";
                        $result = mysqli_query($koneksi, $query);
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Membuat link ke berkas
                            $berkasLink = !empty($row['Berkas']) ? "<a href='/Karyawan/{$row['Berkas']}' target='_blank'>Lihat Berkas</a>" : "Tidak ada berkas";

                            echo "<tr>
                                    <td class='text-center'>{$no}</td>
                                    <td>{$row['NamaLengkap']}</td>
                                    <td>{$row['NIK']}</td>
                                    <td>{$row['NamaJenisAbsensi']}</td>
                                    <td>{$row['TanggalPengajuan']}</td>
                                    <td class='text-center'>{$berkasLink}</td>
                                    <td>
                                        <form method='post'>
                                          <input type='hidden' name='absensiID' value='{$row['AbsensiID']}'>
                                          <button type='submit' name='approve' class='btn custom-approval-btn-green' data-toggle='modal'>Approve</button>
                                          <button type='submit' name='decline' class='btn custom-decline-btn-red' data-toggle='modal'>Decline</button>
                                          
                                        </form>
                                        <div style='text-align: center; margin-top: 10px;'>
                                          <button type='button' class='btn custom-detail-btn-blue' data-toggle='modal' data-target='#detailModal'>Detail</button>
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
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/datatables-demo.js"></script>
<script src="js/datatables-demo.js"></script>

<script src="./js/detailAbsensi.js"></script>
<script src="./js/script.js"></script>

</body>
</html>