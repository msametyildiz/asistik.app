<?php
session_start();
require 'config.php';

// Oturum kontrolü
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: girisyap.php');
    exit;
}

$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id']; // Kullanıcının ID'si

// İş ilanlarını veritabanından çek
$stmt = $db->prepare("
    SELECT positions.id, positions.position_name, sectors.sector_name, positions.is_open, positions.employer_id, positions.sector_id 
    FROM positions 
    JOIN sectors ON positions.sector_id = sectors.id 
    ORDER BY positions.created_at DESC
");
$stmt->execute();
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// İş ilanı ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add' && $user_role === 'employer') {
    $sector_id = $_POST['sector_id'] ?? null;
    $position_name = trim($_POST['position_name'] ?? '');

    if (!empty($sector_id) && !empty($position_name)) {
        $insertStmt = $db->prepare("
            INSERT INTO positions (employer_id, sector_id, position_name, is_open, created_at, updated_at) 
            VALUES (?, ?, ?, 1, NOW(), NOW())
        ");
        $insertStmt->execute([$user_id, $sector_id, $position_name]);
        header('Location: employer_positions.php');
        exit;
    } else {
        $error_message = 'Lütfen tüm alanları doldurunuz!';
    }
}

// İş ilanı silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && $user_role === 'employer') {
    $position_id = $_POST['position_id'] ?? null;

    if (!empty($position_id)) {
        $deleteStmt = $db->prepare("DELETE FROM positions WHERE id = ? AND employer_id = ?");
        $deleteStmt->execute([$position_id, $user_id]);
        header('Location: employer_positions.php');
        exit;
    }
}

// İş ilanı güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update' && $user_role === 'employer') {
    $position_id = $_POST['position_id'] ?? null;
    $position_name = trim($_POST['position_name'] ?? '');
    $sector_id = $_POST['sector_id'] ?? null;

    if (!empty($position_id) && !empty($position_name) && !empty($sector_id)) {
        $updateStmt = $db->prepare("
            UPDATE positions 
            SET position_name = ?, sector_id = ?, updated_at = NOW() 
            WHERE id = ? AND employer_id = ?
        ");
        $updateStmt->execute([$position_name, $sector_id, $position_id, $user_id]);
        header('Location: employer_positions.php');
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
    <title>İşveren</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="pic/asistik_logo.png">

    <style>
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }

        .card-employer {
            background-color: #f8d7da; /* Kullanıcının kendi ilanları için farklı renk */
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
                        <input type="hidden" name="action" value="add">
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
                            <div class="card mb-3 <?= $position['employer_id'] == $user_id ? 'card-employer' : '' ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($position['position_name']); ?></h5>
                                    <p class="card-text"><strong>Sektör:</strong> <?= htmlspecialchars($position['sector_name']); ?></p>
                                    <p class="card-text"><strong>Durum:</strong> <?= $position['is_open'] ? 'Açık' : 'Kapalı'; ?></p>
                                    <?php if ($position['employer_id'] == $user_id && $user_role === 'employer'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="position_id" value="<?= $position['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                                        </form>
                                        <button class="btn btn-warning btn-sm" onclick="editPosition(<?= $position['id'] ?>, '<?= htmlspecialchars($position['position_name']) ?>', <?= $position['sector_id'] ?>)">Düzenle</button>
                                    <?php endif; ?>
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

    <!-- Düzenleme Modalı -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="position_id" id="edit_position_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">İlanı Düzenle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_position_name" class="form-label">Pozisyon Adı</label>
                            <input type="text" name="position_name" id="edit_position_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_sector_id" class="form-label">Sektör</label>
                            <select name="sector_id" id="edit_sector_id" class="form-control" required>
                                <?php foreach ($sectors as $sector): ?>
                                    <option value="<?= $sector['id'] ?>"><?= htmlspecialchars($sector['sector_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editPosition(id, name, sectorId) {
            document.getElementById('edit_position_id').value = id;
            document.getElementById('edit_position_name').value = name;
            document.getElementById('edit_sector_id').value = sectorId;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</body>
</html>
