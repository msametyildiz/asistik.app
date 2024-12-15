document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("toggle-btn");
  const sidebar = document.querySelector(".sidebar");
  const closeBtn = document.querySelector(".close-btn");
  const navLinks = document.querySelectorAll(".nav-links a");
  const dashboard = document.querySelector(".dashboard");

  // Sidebar açma/kapatma işlemi
  toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("collapsed");
  });

  // Sidebar'ı kapatma (X butonu ile)
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      sidebar.classList.add("collapsed");
    });
  }
  

  // Sidebar'ı kapatma (X butonu ile)
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      sidebar.classList.add("collapsed");
      sidebar.classList.remove("open");
      dashboard.classList.remove("expanded");
    });
  }

  // Herhangi bir linke tıklandığında sidebar kapanır ve sayfa açılır
  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      sidebar.classList.add("collapsed");
      sidebar.classList.remove("open");
      dashboard.classList.remove("expanded");
    });
  });
});


// Sayfa yüklendiğinde durum kontrolü - Sayfa yüklendiğinde, sidebar'ın açık mı kapalı mı olduğunu kontrol eder ve buna göre dashboard’un genişliğini ayarlar.
document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.querySelector(".sidebar");
  const dashboard = document.querySelector(".dashboard");

  if (sidebar.classList.contains("collapsed")) {
    dashboard.classList.remove("expanded");
  } else {
    dashboard.classList.add("expanded");
  }
});

//All Jobs html için search 
document.getElementById("search-form").addEventListener("submit", function (event) {
  event.preventDefault(); // Formun sayfayı yenilemesini engelle

  const searchKeyword = document.getElementById("search").value.toLowerCase();
  const jobStatus = document.getElementById("job-status").value;
  const jobType = document.getElementById("job-type").value;
  const sort = document.getElementById("sort").value;

  // Filtreleme işlemi (örnek veriyle çalışan bir simülasyon)
  const jobs = [
    { title: "Test1", status: "pending", type: "full-time", date: "2024-12-05" },
    { title: "Test2", status: "approved", type: "part-time", date: "2024-12-04" },
    { title: "Test3", status: "pending", type: "internship", date: "2024-12-03" },
  ];

  let filteredJobs = jobs.filter((job) => {
    return (
      (searchKeyword === "" || job.title.toLowerCase().includes(searchKeyword)) &&
      (jobStatus === "all" || job.status === jobStatus) &&
      (jobType === "all" || job.type === jobType)
    );
  });

  if (sort === "newest") {
    filteredJobs.sort((a, b) => new Date(b.date) - new Date(a.date));
  } else if (sort === "oldest") {
    filteredJobs.sort((a, b) => new Date(a.date) - new Date(b.date));
  }

  console.log("Filtered Jobs:", filteredJobs); // Filtrelenen işleri konsola yazdır
});

// DOM yüklendiğinde bu kodu çalıştır
document.addEventListener('DOMContentLoaded', function () {
  // İstatistik linkini seç
  const istatistikLink = document.getElementById('istatistik-link');
  // Tıklama olayını dinle
  if (istatistikLink) {
    istatistikLink.addEventListener('click', function (event) {
      event.preventDefault(); // Varsayılan davranışı engelle
      alert('Üzerinde çalışılıyor!'); // Alert mesajı
    });
  }
});


// user menu kısımındaki kontroller
let isLoggedIn = false; // Varsayılan olarak kullanıcı giriş yapmamış

function toggleDropdown() {
  const dropdownMenu = document.getElementById('dropdown-menu');
  dropdownMenu.style.display =
    dropdownMenu.style.display === 'block' ? 'none' : 'block';
}

function login() {
  isLoggedIn = true; // Kullanıcı giriş yapar
  updateUserMenu();
}

function logout() {
  isLoggedIn = false; // Kullanıcı çıkış yapar
  updateUserMenu();
}

function updateUserMenu() {
  const userName = document.getElementById('user-name');
  const userAvatar = document.getElementById('user-avatar');
  const loginButton = document.getElementById('login-button');
  const dropdownBtn = document.getElementById('dropdown-btn');

  if (isLoggedIn) {
    userName.textContent = 'USER'; // Kullanıcı adı
    userAvatar.src = 'user.png'; // Avatar
    userAvatar.style.display = 'block'; // Avatar görünür
    userName.style.display = 'block'; // Kullanıcı adı görünür
    dropdownBtn.style.display = 'inline-block'; // Dropdown butonu görünür
    loginButton.style.display = 'none'; // Giriş yap butonu gizlenir
  } else {
    userName.textContent = ''; // Kullanıcı adı gizlenir
    userAvatar.style.display = 'none'; // Avatar gizlenir
    userName.style.display = 'none'; // Kullanıcı adı gizlenir
    dropdownBtn.style.display = 'none'; // Dropdown butonu gizlenir
    loginButton.style.display = 'inline-block'; // Giriş yap butonu görünür
  }
}

// Sayfa yüklendiğinde menüyü güncelle
document.addEventListener('DOMContentLoaded', updateUserMenu);



// 1. Gerekli Butonları Seç
const darkModeBtn = document.getElementById("dark-mode-btn");
const highContrastBtn = document.getElementById("high-contrast-btn");

// 2. Mod Geçiş Fonksiyonları
function enableDarkMode() {
  document.body.classList.add("dark-mode");
  document.body.classList.remove("high-contrast"); // High Contrast devre dışı
  localStorage.setItem("theme", "dark-mode"); // Dark Mode tercihini kaydet
}

function enableHighContrast() {
  document.body.classList.add("high-contrast");
  document.body.classList.remove("dark-mode"); // Dark Mode devre dışı
  localStorage.setItem("theme", "high-contrast"); // High Contrast tercihini kaydet
}

// 3. Kullanıcı Tercihini Yükleme Fonksiyonu
function loadThemePreference() {
  const savedTheme = localStorage.getItem("theme"); // LocalStorage'den temayı al
  if (savedTheme === "dark-mode") {
    enableDarkMode();
  } else if (savedTheme === "high-contrast") {
    enableHighContrast();
  }
}

// 4. Event Listener'ları Ekle
darkModeBtn.addEventListener("click", enableDarkMode);
highContrastBtn.addEventListener("click", enableHighContrast);

// 5. Sayfa Yüklendiğinde Tercihi Kontrol Et
document.addEventListener("DOMContentLoaded", loadThemePreference);
