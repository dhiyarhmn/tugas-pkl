<?php
session_start();

$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Manajer') {
    header("Location: /Login.php");
    exit(); 
}

$queryManajer = "SELECT NamaLengkap, departemen, jabatan FROM Manajer WHERE UserID = '{$_SESSION["UserID"]}'";
$resultManajer = mysqli_query($koneksi, $queryManajer);
$rowManajer = mysqli_fetch_assoc($resultManajer);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Atur zona waktu sesuai dengan lokasi pengguna
    date_default_timezone_set('Asia/Jakarta'); // Sesuaikan dengan zona waktu pengguna

    // Ambil tanggal dan waktu saat ini sesuai zona waktu yang telah diatur
    $tanggal_pengajuan = date('Y-m-d H:i:s');
    $jenis_absensi = $_POST["jenis_absensi"];
    $lama_periode_absensi = $_POST["lama_periode_absensi"];
    $keterangan = $_POST["keterangan"];

    $periode_awal = new DateTime($_POST["periode_awal"]);
    $periode_akhir = new DateTime($_POST["periode_akhir"]);
    $periode_awal_str = $periode_awal->format('Y-m-d H:i:s');
    $periode_akhir_str = $periode_akhir->format('Y-m-d H:i:s');

    $berkas = '';
    $upload_dir = "BerkasManajer/";  // Naik satu level dari direktori skrip saat ini
    
        // Proses pengajuan cuti tahunan
        if ($_POST["jenis_absensi"] == "AL") {
            $periode_awal = new DateTime($_POST["periode_awal"]);
            $periode_akhir = new DateTime($_POST["periode_akhir"]);

            // Mengonversi DateTime ke string
            $periode_awal_str = $periode_awal->format('Y-m-d H:i:s');
            $periode_akhir_str = $periode_akhir->format('Y-m-d H:i:s');

            $durasiCuti = 0;

            // Menghitung durasi cuti, mengabaikan hari Sabtu dan Minggu
            while ($periode_awal <= $periode_akhir) {
                if ($periode_awal->format('N') <= 5) { // Hanya hari Senin-Jumat yang dihitung
                    $durasiCuti++;
                }
                $periode_awal->modify('+1 day');
            }

            // Query untuk mendapatkan data sisa cuti
            $queryCuti = "SELECT CutiTerpakai, CutiSisa FROM CutiTahunan WHERE UserID = '{$_SESSION["UserID"]}'";
            $resultCuti = mysqli_query($koneksi, $queryCuti);

            if ($resultCuti && mysqli_num_rows($resultCuti) > 0) {
                $dataCuti = mysqli_fetch_assoc($resultCuti);

                if ($durasiCuti <= $dataCuti['CutiSisa']) {
                    $cutiTerpakaiBaru = $dataCuti['CutiTerpakai'] + $durasiCuti;
                    $cutiSisaBaru = $dataCuti['CutiSisa'] - $durasiCuti;

                    $queryUpdateCuti = "UPDATE CutiTahunan SET CutiTerpakai = '$cutiTerpakaiBaru', CutiSisa = '$cutiSisaBaru' WHERE UserID = '{$_SESSION["UserID"]}'";
                    mysqli_query($koneksi, $queryUpdateCuti);
                } else {
                    echo "Jumlah cuti yang diambil melebihi sisa cuti yang tersedia.";
                    exit();
                }
            } else {
                echo "Data cuti tidak ditemukan atau terjadi kesalahan query.";
                exit();
            }
    }
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Membuat direktori jika belum ada
    }

    if (isset($_FILES["Berkas"]) && $_FILES["Berkas"]["error"] == 0) {
        $file_name = str_replace(" ", "_", $_FILES["Berkas"]["name"]); // Mengganti spasi dengan underscore
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
    VALUES ('{$_SESSION["UserID"]}', '$tanggal_pengajuan', '$jenis_absensi', '$keterangan', '$lama_periode_absensi', '$periode_awal_str', '$periode_akhir_str', '$berkas')";

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
                header("Location: StatusPengajuanManajer.php");
                exit();

            if ($_POST["jenis_absensi"] == "AL") {
                $durasiCuti = // Hitung durasi cuti dari $periode_awal dan $periode_akhir
                $queryUpdateCuti = "UPDATE CutiTahunan SET CutiTerpakai = CutiTerpakai + $durasiCuti, CutiSisa = CutiSisa - $durasiCuti WHERE UserID = '{$_SESSION["UserID"]}'";
                mysqli_query($koneksi, $queryUpdateCuti);
            }
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
$userDetailsQuery = "SELECT Manajer.NamaLengkap, Manajer.Departemen, Manajer.Jabatan, User.ProfilePhoto 
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Adjusted CSS link. Assuming 'style.css' is in the same directory as the PHP file -->
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
                $defaultProfilePhoto = 'ProfileManajer/profile.jpeg'; // Lokasi foto default
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
            <li class="active">
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
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8); margin-bottom: 30px;">
                        <div class="card-body">
                            <h5 class="card-title text-center mb-4">PENGAJUAN ABSENSI</h5>
                            <form method="post" action="" enctype="multipart/form-data">
                                <div class="container input-container" style="margin-top: 70px;">
                                    <input required="" type="text" name="nama" class="input" value="<?php echo htmlspecialchars($rowManajer['NamaLengkap']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Nama</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="departemen" class="input" value="<?php echo htmlspecialchars($rowManajer['departemen']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Departemen</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="jabatan" class="input" value="<?php echo htmlspecialchars($rowManajer['jabatan']); ?>" onfocus="focusInput(this)" onblur="blurInput(this)"readonly style="background-color: #8a8a8a; color: black;">
                                    <label class="label static-label">Jabatan</label>
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
                                    <input required="" class="flex w-full rounded-md border border-blue-300 border-input bg-white text-sm text-gray-400 file:border-0 file:bg-blue-600 file:text-white file:text-sm file:font-medium" style="width:100%; padding: 0px;" type="file" name="Berkas" id="picture"/>
                                </div>
                                <div class="container input-container" id="sisaCutiContainer" style="display: none;">
                                    <span style="color:black;" id= "sisaCuti" class="label"></span>
                                    <input required="" type="text" name="sisa_cuti_tahunan" class="input" onfocus="focusInput(this)" onblur="blurInput(this)" readonly style="background-color: #8a8a8a; color: black;">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#jenis_absensi').change(function() {
        var jenisAbsensi = $(this).val();
        if (jenisAbsensi == 'AL') { // 'AL' adalah kode untuk Annual Leave
            $.ajax({
                url: 'getSisaCuti.php', // Lokasi file PHP yang akan dipanggil
                type: 'POST',
                data: { userID: "<?php echo $_SESSION['UserID']; ?>" },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert(response.error);
                    } else {
                        $('#sisaCutiContainer').show();
                        $('#sisaCuti').text('Sisa Cuti Tahunan: ' + response.cutiSisa);
                    }
                },
                error: function() {
                    alert('Tidak dapat mengambil data sisa cuti');
                }
            });
        } else {
            $('#sisaCutiContainer').hide();
        }
    });
});

