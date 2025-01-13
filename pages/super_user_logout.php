<?php
session_start(); // Oturum başlat
session_destroy(); // Oturumu sonlandır
header('Location: super_user_login.php?logout=success'); // Giriş sayfasına yönlendir ve bir parametre gönder
exit;
?>
