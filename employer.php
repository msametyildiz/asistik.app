<?php
require 'config.php'; // Veritabanı bağlantısı

session_start(); // Oturum başlat

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: girisyap.html'); // Oturum yoksa giriş sayfasına yönlendir
    exit;
}

// Kullanıcı rolünü kontrol et
$role = $_SESSION['role'] ?? '';

if ($role !== 'employer' && $role !== 'job-seeker') {
    header('Location: girisyap.html'); // Geçerli rol değilse yönlendir
    exit;
}

// İlanları veritabanından çek
$stmt = $db->prepare("
    SELECT positions.id, positions.position_name, sectors.sector_name, positions.is_open 
    FROM positions 
    JOIN sectors ON positions.sector_id = sectors.id 
    ORDER BY positions.created_at DESC
");
$stmt->execute();
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Yeni iş ilanı ekleme işlemi (sadece employer rolü için)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'employer') {
    $sector_id = $_POST['sector_id'];
    $position_name = trim($_POST['position_name']);

    if (!empty($sector_id) && !empty($position_name)) {
        $insertStmt = $db->prepare("
            INSERT INTO positions (employer_id, sector_id, position_name, is_open, created_at, updated_at) 
            VALUES (?, ?, ?, 1, NOW(), NOW())
        ");
        $insertStmt->execute([$_SESSION['user_id'], $sector_id, $position_name]);
        header('Location: employer.php');
        exit;
    } else {
        $error_message = 'Tüm alanları doldurunuz!';
    }
}

// Sektörleri çek (sadece employer rolü için)
$sectors = [];
if ($role === 'employer') {
    $sectorStmt = $db->prepare("SELECT id, sector_name FROM sectors WHERE is_open = 1");
    $sectorStmt->execute();
    $sectors = $sectorStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
