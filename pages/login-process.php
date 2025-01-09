<?php
session_start();
require 'config.php'; // Veritabanı bağlantısı

// POST ile gelen giriş bilgilerini kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Eğer e-posta veya şifre boşsa hata döndür
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'E-posta veya şifre boş olamaz!';
        header('Location: girisyap.php');
        exit;
    }

    try {
        // Kullanıcıyı veritabanında ara
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kullanıcı bulundu mu ve şifre doğru mu kontrol et
        if ($user && password_verify($password, $user['password'])) {
            // Onay bekleyen hesaplar için kontrol
            if ($user['role'] === 'employer' && $user['is_pending'] == 1) {
                $_SESSION['error'] = 'Hesabınız henüz onaylanmamıştır. Lütfen bekleyiniz.';
                header('Location: girisyap.php');
                exit;
            }
            // Kullanıcı oturum bilgilerini sakla
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_role'] = $user['role'];

            // Yönlendirme parametresi varsa onu kullan
            $redirect_to = $_GET['redirect_to'] ?? 'index.php';
            header("Location: ../$redirect_to");
            exit;
        } else {
            // Geçersiz giriş durumunda hata mesajı
            $_SESSION['error'] = 'Geçersiz e-posta veya şifre!';
            header('Location: girisyap.php');
            exit;
        }
    } catch (PDOException $e) {
        // Veritabanı hatası durumunda
        $_SESSION['error'] = 'Bir hata oluştu: ' . $e->getMessage();
        header('Location: girisyap.php');
        exit;
    }
} else {
    // Doğrudan erişim engelleme
    $_SESSION['error'] = 'Bu sayfaya doğrudan erişim yapılamaz!';
    header('Location: girisyap.php');
    exit;
}
