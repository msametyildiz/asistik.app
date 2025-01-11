<?php
session_start();

// Super User kullanıcı adı ve şifre
define('SUPER_USER', 'super_user');
define('SUPER_PASSWORD', 'superuser_password');

// Giriş kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === SUPER_USER && $password === SUPER_PASSWORD) {
        $_SESSION['is_super_user'] = true; // Oturum değişkeni ayarla
        header('Location: super_user_dashboard.php'); // Ana sayfaya yönlendir
        exit;
    } else {
        $error_message = "Geçersiz kullanıcı adı veya şifre!";
    }
}
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
            background: linear-gradient(135deg, #17a2b8, #1fa3d8, #0f87c2, #17a2b8);
            min-height: 100vh;
            animation: gradient-animation 10s ease infinite;

            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        @keyframes gradient-animation {
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

        .login-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            transition: transform 0.5s ease-in-out, box-shadow 0.5s ease-in-out;
        }

        .login-container:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }

        .login-container h1 {
            font-size: 1.8rem;
            text-align: center;
            color: rgb(0, 0, 0);
            margin-bottom: 20px;
            transition: color 0.3s ease;
        }

        .login-container h1:hover {
            color: #138c9c;
        }

        .form-control {
            background: rgba(240, 248, 255, 0.9);
            color: #333;
            border: 1px solid #17a2b8;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(240, 248, 255, 1);
            color: #000;
            border: 1px solid #138c9c;
            box-shadow: 0 0 10px #17a2b8;
        }

        .btn-primary {
            background: #17a2b8;
            border: none;
            transition: all 0.3s ease;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
        }

        .btn-primary:hover {
            background: #138c9c;
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.6);
            transform: translateY(-2px);
        }

        .btn-primary:active {
            background: #117a8b;
            transform: translateY(2px);
            box-shadow: 0 3px 10px rgba(23, 162, 184, 0.3);
        }

        .alert-danger {
            background: rgba(255, 0, 0, 0.1);
            color: #d9534f;
            border: 1px solid #d9534f;
            border-radius: 8px;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            50% {
                transform: translateX(5px);
            }

            75% {
                transform: translateX(-5px);
            }

            100% {
                transform: translateX(0);
            }
        }

        /* Responsive Tasarım */
        @media (max-width: 576px) {
            .login-container {
                padding: 20px;
                border-radius: 10px;
            }

            .login-container h1 {
                font-size: 1.5rem;
            }

            .form-control {
                font-size: 0.9rem;
            }

            .btn-primary {
                font-size: 0.9rem;
            }
        }

        body {
            background: linear-gradient(135deg, #17a2b8, #1fa3d8, #0f87c2, #17a2b8);
            min-height: 100vh;
            animation: gradient-animation 10s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: Arial, sans-serif;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../assets/images/asistik_logo.png') no-repeat center center;
            background-size: 50%;
            /* Görsel boyutunu %50 yapar */
            opacity: 0.08;
            /* Hafif saydamlık için */
            z-index: -1;
        }

        .login-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            max-width: 400px;
            width: 100%;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            position: relative;
            z-index: 1;
        }

        .login-container:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }

        .login-container h1 {
            font-size: 1.8rem;
            text-align: center;
            color: #17a2b8;
            margin-bottom: 20px;
            transition: color 0.3s ease;
        }

        .login-container h1:hover {
            color: #138c9c;
        }

        .form-control {
            background: rgba(240, 248, 255, 0.9);
            color: #333;
            border: 1px solid #17a2b8;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(240, 248, 255, 1);
            color: #000;
            border: 1px solid #138c9c;
            box-shadow: 0 0 10px rgba(23, 162, 184, 0.5);
        }

        .btn-primary {
            background: #17a2b8;
            border: none;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
        }

        .btn-primary:hover {
            background: #138c9c;
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.6);
            transform: translateY(-2px);
        }

        .btn-primary:active {
            background: #117a8b;
            transform: translateY(2px);
            box-shadow: 0 3px 10px rgba(23, 162, 184, 0.3);
        }

        .alert-danger {
            background: rgba(255, 0, 0, 0.1);
            color: #d9534f;
            border: 1px solid #d9534f;
            border-radius: 8px;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            50% {
                transform: translateX(5px);
            }

            75% {
                transform: translateX(-5px);
            }

            100% {
                transform: translateX(0);
            }
        }

        /* Responsive Tasarım */
        @media (max-width: 576px) {
            .login-container {
                padding: 20px;
                border-radius: 10px;
            }

            .login-container h1 {
                font-size: 1.5rem;
            }

            .form-control {
                font-size: 0.9rem;
            }

            .btn-primary {
                font-size: 0.9rem;
            }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 100px;
            /* Logonun genişliği */
            height: auto;
            /* Oranlı boyutlandırma */
            opacity: 0.9;
            /* Hafif saydamlık */
            transition: all 0.3s ease;
            /* Hover efekti için */
        }

        .logo:hover {
            transform: scale(1.1);
            /* Hover ile hafif büyütme */
            opacity: 1;
            /* Hover sırasında tam görünürlük */
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="../assets/images/asistik_logo.png" alt="ASİSTİK Logo" class="logo">
        </div>
        <h1>Yetkili Girişi</h1>
        <form method="POST" action="">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="username" class="form-label">Kullanıcı Adı</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Kullanıcı Adınızı Girin" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Şifrenizi Girin" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
        </form>
    </div>
</body>

</html>