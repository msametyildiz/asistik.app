<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-check-label a {
            color: #007bff;
            text-decoration: none;
        }
        .form-check-label a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4 shadow-lg" style="width: 100%; max-width: 400px;">
            <h2 class="text-center mb-4">Hesap Oluştur</h2>
            <form action="#" method="POST" onsubmit="return validateForm()">
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


        // Kayıt başarılı mesajı
        echo "<div style='text-align: center; margin-top: 50px;'>";
        echo "<h3>Kayıt başarılı!</h3>";
        echo "<a href='girisyap.html' style='text-decoration: none; color: blue;'>Giriş yap</a> sayfasına gidin.";
        echo "</div>";
    } catch (PDOException $e) {
        // Hata kontrolü: Aynı e-posta adresi mevcutsa
        if ($e->getCode() === '23000') { // UNIQUE constraint violation
            echo "Bu e-posta adresi zaten kayıtlı.";
        } else {
            echo "Bir hata oluştu: " . $e->getMessage();
        }
    }
} else {
    echo "Bu sayfaya doğrudan erişim yapılamaz.";
}
?>
                <!-- Ad ve Soyad -->
                <div class="mb-3">
                    <label for="firstName" class="form-label">Ad:</label>
                    <input type="text" id="firstName" name="firstName" class="form-control" placeholder="Adınızı girin" required>
                </div>
                <div class="mb-3">
                    <label for="lastName" class="form-label">Soyad:</label>
                    <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Soyadınızı girin" required>
                </div>
                <!-- E-posta -->
                <div class="mb-3">
                    <label for="email" class="form-label">E-posta:</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="E-posta adresinizi girin" required>
                </div>
                <!-- Şifre ve Şifre Tekrar -->
                <div class="mb-3">
                    <label for="password" class="form-label">Şifre:</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Şifrenizi oluşturun" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Şifre Tekrar:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Şifrenizi tekrar girin" required>
                </div>
                <!-- Kullanım Şartları -->
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label for="terms" class="form-check-label">
                        <a href="kullanim-sartlari.html" target="_blank">Kullanım şartlarını</a> kabul ediyorum.
                    </label>
                </div>
                <!-- Kayıt Ol Butonu -->
                <button type="submit" class="btn btn-success w-100">Kayıt Ol</button>
            </form>
            <p class="mt-4 text-center">Zaten bir hesabınız var mı? <a href="girisyap.html" class="text-decoration-none">Giriş Yapın</a></p>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                alert('Şifreler eşleşmiyor. Lütfen tekrar kontrol edin.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>

