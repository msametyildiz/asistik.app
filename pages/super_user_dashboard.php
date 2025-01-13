<?php
session_start();
require 'config.php'; // Veritabanı bağlantısı
require 'vendor/autoload.php'; // PHPMailer yüklemesi

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['is_super_user']) || $_SESSION['is_super_user'] !== true) {
    header('Location: super_user_login.php'); // Giriş sayfasına yönlendir
    exit;
}

// Onay bekleyen kullanıcıları listele
$stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, company_name, created_at FROM users WHERE role = 'employer' AND is_pending = 1");
$stmt->execute();
$pendingUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// İşlem fonksiyonu
function handleUserAction($db, $action, $userId)
{
    $mail = new PHPMailer(true);
    try {
        // SMTP Ayarları
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info.asistik@gmail.com'; // Gmail adresinizi girin
        $mail->Password = 'mody xldl biyv hfmg'; // Gmail uygulama şifresini girin
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('info.asistik@gmail.com', 'Asistik');

        /*______________________________________________________________________*/
        if ($action === 'approve') {
            // Kullanıcıyı onayla
            $updateStmt = $db->prepare("UPDATE users SET is_pending = 0 WHERE id = ?");
            $updateStmt->execute([$userId]);

            // Kullanıcı bilgilerini al
            $stmt = $db->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Onay maili gönder
                try {
                    $mail->addAddress($user['email']);
                    $mail->isHTML(true);
                    $mail->Subject = 'Hesabınız Onaylandı';
                    $mail->Body = "
                            Sayın <strong>{$user['first_name']} {$user['last_name']}</strong>,<br><br>
                            Tarafımızca değerlendirilen kayıt başvurunuz <strong>onaylanmıştır</strong>. 
                            Sisteme giriş yapabilirsiniz. Keyifli kullanımlar dileriz.<br><br>
                            Saygılarımızla,<br>
                            <strong>ASİSTİK Ekibi</strong>
                        ";
                    $mail->send();
                } catch (Exception $e) {
                    error_log("Mail gönderim hatası: " . $mail->ErrorInfo);
                }
            }
            $_SESSION['success_message'] = 'Kullanıcı başarıyla onaylandı.';
            error_log("Approve işlemi başarıyla tamamlandı.");
            /*______________________________________________________________________*/
        } elseif ($action === 'reject') {
            // Kullanıcı bilgilerini al
            $stmt = $db->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Kullanıcıyı ve ilişkili verileri sil
                $db->beginTransaction();
                $deletePositionsStmt = $db->prepare("DELETE FROM positions WHERE employer_id = ?");
                $deletePositionsStmt->execute([$userId]);

                $deleteStmt = $db->prepare("DELETE FROM users WHERE id = ?");
                $deleteStmt->execute([$userId]);
                $db->commit();

                // Reddetme maili gönder
                try {
                    $mail->addAddress($user['email']);
                    $mail->isHTML(true);
                    $mail->Subject = 'Kayıt Başvurunuz Reddedildi';
                    $mail->Body = "
                            Sayın <strong>{$user['first_name']} {$user['last_name']}</strong>,<br><br>
                            Tarafımızca değerlendirilen kayıt başvurunuz ne yazık ki <strong>reddedilmiştir</strong>.<br>
                            Eğer başka bir konuda destek almak isterseniz bizimle iletişime geçebilirsiniz.<br><br>
                            Saygılarımızla,<br>
                            <strong>ASİSTİK Ekibi</strong>
                        ";
                    $mail->send();
                } catch (Exception $e) {
                    error_log("Mail gönderim hatası: " . $mail->ErrorInfo);
                }
            }
            $_SESSION['success_message'] = 'Kullanıcı başarıyla reddedildi.';
        }
    } catch (Exception $e) {
        error_log("Hata meydana geldi: " . $e->getMessage());
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $_SESSION['error_message'] = 'Bir hata oluştu: ' . $e->getMessage();
    }
}

// POST işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_POST['user_id'] ?? 0;

    if ($userId && in_array($action, ['approve', 'reject'])) {
        handleUserAction($db, $action, $userId);
    } else {
        $_SESSION['error_message'] = 'Geçersiz işlem.';
    }

    // Sayfayı yenile
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}


// Sayfa numarasını kontrol et
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10; // Her sayfada gösterilecek kullanıcı sayısı
$offset = ($page - 1) * $limit; // Başlangıç noktası

