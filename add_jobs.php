<?php
ob_start();
session_start();
require 'pages/config.php';

// Kullanıcı oturumda mı?
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Misafir';

// Oturum kontrolü
if (!$isLoggedIn) {
  header('Location: pages/login.php');
  exit;
}

// Kullanıcı bilgileri
$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];

// Sektör bilgilerini çek
$sectorStmt = $db->prepare("SELECT id, sector_name FROM sectors WHERE is_open = 1");
$sectorStmt->execute();
$sectors = $sectorStmt->fetchAll(PDO::FETCH_ASSOC);

// İş ilanı ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sector_id = $_POST['sector_id'] ?? null;
  $position_name = trim($_POST['position_name'] ?? '');
  $location = trim($_POST['location'] ?? '');
  $job_type = $_POST['job_type'] ?? '';
  $is_open = $_POST['is_open'] ?? '1'; // Varsayılan açık

  if (!empty($sector_id) && !empty($position_name) && !empty($location) && !empty($job_type)) {
    $insertStmt = $db->prepare("INSERT INTO positions (employer_id, sector_id, position_name, location, job_type, is_open, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $insertStmt->execute([$user_id, $sector_id, $position_name, $location, $job_type, $is_open]);

    // Oturum değişkenine başarı mesajını kaydet
    $_SESSION['success_message'] = "İlan başarıyla eklendi!";

    // all_jobs.php sayfasına yönlendir
    header('Location: all_jobs.php');
    exit;
  } else {
    $error_message = "Lütfen tüm alanları doldurunuz!";
  }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>İş İlanı Ekle</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="shortcut icon" type="image/x-icon" href="assets/images/asistik_logo.png">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div id="root">
    <?php include 'include/sidebar.php'; ?>
    <main class="dashboard">
      <?php include 'include/navbar.php'; ?>
      <section class="dashboard-content">
        <div class="form-container">
          <h4 class="form-title">Yeni İş İlanı Ekle</h4>

          <!-- Hata Mesajı -->
          <?php if (!empty($error_message)): ?>
            <script>
              Swal.fire({
                icon: 'error',
                title: 'Hata',
                text: '<?= htmlspecialchars($error_message) ?>',
                confirmButtonText: 'Tamam'
              });
            </script>
          <?php endif; ?>

          <form method="post" action="add_jobs.php" class="form">
            <div class="form-row">
              <label for="position_name">Pozisyon Adı</label>
              <input type="text" id="position_name" name="position_name" placeholder="Pozisyon adı girin" required>
            </div>
            <div class="form-row">
              <label for="sector_id">Sektör</label>
              <select id="sector_id" name="sector_id" required>
                <option value="" disabled selected>Sektör Seçin</option>
                <?php foreach ($sectors as $sector): ?>
                  <option value="<?= $sector['id'] ?>"><?= htmlspecialchars($sector['sector_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-row">
              <label for="location">Lokasyon</label>
              <input type="text" id="location" name="location" placeholder="Lokasyon girin" required>
            </div>
            <div class="form-row">
              <label for="job_type">Çalışma Türü</label>
              <select id="job_type" name="job_type" required>
                <option value="" disabled selected>Çalışma Türü Seçin</option>
                <option value="full-time">Full-Time</option>
                <option value="part-time">Part-Time</option>
                <option value="internship">Internship</option>
              </select>
            </div>
            <div class="form-row">
              <label for="is_open">Durum</label>
              <select id="is_open" name="is_open" required>
                <option value="1">Açık</option>
                <option value="0">Kapalı</option>
              </select>
            </div>
            <button type="submit" class="submit-btn" style="background-color: #17a2b8;">Kaydet</button>
          </form>
        </div>
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
    document.querySelectorAll('.alert-section').forEach(function(element) {
      element.addEventListener('click', function(event) {
        event.preventDefault();
        const sectionName = this.getAttribute('data-section');
        alert(`${sectionName} kısmı üzerinde çalışmalarımız devam ediyor.`);
      });
    });
  </script>
</body>

</html>