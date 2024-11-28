
<?php
// Veritabanı bağlantısını sağlayın
include 'config.php'; // Veritabanı bağlantısı için gerekli bilgileri içeren dosya

if ($_POST) {
    // Formdan gelen verileri alın ve temizleyin
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Alanların boş olup olmadığını kontrol edin
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        die("Lütfen tüm alanları doldurun.");
    }

    // Şifrelerin eşleşip eşleşmediğini kontrol edin
    if ($password !== $confirmPassword) {
        die("Şifreler eşleşmiyor. Lütfen tekrar deneyin.");
    }

    // E-posta geçerliliğini kontrol edin
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Geçersiz bir e-posta adresi girdiniz.");
    }

    // Şifreyi hashle
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Kullanıcıyı veritabanına ekleyin
        $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);
    
        // Kayıt başarılı mesajı ve yönlendirme
        echo "<script>
                alert('Kayıt başarılı! Giriş yap ekranına yönlendiriliyorsunuz.');
                window.location.href = 'girisyap.php';
              </script>";
        exit; // İşlem tamamlandıktan sonra scripti sonlandır
    } catch (PDOException $e) {
        // Hata kontrolü
        if ($e->getCode() === '23000') { // UNIQUE constraint violation
            $errorMessage = "Bu e-posta adresi zaten kayıtlı.";
        } else {
            $errorMessage = "Bir hata oluştu: " . $e->getMessage();
        }
    
        // Hata mesajı
        echo "<script>alert('$errorMessage'); window.history.back();</script>";
        exit;
    }
    
    
} else {
    echo "Bu sayfaya doğrudan erişim yapılamaz.";
}
?>
