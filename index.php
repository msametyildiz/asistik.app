<?php
require 'vendor/autoload.php';

use Samet\Asistik\Mailer;

session_start();
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
    <link rel="shortcut icon" type="image/x-icon" href="pic/asistik_logo.png">
    <style>
        .thumb img {
            transition: transform 0.3s ease;
        }

        .thumb img:hover {
            transform: scale(1.05);
        }

        .header-area {
            background-color: #f8f9fa;
            padding: 1rem 0;
        }

        .responsive-logo {
            max-width: 200px;
            height: auto;
        }

        .action img {
            max-width: 100px;
            height: auto;
            transition: transform 0.3s ease;
        }

        .action img:hover {
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="pic/asistik_logo.png" alt="Asistik Logo" height="30">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!--<li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Anasayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="employer_positions.php">İşveren</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="career.php">Kariyer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Hakkımızda</a>
                    </li>-->
                </ul>
                <div class="d-flex">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-info dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= htmlspecialchars($_SESSION['user_name']); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="logout.php">Çıkış Yap</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="girisyap.php" class="btn btn-info">Giriş Yap</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="text-center mb-5">
            <img src="pic/asistik_logo.png" alt="Asistik Logo" class="responsive-logo">
        </div>

        <div class="row gx-1 gy-4 custom-grid">
            <!-- Responsive grid items -->
            <?php
            $sections = [
                ['url' => 'redirect_check.php?target=employer_positions.php', 'img' => 'pic/isveren.png', 'alt' => 'İşveren'],
                ['url' => 'career.php', 'img' => 'pic/kariyer.png', 'alt' => 'Kariyer'],
                ['url' => '#', 'img' => 'pic/kocluk.png', 'alt' => 'Koçluk', 'data' => 'Koçluk'],
                ['url' => '#', 'img' => 'pic/analiz.png', 'alt' => 'Analiz', 'data' => 'Analiz'],
                ['url' => '#', 'img' => 'pic/egitim.png', 'alt' => 'Eğitim', 'data' => 'Eğitim'],
                ['url' => '#', 'img' => 'pic/verimlilik.png', 'alt' => 'Verimlilik', 'data' => 'Verimlilik']
            ];

            foreach ($sections as $section): ?>
                <div class="col-6 col-md-4 col-lg-custom d-flex justify-content-center">
                    <a href="<?= $section['url']; ?>"
                        class="thumb <?= isset($section['data']) ? 'alert-section' : ''; ?>"
                        data-section="<?= $section['data'] ?? ''; ?>">
                        <img src="<?= $section['img']; ?>" alt="<?= $section['alt']; ?>" class="img-fluid">
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-6 col-md-2 text-center">
                <a href="#" class="action alert-section" data-section="Canlı Görüşme">
                    <img src="pic/canli_gorusme.svg" alt="Canlı Görüşme">
                </a>
            </div>
            <div class="col-6 col-md-2 text-center">
                <a href="redirect_check.php?target=upload_resume.php" class="action">
                    <img src="pic/ozgecmis.png" alt="Özgeçmiş Yükle">
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.alert-section').forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                const sectionName = this.getAttribute('data-section');
                alert(`${sectionName} kısmı üzerinde çalışmalarımız devam ediyor.`);
            });
        });
    </script>
</body>

</html>