<?php
session_start();
require 'config.php';

// Oturum kontrolü
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: girisyap.html');
    exit;
}

$user_role = $_SESSION['user_role'];

// İş ilanlarını veritabanından çek
$stmt = $db->prepare("
    SELECT positions.id, positions.position_name, sectors.sector_name, positions.is_open 
    FROM positions 
    JOIN sectors ON positions.sector_id = sectors.id 
    WHERE positions.is_open = 1
    ORDER BY positions.created_at DESC
");
$stmt->execute();
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Employer için yeni iş ilanı ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_role === 'employer') {
    $sector_id = $_POST['sector_id'] ?? null;
    $position_name = trim($_POST['position_name'] ?? '');

    if (!empty($sector_id) && !empty($position_name)) {
        $insertStmt = $db->prepare("
            INSERT INTO positions (employer_id, sector_id, position_name, is_open, created_at, updated_at) 
            VALUES (?, ?, ?, 1, NOW(), NOW())
        ");
        $insertStmt->execute([$_SESSION['user_id'], $sector_id, $position_name]);
        header('Location: employer_positions.php'); // Sayfayı yeniden yükle
        exit;
    } else {
        $error_message = 'Lütfen tüm alanları doldurunuz!';
    }
}

// Sektörleri çek
$sectorStmt = $db->prepare("SELECT id, sector_name FROM sectors WHERE is_open = 1");
$sectorStmt->execute();
$sectors = $sectorStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İş İlanları</title>
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
    <!-- Geri Dön Butonu -->
    <a href="index.php" class="btn btn-secondary back-button">Ana Sayfa</a>
    <div class="container mt-5">
        <h1 class="text-center mb-4">İş İlanları</h1>

        <!-- İşveren için İş İlanı Ekleme Formu -->
        <?php if ($user_role === 'employer'): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Yeni İş İlanı Oluştur</h5>
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="sector_id" class="form-label">Sektör</label>
                            <select name="sector_id" id="sector_id" class="form-control" required>
                                <option value="" disabled selected>Sektör Seçin</option>
                                <?php foreach ($sectors as $sector): ?>
                                    <option value="<?= $sector['id'] ?>"><?= htmlspecialchars($sector['sector_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="position_name" class="form-label">Pozisyon Adı</label>
                            <input type="text" name="position_name" id="position_name" class="form-control" placeholder="Pozisyon adı girin" required>
                        </div>
                        <button type="submit" class="btn btn-primary">İlan Oluştur</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- İş İlanları Listesi -->
        <div>
            <h2 class="mb-3">Mevcut İş İlanları</h2>
            <?php if (count($positions) > 0): ?>
                <div class="row">
                    <?php foreach ($positions as $position): ?>
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($position['position_name']); ?></h5>
                                    <p class="card-text"><strong>Sektör:</strong> <?= htmlspecialchars($position['sector_name']); ?></p>
                                    <p class="card-text"><strong>Durum:</strong> <?= $position['is_open'] ? 'Açık' : 'Kapalı'; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">Henüz iş ilanı oluşturulmamış.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
