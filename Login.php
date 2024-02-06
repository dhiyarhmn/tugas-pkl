<?php
session_start();

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

// Fungsi untuk membersihkan data input
function clean($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi Login
function loginUser($username, $password) {
    global $koneksi;

    $username = clean($username);
    $password = clean($password);

    // Query untuk mengambil data user
    $query = "SELECT * FROM User WHERE Username='$username' AND Password='$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Menyimpan data user ke dalam session
        $_SESSION['UserID'] = $row['UserID'];
        $_SESSION['Username'] = $row['Username'];
        $_SESSION['Role'] = $row['Role'];

        // Mengarahkan user ke dashboard berdasarkan role
        switch ($row['Role']) {
            case 'Karyawan':
                header("Location: ./Karyawan/DashboardKaryawan.php");
                break;
            case 'Manajer':
                header("Location: ./Manajer/DashboardManajer.php");
                break;
            case 'HRGA':
                header("Location: ./HRGA/DashboardHRGA.php");
                break;
            case 'Direktur':
                header("Location: ./Direktur/DashboardDirektur.php");
                break;
            case 'Admin':
                header("Location: ./Admin/DashboardAdmin.php");
                break;
            default:
                // Jika role tidak dikenal
                header("Location: login.php?error=role");
                break;
        }
        exit();
    } else {
        // Jika username atau password salah
        return false;
    }
}

$error = "";

// Menangani request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (loginUser($username, $password) === false) {
        $error = "Username atau password salah. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>PT. Daekyung Indah Heavy Industry</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <link rel="stylesheet" href="/css/style.css"> 
</head>

<body>
  <div class="bg"></div>
  <div class="bg bg2"></div>
  <div class="bg bg3"></div>
  <div class="header">
    <div class="inner-header flex flex-col items-center justify-center h-screen">
        <div class="container">
        <form class="form" method="post" action="">
          <div class="form_front login-box">
                <div class="col-auto">
                  <img src="/Assets/img/logo3.png" class="img-fluid" style="max-width: 100px; height: auto;">  
                </div>
                  <div class="form_details">Login</div>
                    <input required="" type="text" id="username" name="username" class="input" placeholder="Username" autocomplete="username">
                    
                        <div class="input-group">
                            <input required="" type="password" id="password" name="password" class="input" placeholder="Password" autocomplete="current-password">
                            <div class="input-group-append">
                                <span id="togglePasswordVisibility" onclick="togglePasswordVisibility('password')" class="inputEye" style="cursor: pointer; margin-top: -35.2px;">
                                    <!-- Bootstrap eye icon -->
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                        <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                   
                  <button class="btn">Login</button>
              </div>
          </form>
      </div>
    </div>
  </div>
  
  <!-- Pop-up Card untuk Menampilkan Pesan Kesalahan -->
  <div id="popup-card" class="fixed inset-0 flex items-center justify-center hidden">
    <div class="bg-white p-4 shadow-lg rounded-md">
      <h2 class="text-lg font-semibold mb-2">Error</h2>
      <p><?= $error ?></p>
        <button id="close-popup" style="background-color: #160066; color: white; padding: 4px 12px; margin-top: 10px; border-radius: 4px; float: right;">Close</button>
    </div>
  </div>
  <script>
    // Fungsi untuk menampilkan pop-up card
    function showPopup() {
      document.getElementById("popup-card").classList.remove("hidden");
    }

    // Fungsi untuk menyembunyikan pop-up card
    function closePopup() {
      document.getElementById("popup-card").classList.add("hidden");
    }

    // Mendaftarkan event click pada tombol close-popup
    document.getElementById("close-popup").addEventListener("click", closePopup);

    // Menampilkan pop-up card jika error tidak kosong
    <?php if (!empty($error)) : ?>
      showPopup();
    <?php endif; ?>

    function togglePasswordVisibility(passwordFieldId) {
      var field = document.getElementById(passwordFieldId);
      var button = field.parentNode.querySelector(".input-group-append .inputEye");

      if (field.type === "password") {
        field.type = "text";
        button.innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                              <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                              <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                              </svg>`;
      } else {
        field.type = "password";
        button.innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                              <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                              </svg>`;
      }
    }
  </script>
</body>
</html>
