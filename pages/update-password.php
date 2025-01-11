<?php
// Veritabanı bağlantısını sağlayın
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formdan gelen veriler
    $token = trim($_POST['token']);
    $newPassword = trim($_POST['newPassword']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Alanların kontrolü
    if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
        die("Lütfen tüm alanları doldurun.");
    }

    // Şifrelerin eşleşip eşleşmediğini kontrol edin
    if ($newPassword !== $confirmPassword) {
        die("Şifreler eşleşmiyor. Lütfen tekrar deneyin.");
    }

    // Şifre uzunluğu kontrolü
    if (strlen($newPassword) < 6) {
        die("Şifreniz en az 6 karakter uzunluğunda olmalıdır.");
    }

    try {
        // Token'ın veritabanında geçerli olup olmadığını kontrol edin
        $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = ? AND created_at >= NOW() - INTERVAL 1 HOUR");
        
        $stmt->execute([$token]);
        $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resetRequest) {
            die("Bu sıfırlama bağlantısı geçersiz veya süresi dolmuş.");
        }

        // Kullanıcı e-posta adresini alın
        $email = $resetRequest['email'];

        // Şifreyi hash'le
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Kullanıcı şifresini güncelle
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);

        // Kullanılmış token'ı sil
        $stmt = $db->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);

        // Başarılı mesajı ve yönlendirme
        echo "<script>
            alert('Şifreniz başarıyla güncellendi.');
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 2000); // 2 saniye gecikme
        </script>";



    } catch (PDOException $e) {
        die("Bir hata oluştu: " . $e->getMessage());
    }
} else {
    echo "Bu sayfaya doğrudan erişim yapılamaz.";
}
?>
