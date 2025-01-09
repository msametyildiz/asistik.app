
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="shortcut icon" type="image/x-icon" href="assets/images/asistik_logo.png">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    </style>
</head>
<body>
      <!-- Geri Butonu -->
      <a href="girisyap.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Geri
    </a>
    <div id="root">
        <main class="dashboard">
            <section class="container mt-5">
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
            </section>
        </main>
    </div>

    <?php include '../include/footer.php'; ?>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script></body>
</html>
