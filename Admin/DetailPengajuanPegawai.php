<?php
session_start();

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Admin') {
    header("Location: /Login.php");
    exit();
}

// Inisialisasi variabel
$rowAdmin = null;
$userDetails = [];

// Cek apakah UserID ada di session
if (isset($_SESSION['UserID'])) {
    $userID = $_SESSION['UserID'];

    // Ambil data Admin dan username berdasarkan UserID
    $queryGetDataAdmin = "SELECT K.NIK, K.NamaLengkap, K.Email, K.NoHP, K.Departemen, K.JenisKelamin, K.Jabatan, U.Username, U.ProfilePhoto FROM Admin AS K INNER JOIN User AS U ON K.UserID = U.UserID WHERE K.UserID = '$userID'";
    $resultGetDataAdmin = $koneksi->query($queryGetDataAdmin);

    if ($resultGetDataAdmin->num_rows > 0) {
        $rowAdmin = $resultGetDataAdmin->fetch_assoc();
        $userDetails = $rowAdmin; // data untuk tampilan profil
    }
}

// Ambil NIK dari URL
$nik = isset($_GET['NIK']) ? $_GET['NIK'] : '';

// Tentukan tabel pengguna berdasarkan NIK (misalnya, cek di tabel Karyawan, Manajer, HRGA)
// Contoh sederhana, Anda mungkin perlu logika yang lebih kompleks
if (cekNIKdiTabel($nik, 'Karyawan', $koneksi)) {
    $tabelPengguna = 'Karyawan';
} elseif (cekNIKdiTabel($nik, 'Manajer', $koneksi)) {
    $tabelPengguna = 'Manajer';
} elseif (cekNIKdiTabel($nik, 'HRGA', $koneksi)) {
    $tabelPengguna = 'HRGA';
} else {
    die("NIK tidak ditemukan di database.");
}

// Persiapan Query dengan Prepared Statement untuk pengajuan absensi
$query = "SELECT a.AbsensiID, a.TanggalPengajuan, a.NamaJenisAbsensi, a.Berkas, pa.StatusPersetujuan
          FROM Absensi a
          JOIN PersetujuanAbsensi pa ON a.AbsensiID = pa.AbsensiID
          JOIN $tabelPengguna k ON a.UserID = k.UserID
          WHERE k.NIK = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $nik);
$stmt->execute();
$result = $stmt->get_result();

