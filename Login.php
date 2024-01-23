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
                header("Location: ./HRGA/DashboardHRGA.php.php");
                break;
            case 'Direktur':
                header("Location: ./Direktur/DashboardDirektur.php.php");
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
                    <input required="" type="password" id="password" name="password" class="input" placeholder="Password" autocomplete="current-password">
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
  </script>
</body>
</html>
