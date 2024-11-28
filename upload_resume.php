<?php
require 'config.php'; // Veritabanı bağlantısı
require 'vendor/autoload.php'; // PHPMailer yükleyici

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// Giriş kontrolü
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: girisyap.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Dosya yükleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = 'resumes/'; // Kayıt dizini
    $file = $_FILES['resumeFile'];
    $allowedExtensions = ['pdf', 'doc', 'docx', 'png', 'jpg'];

    // Dosya bilgileri
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Hata kontrolü
    if (!in_array($fileExt, $allowedExtensions)) {
        $error_message = "Geçersiz dosya formatı! Sadece PDF, DOC, DOCX, PNG ve JPG formatları kabul edilir.";
    } else {
        // Dosya adı
        $newFileName = $user_name . '_cv.' . $fileExt;
        $targetPath = $uploadDir . $newFileName;

        // Dosya yükle
        if (move_uploaded_file($fileTmp, $targetPath)) {
            // Veritabanına kaydet
            $stmt = $db->prepare("INSERT INTO resumes (user_id, file_path) VALUES (?, ?)");
            $stmt->execute([$user_id, $targetPath]);

            // Mail gönder
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'samet.saray.06@gmail.com'; // Değiştir
                $mail->Password = 'sazjvbfajhwnketb'; // Değiştir
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('samet.saray.06@gmail.com', 'Asistik'); // Değiştir
                $mail->addAddress('samet.saray.06@gmail.com'); // Kendi e-posta adresiniz


                $mail->isHTML(true);
                $mail->Subject = 'Yeni Özgeçmiş Yüklemesi';
                $mail->Body = "
                    <p><strong>Yükleyen Kullanıcı:</strong> {$user_name}</p>
                    <p>Özgeçmiş Dosyası ektedir.</p>
                ";

                $mail->addAttachment($targetPath);
                $mail->send();

                $success_message = "Özgeçmiş başarıyla yüklendi ve gönderildi.";
            } catch (Exception $e) {
                $error_message = "Mail gönderimi başarısız: {$mail->ErrorInfo}";
            }
        } else {
            $error_message = "Dosya yükleme başarısız oldu!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Özgeçmiş Yükle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }

        .page-title {
            font-size: 2rem;
            margin-top: 4rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.5rem; /* Mobilde daha küçük başlık */
                margin-top: 6rem; /* Geri dön butonu ile çakışmayı önler */
            }

            .back-button {
                top: 10px; /* Butonun mobilde yukarı kayması */
                left: 10px; /* Mobilde biraz daha dar alana oturması */
            }
        }
    </style>
</head>
<body>
    <a href="index.php" class="btn btn-secondary back-button">Ana Sayfa</a>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Özgeçmiş Yükle</h1>

        <!-- Başarı veya hata mesajları -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message); ?></div>
        <?php elseif (!empty($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Form -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Özgeçmiş Yükleme Talimatları</h5>
                <p>Yükleyeceğiniz özgeçmiş şu formatlarda olmalıdır:</p>
                <ul>
                    <li><strong>Dosya formatı:</strong> PDF, DOC, DOCX, PNG, JPG</li>
                    <li><strong>İçerik:</strong> İsim, Soyisim, İletişim Bilgileri, Eğitim, İş Deneyimi</li>
                </ul>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="resumeFile" class="form-label">Özgeçmiş Dosyası</label>
                        <input type="file" name="resumeFile" id="resumeFile" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Özgeçmiş Yükle</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
