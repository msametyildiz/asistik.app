<?php
session_start();
include 'config.php'; // Veritabanı bağlantısı

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Kullanıcı kontrolü
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Oturum bilgilerini sakla
        $_SESSION['user_name'] = $user['first_name'] . " " . $user['last_name'];
        $_SESSION['logged_in'] = true;
        header("Location: index.php"); // Giriş başarılı, index sayfasına yönlendir
        exit;
    } else {
        echo "<script>alert('Hatalı e-posta veya şifre!'); window.location.href = 'girisyap.html';</script>";
    }
} else {
    echo "Bu sayfaya doğrudan erişim yapılamaz.";
}
?>
