<?php
session_start();

// Kullanıcı giriş durumu kontrolü
$isLoggedIn = isset($_SESSION['user_name']);
?>
