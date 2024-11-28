<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $companyName = trim($_POST['companyName']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    $errors = [];
    if (empty($firstName)) $errors[] = 'Ad alanı boş olamaz.';
    if (empty($lastName)) $errors[] = 'Soyad alanı boş olamaz.';
    if (empty($companyName)) $errors[] = 'Şirket adı boş olamaz.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Geçerli bir e-posta adresi girin.';
    if (empty($phone) || !preg_match('/^[0-9]{10,15}$/', $phone)) $errors[] = 'Telefon numarası 10-15 rakamdan oluşmalıdır.';
    if (empty($password)) $errors[] = 'Şifre alanı boş olamaz.';
    if ($password !== $confirmPassword) $errors[] = 'Şifreler eşleşmiyor.';

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = $db->prepare("INSERT INTO users (first_name, last_name, company_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?, 'employer')");
            $stmt->execute([$firstName, $lastName, $companyName, $email, $phone, $hashedPassword]);

            // Başarılı işlem mesajı ve yönlendirme
            $successMessage = "İşleminiz başarılı bir şekilde gerçekleştirilmiştir. Giriş sayfasına yönlendiriliyorsunuz...";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'girisyap.html';
                }, 2000);
            </script>";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'Bu e-posta adresi zaten kayıtlı.';
            } else {
                $errors[] = 'Bir hata oluştu: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İşveren Kayıt</title>
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
        .card-container {
            margin-top: 6%; /* Kartın yukarıdan boşluk bırakmasını sağlar */
            margin-bottom: 6%;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center card-container" style="height: 100vh;">
        <div class="card p-4 shadow-lg" style="width: 100%; max-width: 400px;">
            <h2 class="text-center mb-4">İşveren Kayıt</h2>

            <!-- Başarılı işlem mesajı -->
            <?php if (isset($successMessage)): ?>
                <div class="alert alert-success text-center">
                    <?= $successMessage; ?>
                </div>
            <?php endif; ?>

            <!-- Hata mesajları -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?= implode('<br>', $errors); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <!-- Ad ve Soyad -->
                <div class="mb-3">
                    <label for="firstName" class="form-label">Ad:</label>
                    <input type="text" id="firstName" name="firstName" class="form-control" placeholder="Adınızı girin" required>
                </div>
                <div class="mb-3">
                    <label for="lastName" class="form-label">Soyad:</label>
                    <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Soyadınızı girin" required>
                </div>
                <!-- Şirket Adı -->
                <div class="mb-3">
                    <label for="companyName" class="form-label">Şirket Adı:</label>
                    <input type="text" id="companyName" name="companyName" class="form-control" placeholder="Şirket adınızı girin" required>
                </div>
                <!-- E-posta -->
                <div class="mb-3">
                    <label for="email" class="form-label">E-posta:</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="E-posta adresinizi girin" required>
                </div>
                <!-- Telefon -->
                <div class="mb-3">
                    <label for="phone" class="form-label">Telefon:</label>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="Telefon numaranızı girin" pattern="[0-9]{10,15}" required>
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
                    <input type="checkbox" id="terms" class="form-check-input" required>
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
</body>
</html>
