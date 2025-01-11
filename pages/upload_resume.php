<?php
session_start();
require 'config.php'; // Veritabanı bağlantısı
require 'vendor/autoload.php'; // PHPMailer yükleyici

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Kullanıcı oturumda mı?
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Misafir';

// Oturum kontrolü
if (!$isLoggedIn) {
    header('Location: pages/login.php');
    exit;
}

// Kullanıcı bilgileri
$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini çek
$stmt = $db->prepare("SELECT email, phone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    $error_message = "Kullanıcı bilgileri bulunamadı.";
} else {
    $userEmail = $userData['email'];
    $userPhone = $userData['phone'];
}

// Desteklenen uzantılar ve mime tipleri
$allowedExtensions = ['jpeg', 'jpg', 'png', 'svg', 'gif', 'bmp', 'webp', 'pdf', 'doc', 'docx', 'rtf', 'txt', 'odt', 'ppt', 'pptx', 'odp', 'xls', 'xlsx', 'ods', 'csv', 'zip', 'rar', '7z', 'json', 'xml', 'html', 'htm', 'md'];
$allowedMimeTypes = [
    'image/jpeg', 'image/png', 'image/svg+xml', 'image/gif', 'image/bmp', 'image/webp',
    'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/rtf', 'text/plain', 'application/vnd.oasis.opendocument.text',
    'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'application/vnd.oasis.opendocument.presentation', 'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.oasis.opendocument.spreadsheet',
    'text/csv', 'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed',
    'application/json', 'application/xml', 'text/html', 'text/markdown'
];



// Dosya yükleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = 'resumes/'; // Kayıt dizini
    $file = $_FILES['resumeFile'];
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Hata kontrolü
    if (!in_array($fileExt, $allowedExtensions) || !in_array(mime_content_type($fileTmp), $allowedMimeTypes)) {
        $error_message = "Geçersiz dosya formatı veya mime tipi!";
    } elseif ($file['size'] > 10 * 1024 * 1024) {
        $error_message = "Dosya boyutu 10 MB'yi aşmamalıdır.";
    } else {
        $newFileName = uniqid($username . "_file_", true) . '.' . $fileExt;
        $targetPath = $uploadDir . $newFileName;

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
                $mail->Password = '
                '; // Değiştir
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom('samet.saray.06@gmail.com', 'Asistik');
                $mail->addAddress('samet.saray.06@gmail.com');

                $mail->isHTML(true);
                $mail->Subject = 'Yeni Özgeçmiş Yüklemesi';
                $mail->Body = "
                    <p><strong>Yükleyen Kullanıcı:</strong> {$username}</p>
                    <p><strong>E-posta:</strong> {$userEmail}</p>
                    <p><strong>Telefon:</strong> {$userPhone}</p>
                    <p>Özgeçmiş Dosyası ektedir.</p>
                ";
                $mail->addAttachment($targetPath);
                $mail->send();

                $success_message = "Özgeçmiş başarıyla yüklendi ve gönderildi.";
                echo '<script>
                        setTimeout(function() {
                            window.location.href = "' . $_SERVER['PHP_SELF'] . '";
                        }, 2000);
                      </script>';
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
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/asistik_logo.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .page-title {
            font-size: 2rem;
            margin-top: 4rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.5rem;
                /* Mobilde daha küçük başlık */
                margin-top: 6rem;
            }
        }
    </style>
</head>

<body>
    <div id="root">
        <aside class="sidebar collapsed">
            <button class="close-btn">✖</button>
            <div class="sidebar-header" onclick="redirectToHomePage()">
                <img src="../assets/images/asistik_logo.png" alt="Asistik Logo" class="logo">
                <h4 class="logo-text">ASİSTİK</h4>
            </div>

            <!-- Kullanıcı Durumuna Göre İçerik -->
            <?php if ($isLoggedIn): ?>
                <div class="sidebar-user">
                    <p class="user-name"><strong><?= htmlspecialchars($_SESSION['user_name']); ?></strong></p>
                </div>
            <?php else: ?>
                <div class="sidebar-user">
                    <a href="../pages/login.php" class="btn btn-info">Giriş Yap</a>
                </div>
            <?php endif; ?>

            <!--<div class="theme-buttons">
        <button id="dark-mode-btn" class="theme-toggle-btn">🌙 Dark Mod</button>
        <button id="high-contrast-btn" class="theme-toggle-btn">🔲 High Contrast</button>
      </div>-->

            <div class="nav-links">
                <?php if ($isLoggedIn && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'employer'): ?>
                    <li><a href="../add_jobs.php" data-tooltip="İş Ekle"><span>📋</span> İş Ekle</a></li>
                <?php endif; ?>
                <li><a href="../all_jobs.php" data-tooltip="Tüm İşler"><span>📄</span> Tüm İşler</a></li>
                <li><a href="#" data-section="İstatistik" id="istatistik-link" class="alert-section"><span>📊</span> İstatistik</a></li>
                <li><a href="../profile.php" data-tooltip="Profil"><span>👤</span> Profil</a></li>
            </div>

            <?php if ($isLoggedIn): ?>
                <div class="logout text-center mt-3">
                    <a href="../pages/logout.php" class="btn btn-danger">Çıkış Yap</a>
                </div>
            <?php endif; ?>

        </aside>
        <main class="dashboard">
            <nav class="navbar">
                <button id="toggle-btn" class="toggle-btn">
                    <img src="../assets/images/menu.png" alt="Menu" style="width: 24px; height: 24px;background-color:white !important;">
                </button>
            </nav>
            <main class="dashboard">
            <section class="container mt-5">
                <h1 class="text-center mb-4">Özgeçmiş Yükle</h1>
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success_message); ?></div>
                <?php elseif (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Özgeçmiş Yükleme Talimatları</h5>
                        <ul>
                            <li><strong>Desteklenen formatlar:</strong> <?= implode(', ', $allowedExtensions); ?></li>
                            <li><strong>Maksimum dosya boyutu:</strong> 10 MB</li>
                        </ul>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="resumeFile" class="form-label">Özgeçmiş Dosyası</label>
                                <input type="file" name="resumeFile" id="resumeFile" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" style="background-color: #17a2b8;">Özgeçmiş Yükle</button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
        </main>
    </div>
    <?php include '../include/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
    <script>
    function showAlert(event) {
      event.preventDefault();
      alert('Üzerinde çalışılıyor!');
    }
    document.querySelectorAll('.alert-section').forEach(function(element) {
      element.addEventListener('click', function(event) {
        event.preventDefault();
        const sectionName = this.getAttribute('data-section');
        alert(`${sectionName} kısmı üzerinde çalışmalarımız devam ediyor.`);
      });
    });
  </script>
</body>

</html>