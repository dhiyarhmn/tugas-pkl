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
        header("Location: Login.php?error=invalid");
        exit();
    }
}

// Menangani request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    loginUser($username, $password);
}
?>

<!-- Tambahkan formulir login di sini -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Halaman Login</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    /* Custom styles */
  </style>
</head>

<body>
  <div class="header">
    <div class="inner-header flex flex-col items-center justify-center h-screen">
      <form class="grid card w-150 px-10 py-8 bg-white" method="post">
        <div class="text-center text-5xl font-medium text-gray-800 mb-5">
          Pengajuan Absensi
        </div>
        <div class="text-center text-xl font-medium text-gray-600 mb-5">
          PT. Daekyung Indah Heavy Industry
        </div>
        <div class="mb-3">
          <label class="block text-gray-800 text-xl font-bold mb-0 pr-4" for="inline-full-name">Username</label>
          <input name="username" id="inline-full-name" class="bg-gray-300 appearance-none border-2 border-gray-400 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-gray-200  focus:border-gray-300" type="text" placeholder="Enter your username">
        </div>
        <div class="mb-4">
          <label class="block text-gray-800 text-xl font-bold mb-0 pr-4" for="inline-password">Password</label>
          <input name="password" id="inline-password" class="bg-gray-300 appearance-none border-2 border-gray-400 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-gray-200 focus:border-gray-300" type="password" placeholder="Enter your password">
        </div>
        <div class="justify-self-end">
          <button name="submit" class="focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded bg-green-500 hover:bg-green-600" type="submit">
            Login
          </button>
        </div>
        <?php
          // Menampilkan pesan kesalahan jika terdapat kesalahan
          if (!empty($error)) {
              echo '<div class="text-red-500 text-center mt-2">' . $error . '</div>';
          }
        ?>
      </form>
    </div>
  </div>
</body>

</html>