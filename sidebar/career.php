<?php
ob_start();
session_start();
require 'pages/config.php';

// Kullanıcı oturum kontrolü
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Misafir';

// Oturum kontrolü
if (!$isLoggedIn) {
    header('Location: pages/girisyap.php');
    exit;
}

// Kullanıcı bilgileri
$user_role = $_SESSION['user_role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// Sektör bilgilerini çek
$sectorStmt = $db->prepare("SELECT id, sector_name FROM sectors WHERE is_open = 1");
$sectorStmt->execute();
$sectors = $sectorStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kariyer Sektörleri</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/asistik_logo.png">
    <style>
        .sectors-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .sector-card {
            background: #f8f9fa;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
        }

        .sector-card h5 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .alert {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
        }

        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
    </style>
</head>

<body>
    <div id="root">
        <?php include 'include/sidebar.php'; ?>
        <main class="dashboard">
            <?php include 'include/navbar.php'; ?>
            <section class="dashboard-content">
                <div class="section-header">
                    <h4>Kariyer Sektörleri</h4>
                </div>

                <?php if (count($sectors) > 0): ?>
                    <div class="sectors-container">
                        <?php foreach ($sectors as $sector): ?>
                            <div class="sector-card">
                                <h5><?= htmlspecialchars($sector['sector_name']); ?></h5>
                                <p>ID: <?= htmlspecialchars($sector['id']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Henüz sektör eklenmemiş.
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
    <?php include 'include/footer.php'; ?>
    <script src="assets/js/script.js"></script>
    <script>function showAlert(event) {
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