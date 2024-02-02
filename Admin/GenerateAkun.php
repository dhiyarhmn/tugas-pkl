<?php
session_start();

// Koneksi ke database (sesuaikan dengan detail koneksi Anda)
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Admin') {
    header("Location: /Login.php");
    exit(); // Penting untuk menghentikan eksekusi skrip lebih lanjut
}

// Ambil detail user untuk tampilan profil
$userDetailsQuery = "SELECT K.NamaLengkap, K.Email, K.NoHP, K.Departemen, K.Jabatan, U.ProfilePhoto 
                     FROM Admin AS K
                     INNER JOIN User AS U ON K.UserID = U.UserID
                     WHERE K.UserID = '".$_SESSION["UserID"]."'";
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
                <img src="/Assets/img/logo3.png" class="navbar-logo" style="margin-bottom: 10px;">
                <h3 class="profile-text" style="font-size: 16px; color:white;">PT. DAEKYUNG INDAH HEAVY INDUSTRY</h3>
                <h3 class="profile-text" style="font-size: 16px; color:white;">-Admin-</h3>
                
            </div>
        </div>
        <ul class="list-unstyled components">
            <li>
                <a href="DashboardAdmin.php">
                    <i class="fas fa-tachometer-alt"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="GenerateAkun.php">
                    <i class="fas fa-plus"></i> 
                    <span>Generate Akun</span>
                </a>
            </li>
            <li>
                <a href="ListKaryawanAdmin.php">
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
            <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8); margin-bottom: 30px;">
                        <div class="card-body">
                            <h5 class="card-title text-center mb-4">Generate Akun</h5>
                            <!-- Form untuk input manual -->
                            <form action="CreateUser.php" method="post">
                                <div class="container input-container">
                                    <input required="" type="text" name="nik" class="input" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label">NIK</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="namaLengkap" class="input" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label">Nama Lengkap</label>
                                </div>
                                <div class="container input-container">
                                    <select required="" name="jenisKelamin" class="input" onfocus="focusInput(this)" onblur="blurInput(this)">
                                        <option value=""></option>
                                        <option value="Pria">Pria</option>
                                        <option value="Wanita">Wanita</option>
                                    </select>
                                    <label class="label">Jenis Kelamin</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="tahunMasuk" class="input" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label">Tahun Masuk</label>
                                </div>
                                <div class="container input-container">
                                    <select required="" name="departemen" class="input" onfocus="focusInput(this)" onblur="blurInput(this)">
                                        <option value=""></option>
                                        <option value="Administration">Administration</option>
                                        <option value="Accounting">Accounting</option>
                                        <option value="Procurement">Procurement</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="PPC">PPC</option>
                                        <option value="QHSE">QHSE</option>
                                        <option value="Engineering">Engineering</option>
                                        <option value="Production">Production</option>
                                    </select>
                                    <label class="label">Departemen</label>
                                </div>
                                <div class="container input-container">
                                    <input required="" type="text" name="jabatan" class="input" onfocus="focusInput(this)" onblur="blurInput(this)">
                                    <label class="label">Jabatan</label>
                                </div>
                                <div class="container input-container">
                                    <select required="" name="role" class="input" onfocus="focusInput(this)" onblur="blurInput(this)">
                                        <option value=""></option>    
                                        <option value="Karyawan">Karyawan</option>
                                        <option value="Manajer">Manajer</option>
                                        <option value="HRGA">HRGA</option>
                                    </select>
                                    <label class="label">Role</label>
                                </div>
                                <div class="container input-container">
                                    <button class="button-submit" type="submit">Create</button>
                                </div>
                            </form>

                            <!-- Form untuk unggah CSV -->
                            <form action="UploadCSV.php" method="post" enctype="multipart/form-data">
                                <div class="container input-container" id="fileContainer">
                                    <label for="csvFile">Upload CSV File</label>
                                    <input required="" class="flex w-full rounded-md border border-blue-300 border-input bg-white text-sm text-gray-400 file:border-0 file:bg-blue-600 file:text-white file:text-sm file:font-medium" type="file" name="csvFile" id="csvFile" accept=".csv"/>
                                </div>
                                <div class="container input-container">
                                    <button class="button-submit" type="submit" name="uploadCsv">Upload CSV File</button>
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