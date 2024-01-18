<?php
session_start();


// Koneksi ke database (sesuaikan dengan detail koneksi Anda)
$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Manajer') {
    header("Location: /Login.php");
    exit(); // Penting untuk menghentikan eksekusi skrip lebih lanjut
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

<div class="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <button type="button" id="sidebarCollapse" class="btn">
                <i class="fas fa-bars"></i> <!-- Ikon hamburger -->
            </button>
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
            <li>
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
            <li class="active">
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
            
            <div class="custom-table-container">
                <table class="table table-bordered" style="background-color: rgba(220, 220, 220, 0.8);" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center table-column" style="width: 20px;">No</th>
                        <th class="text-center table-column">Nama</th>
                        <th class="text-center table-column">NIK</th>
                        <th class="text-center table-column">Jenis Absensi</th>
                        <th class="text-center table-column" style="width: 20px;">Tanggal Pengajuan</th>
                        <th class="text-center table-column">Berkas</th>
                        <th class="text-center detail-column" style="width: 50px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>Puti Dhiya</td>
                        <td>24060121140173</td>
                        <td>AL (Annual Leave)</td>
                        <td>26-09-2023</td>
                        <td class="text-center">bukti.pdf</td>
                        <td>
                            <div class="button-row">
                              <button type="button" class="btn custom-approval-btn-green" data-toggle="modal" data-target="#approveModal">Approve</button>
                              <button type="button" class="btn custom-decline-btn-red" data-toggle="modal" data-target="#declineModal">Decline</button>
                            </div>

                            <!-- Wrapper for the lower row of buttons (Detail) -->
                            <div style="text-align: center;">
                              <button type="button" class="btn custom-detail-btn-blue" data-toggle="modal" data-target="#detailModal">Detail</button>
                          </div>
                      </td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td>Satria</td>
                        <td>24060121140099</td>
                        <td>Late (Terlambat)</td>
                        <td>06-09-2023</td>
                        <td class="text-center">bukti.pdf</td>
                        <td>
                            <div class="button-row">
                              <button type="button" class="btn custom-approval-btn-green" data-toggle="modal" data-target="#approveModal">Approve</button>
                              <button type="button" class="btn custom-decline-btn-red" data-toggle="modal" data-target="#declineModal">Decline</button>
                            </div>

                            <!-- Wrapper for the lower row of buttons (Detail) -->
                            <div style="text-align: center;">
                              <button type="button" class="btn custom-detail-btn-blue" data-toggle="modal" data-target="#detailModal">Detail</button>
                          </div>
                      </td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td>Pusat</td>
                        <td>24060121140040</td>
                        <td>Legal Permit (Izin Resmi)</td>
                        <td>10-01-2023</td>
                        <td class="text-center">bukti.pdf</td>
                        <td>
                            <div class="button-row">
                              <button type="button" class="btn custom-approval-btn-green" data-toggle="modal" data-target="#approveModal">Approve</button>
                              <button type="button" class="btn custom-decline-btn-red" data-toggle="modal" data-target="#declineModal">Decline</button>
                            </div>

                            <!-- Wrapper for the lower row of buttons (Detail) -->
                            <div style="text-align: center;">
                              <button type="button" class="btn custom-detail-btn-blue" data-toggle="modal" data-target="#detailModal">Detail</button>
                          </div>
                      </td>
                    </tr>
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
        <h5 class="modal-title" id="detailModalLabel">Detail Pengajuan Absensi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Isi detail pengajuan absensi akan ditampilkan di sini -->
        <!-- Anda bisa menambahkan informasi absensi seperti jenis, tanggal, dan lainnya di sini -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Popup card approve -->
<div id="approveModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Approval</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        Pengajuan Absensi Berhasil Di Approve
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Popup card decline -->
<div id="declineModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Decline</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        Pengajuan Absensi Berhasil Di Decline
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
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/datatables-demo.js"></script>

<script src="./js/script.js"></script>

</body>
</html>
