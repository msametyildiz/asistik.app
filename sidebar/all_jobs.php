<?php
ob_start();
session_start();
require '../config.php';

// Kullanıcı oturumda mı?
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Misafir';

// Oturum kontrolü
if (!$isLoggedIn) {
    header('Location: pages/girisyap.php');
    exit;
}

// Kullanıcı bilgileri
$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];

// İş ilanlarını veritabanından çek
$search_query = '';
$params = [];
$sort_order = 'DESC'; // Varsayılan sıralama

// Search formundan gelen veriler
$search = isset($_GET['search']) ? $_GET['search'] : '';
$job_type = isset($_GET['job_type']) ? $_GET['job_type'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Search (position_name ve sector_name'de arama)
if (!empty($search)) {
    $search_query .= " AND (positions.position_name LIKE ? OR sectors.sector_name LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

// Job Type (job_type'ye göre filtreleme)
if (!empty($job_type)) {
    $search_query .= " AND positions.job_type = ?";
    $params[] = $job_type;
}

// Sort (updated_at'e göre sıralama)
$sort_order = $sort === 'oldest' ? 'ASC' : 'DESC';

// Dinamik SQL sorgusu
$sql = "
    SELECT positions.id, positions.position_name, sectors.sector_name, positions.location, positions.updated_at, positions.job_type, positions.employer_id, positions.is_open
    FROM positions 
    JOIN sectors ON positions.sector_id = sectors.id 
    WHERE 1=1 $search_query
    ORDER BY (positions.employer_id = ?) DESC, positions.updated_at $sort_order
";
$params[] = $user_id; // Kullanıcı önceliği parametresi

// Sorguyu hazırlayıp çalıştırma
$stmt = $db->prepare($sql);
$stmt->execute($params);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// İş ilanı sayısını çek
$countStmt = $db->prepare("SELECT COUNT(*) AS count FROM positions JOIN sectors ON positions.sector_id = sectors.id WHERE 1=1 $search_query");
$countStmt->execute(array_slice($params, 0, count($params) - 1)); // Kullanıcı önceliği parametresini çıkarıyoruz
$job_count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];

// Sektör bilgileri
$sectorStmt = $db->prepare("SELECT id, sector_name FROM sectors WHERE is_open = 1");
$sectorStmt->execute();
$sectors = $sectorStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASİSTİK</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/all_job_style.css">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/asistik_logo.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .dashboard-content {
            min-height: auto !important;
        }
        .btn {
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 14px;
}
.btn-warning {
    background-color: #17a2b8;
    color: #fff;
}
.btn-danger {
    background-color: #dc3545;
    color: #fff;
}

    </style>
</head>

<body>
    <div id="root">
        <?php include 'include/sidebar.php'; ?>
        <main class="dashboard">
            <?php include 'include/navbar.php'; ?>
            <section class="filter-form">
                <h4 class="filter-title">Search Form</h4>
                <form id="search-form" method="GET">
                    <div class="form-row">
                        <label for="search">Search</label>
                        <input type="text" id="search" name="search" placeholder="Enter job title">
                    </div>
                    <div class="form-row">
                        <label for="job-type">Job Type</label>
                        <select id="job-type" name="job_type">
                            <option value="">All</option>
                            <option value="full-time">Full-Time</option>
                            <option value="part-time">Part-Time</option>
                            <option value="internship">Internship</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="sort">Sort</label>
                        <select id="sort" name="sort">
                            <option value="newest">Newest</option>
                            <option value="oldest">Oldest</option>
                        </select>
                    </div>
                    <button type="button" class="reset-btn" id="reset-btn">Reset</button>
                </form>
            </section>
            <section class="dashboard-content">
                <h4 class="dashboard-title" id="job-count-title"><?= $job_count ?> İş Bulundu</h4>
                <div class="job-cards-container" id="job-cards">
                    <?php foreach ($positions as $position): ?>
                        <div class="job-card">
                            <div class="job-card-header">
                                <div class="job-icon">T</div>
                                <div class="job-info">
                                    <h5 class="job-title"><?= htmlspecialchars($position['position_name']); ?></h5>
                                    <p class="job-subtitle">Sektör: <?= htmlspecialchars($position['sector_name']); ?></p>
                                    <p class="job-subtitle">Durum: <?= $position['is_open'] ? 'Açık' : 'Kapalı'; ?></p>
                                </div>
                            </div>
                            <div class="job-card-body">
                                <p><span>📍</span> <?= htmlspecialchars($position['location']); ?></p>
                                <p><span>📅</span> <?= date('M jS, Y', strtotime($position['updated_at'])); ?></p>
                                <p><span>💼</span> <?= htmlspecialchars($position['job_type']); ?></p>
                            </div>
                            <div class="job-card-footer">
                                <?php if ($user_role === 'employer' && $position['employer_id'] == $user_id): ?>
                                    <button class="btn btn-warning edit-btn" onclick="editPosition(<?= $position['id'] ?>, '<?= htmlspecialchars($position['position_name']) ?>', '<?= htmlspecialchars($position['location']) ?>', '<?= htmlspecialchars($position['job_type']) ?>', <?= $position['is_open'] ?>)">Edit</button>
                                    <button class="btn btn-danger delete-btn" onclick="deletePosition(<?= $position['id'] ?>)">Delete</button>
                                <?php endif; ?>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Düzenleme Modalı -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="position_id" id="edit_position_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pozisyon Düzenle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_position_name" class="form-label">Pozisyon Adı</label>
                            <input type="text" name="position_name" id="edit_position_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_location" class="form-label">Lokasyon</label>
                            <input type="text" name="location" id="edit_location" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_job_type" class="form-label">İş Türü</label>
                            <select name="job_type" id="edit_job_type" class="form-control" required>
                                <option value="full-time">Full-Time</option>
                                <option value="part-time">Part-Time</option>
                                <option value="internship">Internship</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_sector_id" class="form-label">Sektör</label>
                            <select name="sector_id" id="edit_sector_id" class="form-control" required>
                                <?php foreach ($sectors as $sector): ?>
                                    <option value="<?= $sector['id'] ?>"><?= htmlspecialchars($sector['sector_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_is_open" class="form-label">Durum</label>
                            <select name="is_open" id="edit_is_open" class="form-control" required>
                                <option value="1">Açık</option>
                                <option value="0">Kapalı</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <?php include 'include/footer.php'; ?>
    <script src="assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
            data: { position_id: id },
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



        $(document).ready(function() {
            // İş ilanlarını dinamik olarak getirir
            function fetchJobs() {
                $.get('all_jobs.php', $('#search-form').serialize(), function(data) {
                    $('#job-cards').html($(data).find('#job-cards').html());
                    $('#job-count-title').text($(data).find('#job-count-title').text());
                });
            }

            // Search kısmında her harf yazıldığında çalışır
            $('#search').on('keyup', function() {
                fetchJobs();
            });

            // Job Type ve Sort seçenekleri değiştiğinde çalışır
            $('#job-type, #sort').on('change', function() {
                fetchJobs();
            });

            // Reset butonuna tıklandığında filtreleri sıfırlar
            $('#reset-btn').on('click', function() {
                $('#search-form')[0].reset(); // Formu sıfırlar
                fetchJobs(); // Varsayılan listeyi getirir
            });
        });

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

    <?php
    // İş ilanı güncelleme işlemi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update' && $user_role === 'employer') {
        $position_id = $_POST['position_id'] ?? null;
        $position_name = trim($_POST['position_name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $job_type = $_POST['job_type'] ?? '';
        $sector_id = $_POST['sector_id'] ?? null;
        $is_open = $_POST['is_open'] ?? null;

        if (!empty($position_id) && !empty($position_name) && !empty($location) && !empty($job_type) && !empty($sector_id) && isset($is_open)) {
            $updateStmt = $db->prepare("UPDATE positions SET position_name = ?, location = ?, job_type = ?, sector_id = ?, is_open = ?, updated_at = NOW() WHERE id = ? AND employer_id = ?");
            $updateStmt->execute([$position_name, $location, $job_type, $sector_id, $is_open, $position_id, $user_id]);
            header('Location: all_jobs.php');
            exit;
        } else {
            echo '<script>alert("Lütfen tüm alanları doldurunuz!");</script>';
        }
    }
    ?>
</body>

</html>