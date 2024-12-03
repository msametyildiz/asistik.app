<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/crypto-js@4.1.1/crypto-js.min.js"></script> <!-- Şifreleme için -->
    <link rel="shortcut icon" type="image/x-icon" href="pic/asistik_logo.png">

</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4 shadow-lg" style="width: 100%; max-width: 400px;">
            <h2 class="text-center mb-4">Hesabınıza Giriş Yapın</h2>
            <form action="login-process.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">E-posta veya Kullanıcı Adı:</label>
                    <input type="text" id="email" name="email" class="form-control" placeholder="E-posta veya kullanıcı adınızı girin" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Şifre:</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Şifrenizi girin" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label for="rememberMe" class="form-check-label">Beni Hatırla</label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
            </form>
            <a href="forgot-password.php" class="text-decoration-none d-block mt-3">Şifrenizi mi unuttunuz?</a>
            <p class="mt-4 text-center">Hesabınız yok mu? <a href="register.php" class="text-decoration-none">Kayıt Olun</a></p>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector("form").addEventListener("submit", function(e) {
            const rememberMe = document.getElementById("rememberMe").checked;
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            if (rememberMe) {
                // Şifreyi şifreleyerek sakla
                const encryptedPassword = CryptoJS.AES.encrypt(password, "secret_key").toString();
                localStorage.setItem("rememberMe", true);
                localStorage.setItem("email", email);
                localStorage.setItem("password", encryptedPassword);
            } else {
                // Kullanıcı bilgilerini temizle
                localStorage.removeItem("rememberMe");
                localStorage.removeItem("email");
                localStorage.removeItem("password");
            }
        });

        // Sayfa yüklendiğinde input alanını doldur
        window.onload = function() {
            if (localStorage.getItem("rememberMe")) {
                document.getElementById("email").value = localStorage.getItem("email");
                const encryptedPassword = localStorage.getItem("password");
                if (encryptedPassword) {
                    const decryptedPassword = CryptoJS.AES.decrypt(encryptedPassword, "secret_key").toString(CryptoJS.enc.Utf8);
                    document.getElementById("password").value = decryptedPassword;
                }
                document.getElementById("rememberMe").checked = true;
            }
        };
    </script>
</body>
</html>
