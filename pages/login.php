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
    

    <style>
        .register-option {
    text-align: center;
    margin-top: 100px;
}

.option {
    display: inline-block;
    margin: 30px;
    padding: 30px;
    border: 2px solid #ccc;
    border-radius: 15px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    width: 200px;
    height: 250px;
}

.option img {
    width: 120px;
    height: 120px;
    margin-bottom: 10px;
}

.option p {
    font-size: 1.2rem;
    font-weight: bold;
}

.option:hover {
    transform: scale(1.15);
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
}

.page-title {
    font-size: 2.5rem;
    margin-top: 4rem;
    text-align: center;
}

.back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    background-color: #17a2b8;
    color: white;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 50px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(23, 162, 184, 0.3);
}

.back-button:hover {
    background-color: #138c9c;
    box-shadow: 0 6px 15px rgba(23, 162, 184, 0.5);
    transform: translateY(-2px);
}

.back-button i {
    margin-right: 8px;
}

@media (max-width: 768px) {
    .page-title {
        font-size: 1.8rem;
        margin-top: 3rem;
    }

    .option {
        width: 150px;
        height: 200px;
        padding: 20px;
    }

    .option img {
        width: 100px;
        height: 100px;
    }

    .option p {
        font-size: 1rem;
    }

    .back-button {
        padding: 8px 16px;
        font-size: 0.9rem;
    }
}
/* Footer */
.site-footer {
  background-color: #f8f9fa; /* Hafif gri arka plan */
  color: #333; /* Yazı rengi */
  text-align: center; /* Ortalanmış metin */
  padding: 10px 20px; /* İç boşluklar */
  border-top: 1px solid #ddd; /* Üst kenarda bir ayrım çizgisi */
  width: 100%; /* Tam genişlik */
  font-size: 14px; /* Yazı boyutu */
  margin-top: 5%;
}

    </style>
</head>
<body>
    <!-- Geri Butonu -->
    <a href="../index.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Geri
    </a>
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
                    <input type="checkbox" class="form-check-input" id="rememberMe" style="background-color: #17a2b8;">
                    <label for="rememberMe" class="form-check-label" style="color: #17a2b8;">Beni Hatırla</label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100" style="background-color: #17a2b8;">Giriş Yap</button>
            </form>
            <a href="forgot-password.php" class="text-decoration-none d-block mt-3 " style="color: #17a2b8;">Şifrenizi mi unuttunuz?</a>
            <p class="mt-4 text-center">Hesabınız yok mu? <a href="register.php" class="text-decoration-none" style="color: #17a2b8;">Kayıt Olun</a></p>
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
        <?php include '../include/footer.php'; ?>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script></body>
</body>
</html>
