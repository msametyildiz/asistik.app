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

  // Herhangi bir linke tıklandığında sidebar kapanır ve sayfa açılır
  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      sidebar.classList.add("collapsed");
    });
  });

  // Sayfa yüklendiğinde durum kontrolü
  if (sidebar.classList.contains("collapsed")) {
    dashboard.classList.remove("expanded");
  } else {
    dashboard.classList.add("expanded");
  }
});

// Dinamik veri alma ve filtreleme
document.getElementById("search-form").addEventListener("submit", async function (event) {
  event.preventDefault(); // Formun sayfayı yenilemesini engelle

  const searchKeyword = document.getElementById("search").value.toLowerCase();
  const jobStatus = document.getElementById("job-status").value;
  const jobType = document.getElementById("job-type").value;
  const sort = document.getElementById("sort").value;

  try {
    // API'den verileri al
    const response = await fetch("http://localhost:5000/jobs");
    const jobs = await response.json();

    // Filtreleme işlemi
    let filteredJobs = jobs.filter((job) => {
      return (
        (searchKeyword === "" || job.title.toLowerCase().includes(searchKeyword)) &&
        (jobStatus === "all" || job.status === jobStatus) &&
        (jobType === "all" || job.type === jobType)
      );
    });

    // Sıralama işlemi
    if (sort === "newest") {
      filteredJobs.sort((a, b) => new Date(b.date) - new Date(a.date));
    } else if (sort === "oldest") {
      filteredJobs.sort((a, b) => new Date(a.date) - new Date(b.date));
    }

    console.log("Filtered Jobs:", filteredJobs); // Filtrelenen işleri konsola yazdır
  }  catch (error) {
    console.error("Veriler alınırken bir hata oluştu:", error);
    alert("Veriler alınırken bir hata oluştu. Lütfen daha sonra tekrar deneyin.");
}
});

// User Menu için kontroller
let isLoggedIn = false;

function toggleDropdown() {
  const dropdownMenu = document.getElementById('dropdown-menu');
  dropdownMenu.style.display =
    dropdownMenu.style.display === 'block' ? 'none' : 'block';
}

function login() {
  isLoggedIn = true;
  updateUserMenu();
}

function logout() {
  isLoggedIn = false;
  updateUserMenu();
}

function updateUserMenu() {
  const userName = document.getElementById('user-name');
  const userAvatar = document.getElementById('user-avatar');
  const loginButton = document.getElementById('login-button');
  const dropdownBtn = document.getElementById('dropdown-btn');

  if (isLoggedIn) {
    userName.textContent = 'USER';
    userAvatar.src = 'user.png';
    userAvatar.style.display = 'block';
    userName.style.display = 'block';
    dropdownBtn.style.display = 'inline-block';
    loginButton.style.display = 'none';
  } else {
    userName.textContent = '';
    userAvatar.style.display = 'none';
    userName.style.display = 'none';
    dropdownBtn.style.display = 'none';
    loginButton.style.display = 'inline-block';
  }
}
function redirectToHome() {
  window.location.href = "index.php";
}
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
function editPosition(id, name, location, jobType, isOpen) {
  // Modal içindeki alanları doldur
  document.getElementById('edit_position_id').value = id;
  document.getElementById('edit_position_name').value = name;
  document.getElementById('edit_location').value = location;
  document.getElementById('edit_job_type').value = jobType;
  document.getElementById('edit_is_open').value = isOpen;

  // Modal'ı aç
  const editModal = new bootstrap.Modal(document.getElementById('editModal'));
  editModal.show();
}


function deletePosition(id) {
  if (confirm("Bu ilanı silmek istediğinize emin misiniz?")) {
      $.ajax({
          url: 'pages/delete_job.php',
          type: 'POST',
          data: {
              position_id: id
          },
          dataType: 'json',
          success: function(response) {
              if (response.success) {
                  alert(response.message);
                  location.reload(); // Sayfayı yeniler
              } else {
                  alert(response.message); // Başarısız mesajını gösterir
              }
          },
          error: function(xhr, status, error) {
              console.error("Silme işlemi sırasında bir hata oluştu: ", error);
              alert("Silme işlemi başarısız oldu. Lütfen tekrar deneyin.");
          }
      });
  }
}
