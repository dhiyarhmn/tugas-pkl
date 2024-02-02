<?php
session_start();

$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Manajer') {
    header("Location: /Login.php");
    exit(); 
}

// Pastikan pengguna telah terautentikasi, misalnya dengan menggunakan sesi atau metode lainnya.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST["new_password"];
    
    // Di sini Anda perlu memeriksa apakah pengguna yang saat ini masuk adalah pengguna yang sah
    // Kemudian, perbarui password pengguna dalam database sesuai dengan nilai $newPassword
    
    // Contoh: Update password pengguna dengan username tertentu
    $username = "nama_pengguna"; // Gantilah dengan username yang sesuai
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash password baru
    $sql = "UPDATE User SET Password='$hashedPassword' WHERE Username='$username'";
    
    if (mysqli_query($conn, $sql)) {
        echo "Password telah diperbarui.";
    } else {
        echo "Terjadi kesalahan saat memperbarui password: " . mysqli_error($conn);
    }
    
    // Tutup koneksi database Anda di sini jika diperlukan
}
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
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8); margin-top: 100px; margin-bottom: 30px;">
                        <div class="card-body">
                        <h5 class="card-title text-center mb-4">Reset Password</h5>
                            <form method="post" enctype="multipart/form-data">
                                <div class="container input-container">
                                    <label for="exampleInputEmail1" style="margin-bottom: -8px; margin-top: 30px;"> New Password</label>
                                    <div class="input-group">
                                        <input type="password" id="pass" class="input" onfocus="focusInput(this)" onblur="blurInput(this)">
                                        <div class="input-group-append" style="transform: translateY(-78%);">
                                            <span id="mybutton" onclick="change()" class="input" style="cursor: pointer;">
                                                <!-- icon mata bawaan bootstrap -->
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
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