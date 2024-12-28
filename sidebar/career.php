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
$stmt = $db->prepare("SELECT id, sector_name FROM sectors WHERE is_open = 1 LIMIT :limit OFFSET :offset");
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
    <link rel="stylesheet" href="assets/css/all_job_style.css">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/asistik_logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .pagination {
            margin-top: 20px;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .pagination .page-link {
            color: #007bff;
            text-decoration: none;
        }

        .pagination .page-link:hover {
            background-color: #0056b3;
            color: white;
        }
    </style>
</head>

<body>
    <div id="root">
        <?php include 'include/sidebar.php'; ?>
        <main class="dashboard">
            <?php include 'include/navbar.php'; ?>

            <section class="page-header">
                <h1>Sektörler ve Fırsatlar</h1>
                <p>Kariyer yolculuğunuzda başarıya ulaşmak için aşağıdaki sektörleri inceleyin ve başvurun.</p>
            </section>

            <section class="sector-list">
                <div class="container">
                    <div class="row">
                        <?php foreach ($sectors as $sector): ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="card sector-card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title text-center"><?= htmlspecialchars($sector['sector_name']); ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">Bu sektör hakkında detaylı bilgi edinerek başvurunuzu tamamlayabilirsiniz.</p>
                                        <a href="apply.php?sector_id=<?= $sector['id'] ?>" class="btn btn-outline-primary d-block">Başvur</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Pagination -->
                    <nav>
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
</body>



</html>