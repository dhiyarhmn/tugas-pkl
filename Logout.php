<?php
session_start();

// Menghancurkan semua data sesi
session_unset();
session_destroy();

// Mengarahkan kembali ke halaman login
header("Location: /Login.php");
exit();
?>