<?php
session_start();

// Koneksi ke database (sesuaikan dengan detail koneksi Anda)
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$database = "pengajuanabsensi3";

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Karyawan') {
    header("Location: /Login.php");
    exit(); // Penting untuk menghentikan eksekusi skrip lebih lanjut
}

$conn = new mysqli($servername, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Inisialisasi variabel rowKaryawan
$rowKaryawan = null;

// Cek apakah UserID ada di session
if (isset($_SESSION['UserID'])) {
    $userID = $_SESSION['UserID'];

    // Ambil data karyawan dan username berdasarkan UserID
    $queryGetDataKaryawan = "SELECT Karyawan.*, User.Username FROM Karyawan JOIN User ON Karyawan.UserID = User.UserID WHERE Karyawan.UserID = '$userID'";
    $resultGetDataKaryawan = $conn->query($queryGetDataKaryawan);

    if ($resultGetDataKaryawan->num_rows > 0) {
        $rowKaryawan = $resultGetDataKaryawan->fetch_assoc();
    }
}

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $Email = $_POST["Email"];
    $NoHP = $_POST["NoHP"];
    $Username = $_POST["Username"];

    if ($rowKaryawan) {
        // Update data Karyawan
        $sqlKaryawan = "UPDATE Karyawan SET Email = '$Email', NoHP = '$NoHP' WHERE UserID = '$userID'";

        if ($conn->query($sqlKaryawan) === TRUE) {
            echo "";
        } else {
            echo "Error: " . $sqlKaryawan . "<br>" . $conn->error;
        }

        // Update data User
        $sqlUser = "UPDATE User SET Username = '$Username' WHERE UserID = '$userID'";
        if ($conn->query($sqlUser) === TRUE) {
            echo "";
        } else {
            echo "Error: " . $sqlUser . "<br>" . $conn->error;
        }
    }
    // Tutup koneksi ke database
    $conn->close();
}

// buat foto profile, nama lengkap, dan jabatan sesuai user yang login
// Adjust this query based on your actual database schema
$userDetailsQuery = "SELECT Karyawan.NamaLengkap, Karyawan.Jabatan, User.ProfilePhoto 
                     FROM Karyawan
                     JOIN User ON Karyawan.UserID = User.UserID
                     WHERE Karyawan.UserID = '".$_SESSION["UserID"]."'";
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
                <img src="/Assets/img/<?php echo $userDetails['ProfilePhoto']; ?>" width="80" class="rounded-circle" style="margin-bottom: 10px;">
                <h3 class="profile-text" style="font-size: 16px; color:white"><?php echo $userDetails['NamaLengkap']; ?></h3>
                <h3 class="profile-text" style="font-size: 16px; color:white">[<?php echo $userDetails['Jabatan']; ?>]</h3>
              </div>
        </div>
        <ul class="list-unstyled components">
            <li>
                <a href="DashboardKaryawan.php">
                    <i class="fas fa-tachometer-alt"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="EditProfileKaryawan.php">
                    <i class="fas fa-user"></i> 
                    <span>Edit Profile</span>
                </a>
            </li>
            <li>
                <a href="PengajuanAbsensiKaryawan.php">
                    <i class="fas fa-plus"></i> 
                    <span>Pengajuan Absensi</span>
                </a>
            </li>
            <li>
                <a href="StatusPengajuanKaryawan.php">
                    <i class="fas fa-list-alt"></i> 
                    <span>Status Pengajuan</span>
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
            <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body">
                            <h5 class="card-title text-center mb-4">EDIT PROFILE</h5>
                            <form method="post">
                                <div class="container input-container">
                                    <input required="" type="text" name="NIK" class="input" value="<?php echo htmlspecialchars($rowKaryawan['NIK']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">NIK</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Nama" class="input" value="<?php echo htmlspecialchars($rowKaryawan['NamaLengkap']); ?>" readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Nama</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Departemen" class="input" value="<?php echo htmlspecialchars($rowKaryawan['Departemen']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Departemen</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="jabatan" class="input" value="<?php echo htmlspecialchars($rowKaryawan['Jabatan']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Jabatan</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Gender" class="input" value="<?php echo htmlspecialchars($rowKaryawan['JenisKelamin']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Gender</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type='email' pattern=".+@*\.com" name="Email" class="input" value="<?php echo htmlspecialchars($rowKaryawan['Email'] ?? ''); ?>" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label" for="Email">Email</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="NoHP" class="input" value="<?php echo htmlspecialchars($rowKaryawan['NoHP'] ?? ''); ?>" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label">No HP</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Username" class="input" value="<?php echo htmlspecialchars($rowKaryawan['Username'] ?? ''); ?>" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label">Username</label>
                                </div>
                                <div class="container">
                                    <button class="button-submit" type="submit">Save</button>
                                </div>
                            </form>
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
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    
    <!-- Adjusted JS link. Assuming 'script.js' is in the same directory as the PHP file -->
    <script src="./js/script.js"></script>
</body>
</html>