$(document).ready(function() {
    // Mengatur tanggal dan waktu minimum untuk periode_awal ke waktu saat ini
        var now = new Date();
        var month = ('0' + (now.getMonth() + 1)).slice(-2); // Mengubah bulan ke format 2 digit
        var day = ('0' + now.getDate()).slice(-2); // Mengubah hari ke format 2 digit
        var hour = ('0' + now.getHours()).slice(-2); // Mengubah jam ke format 2 digit
        var minute = ('0' + now.getMinutes()).slice(-2); // Mengubah menit ke format 2 digit
        var nowFormatted = now.getFullYear() + '-' + month + '-' + day + 'T' + hour + ':' + minute;

        // Menetapkan nilai min untuk periode_awal dengan tanggal dan waktu saat ini
        $('#periode_awal').attr('min', nowFormatted);

        $('#periode_awal').change(function() {
            var startDate = $(this).val();
            var startDateObject = new Date(startDate);
            var nowObject = new Date();

            // Jika tanggal awal lebih awal dari waktu saat ini, set tanggal awal ke waktu saat ini
            if (startDateObject < nowObject) {
                $(this).val(nowFormatted); // Set periode_awal ke waktu saat ini jika lebih awal
                startDate = nowFormatted; // Update startDate untuk memastikan logika berikutnya menggunakan waktu yang benar
            }

            $('#periode_akhir').attr('min', startDate);

            // Event listener untuk periode_akhir dengan logika penyesuaian waktu tambahan
            $('#periode_akhir').change(function() {
                var endDate = $('#periode_akhir').val();
                if (startDate.substr(0, 10) == endDate.substr(0, 10)) {
                    if (endDate < startDate) {
                        $('#periode_akhir').val(startDate); // Jika periode_akhir lebih awal, set sama dengan periode_awal
                    }
                }
            });
        });

        $('#jenis_absensi').change(function() {
            // Kode existing untuk jenis_absensi change event
        });
    });
</script>
    <!-- Adjusted JS link. Assuming 'script.js' is in the same directory as the PHP file -->
    <script src="./js/script.js"></script>
</body>
</html>