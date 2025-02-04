<aside class="sidebar collapsed">
  <button class="close-btn">✖</button>
  <div class="sidebar-header" onclick="redirectToHome()">
    <img src="assets/images/asistik_logo.png" alt="Asistik Logo" class="logo">
    <h4 class="logo-text">ASİSTİK</h4>
  </div>

  <!-- Kullanıcı Durumuna Göre İçerik -->
  <?php if ($isLoggedIn): ?>
    <div class="sidebar-user">
      <p class="user-name"><strong><?= htmlspecialchars($_SESSION['user_name']); ?></strong></p>
    </div>
  <?php else: ?>
    <div class="sidebar-user">
      <a href="pages/login.php" class="btn btn-info" style="background-color: #17a2b8;">Giriş Yap</a>
    </div>
  <?php endif; ?>

  <!--<div class="theme-buttons">
        <button id="dark-mode-btn" class="theme-toggle-btn">🌙 Dark Mod</button>
        <button id="high-contrast-btn" class="theme-toggle-btn">🔲 High Contrast</button>
      </div>-->

  <div class="nav-links">
    <?php if ($isLoggedIn && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'employer'): ?>
      <li><a href="add_jobs.php" data-tooltip="İş Ekle"><span>📋</span> İş Ekle</a></li>
    <?php endif; ?>
    <li><a href="all_jobs.php" data-tooltip="Tüm İşler"><span>📄</span> Tüm İşler</a></li>
    <li><a href="#" data-section="İstatistik" id="istatistik-link" class="alert-section"><span>📊</span> İstatistik</a></li>
    <?php if ($isLoggedIn): ?>
      <li><a href="profile.php" data-tooltip="Profil"><span>👤</span> Profil</a></li>
    <?php endif; ?>
  </div>

  <?php if ($isLoggedIn): ?>
    <div class="logout text-center mt-3">
      <a href="pages/logout.php" class="btn btn-danger">Çıkış Yap</a>
    </div>
  <?php endif; ?>

</aside>