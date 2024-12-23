<?php
require '../pages/config.php';

try {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $job_type = isset($_GET['job_type']) ? trim($_GET['job_type']) : '';
    $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';

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
        LIMIT ? OFFSET ?
    ";

    $params[] = $limit;
    $params[] = $offset;

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countStmt = $db->prepare("
        SELECT COUNT(*) AS count
        FROM positions
        JOIN sectors ON positions.sector_id = sectors.id
        WHERE 1=1 $search_query
    ");
    $countStmt->execute(array_slice($params, 0, count($params) - 2));
    $total_count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
    $total_pages = ceil($total_count / $limit);

    $html = '';
    if ($positions) {
        foreach ($positions as $position) {
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
                </div>
            ";
        }
    }

    $pagination = '';
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $page) ? 'active' : '';
        $pagination .= "<a href='#' class='pagination-link $active' data-page='$i'>$i</a>";
    }

    echo json_encode([
        'success' => true,
        'html' => $html,
        'pagination' => $pagination,
        'total' => $total_count
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'PDO Hatası: ' . $e->getMessage()
    ]);
}
