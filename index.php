<?php
require 'vendor/autoload.php';

use Samet\Asistik\Mailer;

session_start();
$sidebarOpen = isset($_SESSION['user_name']); // Sidebar durumu kullanıcı girişine göre belirleniyor.
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
        .sidebar {
            position: fixed;
            width: 250px;
            height: 100vh;
            background-color: #f1f1f1;
            overflow-x: hidden;
            transition: left 0.7s ease-in-out;

            z-index: 100;
            left: -250px;
            /* Kapalı halde solda gizlenir */
        }

        .sidebar.open {
            left: 0;
            /* Açık durumda görünür */
        }

        .sidebar a {
            display: block;
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: #333;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #ddd;
            color: #000;
        }

        .main-content {
            transition: 0.3s ease margin-left;
            text-align: center;
            margin: 0 auto;
            /* Varsayılan olarak ortalanır */
        }

        .header-area {
            background-color: #f8f9fa;
            padding: 1rem 0;
            transition: 0.3s ease margin-left;
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                left: -250px;
                /* Küçük ekranlarda varsayılan olarak gizli */
            }

            .sidebar.open {
                left: 0;
            }

            .main-content,
            .header-area {
                margin-left: 0;
            }
        }

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

        .sidebar-user {
            padding: 10px 15px;
            background-color: #f8f9fa;
            /* Sidebar'ın üst kısmına hafif gri arka plan */
            text-align: center;
            /* Metni ortalar */
            border-bottom: 1px solid #ddd;
            /* Ayrım çizgisi */
        }

        .sidebar-user p {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .sidebar-user .btn {
            display: block;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
        }

        .custom-grid {
            margin: 0 auto;
            /* Ortalamak için */
            max-width: 1200px;
            /* İsteğe bağlı olarak grid genişliğini sınırla */
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div id="mySidebar" class="sidebar <?= $sidebarOpen ? 'open' : ''; ?>">
        <!-- Kullanıcı Durumuna Göre İçerik -->
        <div class="sidebar-user">
            <?php if ($sidebarOpen): ?>
                <p class="text-center mt-3"><strong><?= htmlspecialchars($_SESSION['user_name']); ?></strong></p>
            <?php else: ?>
                <a href="girisyap.php" class="btn btn-info w-100 mt-3 text-center">Giriş Yap</a>
            <?php endif; ?>
        </div>
        <hr> <!-- Ayrım çizgisi -->

        <!-- Sidebar Menü -->
        <a href="index.php"><i class="fas fa-home"></i> Anasayfa</a>
        <a href="employer_positions.php"><i class="fas fa-briefcase"></i> İşveren</a>
        <a href="career.php"><i class="fas fa-chart-line"></i> Kariyer</a>
        <!--<a href="#"><i class="fas fa-info-circle"></i> Hakkımızda</a>-->
        <?php if ($sidebarOpen): ?>
            <!-- Kullanıcı Girişi Yapıldıysa "Çıkış Yap" Butonu -->
            <a href="logout.php" class="btn btn-danger w-100 mt-3 text-center">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
            </a>
        <?php endif; ?>

    </div>


    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light header-area" <?= $sidebarOpen ? 'style="margin-left: 250px;"' : ''; ?>>
        <button class="btn" id="sidebarToggle">&#9776;</button>
        <!-- <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="d-flex">
                    <?php if ($sidebarOpen): ?>
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
        </div>-->
    </nav>

    <!-- Main Content -->
    <div class="main-content container my-5" <?= $sidebarOpen ? 'style="margin-left: 250px;"' : ''; ?>>
        <div class="text-center mb-5">
            <img src="pic/asist_logo.svg" alt="Asistik Logo" class="responsive-logo">
        </div>
        <div id="responsiveDiv" class="row gx-1 gy-4 custom-grid d-flex justify-content-center col-6">

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
        <div class="row justify-content-center mt-4" style="padding-top: 3%;">
            <div class="col-6 col-md-2 text-center">
                <a href="#" class="action alert-section" data-section="Canlı Görüşme">
                    <img src="pic/canli_gorusme.svg" alt="Canlı Görüşme">
                </a>
            </div>
            <div class="col-6 col-md-2 text-center">
                <a href="redirect_check.php?target=upload_resume.php" class="action">
                    <img src="pic/ozgecmis.svg" alt="Özgeçmiş Yükle">
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.alert-section').forEach(function(element) {
            element.addEventListener('click', function(event) {
                event.preventDefault();
                const sectionName = this.getAttribute('data-section');
                alert(`${sectionName} kısmı üzerinde çalışmalarımız devam ediyor.`);
            });
        });
        const sidebar = document.getElementById("mySidebar");
        const mainContent = document.querySelector(".main-content");
        const headerArea = document.querySelector(".header-area");
        const toggleButton = document.getElementById("sidebarToggle");

        toggleButton.addEventListener("click", () => {
            sidebar.classList.toggle("open");

            if (sidebar.classList.contains("open")) {
                mainContent.style.marginLeft = "250px";
                headerArea.style.marginLeft = "250px";
            } else {
                mainContent.style.marginLeft = "0";
                headerArea.style.marginLeft = "0";
                mainContent.style.textAlign = "center"; // Ortalamayı tekrar uygular
                mainContent.style.margin = "0 auto"; // Varsayılan margin'i tekrar uygular
            }
        });

        function updateClassBasedOnViewport() {
            const div = document.getElementById("responsiveDiv");
            const width = window.innerWidth;
            const height = window.innerHeight;

            if (width > 992 && height > 616) {
                div.className = "row gx-1 gy-4 custom-grid d-flex justify-content-center col-6";
            } else {
                div.className = "row gx-1 gy-4 custom-grid";
            }
        }

        // Sayfa yüklendiğinde kontrol et
        window.addEventListener("load", updateClassBasedOnViewport);

        // Pencere yeniden boyutlandırıldığında kontrol et
        window.addEventListener("resize", updateClassBasedOnViewport);
    </script>
</body>

</html>