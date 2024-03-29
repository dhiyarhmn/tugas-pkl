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

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $Email = $_POST["Email"] ?? '';
    $NoHP = $_POST["NoHP"] ?? '';
    $Username = $_POST["Username"] ?? '';

    // Periksa dan unggah foto profil jika ada
    if (isset($_FILES["profilePhoto"]) && $_FILES["profilePhoto"]["error"] == 0) {
        $target_dir = "ProfileDirektur/";
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
                
        
                // Update database dengan foto profile baru
                $sqlUpdateProfilePhoto = "UPDATE User SET ProfilePhoto = '$target_file' WHERE UserID = '$userID'";
                if ($koneksi->query($sqlUpdateProfilePhoto) === TRUE) {
                    // Update $userDetails dengan foto profile baru
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

    if ($rowDirektur) {
        // Update data Direktur
        $sqlDirektur = "UPDATE Direktur SET Email = '$Email', NoHP = '$NoHP' WHERE UserID = '$userID'";
        if ($koneksi->query($sqlDirektur) === TRUE) {
            echo "";
        } else {
            echo "Error: " . $sqlDirektur . "<br>" . $koneksi->error;
        }

        // Update data User
        $sqlUser = "UPDATE User SET Username = '$Username' WHERE UserID = '$userID'";
        if ($koneksi->query($sqlUser) === TRUE) {
            echo "";
        } else {
            echo "Error: " . $sqlUser . "<br>" . $koneksi->error;
        }
    }
    header("Location: DashboardDirektur.php");
    exit;
}

// Ambil detail user untuk tampilan profil
$userDetailsQuery = "SELECT K.NamaLengkap, K.Email, K.NoHP, K.Departemen, K.Jabatan, U.ProfilePhoto 
                     FROM Direktur AS K
                     INNER JOIN User AS U ON K.UserID = U.UserID
                     WHERE K.UserID = '".$_SESSION["UserID"]."'";
$userDetailsResult = mysqli_query($koneksi, $userDetailsQuery);
$userDetails = mysqli_fetch_assoc($userDetailsResult);

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
            <li class="active">
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
            <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8); margin-bottom: 30px;">
                        <div class="card-body">
                        <h5 class="card-title text-center mb-4">EDIT PROFILE</h5>
                            <form method="post" enctype="multipart/form-data">
                                <div class="container input-container">
                                    <div class="card-body text-center" >
                                        <input id="imageUpload" type="file" name="profilePhoto" accept="image/*" style="display: none;">
                                        <?php
                                            // untuk menampilkan foto profil
                                            $defaultProfilePhoto = 'ProfileDirektur/profile.jpeg'; 
                                            $userProfilePhoto = $userDetails['ProfilePhoto'] ?? null; // Foto profil yang diunggah oleh pengguna

                                            $photoToDisplay = $userProfilePhoto ? $userProfilePhoto : $defaultProfilePhoto; // Menentukan foto yang akan ditampilkan

                                            echo '<img src="'.htmlspecialchars($photoToDisplay).'" class="rounded-circle profile-image" style="width: 180px; height: 180px; border-radius: 50%; margin: 40px auto 30px auto;">';
                                        ?>
                                        <br>
                                        <label for="imageUpload" class="btn btn-primary" style="font-size: 10px; background-color: #160066; border: #160066;">Upload Photo</label>
                                    </div>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="NIK" class="input" value="<?php echo htmlspecialchars($rowDirektur['NIK']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">NIK</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Nama" class="input" value="<?php echo htmlspecialchars($rowDirektur['NamaLengkap']); ?>" readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Nama</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Departemen" class="input" value="<?php echo htmlspecialchars($rowDirektur['Departemen']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Departemen</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="jabatan" class="input" value="<?php echo htmlspecialchars($rowDirektur['Jabatan']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Jabatan</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Gender" class="input" value="<?php echo htmlspecialchars($rowDirektur['JenisKelamin']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Gender</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type='email' pattern=".+(@\w+\.com|@daekyung\.co\.id)" name="Email" class="input" value="<?php echo htmlspecialchars($rowDirektur['Email'] ?? ''); ?>" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label" for="Email">Email</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="NoHP" class="input" value="<?php echo htmlspecialchars($rowDirektur['NoHP'] ?? ''); ?>" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label">No HP</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="Username" class="input" value="<?php echo htmlspecialchars($rowDirektur['Username'] ?? ''); ?>" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label">Username</label>
                                </div>
                                <div class="container input-container">
                                    <div style="text-align: right; margin-top: -30px;">
                                        <a href="ChangePassword.php" style="font-size: 12;">Change Password</a>
                                    </div>
                                </div>
                                <div class="container">
                                    <button class="button-submit" type="submit" style="margin-top: -30px;">Save</button>
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
    
<script src="./js/script.js"></script>

</body>
</html>