<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "pengajuanabsensi3";

$koneksi = new mysqli($host, $username, $password, $dbname);

if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

if (isset($_POST['uploadCsv']) && isset($_FILES['csvFile'])) {
    $csvFile = $_FILES['csvFile']['tmp_name'];

    if (!is_uploaded_file($csvFile)) {
        exit("Please upload a CSV file.");
    }

    $file = fopen($csvFile, 'r');
    fgetcsv($file); // Skip the header row

    while (($row = fgetcsv($file)) !== FALSE) {
        $nik = mysqli_real_escape_string($koneksi, $row[0]);
        $namaLengkap = mysqli_real_escape_string($koneksi, $row[1]);
        $email = mysqli_real_escape_string($koneksi, $row[2]);
        $noHP = mysqli_real_escape_string($koneksi, $row[3]);
        $jenisKelamin = mysqli_real_escape_string($koneksi, $row[4]);
        $tahunMasuk = mysqli_real_escape_string($koneksi, $row[5]);
        $departemen = mysqli_real_escape_string($koneksi, $row[6]);
        $jabatan = mysqli_real_escape_string($koneksi, $row[7]);
        $role = mysqli_real_escape_string($koneksi, $row[8]);

        $username = strtolower(str_replace(' ', '.', $namaLengkap));
        $password = mt_rand(100000, 999999);

        // Check if username already exists
        $checkUsername = $koneksi->prepare("SELECT Username FROM User WHERE Username = ?");
        $checkUsername->bind_param("s", $username);
        $checkUsername->execute();
        $result = $checkUsername->get_result();
        if ($result->num_rows > 0) {
            $username .= '.' . $nik;
        }

        $insertUser = "INSERT INTO User (Username, Password, Role) VALUES ('$username', '$password', '$role')";
        if ($koneksi->query($insertUser) === TRUE) {
            $last_id = $koneksi->insert_id;

            // Check if NIK already exists in the role table
            $checkNIK = $koneksi->prepare("SELECT NIK FROM $role WHERE NIK = ?");
            $checkNIK->bind_param("s", $nik);
            $checkNIK->execute();
            $resultNIK = $checkNIK->get_result();
            if ($resultNIK->num_rows == 0) {
                $insertRoleTable = "INSERT INTO $role (NIK, UserID, NamaLengkap, Email, NoHP, JenisKelamin, TahunMasuk, Departemen, Jabatan) 
                                    VALUES ('$nik', $last_id, '$namaLengkap', '$email', '$noHP', '$jenisKelamin', $tahunMasuk, '$departemen', '$jabatan')";
                if ($koneksi->query($insertRoleTable) === TRUE) {
                    $tahunSekarang = date("Y");
                    $insertCutiTahunan = "INSERT INTO CutiTahunan (UserID, Tahun, JatahCuti, CutiTerpakai, CutiSisa) 
                                          VALUES ($last_id, '$tahunSekarang', 12, 0, 12)";
                    $koneksi->query($insertCutiTahunan);
                } else {
                    echo "Error in Role Table: " . $koneksi->error;
                }
            } else {
                echo "NIK already exists in the $role table, skipping...";
            }
        } else {
            echo "Error in User Table: " . $koneksi->error;
        }
    }

    fclose($file);
    header("Location: DashboardAdmin.php");
    exit();
}
$koneksi->close();
?>