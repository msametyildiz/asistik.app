<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            font-size: 1.2rem; /* Daha büyük yazı */
            font-weight: bold;
        }

        .option:hover {
            transform: scale(1.15); /* Daha fazla büyüme efekti */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }

        .page-title {
            font-size: 2.5rem; /* Başlık boyutunu artırdım */
            margin-top: 4rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.8rem; /* Mobilde daha küçük başlık */
                margin-top: 6rem; /* Geri dön butonu ile çakışmayı önler */
            }

            .back-button {
                top: 10px; /* Butonun mobilde yukarı kayması */
                left: 10px; /* Mobilde biraz daha dar alana oturması */
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
        }
    </style>
</head>
<body>
    <a href="girisyap.php" class="btn btn-secondary back-button">Geri</a>
    <div class="container">
        <h1 class="page-title">Kayıt Türü Seçin</h1>
        <div class="register-option">
            <!-- İşveren Kaydı -->
            <div class="option" onclick="location.href='employer_register.php'">
                <img src="pic/isveren_kayit.png" alt="İşveren">
                <p>İşveren Kaydı</p>
            </div>
            <!-- Kariyer Girişi -->
            <div class="option" onclick="location.href='career_register.php'">
                <img src="pic/kariyer_kayit.png" alt="Kariyer">
                <p>Kariyer Kaydı</p>
            </div>
        </div>
    </div>
</body>
</html>