// Toplam kullanıcı sayısını al
$totalStmt = $db->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'employer' AND is_pending = 1");
$totalStmt->execute();
$totalCount = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalCount / $limit);

// Kullanıcıları çek (limit ve offset ile)
$stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, company_name, created_at FROM users 
    WHERE role = 'employer' AND is_pending = 1 
    ORDER BY created_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$pendingUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Kullanıcı oturum kontrolü
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Misafir';
$user_role = $_SESSION['user_role'] ?? '';

$searchQuery = $_GET['search'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

if (!empty($searchQuery)) {
    $stmt = $db->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'employer' AND is_pending = 1 AND 
        (first_name LIKE :search OR last_name LIKE :search OR company_name LIKE :search OR email LIKE :search)");
    $stmt->bindValue(':search', "%$searchQuery%", PDO::PARAM_STR);
    $stmt->execute();
    $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, company_name, created_at FROM users 
    WHERE role = 'employer' AND is_pending = 1 
    AND (first_name LIKE :search OR last_name LIKE :search OR company_name LIKE :search OR email LIKE :search) 
    ORDER BY created_at DESC 
    LIMIT :limit OFFSET :offset

");
    $stmt->bindValue(':search', "%$searchQuery%", PDO::PARAM_STR);
} else {
    $stmt = $db->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'employer' AND is_pending = 1");
    $stmt->execute();
    $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, company_name, created_at FROM users 
        WHERE role = 'employer' AND is_pending = 1 
        ORDER BY created_at DESC 
        LIMIT :limit OFFSET :offset");
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$pendingUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPages = ceil($totalCount / $limit);
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASİSTİK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../assets/images/asistik_logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/super_user.css">

</head>

<body>
    <div class="container">
        <div class="container-custom">
            <h1>Onay Bekleyen İşverenler</h1>
            <div class="search-box">
                <form method="get" action="" class="w-100 d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Tabloda ara..." value="<?= htmlspecialchars($searchQuery) ?>">
                    <button type="submit" class="btn btn-primary">Ara</button>
                    <a href="?search=" class="btn btn-secondary">Sıfırla</a>
                </form>
            </div>
            <?php if (!empty($_SESSION['success_message'])): ?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı',
                        text: '<?= htmlspecialchars($_SESSION['success_message']) ?>',
                        confirmButtonText: 'Tamam'
                    });
                </script>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['error_message'])): ?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata',
                        text: '<?= htmlspecialchars($_SESSION['error_message']) ?>',
                        confirmButtonText: 'Tamam'
                    });
                </script>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (empty($pendingUsers)): ?>
                <p>Onay bekleyen kullanıcı bulunmamaktadır.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ad</th>
                                <th>Soyad</th>
                                <th>Şirket Adı</th>
                                <th>E-posta</th>
                                <th>Telefon</th>
                                <th>Kayıt Tarihi</th> <!-- Yeni sütun -->
                                <th>İşlemler</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($pendingUsers as $index => $user): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                                    <td><?= htmlspecialchars($user['company_name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['phone']) ?></td>
                                    <td><?= !empty($user['created_at'])? htmlspecialchars(date('d-m-Y H:i', strtotime($user['created_at']))): 'Tarih Yok' ?>
                                    </td>
                                    <!-- Tarihi formatlayarak göster -->
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="button" class="btn btn-success btn-sm approve-btn">Onayla</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="button" class="btn btn-danger btn-sm reject-btn">Reddet</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>


                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($searchQuery) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>Copyright © 2024 <strong>Company of MSY</strong>. All rights reserved.</p>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.approve-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Onayla',
                        text: 'Bu kullanıcıyı onaylamak istediğinize emin misiniz?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Evet, onayla',
                        cancelButtonText: 'Hayır'
                    }).then(result => {
                        if (result.isConfirmed) {
                            btn.closest('form').submit(); // 'this' yerine 'btn' kullanıldı
                        }
                    });
                });
            });

            document.querySelectorAll('.reject-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Reddet',
                        text: 'Bu kullanıcıyı reddetmek istediğinize emin misiniz?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Evet, reddet',
                        cancelButtonText: 'Hayır'
                    }).then(result => {
                        if (result.isConfirmed) {
                            btn.closest('form').submit();
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>