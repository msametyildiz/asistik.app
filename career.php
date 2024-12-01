<?php
require 'config.php'; // Veritabanı bağlantısı

// Sektörleri çek
$stmt = $db->prepare("SELECT id, sector_name FROM sectors WHERE is_open = 1");
$stmt->execute();
$sectors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kariyer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="pic/ASİST LOGO-Photo.png">

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
    <!-- Geri Dön Butonu -->
    <a href="index.php" class="btn btn-secondary back-button">Ana Sayfa</a>

    <div class="container mt-5">
        <!-- Başlık -->
        <h1 class="page-title">Başvuruya Açık Sektörler</h1>

        <!-- Sektör Listesi -->
        <ul class="list-group" style="padding-bottom: 5%;">
            <?php foreach ($sectors as $sector): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($sector['sector_name']) ?>
                    <!--<a href="apply.php?sector_id=<?= $sector['id'] ?>" class="btn btn-primary btn-sm">Başvur</a>-->
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
