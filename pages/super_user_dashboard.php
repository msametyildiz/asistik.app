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
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/asistik_logo.png">
    <style>
        body {
            background: linear-gradient(135deg, #17a2b8, #1fa3d8, #0f87c2, #17a2b8);

            background-size: 400% 400%;
            animation: gradientBG 10s ease infinite;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .dashboard-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            max-width: 900px;
            width: 100%;
            margin: 20px;
        }

        h1 {
            color: #17a2b8;
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        table thead {
            background: #17a2b8;
            color: #fff;
        }

        table tbody tr {
            transition: all 0.2s ease;
        }

        table tbody tr:hover {
            background: rgba(23, 162, 184, 0.1);
        }

        .btn {
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(23, 162, 184, 0.2);
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background-color: #138c9c;
            box-shadow: 0 6px 15px rgba(23, 162, 184, 0.4);
        }

        .btn-danger:hover {
            background-color: #d9534f;
            box-shadow: 0 6px 15px rgba(217, 83, 79, 0.4);
        }

        .empty-state {
            text-align: center;
            color: #555;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 20px;
            }

            h1 {
                font-size: 1.5rem;
            }
        }

        body,
        html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .dashboard-container {
            flex: 1;
            /* İçeriğin yüksekliğini uzatır */
        }

        footer {
            background: #17a2b8;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: relative;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <h1>Onay Bekleyen İşverenler</h1>

        <?php if (empty($pendingUsers)): ?>
            <div class="empty-state">
                <p>Şu anda onay bekleyen işveren bulunmamaktadır.</p>
            </div>
        <?php else: ?>
            <table class="table table-striped">
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
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($user['first_name']) ?></td>
                            <td><?= htmlspecialchars($user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['company_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td>
                                <form method="post" style="display: inline-block;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Onayla</button>
                                </form>
                                <form method="post" style="display: inline-block;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reddet</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <footer class="site-footer">
        <div class="footer-content">
            <p>Copyright © 2024 <strong> Company of MSY</strong>. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>