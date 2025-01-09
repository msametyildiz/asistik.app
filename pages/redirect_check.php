<?php
session_start();

// Hedef sayfa
$target = isset($_GET['target']) ? $_GET['target'] : 'index.php';

// Oturum açık değilse giriş sayfasına yönlendir
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: girisyap.php?redirect_to=$target");
    exit;
}

// Kullanıcı role göre yönlendirme
if ($_SESSION['user_role'] === 'employer' || $_SESSION['user_role'] === 'job_seeker') {
    header("Location: $target");
} else {
    header("Location: ../index.php"); // Geçersiz role durumunda ana sayfaya yönlendir
}
exit;
