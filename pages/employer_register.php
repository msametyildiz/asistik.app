<?php
require 'config.php';
require 'vendor/autoload.php'; // PHPMailer yükleyici

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
            $insertStmt = $db->prepare("INSERT INTO users (first_name, last_name, company_name, email, phone, password, role, is_pending) VALUES (?, ?, ?, ?, ?, ?, 'employer', 1)");
            $insertStmt->execute([$firstName, $lastName, $companyName, $email, $phone, $hashedPassword]);

            // Mail Gönderimi
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'info.asistik@gmail.com'; // Değiştir
                $mail->Password = 'mody xldl biyv hfmg'; // Değiştir
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom('info.asistik@gmail.com', 'Asistik');
                $mail->addAddress($email); // İşverenin e-posta adresi

                $mail->isHTML(true);
                $mail->Subject = 'Kayıt Başvurunuz Alındı';
                $mail->Body = "
                    <p>Merhaba <strong>{$firstName} {$lastName}</strong>,</p>
                    <p>Kayıt başvurunuz alınmıştır. Şirketinizin kaydı, yetkililerimiz tarafından incelenmektedir.</p>
                    <p>Onay süreci tamamlandıktan sonra size bilgi verilecektir.</p>
                    <p>Teşekkür ederiz,</p>
                    <p><strong>ASİSTİK Ekibi</strong></p>
                ";

                $mail->send();
                $successMessage = "Kayıt işleminiz başarılıdır. E-posta adresinize bilgi gönderilmiştir.";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
                </script>";
            } catch (Exception $e) {
                $errors[] = "E-posta gönderiminde bir hata oluştu: " . $mail->ErrorInfo;
            }
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
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/asistik_logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .form-check-label a {
            color: #007bff;
            text-decoration: none;
        }

        .form-check-label a:hover {
            text-decoration: underline;
        }

        .card-container {
            margin-top: 6%;
            /* Kartın yukarıdan boşluk bırakmasını sağlar */
    
        }

        .register-option {
            text-align: center;
            margin-top: 100px;
        }

        .option {
            display: inline-block;
            margin: 30px;
            padding: 30px;
            border: 2px solid #ccc;
            border-radius: 15px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 200px;
            height: 250px;
        }

        .option img {
            width: 120px;
            height: 120px;
            margin-bottom: 10px;
        }

        .option p {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .option:hover {
            transform: scale(1.15);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .page-title {
            font-size: 2.5rem;
            margin-top: 4rem;
            text-align: center;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #17a2b8;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(23, 162, 184, 0.3);
            z-index: 1000;
        }

        .back-button:hover {
            background-color: #138c9c;
            box-shadow: 0 6px 15px rgba(23, 162, 184, 0.5);
            transform: translateY(-2px);
        }

        .back-button i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.8rem;
                margin-top: 3rem;
            }

            .option {
                width: 150px;
                height: 200px;
                padding: 20px;
            }

            .option img {
                width: 100px;
                height: 100px;
            }

            .option p {
                font-size: 1rem;
            }

            .back-button {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
        @media (max-width: 580) {
    .card{
        padding-top: 10%;
    }
}
    </style>
</head>

<body>
    <!-- Geri Butonu -->
    <a href="register.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Geri
    </a>
    <div class="container d-flex justify-content-center align-items-center card-container" >
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
                        <a href="kullanim-sartlari.php" target="_blank" style="color: #17a2b8;" >Kullanım şartlarını</a> kabul ediyorum.
                    </label>
                </div>
                <!-- Kayıt Ol Butonu -->
                <button type="submit" class="btn btn-success w-100" style="background-color: #17a2b8;">Kayıt Ol</button>
            </form>
            <p class="mt-4 text-center">Zaten bir hesabınız var mı? <a href="login.php" class="text-decoration-none" style="color: #17a2b8;">Giriş Yapın</a></p>
        </div>
    </div>
    <?php include '../include/footer.php'; ?>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>