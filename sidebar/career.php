<?php
session_start();
require 'pages/config.php';

// Kullanıcı oturumda mı?
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Misafir';
// Oturum kontrolü
if (!$isLoggedIn) {
    header('Location: pages/girisyap.php');
    exit;
}

// Sayfa numarasını kontrol et
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12; // Her sayfada gösterilecek sektör sayısı
$offset = ($page - 1) * $limit; // Sektörlerin başlangıç noktası

// Toplam sektör sayısını çek
$totalStmt = $db->prepare("SELECT COUNT(*) AS total FROM sectors WHERE is_open = 1");
$totalStmt->execute();
$totalCount = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalCount / $limit);

// Sektörleri çek (limit ve offset ile)
$stmt = $db->prepare("SELECT id, sector_name, sector_description FROM sectors WHERE is_open = 1 LIMIT :limit OFFSET :offset");
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1; // Negatif veya 0 değerlerini önleyin

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$sectors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kariyer</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/asistik_logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <style>
        .sector-card {
            perspective: 1000px;
            height: 200px;
            /* Kartın boyutunu ayarlayın */
            border-radius: 8px;
            /* Kart kenarlarının yuvarlatılması */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Hafif gölge efekti */
            overflow: hidden;
            /* Taşmaları gizleyin */
            background-color: #fff;
            /* Kartın arka planını beyaz yapın */
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
            transition: transform 1.2s ease-in-out;
        }

        .card-front,
        .card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 10px;
        }

        .card-front {
            background-color: #17a2b8;
            color: white;
        }

        .card-back {
            background-color: #f8f9fa;
            transform: rotateY(180deg);
            border: 1px solid #ddd;
            padding: 20px;
        }

        .sector-card:hover .card-inner {
            transform: rotateY(180deg);
        }

        /* Grid düzeni ve kart boşlukları */
        .sector-list .row {
            gap: 20px;
            /* Kartlar arasında boşluk */
        }

        .col-md-4.col-lg-3 {
            padding-bottom: 20px;
        }

        /* Kartlar İçin Genel Stil */
        .card-title {
            font-size: 1.2rem;
            /* Varsayılan yazı boyutu */
            font-weight: bold;
        }

        .card-text {
            font-size: 1rem;
            /* Varsayılan yazı boyutu */
            line-height: 1.4;
            /* Daha okunabilir bir satır aralığı */
        }

        /* Küçük Ekranlar İçin Yazı Boyutları */
        @media (max-width: 768px) {
            .card-title {
                font-size: 1rem;
                /* Küçük ekranlarda başlık boyutu */
            }

            .card-text {
                font-size: 0.9rem;
                /* Küçük ekranlarda metin boyutu */
            }
        }

        /* Çok Küçük Ekranlar İçin Yazı Boyutları */
        @media (max-width: 576px) {
            .card-title {
                font-size: 0.9rem;
                /* Mobil cihazlarda başlık boyutu */
            }

            .card-text {
                font-size: 0.8rem;
                /* Mobil cihazlarda metin boyutu */
            }
        }

        /* Responsive Tasarım */
        @media (max-width: 768px) {
            .sector-card {
                height: 200px;
                /* Küçük ekranlarda kart yüksekliğini azalt */
            }

            .card-front,
            .card-back {
                font-size: 0.9rem;
                /* Yazı boyutunu küçült */
            }
        }

        @media (max-width: 576px) {
            .sector-card {
                height: 180px;
                /* Mobil ekranlarda daha küçük kartlar */
            }

            .card-front,
            .card-back {
                font-size: 0.8rem;
                padding: 5px;
            }
        }

        .pagination .page-item.active .page-link {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }

        .pagination .page-link {
            color: #17a2b8;
            text-decoration: none;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination .page-item.active .page-link {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }

        .pagination .page-link {
            color: #17a2b8;
            text-decoration: none;
        }

        .pagination .page-link:hover {
            background-color: #17a2b8;
            color: white;
        }

        .career-opportunities {
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div id="root">
        <?php include 'include/sidebar.php'; ?>
        <main class="dashboard">
            <nav class="navbar">
                <button id="toggle-btn" class="toggle-btn" style="padding-left: 15px;">
                    <img src="assets/images/menu.png" alt="Menu" style="width: 24px; height: 24px;background-color:white !important;">
                </button>
            </nav>
            <section class="career-opportunities">
                <div class="container">
                    <header class="section-header text-center">
                        <h1>Sektörler ve Fırsatlar</h1>
                        <p>Kariyer yolculuğunuzda başarıya ulaşmak için aşağıdaki sektörleri inceleyin ve başvurun.</p>
                    </header>

                    <div class="sector-list" style="padding-top: 4%;">
                        <div class="row">
                            <?php foreach ($sectors as $sector): ?>
                                <!-- Büyük ve orta ekranlarda 3 kart, küçük ekranlarda 2 kart -->
                                <div class="col-4 col-md-3 mb-4">
                                    <div class="card sector-card">
                                        <div class="card-inner">
                                            <!-- Kartın Ön Yüzü -->
                                            <div class="card-front">
                                                <h5 class="card-title"><?= htmlspecialchars($sector['sector_name']); ?></h5>
                                            </div>

                                            <!-- Kartın Arka Yüzü -->
                                            <div class="card-back">
                                                <p class="card-text"><?= htmlspecialchars($sector['sector_description']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>


                    <nav class="pagination-nav">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>">Önceki</a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">Sonraki</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </section>
        </main>
    </div>
    <?php include 'include/footer.php'; ?>
    <script src="assets/js/script.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const cards = document.querySelectorAll(".sector-card");

            cards.forEach(card => {
                const cardInner = card.querySelector(".card-inner");

                card.addEventListener("mouseenter", () => {
                    cardInner.style.transform = "rotateY(180deg)";
                });

                card.addEventListener("mouseleave", () => {
                    cardInner.style.transform = "rotateY(0deg)";
                });
            });
        });
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