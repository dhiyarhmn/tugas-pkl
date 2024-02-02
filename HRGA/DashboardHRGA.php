<?php
session_start();

// Koneksi ke database 
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'HRGA') {
    header("Location: /Login.php");
    exit(); 
}

// -------------------------------------------------------------------
// buat foto profile, nama lengkap, dan jabatan sesuai user yang login
// -------------------------------------------------------------------
// Adjust this query based on your actual database schema
$userDetailsQuery = "SELECT HRGA.NamaLengkap, HRGA.Departemen, HRGA.Jabatan, User.ProfilePhoto 
                     FROM HRGA
                     JOIN User ON HRGA.UserID = User.UserID
                     WHERE HRGA.UserID = '".$_SESSION["UserID"]."'";
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
                $defaultProfilePhoto = 'ProfileHRGA/profile.jpeg'; 
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
            <li class="active">
                <a href="DashboardHRGA.php">
                    <i class="fas fa-tachometer-alt"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="EditProfileHRGA.php">
                    <i class="fas fa-user"></i> 
                    <span>Edit Profile</span>
                </a>
            </li>
            <li>
                <a href="PengajuanAbsensiHRGA.php">
                    <i class="fas fa-plus"></i> 
                    <span>Pengajuan Absensi</span>
                </a>
            </li>
            <li>
                <a href="StatusPengajuanHRGA.php">
                    <i class="fas fa-list-alt"></i> 
                    <span>Status Pengajuan</span>
                </a>
            </li>
            <li>
                <a href="ApprovalHRGA.php">
                    <i class="fa fa-check-square"></i> 
                    <span>Approval</span>
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
            <div class="row justify-content-center mt-4 box-container">
                <div class="col-auto mb-3 larger-card" style="margin-top: 30px;">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Approved</h5>
                                <p class="card-text"><?php echo $approvedCount; ?></p>
                            </div>
                            <i class="fa fa-check-circle approved-icon" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="col-auto mb-3 larger-card" style="margin-top: 30px;">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Declined</h5>
                                <p class="card-text"><?php echo $declinedCount; ?></p>
                            </div> 
                            <i class="fa fa-times-circle declined-icon" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="col-auto mb-3 larger-card" style="margin-top: 30px;">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>     
                                <h5 class="card-title">On Process</h5>
                                <p class="card-text"><?php echo $onProcessCount; ?></p>
                            </div>
                            <i class="fa fa-exclamation-circle on-process-icon" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mt-4 box-container" style="margin-top: 100px;">
                <div class="col-md-6">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body">
                            <h5 class="card-title">Edit Profile</h5>
                            <p class="card-text">Optimize Your Profile Settings</p>
                            <button type="button" class="btn custom-btn-blue btn-large button" style="padding: 0.5em 1em;" onclick="window.location.href='EditProfileHRGA.php'">
                                <span class="button__text">Click Here</span>
                                <span class="button__icon"><i class="fas fa-user"></i></i></span>
                            </button>
                        </div>
                    </div>
                </div> 
                <div class="col-md-6">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body">
                            <h5 class="card-title">Pengajuan Absensi</h5>
                            <p class="card-text">Submit Your Absence Submissions</p>
                            <button type="button" class="btn custom-btn-blue btn-large button" style="padding: 0.5em 1em;" onclick="window.location.href='PengajuanAbsensiHRGA.php'">
                                <span class="button__text">Click Here</span>
                                <span class="button__icon"><i class="fas fa-plus"></i></i></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" style="margin-top: 30px;">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body">
                            <h5 class="card-title">Status Pengajuan</h5>
                            <p class="card-text">Check Your Submissions Status</p>
                            <button type="button" class="btn custom-btn-blue btn-large button" style="padding: 0.5em 1em;" onclick="window.location.href='StatusPengajuanHRGA.php'">
                                <span class="button__text">Show More</span>
                                <span class="button__icon"><i class="fas fa-list-alt"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" style="margin-top: 30px; margin-bottom: 30px;">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body">
                            <h5 class="card-title">Approval</h5>
                            <p class="card-text">Approval for Employees Absence Submissions</p>
                            <button type="button" class="btn custom-btn-blue btn-large button" style="padding: 0.5em 1em;" onclick="window.location.href='ApprovalHRGA.php'">
                                <span class="button__text">Click Here</span>
                                <span class="button__icon"><i class="fa fa-check-square"></i></span>
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
    
<script src="./js/script.js"></script>

</body>
</html>