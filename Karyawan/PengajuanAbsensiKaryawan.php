<?php
session_start();

$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Karyawan') {
    header("Location: /Login.php");
    exit(); 
}

$queryKaryawan = "SELECT NamaLengkap, departemen, jabatan FROM Karyawan WHERE UserID = '{$_SESSION["UserID"]}'";
$resultKaryawan = mysqli_query($koneksi, $queryKaryawan);
$rowKaryawan = mysqli_fetch_assoc($resultKaryawan);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal_pengajuan = $_POST["tanggal_pengajuan"];
    $jenis_absensi = $_POST["jenis_absensi"];
    $lama_periode_absensi = $_POST["lama_periode_absensi"];
    $periode_awal = $_POST["periode_awal"];
    $periode_akhir = $_POST["periode_akhir"];
    $keterangan = $_POST["keterangan"];

    $berkas = '';
    $upload_dir = "BerkasKaryawan/"; // Adjusted path
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (isset($_FILES["Berkas"]) && $_FILES["Berkas"]["error"] == 0) {
        $file_name = $_FILES["Berkas"]["name"];
        $file_tmp = $_FILES["Berkas"]["tmp_name"];

        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $berkas = $file_path;
        } else {
            echo "Error: Gagal meng-upload file.";
            exit();
        }
    }

    // Insert data into the "Absensi" table
    $queryAbsensi = "INSERT INTO Absensi (UserID, TanggalPengajuan, NamaJenisAbsensi, Keterangan, PeriodeAbsensi, WaktuPeriodeAbsensiMulai, WaktuPeriodeAbsensiSelesai, Berkas)
                    VALUES ('{$_SESSION["UserID"]}', '$tanggal_pengajuan', '$jenis_absensi', '$keterangan', '$lama_periode_absensi', '$periode_awal', '$periode_akhir', '$berkas')";

    if (mysqli_query($koneksi, $queryAbsensi)) {
        $absensiID = mysqli_insert_id($koneksi);

        // Retrieve the AlurPersetujuanID for the logged-in user's role
        $role = $_SESSION["Role"];
        $queryAlurPersetujuan = "SELECT AlurPersetujuanID FROM AlurPersetujuan WHERE Role = '$role'";
        $resultAlurPersetujuan = mysqli_query($koneksi, $queryAlurPersetujuan);

        if ($rowAlurPersetujuan = mysqli_fetch_assoc($resultAlurPersetujuan)) {
            $alurPersetujuanID = $rowAlurPersetujuan['AlurPersetujuanID'];

            // Define PersetujuanAbsensi information
            $statusPersetujuan = 'On Process';
            $tanggalPersetujuan = date('Y-m-d');
            $tahapanSaatIni = 1;

            // Insert data into PersetujuanAbsensi table using the retrieved AlurPersetujuanID
            $queryPersetujuan = "INSERT INTO PersetujuanAbsensi (AbsensiID, UserID, StatusPersetujuan, TanggalPersetujuan, AlurPersetujuanID, TahapanSaatIni) 
                                 VALUES ('$absensiID', '{$_SESSION["UserID"]}', '$statusPersetujuan', '$tanggalPersetujuan', '$alurPersetujuanID', '$tahapanSaatIni')";

            if (mysqli_query($koneksi, $queryPersetujuan)) {
                header("Location: StatusPengajuanKaryawan.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($koneksi);
            }
        } else {
            echo "Error: Unable to find AlurPersetujuanID for the role";
        }
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// -------------------------------------------------------------------
// buat foto profile, nama lengkap, dan jabatan sesuai user yang login
// -------------------------------------------------------------------
// Adjust this query based on your actual database schema
$userDetailsQuery = "SELECT Karyawan.NamaLengkap, Karyawan.Departemen, Karyawan.Jabatan, User.ProfilePhoto 
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
                <h3 class="profile-text" style="font-size: 16px; color:white"><?php echo $userDetails['Departemen']; ?></h3>
                <h3 class="profile-text" style="font-size: 16px; color:white">-<?php echo $userDetails['Jabatan']; ?>-</h3>
            </div>
        </div>
        <ul class="list-unstyled components">
            <li>
                <a href="DashboardKaryawan.php">
                    <i class="fas fa-tachometer-alt"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="EditProfileKaryawan.php">
                    <i class="fas fa-user"></i> 
                    <span>Edit Profile</span>
                </a>
            </li>
            <li class="active">
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
                            <h5 class="card-title text-center mb-4">PENGAJUAN ABSENSI</h5>
                            <form method="post" action="" enctype="multipart/form-data">
                                <div class="container input-container">
                                    <input required="" type="text" name="nama" class="input" value="<?php echo htmlspecialchars($rowKaryawan['NamaLengkap']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Nama</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="departemen" class="input" value="<?php echo htmlspecialchars($rowKaryawan['departemen']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Departemen</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="jabatan" class="input" value="<?php echo htmlspecialchars($rowKaryawan['jabatan']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Jabatan</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="datetime-local" name="tanggal_pengajuan" class="input" id="tanggal_pengajuan" onfocus="focusInput(this)" onblur="blurInput(this)" readonly>
                                    <label for="tanggal_pengajuan" class="label">Tanggal Pengajuan</label>
                                </div>
                                <div class="container input-container">
                                    <select required="" name="jenis_absensi" class="input" id="jenis_absensi" onfocus="focusInput(this)" onblur="blurInput(this)">
                                        <option value=""></option>
                                        <option value="A">Absent (Absen)</option>
                                        <option value="P">Permit (Izin)</option>
                                        <option value="L">Late (Terlambat)</option>
                                        <option value="BT">Business Trip (Dinas)</option>
                                        <option value="DL">Doctor Letter (Sakit dengan Surat Dokter)</option>
                                        <option value="AL">Annual Leave (Cuti Tahunan)</option>
                                        <option value="SBA">Sick By Accident (Sakit Akibat Kecelakaan Kerja)</option>
                                        <option value="LP">Legal Permit (Izin Resmi)</option>
                                        <option value="S">Suspend (Skors)</option>
                                    </select>
                                    <label class="label">Jenis Absensi</label>
                                </div>
                                <div class="container input-container" id="fileContainer" style="display: none;">
                                    <input required="" class="flex w-full rounded-md border border-blue-300 border-input bg-white text-sm text-gray-400 file:border-0 file:bg-blue-600 file:text-white file:text-sm file:font-medium" type="file" name="Berkas" id="picture"/>
                                </div>
                                <div class="container input-container">
                                    <select required="" name="lama_periode_absensi" class="input" onfocus="focusInput(this)" onblur="blurInput(this)">
                                        <option value=""></option>
                                        <option value="Hour">Hour</option>
                                        <option value="Day">Day</option>
                                    </select>
                                    <label class="label">Lama Periode Absensi</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="datetime-local" name="periode_awal" class="input" id="periode_awal" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label for="periode_awal" class="label">Periode Awal</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="datetime-local" name="periode_akhir" class="input" id="periode_akhir" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label">Periode Akhir</label>
                                </div>
                                <div class="container input-container">
                                    <textarea required="" type="text" name="keterangan" class="input" onfocus="focusInput(this)" onblur="blurInput(this)" style="height: 100px;"></textarea>
                                    <label class="label">Keterangan</label>
                                </div>
                                <div class="container">
                                    <button class="button-submit" type="submit">Submit</button>
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