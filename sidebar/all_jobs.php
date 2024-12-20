<?php
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
    SELECT positions.id, positions.position_name, sectors.sector_name, positions.location, positions.updated_at, positions.job_type, positions.employer_id
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
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASİSTİK</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/all_job_style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                                    <p class="job-subtitle"><?= htmlspecialchars($position['sector_name']); ?></p>
                                </div>
                            </div>
                            <div class="job-card-body">
                                <p><span>📍</span> <?= htmlspecialchars($position['location']); ?></p>
                                <p><span>📅</span> <?= date('M jS, Y', strtotime($position['updated_at'])); ?></p>
                                <p><span>💼</span> <?= htmlspecialchars($position['job_type']); ?></p>
                            </div>
                            <div class="job-card-footer">
                                <?php if ($user_role === 'employer' && $position['employer_id'] == $user_id): ?>
                                    <button class="btn edit-btn" onclick="editJob(<?= $position['id'] ?>)">Edit</button>
                                    <button class="btn delete-btn" onclick="deleteJob(<?= $position['id'] ?>)">Delete</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
    <?php include 'include/footer.php'; ?>
    <script src="assets/js/script.js"></script>

    <script>
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
    </script>
</body>

</html>