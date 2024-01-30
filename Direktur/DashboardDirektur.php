<?php
session_start();

// Koneksi ke database (sesuaikan dengan detail koneksi Anda)
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Direktur') {
    header("Location: /Login.php");
    exit(); // Penting untuk menghentikan eksekusi skrip lebih lanjut
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

// -------------------------------------------------------------------
// KOTAK APPROVE, ON PROCESS, DECLINE
// -------------------------------------------------------------------
// Hitung jumlah berkas dengan status 'Approved'
$queryApprovedCount = "SELECT COUNT(*) AS ApprovedCount
                        FROM Absensi a
                        JOIN PersetujuanAbsensi pa ON a.AbsensiID = pa.AbsensiID
                        WHERE a.UserID = ".$_SESSION["UserID"]."
                        AND pa.StatusPersetujuan = 'Approved'";
$resultApprovedCount = mysqli_query($koneksi, $queryApprovedCount);
$approvedCount = mysqli_fetch_assoc($resultApprovedCount)["ApprovedCount"];

// Hitung jumlah berkas dengan status 'On Process'
$queryOnProcessCount = "SELECT COUNT(*) AS OnProcessCount
                        FROM Absensi a
                        JOIN PersetujuanAbsensi pa ON a.AbsensiID = pa.AbsensiID
                        WHERE a.UserID = ".$_SESSION["UserID"]."
                        AND pa.StatusPersetujuan = 'On Process'";
$resultOnProcessCount = mysqli_query($koneksi, $queryOnProcessCount);
$onProcessCount = mysqli_fetch_assoc($resultOnProcessCount)["OnProcessCount"];

// Hitung jumlah berkas dengan status 'Declined'
$queryDeclinedCount = "SELECT COUNT(*) AS DeclinedCount
                        FROM Absensi a
                        JOIN PersetujuanAbsensi pa ON a.AbsensiID = pa.AbsensiID
                        WHERE a.UserID = ".$_SESSION["UserID"]."
                        AND pa.StatusPersetujuan = 'Declined'";
$resultDeclinedCount = mysqli_query($koneksi, $queryDeclinedCount);
$declinedCount = mysqli_fetch_assoc($resultDeclinedCount)["DeclinedCount"];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Adjusted CSS link. Assuming 'style.css' is in the same directory as the PHP file -->
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
                <img src="<?php echo htmlspecialchars($userDetails['ProfilePhoto'] ?? 'default.jpg'); ?>" class="rounded-circle profile-image" style="margin-bottom: 10px;">
                <h3 class="profile-text" style="font-size: 16px; color:white"><?php echo $userDetails['NamaLengkap']; ?></h3>
                <h3 class="profile-text" style="font-size: 16px; color:white"><?php echo $userDetails['Departemen']; ?></h3>
                <h3 class="profile-text" style="font-size: 16px; color:white">-<?php echo $userDetails['Jabatan']; ?>-</h3>
            </div>
        </div>
        <ul class="list-unstyled components">
            <li class="active">
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
            <li>
                <a href="ListKaryawanDirektur.php">
                    <i class="fa fa-search"></i> 
                    <span>List Karyawan</span>
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
            <div class="row justify-content-center mt-4 box-container" style="margin-top: 75px;">
                <div class="col-md-6">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body">
                            <h5 class="card-title">Edit Profile</h5>
                            <p class="card-text">Some sample content goes here.</p>
                            <button type="button" class="btn custom-btn-blue btn-large button" style="padding: 0.5em 1em;" onclick="window.location.href='EditProfileDirektur.php'">
                                <span class="button__text">Click Here</span>
                                <span class="button__icon"><i class="fas fa-user"></i></i></span>
                            </button>
                        </div>
                    </div>
                </div>     
            <!-- Kotak Approval -->
                <div class="col-md-6">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body">
                            <h5 class="card-title">Approval</h5>
                            <p class="card-text">Some sample content goes here.</p>
                            <button type="button" class="btn custom-btn-blue btn-large button" style="padding: 0.5em 1em;" onclick="window.location.href='ApprovalDirektur.php'">
                                <span class="button__text">Click Here</span>
                                <span class="button__icon"><i class="fa fa-check-square"></i></i></span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Kotak Status Pengajuan -->
                <div class="col-md-6" style="margin-top: 30px;">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body">
                            <h5 class="card-title">List Karyawan</h5>
                            <p class="card-text">Some sample content goes here.</p>
                            <button type="button" class="btn custom-btn-blue btn-large button" style="padding: 0.5em 1em;" onclick="window.location.href='ListKaryawanDirektur.php'">
                                <span class="button__text">Show More</span>
                                <span class="button__icon"><i class="fa fa-search"></i></i></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap and jQuery libraries -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <!-- Adjusted JS link. Assuming 'script.js' is in the same directory as the PHP file -->
    <script src="./js/script.js"></script>
</body>
</html>