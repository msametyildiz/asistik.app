<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="pic/ASİST LOGO-Photo.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        .card {
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .btn-warning {
            background-color: #ffc107;
            border: none;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .alert {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4" style="width: 100%; max-width: 400px;">
            <h2 class="text-center mb-4">Şifre Sıfırlama</h2>
            <p class="text-center mb-4">
                Şifrenizi sıfırlamak için kayıtlı e-posta adresinizi girin. Size bir sıfırlama bağlantısı göndereceğiz.
            </p>
            <!-- Şifre sıfırlama formu -->
            <form id="forgotPasswordForm" action="reset-password-process.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">E-posta:</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="E-posta adresinizi girin" required>
                </div>
                <button type="submit" class="btn btn-warning w-100">Şifre Sıfırlama Bağlantısı Gönder</button>
            </form>

            <!-- Başarılı veya hata mesajı -->
            <div id="responseMessage" class="alert d-none" role="alert"></div>

            <p class="text-center mt-4">
                <a href="login.php" class="text-decoration-none">Giriş sayfasına dön</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form gönderimini Ajax ile ele alma
        const form = document.getElementById('forgotPasswordForm');
        const responseMessage = document.getElementById('responseMessage');

        form.addEventListener('submit', async (e) => {
            e.preventDefault(); // Sayfanın yeniden yüklenmesini önle

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: form.method,
                    body: formData,
                });

                const result = await response.json();

                responseMessage.classList.remove('d-none', 'alert-danger', 'alert-success');
                responseMessage.classList.add(result.status === 'success' ? 'alert-success' : 'alert-danger');
                responseMessage.textContent = result.message;
            } catch (error) {
                responseMessage.classList.remove('d-none');
                responseMessage.classList.add('alert-danger');
                responseMessage.textContent = 'Bir hata oluştu. Lütfen tekrar deneyin.';
            }
        });
    </script>
</body>
</html>
