<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "pengajuanabsensi3";

$koneksi = new mysqli($host, $username, $password, $dbname);

if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $namaLengkap = mysqli_real_escape_string($koneksi, $_POST['namaLengkap']);
    $jenisKelamin = mysqli_real_escape_string($koneksi, $_POST['jenisKelamin']);
    $tahunMasuk = mysqli_real_escape_string($koneksi, $_POST['tahunMasuk']);
    $departemen = mysqli_real_escape_string($koneksi, $_POST['departemen']);
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    // Create username and password for user account
    $username = strtolower(str_replace(' ', '.', $namaLengkap));

        // Generate random password
    $password = mt_rand(100000, 999999); // Angka acak antara 100000 dan 999999 

    // Insert into User table
    $insertUser = "INSERT INTO User (Username, Password, Role) VALUES ('$username', '$password', '$role')";
    if ($koneksi->query($insertUser) === TRUE) {
        $last_id = $koneksi->insert_id;
        
        // Insert into respective role table
        $insertRoleTable = "INSERT INTO $role (NIK, UserID, NamaLengkap, JenisKelamin, TahunMasuk, Departemen, Jabatan) 
                            VALUES ('$nik', $last_id, '$namaLengkap', '$jenisKelamin', $tahunMasuk, '$departemen', '$jabatan')";
        if ($koneksi->query($insertRoleTable) === TRUE) {
            // Redirect to dashboard.php after successful insertion
            header('Location: DashboardAdmin.php');
            exit();
        } else {
            echo "Error: " . $koneksi->error;
        }
    } else {
        echo "Error: " . $koneksi->error;
    }
    $koneksi->close();
} else {
    echo "Invalid request method";
}
?>