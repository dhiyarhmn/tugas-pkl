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
    // Handle CSV file upload
    $csvFile = $_FILES['csvFile']['tmp_name'];

    if (!is_uploaded_file($csvFile)) {
        exit("Please upload a CSV file.");
    }

    $file = fopen($csvFile, 'r');
    // Skip the header row
    fgetcsv($file);

   while (($row = fgetcsv($file)) !== FALSE) {
    // Asumsikan struktur CSV: NIK, Nama Lengkap, Email, No HP, Jenis Kelamin, Tahun Masuk, Departemen, Jabatan, Role
    $nik = mysqli_real_escape_string($koneksi, $row[0]);
    $namaLengkap = mysqli_real_escape_string($koneksi, $row[1]);
    $jenisKelamin = mysqli_real_escape_string($koneksi, $row[2]);
    $tahunMasuk = mysqli_real_escape_string($koneksi, $row[5]);
    $departemen = mysqli_real_escape_string($koneksi, $row[6]);
    $jabatan = mysqli_real_escape_string($koneksi, $row[7]);
    $role = mysqli_real_escape_string($koneksi, $row[8]);

    // Create username and password for user account
    $username = strtolower(str_replace(' ', '.', $namaLengkap));
        // Generate random password
    $password = mt_rand(100000, 999999); // Contoh: menghasilkan angka acak antara 100000 dan 999999

    // Insert into User table
    $insertUser = "INSERT INTO User (Username, Password, Role) VALUES ('$username', '$password', '$role')";
    if ($koneksi->query($insertUser) === TRUE) {
        $last_id = $koneksi->insert_id;
        
        // Insert into respective role table
        $insertRoleTable = "INSERT INTO $role (NIK, UserID, NamaLengkap, JenisKelamin, TahunMasuk, Departemen, Jabatan) 
                            VALUES ('$nik', $last_id, '$namaLengkap', '$jenisKelamin', $tahunMasuk, '$departemen', '$jabatan')";
        $koneksi->query($insertRoleTable);
    } else {
        echo "Error in User Table: " . $koneksi->error;
    }
}

    fclose($file);
    echo "CSV data processed successfully.";
} else {
    // Handle manual input
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // [Isi dengan kode untuk memproses dan menyimpan data dari form manual]
        $nik = $_POST['nik'];
        $namaLengkap = $_POST['namaLengkap'];
        $email = $_POST['email'];
        $noHP = $_POST['noHP'];
        $jenisKelamin = $_POST['jenisKelamin'];
        $tahunMasuk = $_POST['tahunMasuk'];
        $departemen = $_POST['departemen'];
        $jabatan = $_POST['jabatan'];
        $role = $_POST['role'];

        // Create username and password for user account
        $username = strtolower(str_replace(' ', '.', $namaLengkap));

        // Insert into User table
        $insertUser = "INSERT INTO User (Username, Password, Role) VALUES ('$username', '$password', '$role')";
        if ($koneksi->query($insertUser) === TRUE) {
            $last_id = $koneksi->insert_id;
            
            // Insert into respective role table
            $insertRoleTable = "INSERT INTO $role (NIK, UserID, NamaLengkap, Email, NoHP, JenisKelamin, TahunMasuk, Departemen, Jabatan) 
                                VALUES ('$nik', $last_id, '$namaLengkap', '$email', '$noHP', '$jenisKelamin', $tahunMasuk, '$departemen', '$jabatan')";
            if ($koneksi->query($insertRoleTable) === TRUE) {
                echo "New employee and user account created successfully";
            } else {
                echo "Error: " . $koneksi->error;
            }
        } else {
            echo "Error: " . $koneksi->error;
        }
    } else {
        echo "Invalid request method";
    }
}

$koneksi->close();
?>
