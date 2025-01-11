<?php
session_start();
require 'pages/config.php'; // Veritabanı bağlantısı

// Kullanıcı oturumda mı?
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Misafir';

// Oturum kontrolü
if (!$isLoggedIn) {
    header('Location: pages/login.php');
    exit;
}

// Kullanıcı bilgileri
$user_id = $_SESSION['user_id'];

// Kullanıcı verilerini çek
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    die('Kullanıcı bilgileri bulunamadı.');
}

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $companyName = $_POST['companyName'] ?? null;

    if (!empty($password) && $password !== $confirmPassword) {
        $error_message = 'Parolalar uyuşmuyor!';
    } else {
        // Parola hashleme
        $passwordHash = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : $userData['password'];

        // Veritabanı güncellemesi
        $updateStmt = $db->prepare(
            "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, password = ?, company_name = ? WHERE id = ?"
        );
        $updateStmt->execute([
            $firstName,
            $lastName,
            $email,
            $phone,
            $passwordHash,
            $companyName,
            $user_id
        ]);

        $success_message = 'Bilgiler başarıyla güncellendi.';
        // Verileri yeniden yükle
        $stmt->execute([$user_id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASİSTİK</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/profile.css">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/asistik_logo.png">
    <!--<script src="assets/js/disable_keys.js"></script>-->
    </head>

    <body>
    <div id="root">
        <?php include 'include/sidebar.php'; ?>

        <main class="dashboard">
            <?php include 'include/navbar.php'; ?>

            <section class="profile-dashboard">
                <h4 class="profile-title">Profile</h4>
                 <!-- Hata veya Başarı Mesajları -->
                 <?php if (!empty($error_message)): ?>
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: '<?= htmlspecialchars($error_message) ?>',
                            confirmButtonText: 'Tamam'
                        });
                    </script>
                <?php elseif (!empty($success_message)): ?>
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarı',
                            text: '<?= htmlspecialchars($success_message) ?>',
                            confirmButtonText: 'Tamam'
                        });
                    </script>
                <?php endif; ?>
                <form method="post" action="" class="profile-form">
                    <div class="form-group">
                        <div class="form-field">
                            <label for="firstName">Ad</label>
                            <input type="text" id="firstName" name="name" value="<?= htmlspecialchars($userData['first_name']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="lastName">Soyad</label>
                            <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($userData['last_name']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="email">E-posta</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="phone">Telefon</label>
                            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($userData['phone']) ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="password">Yeni Parola</label>
                            <input type="password" id="password" name="password" placeholder="Yeni parola girin">
                        </div>
                        <div class="form-field">
                            <label for="confirmPassword">Parola Tekrar</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Parolanızı tekrar girin">
                        </div>
                        <?php if ($userData['role'] === 'employer'): ?>
                            <div class="form-field">
                                <label for="companyName">Şirket Adı</label>
                                <input type="text" id="companyName" name="companyName" value="<?= htmlspecialchars($userData['company_name']) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="form-field">
                            <button type="submit" class="submit-btn" style="background-color: #17a2b8;">Güncelle</button>
                        </div>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <?php include 'include/footer.php'; ?>

    <script src="assets/js/script.js"></script>
    <script>
        function showAlert(event) {
            event.preventDefault();
            alert('Üzerinde çalışılıyor!');
        }
        document.querySelectorAll('.alert-section').forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                const sectionName = this.getAttribute('data-section');
                alert(`${sectionName} kısmı üzerinde çalışmalarımız devam ediyor.`);
            });
        });
    </script>
</body>


</html>