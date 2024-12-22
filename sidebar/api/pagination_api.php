<?php
require '../pages/config.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$job_type = isset($_GET['job_type']) ? trim($_GET['job_type']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';
$user_role = isset($_GET['user_role']) ? $_GET['user_role'] : '';
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

$sort_order = ($sort === 'oldest') ? 'ASC' : 'DESC';

$search_query = '';
$params = [];

if (!empty($search)) {
    $search_query .= " AND (positions.position_name LIKE ? OR sectors.sector_name LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if (!empty($job_type)) {
    $search_query .= " AND positions.job_type = ?";
    $params[] = $job_type;
}

$sql = "
    SELECT positions.id, positions.position_name, sectors.sector_name, positions.location, positions.updated_at, positions.job_type, positions.employer_id, positions.is_open
    FROM positions
    JOIN sectors ON positions.sector_id = sectors.id
    WHERE 1=1 $search_query
    ORDER BY positions.updated_at $sort_order
    LIMIT $limit OFFSET $offset
";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$countStmt = $db->prepare("
    SELECT COUNT(*) AS count
    FROM positions
    JOIN sectors ON positions.sector_id = sectors.id
    WHERE 1=1 $search_query
");
$countStmt->execute($params);
$total_count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
$total_pages = ceil($total_count / $limit);

$html = '';
foreach ($positions as $position) {
    $can_edit = $user_role === 'employer' && $position['employer_id'] === $user_id;
    $html .= "
        <div class='job-card'>
            <div class='job-card-header'>
                <div class='job-icon'>T</div>
                <div class='job-info'>
                    <h5 class='job-title'>" . htmlspecialchars($position['position_name']) . "</h5>
                    <p class='job-subtitle'>Sektör: " . htmlspecialchars($position['sector_name']) . "</p>
                    <p class='job-subtitle'>Durum: " . ($position['is_open'] ? 'Açık' : 'Kapalı') . "</p>
                </div>
            </div>
            <div class='job-card-body'>
                <p><span>📍</span> " . htmlspecialchars($position['location']) . "</p>
                <p><span>📅</span> " . date('M jS, Y', strtotime($position['updated_at'])) . "</p>
                <p><span>💼</span> " . htmlspecialchars($position['job_type']) . "</p>
            </div>
            <div class='job-card-footer'>
                " . ($can_edit ? "
                    <button class='btn btn-warning edit-btn' onclick='editPosition({$position['id']}, ...)'>Edit</button>
                    <button class='btn btn-danger delete-btn' onclick='deletePosition({$position['id']})'>Delete</button>
                " : "") . "
            </div>
        </div>
    ";
}

$pagination_html = '';
if ($total_pages > 1) {
    for ($i = 1; $i <= $total_pages; $i++) {
        $active_class = ($i == $page) ? 'active' : '';
        $pagination_html .= "<a href='#' class='pagination-link $active_class' data-page='$i'>$i</a>";
    }
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'html' => $html,
    'pagination' => $pagination_html,
    'total' => $total_count
]);
