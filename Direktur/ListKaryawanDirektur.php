<?php
session_start();

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Direktur') {
    header("Location: /Login.php");
    exit();
}

// Inisialisasi variabel
$rowDirektur = null;
$userDetails = [];

// Cek apakah UserID ada di session
if (isset($_SESSION['UserID'])) {
    $userID = $_SESSION['UserID'];

    // Ambil data Direktur dan username berdasarkan UserID
    $queryGetDataDirektur = "SELECT K.NIK, K.NamaLengkap, K.Email, K.NoHP, K.Departemen, K.JenisKelamin, K.Jabatan, U.Username, U.ProfilePhoto FROM Direktur AS K INNER JOIN User AS U ON K.UserID = U.UserID WHERE K.UserID = '$userID'";
    $resultGetDataDirektur = $koneksi->query($queryGetDataDirektur);

    if ($resultGetDataDirektur->num_rows > 0) {
        $rowDirektur = $resultGetDataDirektur->fetch_assoc();
        $userDetails = $rowDirektur; // data untuk tampilan profil
    }
}

// Ambil detail user untuk tampilan profil
$userDetailsQuery = "SELECT K.NamaLengkap, K.Email, K.NoHP, K.Departemen, K.Jabatan, U.ProfilePhoto 
                     FROM Direktur AS K
                     INNER JOIN User AS U ON K.UserID = U.UserID
                     WHERE K.UserID = '".$_SESSION["UserID"]."'";
$userDetailsResult = mysqli_query($koneksi, $userDetailsQuery);
$userDetails = mysqli_fetch_assoc($userDetailsResult);

// -----------------------------------------------------------------
// menggabungkan data dari berbagai tabel dan mengurutkannya berdasarkan departemen
$query = "
    SELECT K.UserID, K.NamaLengkap, K.NIK, K.Departemen, K.Jabatan 
    FROM Karyawan AS K 
    UNION
    SELECT M.UserID, M.NamaLengkap, M.NIK, M.Departemen, M.Jabatan 
    FROM Manajer AS M
    UNION
    SELECT H.UserID, H.NamaLengkap, H.NIK, H.Departemen, H.Jabatan 
    FROM HRGA AS H
    ORDER BY Departemen
";

$result = mysqli_query($koneksi, $query);

// Simpan data ke dalam array untuk ditampilkan di tabel
$dataKaryawan = [];
while($row = mysqli_fetch_assoc($result)) {
    $dataKaryawan[] = $row;
}

mysqli_close($koneksi);
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
                // navbar header
                $defaultProfilePhoto = 'ProfileDirektur/profile.jpeg';
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
            <li>
                <a href="ApprovalDirektur.php">
                    <i class="fa fa-check-square"></i> 
                    <span>Approval</span>
                </a>
            </li>
            <li class="active">
                <a href="ListKaryawanDirektur.php">
                    <i class="fa fa-search"></i> 
                    <span>List Pegawai</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-logout">
            <a href="/Logout.php" class="btn logout-button">
                <i class="fa fa-sign-out"></i>
                <span>Logout</span> 
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
                                <th class="text-center table-column" style="width: 100px;">UserID</th>
                                <th class="text-center table-column" style="width: 200px;">Nama</th>
                                <th class="text-center table-column" style="width: 100px;">NIK</th>
                                <th class="text-center table-column" style="width: 200px;">Departemen</th>
                                <th class="text-center table-column" style="width: 200px;">Jabatan</th>
                                <th class="text-center table-column" style="width: 145px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $no = 1;
                            foreach($dataKaryawan as $karyawan): ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($karyawan['UserID']); ?></td>
                                <td><?php echo htmlspecialchars($karyawan['NamaLengkap']); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($karyawan['NIK']); ?></td>
                                <td><?php echo htmlspecialchars($karyawan['Departemen']); ?></td>
                                <td><?php echo htmlspecialchars($karyawan['Jabatan']); ?></td>
                                <td class="text-center">
                                    <a href="DetailPengajuanPegawai.php?NIK=<?php echo urlencode($karyawan['NIK']); ?>" style="width: 100%;" class="btn custom-detail-btn-blue">Detail</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
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
                <h5 class="modal-title" id="detailModalLabel">Detail Status Pengajuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
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