document.getElementById("toggle-btn").addEventListener("click", function () {
    const sidebar = document.querySelector(".sidebar");
    const dashboard = document.querySelector(".dashboard");
  
    // Sidebar'ın durumunu değiştirmek için "collapsed" sınıfını ekle/kaldır
    sidebar.classList.toggle("collapsed");
  
    // Dashboard'un konumunu ayarlamak için "expanded" sınıfını kullan
    dashboard.classList.toggle("expanded");
  });
  
  // Sayfa yüklendiğinde durum kontrolü
  document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const dashboard = document.querySelector(".dashboard");
  
    if (sidebar.classList.contains("collapsed")) {
      dashboard.classList.remove("expanded");
    } else {
      dashboard.classList.add("expanded");
    }
  });
  

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