// Ambil departemen dan jabatan user yang dipilih
$deptJabatanQuery = "SELECT NamaLengkap, Departemen, Jabatan FROM $tabelPengguna WHERE NIK = ?";
$deptJabatanStmt = $koneksi->prepare($deptJabatanQuery);
$deptJabatanStmt->bind_param("s", $nik); // 's' menunjukkan bahwa parameter adalah string
$deptJabatanStmt->execute();
$deptJabatanResult = $deptJabatanStmt->get_result();
if ($deptJabatanData = $deptJabatanResult->fetch_assoc()) {
    $namaLengkapUserDipilih = $deptJabatanData['NamaLengkap'];
    $departemenUserDipilih = $deptJabatanData['Departemen'];
    $jabatanUserDipilih = $deptJabatanData['Jabatan'];
} else {
    // Handle kasus dimana data tidak ditemukan
    $namaLengkapUserDipilih = "";
    $departemenUserDipilih = "";
    $jabatanUserDipilih = "";
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
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/fonts.css"> 
</head>
<body>
    <div>
        <div class="container mt-3"> 
            <div class="row justify-content-center align-items-center">
                <div class="col-auto">
                    <img src="/Assets/img/logo3.png" class="img-fluid" style="max-width: 60px; height: auto;">  
                </div>
                <div class="col-auto">
                    <h2>PT. DAEKYUNG INDAH HEAVY INDUSTRY</h2>
                </div>
            </div>
            <div class="custom-table-container">
            <a href="ListKaryawanAdmin.php" class="btn custom-detail-btn-blue" style="font-size: 12px; padding: 10px 10px; width: 120px">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8); margin-bottom: 30px; margin-top: 30px;">
                        <div class="card-body" style="padding-bottom: 0px;">
                            <form method="post" enctype="multipart/form-data">
                                <div class="container">
                                    <div class="d-flex align-items-start">
                                        <!-- Input Fields Section -->
                                        <div class="d-flex justify-content-start align-items-start"> <!-- Container untuk keseluruhan form -->
                                            <!-- Kolom pertama untuk NIK dan Nama -->
                                            <div class="flex-column mr-2" > 
                                                <div class="input-container mb-3" style="width: 425px;">
                                                    <label for="nik">NIK</label>
                                                    <input required="" type="text" id="nik" name="NIK" class="input" value="<?php echo $nik; ?>" readonly style="background-color: #8a8a8a; color: black;">
                                                </div>
                                                <div class="input-container" style="width: 425px;">
                                                    <label for="nama">Nama</label>
                                                    <input required="" type="text" id="nama" name="Nama" class="input" value="<?php echo $namaLengkapUserDipilih; ?>" readonly style="background-color: #8a8a8a; color: black;">
                                                </div>
                                            </div>
                                            <!-- Kolom kedua untuk Departemen dan Jabatan -->
                                            <div class="flex-column">
                                                <div class="input-container mb-3" style="width: 425px;">
                                                    <label for="departemen">Departemen</label>
                                                    <input required="" type="text" id="departemen" name="Departemen" class="input" value="<?php echo $departemenUserDipilih; ?>" readonly style="background-color: #8a8a8a; color: black;">
                                                </div>
                                                <div class="input-container" style="width: 425px;">
                                                    <label for="jabatan">Jabatan</label>
                                                    <input required="" type="text" id="jabatan" name="jabatan" class="input" value="<?php echo $jabatanUserDipilih; ?>" readonly style="background-color: #8a8a8a; color: black;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="custom-table-container">
            <table class="table table-bordered" style="background-color: rgba(220, 220, 220, 0.8);" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr class='text-center'>
                        <th>No</th>
                        <th>AbsensiID</th>
                        <th>Jenis Absensi</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Berkas</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; // Inisialisasi nomor urutan
                    while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                        <tr class='text-center'>
                            <td style="width: 1%;"><?php echo $no; ?></td>
                            <td style="width: 10%;"><?php echo htmlspecialchars($row['AbsensiID']); ?></td>
                            <td style="width: 15%;"><?php echo htmlspecialchars($row['NamaJenisAbsensi']); ?></td>
                            <td style="width: 15%;"><?php echo htmlspecialchars($row['TanggalPengajuan']); ?></td>
                            <td style="width: 20%;">
                                <?php
                                $role = $tabelPengguna; 

                                    $folderBerkas = ''; 
                                    switch ($role) { // Use the assigned role
                                        case 'Karyawan':
                                            $folderBerkas = 'Karyawan/BerkasKaryawan';
                                            break;
                                        case 'Manajer':
                                            $folderBerkas = 'Manajer/BerkasManajer';
                                            break;
                                        case 'HRGA':
                                            $folderBerkas = 'HRGA/BerkasHRGA';
                                            break;
                                        // Include other cases if necessary
                                    }

                                    $namaBerkas = basename($row['Berkas']);
                                    $berkasLink = !empty($namaBerkas) ? "<a href='/$folderBerkas/" . urlencode($namaBerkas) . "' target='_blank'>Lihat Berkas</a>" : "Tidak ada berkas";
                                    echo $berkasLink;
                                ?>
                            </td>
                            <td style="width: 20%;">
                                <i class="fa <?php echo ($row['StatusPersetujuan'] == 'Approved') ? 'fa-check-circle approved-icon' : (($row['StatusPersetujuan'] == 'Declined') ? 'fa-times-circle declined-icon' : 'fa-exclamation-circle on-process-icon'); ?>" aria-hidden="true"></i>
                                <?php echo htmlspecialchars($row['StatusPersetujuan']); ?>
                            </td>
                            <td style="width: 20%;" class="text-center">
                                <button style="width: 100%;" type="button" class="btn custom-detail-btn-blue" data-toggle="modal" data-target="#detailModal" onclick="showDetail(<?php echo htmlspecialchars($row['AbsensiID']); ?>)">Detail</button>
                            </td>
                        </tr>
                        <?php 
                        $no++; // Inkrement nomor urutan
                        endwhile; 
                        ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Popup card detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Absensi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Menampilkan informasi departemen dan jabatan -->
        <p><strong>Departemen:</strong> <?php echo $departemenUserDipilih; ?></p>
        <p><strong>Jabatan:</strong> <?php echo $jabatanUserDipilih; ?></p>
        <!-- Isi detail absensi akan ditampilkan di sini -->
        <!-- Anda bisa menambahkan informasi absensi seperti jenis, tanggal, dan lainnya di sini -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap and jQuery libraries -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/datatables-demo.js"></script>

<script src="./js/script.js"></script>

<script src="./js/detailAbsensi.js"></script>
</body>

</html>

<?php
// Fungsi untuk cek NIK di tabel tertentu
function cekNIKdiTabel($nik, $tabel, $koneksi) {
    $stmt = $koneksi->prepare("SELECT * FROM $tabel WHERE NIK = ?");
    $stmt->bind_param("s", $nik);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
?>