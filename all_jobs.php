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
    header('Location: pages/girisyap.php');
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

    <?php include 'include/footer.php'; ?>
    <script src="assets/js/script.js"></script>
    <script>
        $(document).ready(function () {
            function fetchJobs(page = 1) {
                const formData = $('#search-form').serialize() + `&page=${page}`;
                $.get('all_jobs.php', formData, function (data) {
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
                }).fail(function () {
                    console.error("İş ilanları yüklenirken bir hata oluştu.");
                });
            }

            $('#search, #job-type, #sort').on('input change', function () {
                fetchJobs(1);
            });

            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                const page = $(this).attr('href').split('page=')[1];
                fetchJobs(page);
            });

            $('#reset-btn').on('click', function () {
                $('#search-form')[0].reset();
                fetchJobs(1);
                history.pushState(null, null, 'all_jobs.php');
            });
        });
    </script>
</body>

</html>
