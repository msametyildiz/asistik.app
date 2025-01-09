    <?php
    session_start();
    require 'config.php';

    // POST verisinin gönderilip gönderilmediğini ve oturumun geçerli olup olmadığını kontrol edin
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['position_id']) && isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'employer') {
        $position_id = $_POST['position_id'];
        $user_id = $_SESSION['user_id'];

        try {
            // SQL sorgusunu çalıştır
            $stmt = $db->prepare("DELETE FROM positions WHERE id = ? AND employer_id = ?");
            $stmt->execute([$position_id, $user_id]);

            if ($stmt->rowCount()) {
                echo json_encode(['success' => true, 'message' => 'İlan başarıyla silindi.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'İlan silinemedi. Yetkilendirme hatası veya geçersiz ID.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Geçersiz istek. Gerekli veriler eksik.']);
    }
    ?>
