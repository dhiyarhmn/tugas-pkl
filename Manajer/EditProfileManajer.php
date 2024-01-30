<?php
session_start();

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Manajer') {
    header("Location: /Login.php");
    exit();
}

// Inisialisasi variabel
$rowManajer = null;
$userDetails = [];

// Cek apakah UserID ada di session
if (isset($_SESSION['UserID'])) {
    $userID = $_SESSION['UserID'];

    // Ambil data Manajer dan username berdasarkan UserID
    $queryGetDataManajer = "SELECT K.NIK, K.NamaLengkap, K.Email, K.NoHP, K.Departemen, K.JenisKelamin, K.Jabatan, U.Username, U.ProfilePhoto FROM Manajer AS K INNER JOIN User AS U ON K.UserID = U.UserID WHERE K.UserID = '$userID'";
    $resultGetDataManajer = $koneksi->query($queryGetDataManajer);

    if ($resultGetDataManajer->num_rows > 0) {
        $rowManajer = $resultGetDataManajer->fetch_assoc();
        $userDetails = $rowManajer; // Gunakan data ini untuk tampilan profil
    }
}

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $Email = $_POST["Email"] ?? '';
    $NoHP = $_POST["NoHP"] ?? '';
    $Username = $_POST["Username"] ?? '';

    // Periksa dan unggah foto profil jika ada
    if (isset($_FILES["profilePhoto"]) && $_FILES["profilePhoto"]["error"] == 0) {
        $target_dir = "ProfileManajer/";
        $target_file = $target_dir . basename($_FILES["profilePhoto"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Periksa apakah berkas yang diunggah adalah gambar
        $check = getimagesize($_FILES["profilePhoto"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            echo "Berkas yang diunggah bukan gambar.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["profilePhoto"]["tmp_name"], $target_file)) {
                
        
                // Update database with the new profile photo file path
                $sqlUpdateProfilePhoto = "UPDATE User SET ProfilePhoto = '$target_file' WHERE UserID = '$userID'";
                if ($koneksi->query($sqlUpdateProfilePhoto) === TRUE) {
                    // Update $userDetails to reflect the new profile photo path
                    $userDetails['ProfilePhoto'] = $target_file;
                    echo "";
                } else {
                    echo "Error: " . $sqlUpdateProfilePhoto . "<br>" . $koneksi->error;
                }
            } else {
                echo "Maaf, terjadi kesalahan saat mengunggah berkas Anda.";
            }
        }        
    }

    if ($rowManajer) {
        // Update data Manajer
        $sqlManajer = "UPDATE Manajer SET Email = '$Email', NoHP = '$NoHP' WHERE UserID = '$userID'";
        if ($koneksi->query($sqlManajer) === TRUE) {
            echo "";
        } else {
            echo "Error: " . $sqlManajer . "<br>" . $koneksi->error;
        }

        // Update data User
        $sqlUser = "UPDATE User SET Username = '$Username' WHERE UserID = '$userID'";
        if ($koneksi->query($sqlUser) === TRUE) {
            echo "";
        } else {
            echo "Error: " . $sqlUser . "<br>" . $koneksi->error;
        }
    }
    header("Location: DashboardManajer.php");
    exit;
}

// Ambil detail user untuk tampilan profil
$userDetailsQuery = "SELECT K.NamaLengkap, K.Email, K.NoHP, K.Departemen, K.Jabatan, U.ProfilePhoto 
                     FROM Manajer AS K
                     INNER JOIN User AS U ON K.UserID = U.UserID
                     WHERE K.UserID = '".$_SESSION["UserID"]."'";
$userDetailsResult = mysqli_query($koneksi, $userDetailsQuery);
$userDetails = mysqli_fetch_assoc($userDetailsResult);

// Tutup koneksi ke database di akhir skrip
$koneksi->close();
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
            <li>
                <a href="DashboardManajer.php">
                    <i class="fas fa-tachometer-alt"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="active">
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
            <li>
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
            <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body">
                        <h5 class="card-title text-center mb-4">EDIT PROFILE</h5>
                            <form method="post" enctype="multipart/form-data">
                                <div class="container input-container">
                                    <div class="card-body text-center" >
                                        <input id="imageUpload" type="file" name="profilePhoto" accept="image/*" style="display: none;">
                                        <img src="<?php echo htmlspecialchars($userDetails['ProfilePhoto'] ?? 'default.jpg'); ?>" style="width: 180px; height: 180px; border-radius: 50%; margin: 40px auto 30px auto;">
                                        <br>
                                        <label for="imageUpload" class="btn btn-primary" style="font-size: 10px; background-color: #160066; border: #160066;">Upload Photo</label>
                                    </div>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="NIK" class="input" value="<?php echo htmlspecialchars($rowManajer['NIK']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">NIK</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Nama" class="input" value="<?php echo htmlspecialchars($rowManajer['NamaLengkap']); ?>" readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Nama</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Departemen" class="input" value="<?php echo htmlspecialchars($rowManajer['Departemen']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Departemen</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="jabatan" class="input" value="<?php echo htmlspecialchars($rowManajer['Jabatan']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Jabatan</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Gender" class="input" value="<?php echo htmlspecialchars($rowManajer['JenisKelamin']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Gender</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type='email' pattern=".+@*\.com" name="Email" class="input" value="<?php echo htmlspecialchars($rowManajer['Email'] ?? ''); ?>" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label" for="Email">Email</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="NoHP" class="input" value="<?php echo htmlspecialchars($rowManajer['NoHP'] ?? ''); ?>" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label">No HP</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Username" class="input" value="<?php echo htmlspecialchars($rowManajer['Username'] ?? ''); ?>" onfocus="focusInput(this)" onblur="blurInput(this)">
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
    
<!-- Adjusted JS link. Assuming 'script.js' is in the same directory as the PHP file -->
<script src="./js/script.js"></script>

</body>
</html>