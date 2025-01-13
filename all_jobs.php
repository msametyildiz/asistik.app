<?php
ob_start();
session_start();
require 'pages/config.php';

// Başarı mesajını kontrol et ve göster
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Mesajı bir kez göstermek için temizle
} else {
    $success_message = null;
}
if (isset($success_message)) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Başarılı',
                text: '" . htmlspecialchars($success_message) . "',
                confirmButtonText: 'Tamam'
            });
        });
    </script>";
}

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

// Varsayılan parametreler
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Sayfa başına gösterilecek iş ilanı
$offset = ($page - 1) * $limit; // SQL OFFSET hesaplama

// Search sorgusu ve diğer filtreler
$search = isset($_GET['search']) ? $_GET['search'] : '';
$job_type = isset($_GET['job_type']) ? $_GET['job_type'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$search_query = "";
$params = [];
$sort_order = $sort === 'oldest' ? 'ASC' : 'DESC';

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

// Dinamik SQL sorgusu
$sql = "
    SELECT positions.id, positions.position_name, sectors.sector_name, positions.location, positions.updated_at, positions.job_type, positions.employer_id, positions.is_open
    FROM positions 
    JOIN sectors ON positions.sector_id = sectors.id 
    WHERE 1=1 $search_query
    ORDER BY (positions.employer_id = ?) DESC, positions.updated_at $sort_order
    LIMIT $limit OFFSET $offset
";
$params[] = $user_id; // Kullanıcı önceliği parametresi

$stmt = $db->prepare($sql);
$stmt->execute($params);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Toplam iş sayısını çek
$countStmt = $db->prepare("SELECT COUNT(*) AS count FROM positions JOIN sectors ON positions.sector_id = sectors.id WHERE 1=1 $search_query");
$countStmt->execute(array_slice($params, 0, count($params) - 1)); // Kullanıcı önceliği parametresini çıkarıyoruz
$job_count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
$total_pages = ceil($job_count / $limit);

// Sektör bilgileri
$sectorStmt = $db->prepare("SELECT id, sector_name FROM sectors WHERE is_open = 1");
$sectorStmt->execute();
$sectors = $sectorStmt->fetchAll(PDO::FETCH_ASSOC);

// İş ilanı güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    // Form verilerini al
    $position_id = $_POST['position_id'] ?? null;
    $position_name = trim($_POST['position_name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $job_type = $_POST['job_type'] ?? '';
    $sector_id = $_POST['sector_id'] ?? null;
    $is_open = $_POST['is_open'] ?? null;

    // Tüm alanlar doluysa düzenleme işlemini gerçekleştir
    if (!empty($position_id) && !empty($position_name) && !empty($location) && !empty($job_type) && !empty($sector_id) && isset($is_open)) {
        $updateStmt = $db->prepare("UPDATE positions SET position_name = ?, location = ?, job_type = ?, sector_id = ?, is_open = ?, updated_at = NOW() WHERE id = ? AND employer_id = ?");
        $updateStmt->execute([$position_name, $location, $job_type, $sector_id, $is_open, $position_id, $user_id]);

        // Başarı mesajını oturum değişkenine ekle
        $_SESSION['success_message'] = "Düzenleme başarıyla tamamlandı!";

        // Sayfayı yenile
        header('Location: all_jobs.php');
        exit;
    } else {
        // Hata mesajını göster
        $error_message = "Lütfen tüm alanları doldurunuz!";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    // Gelen ID'yi al
    $position_id = $_POST['position_id'] ?? null;

    // ID kontrolü
    if (!empty($position_id)) {
        // Pozisyonu sil
        $deleteStmt = $db->prepare("DELETE FROM positions WHERE id = ? AND employer_id = ?");
        $isDeleted = $deleteStmt->execute([$position_id, $user_id]);

        if ($isDeleted) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Pozisyon silinirken bir hata oluştu.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Geçersiz pozisyon ID.']);
    }
    exit;
}

if (!empty($success_message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Başarılı',
                text: '<?= htmlspecialchars($success_message) ?>',
                confirmButtonText: 'Tamam'
            });
        });
    </script>
<?php endif;


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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php if (!empty($success_message)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı',
                    text: '<?= htmlspecialchars($success_message) ?>',
                    confirmButtonText: 'Tamam'
                });
            });
        </script>
    <?php endif; ?>
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
                        <div class="job-card <?= ($user_role === 'employer' && $position['employer_id'] == $user_id) ? 'editable-card' : ''; ?>">
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

                <!-- Pagination Linkleri -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Önceki</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Sonraki</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </section>
        </main>
    </div>
    <!-- Düzenleme Modalı -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="position_id" id="edit_position_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pozisyon Düzenle</h5>
                        <button type="button" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem;">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="edit_position_name" class="form-label">Pozisyon Adı</label>
                            <input type="text" name="position_name" id="edit_position_name" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_location" class="form-label">Lokasyon</label>
                            <input type="text" name="location" id="edit_location" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_job_type" class="form-label">İş Türü</label>
                            <select name="job_type" id="edit_job_type" class="form-control" required>
                                <option value="full-time">Full-Time</option>
                                <option value="part-time">Part-Time</option>
                                <option value="internship">Internship</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_sector_id" class="form-label">Sektör</label>
                            <select name="sector_id" id="edit_sector_id" class="form-control" required>
                                <?php foreach ($sectors as $sector): ?>
                                    <option value="<?= $sector['id'] ?>"><?= htmlspecialchars($sector['sector_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_is_open" class="form-label">Durum</label>
                            <select name="is_open" id="edit_is_open" class="form-control" required>
                                <option value="1">Açık</option>
                                <option value="0">Kapalı</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-primary" style="background-color: #17a2b8;">Kaydet</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php include 'include/footer.php'; ?>
    <script src="assets/js/script.js"></script>
    <script>
        $(document).ready(function() {
            function fetchJobs(page = 1) {
                const formData = $('#search-form').serialize() + `&page=${page}`;
                $.get('all_jobs.php', formData, function(data) {
                    const jobCards = $(data).find('#job-cards').html();
                    const pagination = $(data).find('.pagination').html();
                    const jobCount = $(data).find('#job-count-title').text();

                    if (jobCards && pagination && jobCount) {
                        $('#job-cards').html(jobCards);
                        $('.pagination').html(pagination);
                        $('#job-count-title').text(jobCount);
                    } else {
                        console.error("Gelen veriler eksik veya hatalı!");
                    }
                }).fail(function() {
                    console.error("İş ilanları yüklenirken bir hata oluştu.");
                });
            }

            $('#search, #job-type, #sort').on('input change', function() {
                fetchJobs(1);
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const page = $(this).attr('href').split('page=')[1];
                fetchJobs(page);
            });

            $('#reset-btn').on('click', function() {
                $('#search-form')[0].reset();
                fetchJobs(1);
                history.pushState(null, null, 'all_jobs.php');
            });
        });

        function editPosition(id, name, location, jobType, isOpen) {
            // Modal alanlarını doldur
            document.getElementById('edit_position_id').value = id;
            document.getElementById('edit_position_name').value = name;
            document.getElementById('edit_location').value = location;
            document.getElementById('edit_job_type').value = jobType;
            document.getElementById('edit_is_open').value = isOpen;

            // Modal'ı göster
            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }
        if (isset($_SESSION['success_message'])) {
            $success_message = $_SESSION['success_message'];
            unset($_SESSION['success_message']); // Mesajı bir kez göstermek için temizle
        } else {
            $success_message = null;
        }

        function deletePosition(id) {
            Swal.fire({
                title: 'İş Pozisyonu Silme İşlemi Onayı',
                text: "Bu İşi silmek üzeresiniz. Bu işlem geri alınamaz. Devam etmek istediğinizden emin misiniz?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, silmek istiyorum',
                cancelButtonText: 'Hayır, vazgeç',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('all_jobs.php', {
                        action: 'delete',
                        position_id: id
                    }, function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Silindi!',
                                'Sektör başarıyla silindi.',
                                'success'
                            ).then(() => {
                                location.reload(); // Sayfayı yenile
                            });
                        } else {
                            Swal.fire(
                                'Hata!',
                                response.message || 'Sektör silinirken bir hata oluştu.',
                                'error'
                            );
                        }
                    }, 'json').fail(function() {
                        Swal.fire(
                            'Hata!',
                            'Sektör silinirken bir hata oluştu.',
                            'error'
                        );
                    });
                }
            });
        }
    </script>
</body>

</html>