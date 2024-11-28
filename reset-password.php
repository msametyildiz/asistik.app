<?php
// Veritabanı bağlantısını sağlayın
include 'config.php';

// Gelen token'ı kontrol edin
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = htmlspecialchars($_GET['token']); // Token'ı al ve güvenli hale getir
} else {
    echo "<script>
        alert('Bu sıfırlama bağlantısı geçersiz.');
        window.location.href = 'forgot-password.php';
    </script>";
    exit;
}

try {
    // Token'ı veritabanında kontrol et
    $stmt = $db->prepare("SELECT email, created_at FROM password_resets WHERE token = ?");
    $stmt->execute([$token]);
    $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resetRequest) {
        echo "<script>
            alert('Bu sıfırlama bağlantısı geçersiz.');
            window.location.href = 'forgot-password.php';
        </script>";
        exit;
    }

    // Token'ın süresini kontrol et (örnek: 1 saat)
    $createdAt = new DateTime($resetRequest['created_at']);
    $now = new DateTime();
    $interval = $now->diff($createdAt);

    if ($interval->h >= 1 || $interval->days > 0) { // 1 saatten eski veya bir gün önceki token'ları geçersiz kıl
        echo "<script>
            alert('Bu sıfırlama bağlantısının süresi dolmuş.');
            window.location.href = 'forgot-password.php';
        </script>";
        exit;
    }

    // Token geçerli, e-posta adresini al
    $email = $resetRequest['email'];
} catch (PDOException $e) {
    echo "<script>
        alert('Bir hata oluştu: " . htmlspecialchars($e->getMessage()) . "');
        window.location.href = 'index.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4" style="width: 100%; max-width: 400px;">
            <h2 class="text-center mb-4">Yeni Şifre Belirleyin</h2>
            <form action="update-password.php" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
                <div class="mb-3">
                    <label for="newPassword" class="form-label">Yeni Şifre:</label>
                    <input type="password" id="newPassword" name="newPassword" class="form-control" minlength="6" placeholder="Yeni şifrenizi girin" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Yeni Şifre Tekrar:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Şifrenizi tekrar girin" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Şifreyi Güncelle</button>
            </form>
        </div>
    </div>
</body>
</html>
