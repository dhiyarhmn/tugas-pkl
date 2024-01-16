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
                <a href="#">
                    <i class="fas fa-tachometer-alt"></i> 
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-user"></i> 
                    <span>Edit Profile</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-plus"></i> 
                    <span>Pengajuan Absensi</span>
                </a>
            </li>
            <li class="active">
                <a href="#">
                    <i class="fas fa-list-alt"></i> 
                    <span>Status Pengajuan</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-logout">
            <a href="#" class="btn logout-button">
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
            <div class="row justify-content-center mt-4 box-container">
                <div class="col-auto mb-3 larger-card" style="margin-top: 75px; ">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Approved</h5>
                                <p class="card-text">10</p>
                            </div>
                            <i class="fa fa-check-circle approved-icon" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="col-auto mb-3 larger-card" style="margin-top: 75px;">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Declined</h5>
                                <p class="card-text">1</p>
                            </div> 
                            <i class="fa fa-times-circle declined-icon" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="col-auto mb-3 larger-card" style="margin-top: 75px;">
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8);">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>     
                                <h5 class="card-title">On Process</h5>
                                <p class="card-text">9</p>
                            </div>
                            <i class="fa fa-exclamation-circle on-process-icon" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            <div class="custom-table-container">
                <table class="table table-bordered" style="background-color: rgba(220, 220, 220, 0.8);" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center table-column">Status</th>
                        <th class="text-center table-column">Jenis Absensi</th>
                        <th class="text-center table-column">Tanggal Pengajuan</th>
                        <th class="text-center detail-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="fa fa-check-circle approved-icon" aria-hidden="true"></i> Approve</td>
                        <td>AL (Annual Leave)</td>
                        <td>06-09-2023</td>
                        <td class="text-center"><button type="button" class="btn custom-detail-btn-blue">Detail</button></td>
                    </tr>
                    <tr>
                        <td><i class="fa fa-times-circle declined-icon" aria-hidden="true"></i> Declined</td>
                        <td>DL (Doctor Letter)</td>
                        <td>26-09-2023</td>
                        <td class="text-center"><button type="button" class="btn custom-detail-btn-blue">Detail</button></td>
                    </tr>
                    <tr>
                        <td><i class="fa fa-exclamation-circle on-process-icon" aria-hidden="true"></i> On Process</td>
                        <td>L (Late)</td>
                        <td>01-10-2023</td>
                        <td class="text-center"><button type="button" class="btn custom-detail-btn-blue">Detail</button></td>
                    </tr>
                </tbody>
            </table>
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
