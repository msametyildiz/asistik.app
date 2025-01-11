<?php
session_start();

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['is_super_user']) || $_SESSION['is_super_user'] !== true) {
    header('Location: super_user_login.php'); // Giriş sayfasına yönlendir
    exit;
}
require 'config.php'; // Veritabanı bağlantısı
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Kullanıcı oturumda mı?
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Misafir';
$user_role = $_SESSION['user_role'] ?? '';


// Onay bekleyen kullanıcıları listele
$stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, company_name FROM users WHERE role = 'employer' AND is_pending = 1");
$stmt->execute();
$pendingUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Onaylama işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_POST['user_id'] ?? 0;

    if ($action === 'approve') {
        // Kullanıcıyı onayla
        $updateStmt = $db->prepare("UPDATE users SET is_pending = 0 WHERE id = ?");
        $updateStmt->execute([$userId]);

        // Onaylanan kullanıcıya mail gönder
        $stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // PHPMailer ile mail gönder
            require 'vendor/autoload.php';

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'samet.saray.06@gmail.com'; // Değiştir
                $mail->Password = 'sazjvbfajhwnketb'; // Değiştir
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom('samet.saray.06@gmail.com', 'Asistik');
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
                // Mail gönderim hatası
            }
        }
    } elseif ($action === 'reject') {
        // Kullanıcı bilgilerini al
        $stmt = $db->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Kullanıcıyı reddet ve veritabanından sil
            $deleteStmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $deleteStmt->execute([$userId]);

            // PHPMailer ile reddedilme maili gönder
            require 'vendor/autoload.php';

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'samet.saray.06@gmail.com'; // Değiştir
                $mail->Password = 'sazjvbfajhwnketb'; // Değiştir
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom('samet.saray.06@gmail.com', 'Asistik');
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
                // Mail gönderim hatası
                error_log("Reddetme maili gönderimi başarısız: " . $mail->ErrorInfo);
            }
        }
    }


    // Sayfayı yenile
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<?php
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
$stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, company_name FROM users WHERE role = 'employer' AND is_pending = 1 LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$pendingUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php

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

    $stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, company_name FROM users 
    WHERE role = 'employer' AND is_pending = 1 AND 
    (first_name LIKE :search OR last_name LIKE :search OR company_name LIKE :search OR email LIKE :search)
    LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':search', "%$searchQuery%", PDO::PARAM_STR);
} else {
    $stmt = $db->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'employer' AND is_pending = 1");
    $stmt->execute();
    $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, company_name FROM users 
    WHERE role = 'employer' AND is_pending = 1 
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
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/asistik_logo.png">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #17a2b8);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333;
        }

        .container {
            flex: 1;
            margin: 30px auto;
            max-width: 1200px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }


        h1 {
            font-size: 2rem;
            font-weight: bold;
            color: #0056b3;
            text-align: center;
            margin-bottom: 20px;
        }

        .search-box {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .container-custom {
            padding: 20px;
            /* Tüm kenarlara boşluk ekler */
            max-width: 1200px;
            /* İçeriğin genişliğini sınırlar */
            margin: 0 auto;
            /* Ortalar */
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .search-box input {
            flex: 1;
            padding: 8px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        .search-box button {
            padding: 8px 15px;
            border: none;
            background-color: #17a2b8;
            color: white;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        .search-box button:hover {
            background-color: #138c9c;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
        }

        .pagination .page-link {
            color: #17a2b8;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 50px;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background-color: #17a2b8;
            color: white;
        }

        .pagination .page-item.active .page-link {
            background-color: #17a2b8;
            color: white;
            border: 1px solid #17a2b8;
        }

        footer {
            background-color: #17a2b8;
            color: white;
            padding: 10px;
            text-align: center;
            margin-top: auto;
        }

        /* Responsive Tasarım */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.5rem;
            }

            table tbody td {
                font-size: 0.8rem;
            }

            .btn {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 576px) {
            h1 {
                font-size: 1.2rem;
            }

            table tbody td {
                font-size: 0.7rem;
            }
        }
    </style>
</head>

<body>
    <<div class="container-fluid">
        <div class="container-custom">
            <h1>Onay Bekleyen İşverenler</h1>
            <div class="search-box">
                <form method="get" action="" class="w-100 d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Tabloda ara..." value="<?= htmlspecialchars($searchQuery) ?>">
                    <button type="submit" class="btn btn-primary">Ara</button>
                    <a href="?search=" class="btn btn-secondary">Sıfırla</a>
                </form>
            </div>
            <?php if (empty($pendingUsers)): ?>
                <p class="text-center">Sonuç bulunamadı.</p>
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
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingUsers as $index => $user): ?>
                                <tr>
                                    <td><?= $offset + $index + 1 ?></td>
                                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                                    <td><?= htmlspecialchars($user['company_name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['phone']) ?></td>
                                    <td>
                                        <button class="btn btn-success btn-sm">Onayla</button>
                                        <button class="btn btn-danger btn-sm">Reddet</button>
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

</body>

</html>