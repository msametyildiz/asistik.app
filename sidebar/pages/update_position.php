<?php
require '../config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position_id = $_POST['position_id'];
    $position_name = $_POST['position_name'];
    $location = $_POST['location'];
    $job_type = $_POST['job_type'];
    $sector_id = $_POST['sector_id'];

    if (!empty($position_id) && !empty($position_name) && !empty($location) && !empty($job_type) && !empty($sector_id)) {
        $stmt = $db->prepare("UPDATE positions SET position_name = ?, location = ?, job_type = ?, sector_id = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$position_name, $location, $job_type, $sector_id, $position_id]);

        echo "Başarıyla güncellendi!";
    } else {
        echo "Tüm alanlar doldurulmalıdır!";
    }
}
?>
