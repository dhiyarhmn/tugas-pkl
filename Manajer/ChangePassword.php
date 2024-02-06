<?php
session_start();

$koneksi = mysqli_connect("localhost", "root", "", "pengajuanabsensi3");

if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION["UserID"]) || $_SESSION["Role"] != 'Manajer') {
    header("Location: /Login.php");
    exit();
}

function changePassword($koneksi) {
    if (count($_POST) > 0) {
        $sql = "SELECT * FROM User WHERE UserID= ?";
        $statement = $koneksi->prepare($sql);

        if (!$statement) {
            die("Prepare failed: " . $koneksi->error);
        }

        $statement->bind_param('i', $_SESSION["UserID"]);
        $statement->execute();

        if (!$statement->execute()) {
            die("Execute failed: " . $statement->error);
        }

        $result = $statement->get_result();
        $row = $result->fetch_assoc();

        if (!empty($row)) {
            $currentPassword = $_POST["currentPassword"];
            $newPassword = $_POST["newPassword"];
            $confirmPassword = $_POST["confirmPassword"];

            if ($currentPassword === $row["Password"]) {
                if ($newPassword === $confirmPassword) {
                    $sql = "UPDATE User SET Password=? WHERE UserID=?";
                    $updateStatement = $koneksi->prepare($sql);

                    if (!$updateStatement) {
                        die("Prepare failed: " . $koneksi->error);
                    }

                    $updateStatement->bind_param('si', $newPassword, $_SESSION["UserID"]);

                    if ($updateStatement->execute()) {
                        header("Location: EditProfileManajer.php");
                        exit();
                    } else {
                        die("Execute failed: " . $updateStatement->error);
                    }
                } else {
                    // Change the return value for mismatched passwords
                    return "MismatchedPasswords";
                }
            } else {
                return "IncorrectCurrentPassword";
            }
        }
    }
    return "";
}

$message = changePassword($koneksi);
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
            <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                <a href="EditProfileManajer.php" class="btn custom-detail-btn-blue" style="font-size: 12px; padding: 10px 10px; width: 120px; margin-top: 30px;">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a> 
                    <div class="card rounded-card" style="background-color: rgba(220, 220, 220, 0.8); margin-top: 10px; margin-bottom: 30px;">
                        <div class="card-body">
                            <h5 class="card-title text-center mb-4">Change Password</h5>
                            <form name="frmChange" method="post" action="" onSubmit="return validatePassword()">
                                <div class="container input-container">
                                    <label for="currentPassword" style="margin-bottom: -8px; margin-top: 30px;">Current Password</label>
                                    <div class="input-group">
                                        <input required="" type="password" id="currentPassword" name="currentPassword" class="input full-width" onfocus="focusInput(this)" onblur="blurInput(this)">
                                        <div class="input-group-append">
                                            <span id="mybutton" onclick="togglePasswordVisibility('currentPassword')" class="input" style="cursor: pointer; margin-top: -35.2px;">
                                                <!-- Bootstrap eye icon -->
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="container input-container">
                                    <label for="newPassword" style="margin-bottom: -8px;">New Password</label>
                                    <div class="input-group">
                                        <input required="" type="password" id="newPassword" name="newPassword" class="input full-width" onfocus="focusInput(this)" onblur="blurInput(this)">
                                        <div class="input-group-append">
                                            <span id="mybutton" onclick="togglePasswordVisibility('newPassword')" class="input" style="cursor: pointer; margin-top: -35.2px;">
                                                <!-- Bootstrap eye icon -->
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="container input-container">
                                    <label for="confirmPassword" style="margin-bottom: -8px;">Confirm Password</label>
                                    <div class="input-group">
                                        <input required="" type="password" id="confirmPassword" name="confirmPassword" class="input full-width" onfocus="focusInput(this)" onblur="blurInput(this)">
                                        <div class="input-group-append">
                                            <span id="mybutton" onclick="togglePasswordVisibility('confirmPassword')" class="input" style="cursor: pointer; margin-top: -35.2px;">
                                                <!-- Bootstrap eye icon -->
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="container">
                                    <button class="button-submit" type="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Alert Modal -->
    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="bg-white p-4 shadow-lg rounded-md" style="height: 160px; width: 400px; margin: 0 auto; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <div>
                <h2 class="text-lg font-semibold mb-2" id="modalLabel" style="margin-top: -15px;">Error</h2>
                </div>
                <div>
                    <p id="alertMessage"><?= $error ?></p>
                </div>
                <div>
                    <button type="button" class="btn custom-close-btn" data-dismiss="modal" style="background-color: #160066; color: white; padding: 4px 12px; margin-top: 0px; border-radius: 4px; float: right;">Close</button>
                </div>
            </div>
        </div>
    </div>
<!-- Bootstrap and jQuery libraries -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    var message = "<?php echo $message; ?>";
    if (message === "IncorrectCurrentPassword") {
        $('#alertMessage').text("Current Password is not correct.");
        $('#alertModal').modal('show');
    } else if (message === "MismatchedPasswords") { // Handle the new message
        $('#alertMessage').text("New Password and Confirm Password do not match.");
        $('#alertModal').modal('show');
    }
});
</script>

<!-- Adjusted JS link. Assuming 'script.js' is in the same directory as the PHP file -->
<script src="./js/script.js"></script>

</body>
</html>