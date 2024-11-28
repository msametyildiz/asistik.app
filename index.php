<?php

require 'vendor/autoload.php';

use Samet\Asistik\Mailer;

session_start(); // Oturum başlat
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASİSTİK</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="ASİST LOGO-Photo.png">

</head>
<body>
    <!-- Header -->
    <div class="header-area">
        <div class="top-right-button text-end" style="padding-top: 1%; padding-right:1%;">
            <?php if (isset($_SESSION['user_name'])): ?>
                <span class="me-3"> <strong><?= htmlspecialchars($_SESSION['user_name']); ?></strong></span>
                <a href="logout.php" class="btn btn-danger">Çıkış Yap</a>
            <?php else: ?>
                <a href="girisyap.html" class="btn" style="background-color: #80d0d7;">Giriş Yap</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Explorer Section -->
    <div class="explorer_europe">
        <div class="container">
            <!-- Başlık Bölümü -->
            <div class="row">
                <div class="col-xl-12 text-center">
                    <div class="logo mb-4">
                        <img src="ASİST LOGO-Photo.png" alt="Asistik Logo" class="responsive-logo" style="padding-bottom: 2%;">
                    </div>
                </div>
            </div>
            
            <!-- İçerik Bölümü -->
            <div class="row justify-content-center" style="height: auto;">
                <!-- İşveren -->
                <div class="col-lg-4 col-md-6 col-6 d-flex flex-column align-items-end mb-4">
                    <div class="thumb">
                        <a href="redirect_check.php?target=employer_positions.php">
                            <img src="isveren.png" alt="İşveren" class="img-fluid">
                        </a>
                    </div>
                </div>
                <!-- Kariyer -->
                <div class="col-lg-3 col-md-6 col-6 d-flex flex-column align-items-center mb-4 narrow-column">
                    <div class="thumb">
                        <a href="career.php">
                            <img src="kariyer.png" alt="Kariyer" class="img-fluid">
                        </a>
                    </div>
                </div>
                <!-- Koçluk -->
                <div class="col-lg-4 col-md-6 col-6 d-flex flex-column align-items-start mb-4">
                    <a href="#">
                        <div class="thumb">
                            <img src="kocluk.png" alt="Koçluk" class="img-fluid alert-section" data-section="Koçluk">
                        </div>
                    </a>
                </div>
                <!-- Analiz -->
                <div class="col-lg-4 col-md-6 col-6 d-flex flex-column align-items-end mb-4">
                    <a href="#">
                        <div class="thumb">
                            <img src="analiz.png" alt="Analiz" class="img-fluid alert-section" data-section="Analiz">
                        </div>
                    </a>
                </div>
                <!-- Eğitim -->
                <div class="col-lg-3 col-md-6 col-6 d-flex flex-column align-items-center mb-4 narrow-column">
                    <a href="#">
                        <div class="thumb">
                            <img src="egitim.png" alt="Eğitim" class="img-fluid alert-section" data-section="Eğitim">
                        </div>
                    </a>
                </div>
                <!-- Verimlilik -->
                <div class="col-lg-4 col-md-6 col-6 d-flex flex-column align-items-start mb-4">
                    <a href="#">
                        <div class="thumb">
                            <img src="verimlilik.png" alt="Verimlilik" class="img-fluid alert-section" data-section="Verimlilik">
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Canlı Görüşme ve Özgeçmiş -->
            <div class="row align-items-center justify-content-center mt-4">
                <div class="col-md-2 col-6 text-center">
                    <a href="#">
                        <div class="action">
                            <img src="CANLI GÖRÜŞME1.png" alt="Canlı Görüşme" class="action-icon img-fluid alert-section" data-section="Canlı Görüşme">
                        </div>
                    </a>
                </div>
                <div class="col-md-2 col-6 text-center">
                    <a href="redirect_check.php?target=upload_resume.php">
                        <div class="action">
                            <img src="ÖZGEÇMİŞ1.png" alt="Özgeçmiş Yükle" class="action-icon img-fluid">
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Alert için Event Listener
        document.querySelectorAll('.alert-section').forEach(function (element) {
            element.addEventListener('click', function () {
                const sectionName = this.getAttribute('data-section');
                alert(`${sectionName} kısmı üzerinde çalışmalarımız devam ediyor.`);
            });
        });
    </script>
</body>
</html>
