<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ASİSTİK</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="shortcut icon" type="image/x-icon" href="asistik_logo.png">
</head>
<body>
  <div id="root">
    <aside class="sidebar collapsed"> <!-- Varsayılan olarak "collapsed" sınıfı -->
      <button class="close-btn">✖</button> <!-- X Butonu -->
      <div class="sidebar-header">
        <img src="asistik_logo.png" alt="Asistik Logo" class="logo">
        <h4 class="logo-text">ASİSTİK</h4>
      </div>
      <div class="nav-links">
        <li><a href="add_jobs.html"><span>📋</span> İş Ekle</a></li>
        <li><a href="all_jobs.html"><span>📄</span> Tüm İşler</a></li>
        <li><a href="#" id="istatistik-link" onclick="showAlert(event)"><span>📊</span> İstatistik</a></li>
        <li><a href="profile.html"><span>👤</span> Profil</a></li>
      </div>
    </aside>
    <main class="dashboard">
      <nav class="navbar">
        <button id="toggle-btn" class="toggle-btn">
          <img src="menu.png" alt="Menu" style="width: 24px; height: 24px;">
        </button>
        <div class="user-menu">
          <img src="" alt="User Avatar" class="user-avatar" id="user-avatar" style="display: none;">
          <span id="user-name" style="display: none;"></span>
          <button id="login-button" class="login-btn">Giriş Yap</button>
          <button class="dropdown-btn" id="dropdown-btn" onclick="toggleDropdown()" style="display: none;">▼</button>
          <div class="dropdown-menu" id="dropdown-menu" style="display: none;">
            <a href="#" id="logout-link" onclick="logout()">Çıkış Yap</a>
          </div>
        </div>
      </nav>
      <section class="dashboard-content">
        <div class="main-content container my-5" >
          <div class="text-center mb-5">
              <img src="pic/asist_logo.svg" alt="Asistik Logo" class="responsive-logo">
          </div>
          <div id="responsiveDiv" class="row gx-1 gy-4 custom-grid d-flex justify-content-center col-6">
  
              <!-- Responsive grid items -->
              <?php
              $sections = [
                  ['url' => 'redirect_check.php?target=employer_positions.php', 'img' => 'pic/isveren.png', 'alt' => 'İşveren'],
                  ['url' => 'career.php', 'img' => 'pic/kariyer.png', 'alt' => 'Kariyer'],
                  ['url' => '#', 'img' => 'pic/kocluk.png', 'alt' => 'Koçluk', 'data' => 'Koçluk'],
                  ['url' => '#', 'img' => 'pic/analiz.png', 'alt' => 'Analiz', 'data' => 'Analiz'],
                  ['url' => '#', 'img' => 'pic/egitim.png', 'alt' => 'Eğitim', 'data' => 'Eğitim'],
                  ['url' => '#', 'img' => 'pic/verimlilik.png', 'alt' => 'Verimlilik', 'data' => 'Verimlilik']
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
          <div class="row justify-content-center mt-4" style="padding-top: 3%;">
              <div class="col-6 col-md-2 text-center">
                  <a href="#" class="action alert-section" data-section="Canlı Görüşme">
                      <img src="pic/canli_gorusme.svg" alt="Canlı Görüşme">
                  </a>
              </div>
              <div class="col-6 col-md-2 text-center">
                  <a href="redirect_check.php?target=upload_resume.php" class="action">
                      <img src="pic/ozgecmis.svg" alt="Özgeçmiş Yükle">
                  </a>
              </div>
          </div>
      </div>
      </section>
    </main>
  </div>
  <script src="script.js"></script>
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
                mainContent.style.textAlign = "center"; // Ortalamayı tekrar uygular
                mainContent.style.margin = "0 auto"; // Varsayılan margin'i tekrar uygular
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

        // Sayfa yüklendiğinde kontrol et
        window.addEventListener("load", updateClassBasedOnViewport);

        // Pencere yeniden boyutlandırıldığında kontrol et
        window.addEventListener("resize", updateClassBasedOnViewport);
  </script>
</body>
</html>
