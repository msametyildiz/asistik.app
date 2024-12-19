<?php include 'include/session_handler.php'; ?>

<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ASİSTİK</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="shortcut icon" type="image/x-icon" href="assets/images/asistik_logo.png">
<!--<script src="assets/js/disable_keys.js"></script>
-->
</head>

<body>
  <div id="loading" class="loading-spinner"></div>
  <div id="root">
  <?php include 'include/sidebar.php'; ?>


    <main class="dashboard">
    <?php include 'include/navbar.php'; ?>

      <section class="dashboard-content">
        <div class="main-content container my-5">
          <div class="text-center mb-5">
            <img src="assets/images/pic/asist_logo.svg" alt="Asistik Logo" class="responsive-logo">
          </div>
          <div id="responsiveDiv" class="row gx-1 gy-4 custom-grid justify-content-center" style="margin-top: 3%;">
            <!-- Responsive grid items -->
            <?php
            $sections = [
              ['url' => 'pages/employer_positions.php', 'img' => 'assets/images/pic/isveren.png', 'alt' => 'İşveren'],
              ['url' => 'pages/career.php', 'img' => 'assets/images/pic/kariyer.png', 'alt' => 'Kariyer'],
              ['url' => '#', 'img' => 'assets/images/pic/kocluk.png', 'alt' => 'Koçluk', 'data' => 'Koçluk'],
              ['url' => '#', 'img' => 'assets/images/pic/analiz.png', 'alt' => 'Analiz', 'data' => 'Analiz'],
              ['url' => '#', 'img' => 'assets/images/pic/egitim.png', 'alt' => 'Eğitim', 'data' => 'Eğitim'],
              ['url' => '#', 'img' => 'assets/images/pic/verimlilik.png', 'alt' => 'Verimlilik', 'data' => 'Verimlilik']
            ];

            foreach ($sections as $section): ?>
              <div class="col-6 col-md-4 col-lg-custom d-flex justify-content-center">
                <a href="<?= $section['url']; ?>"
                  class="thumb <?= isset($section['data']) ? 'alert-section' : ''; ?>"
                  data-section="<?= $section['data'] ?? ''; ?>">
                  <img src="<?= $section['img']; ?>" alt="<?= $section['alt']; ?>" class="img-fluid">
                </a>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="row justify-content-center mt-4" style="padding-top: 4%; display: flex; gap: 20px;">
            <div class="text-center" style="flex: 0.2;">
              <a href="#" class="action alert-section" data-section="Canlı Görüşme">
                <img src="assets/images/pic/canli_gorusme.svg" alt="Canlı Görüşme" style="max-width: 100px;">
              </a>
            </div>
            <div class="text-center" style="flex: 0.2;">
              <a href="pages/redirect_check.php?target=upload_resume.php" class="action">
                <img src="assets/images/pic/ozgecmis.svg" alt="Özgeçmiş Yükle" style="max-width: 100px;">
              </a>
            </div>
          </div>
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
    const sidebar = document.getElementById("mySidebar");
    const mainContent = document.querySelector(".main-content");
    const headerArea = document.querySelector(".header-area");
    const toggleButton = document.getElementById("sidebarToggle");

    toggleButton.addEventListener("click", () => {
      sidebar.classList.toggle("open");

      if (sidebar.classList.contains("open")) {
        mainContent.style.marginLeft = "250px";
        headerArea.style.marginLeft = "250px";
      } else {
        mainContent.style.marginLeft = "0";
        headerArea.style.marginLeft = "0";
        mainContent.style.textAlign = "center";
        mainContent.style.margin = "0 auto";
      }
    });

    function updateClassBasedOnViewport() {
      const div = document.getElementById("responsiveDiv");
      const width = window.innerWidth;
      const height = window.innerHeight;

      if (width > 992 && height > 616) {
        div.className = "row gx-1 gy-4 custom-grid d-flex justify-content-center col-6";
      } else {
        div.className = "row gx-1 gy-4 custom-grid";
      }
    }

    window.addEventListener("load", updateClassBasedOnViewport);
    window.addEventListener("resize", updateClassBasedOnViewport);
  </script>
</body>

</html>