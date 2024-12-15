<?php include 'include/session_handler.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ASİSTİK</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="shortcut icon" type="image/x-icon" href="assets/images/asistik_logo.png">
  <script src="assets/js/disable_keys.js"></script>
</head>

<body>
  <div id="root">
    <?php include 'include/sidebar.php'; ?>
    <main class="dashboard">
    <?php include 'include/navbar.php'; ?>
      <section class="dashboard-content">
        <div class="form-container">
          <h4 class="form-title">İş Ekle</h4>
          <form method="post" action="#" class="form">
            <div class="form-row">
              <label for="position">Pozisyon</label>
              <input type="text" id="position" name="position" placeholder="Pozisyon girin" required>
            </div>
            <div class="form-row">
              <label for="company">Sektör</label>
              <input type="text" id="company" name="company" placeholder="Sektör girin" required>
            </div>
            <div class="form-row">
              <label for="jobLocation">Lokasyon</label>
              <input type="text" id="jobLocation" name="jobLocation" placeholder="Lokasyon girin" required>
            </div>
            <div class="form-row">
              <label for="jobType">Çalışma Türü</label>
              <select id="jobType" name="jobType">
                <option value="full-time">Full-time</option>
                <option value="part-time">Part-time</option>
                <option value="internship">Internship</option>
              </select>
            </div>
            <button type="submit" class="submit-btn">Kaydet</button>
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