<?php
require 'config.php'; // Veritabanı bilgilerini içeren dosya
require 'vendor/autoload.php'; // PHPMailer otomatik yükleme

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
date_default_timezone_set('Europe/Istanbul');

header('Content-Type: application/json'); // JSON çıktısını belirt

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcıdan gelen e-posta adresini alın
    $email = trim($_POST['email']);

    // E-posta adresi kontrolü
    if (empty($email)) {
        echo json_encode(["status" => "error", "message" => "Lütfen e-posta adresinizi girin."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Geçersiz e-posta adresi."]);
        exit;
    }

    try {
        // Kullanıcıyı veritabanında kontrol et
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(["status" => "error", "message" => "Bu e-posta adresi sistemimizde kayıtlı değil."]);
            exit;
        }

        // Token oluştur
        $token = bin2hex(random_bytes(16)); // Güçlü bir token üretir
        $createdAt = date('Y-m-d H:i:s');

        // Tokenı veritabanına ekle veya güncelle
        $stmt = $db->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, ?)
                              ON DUPLICATE KEY UPDATE token = VALUES(token), created_at = VALUES(created_at)");
        $stmt->execute([$email, $token, $createdAt]);

        // Şifre sıfırlama bağlantısı
        $resetLink = "http://localhost/asistik/reset-password.php?token=$token";
        //$resetLink = "http://78.174.215.243/asistik/reset-password.php?token=$token";

        


        // PHPMailer ile e-posta gönderimi
        $mail = new PHPMailer(true);

        try {
            // SMTP ayarları
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP sunucusu
            $mail->SMTPAuth = true;
            $mail->Username = 'samet.saray.06@gmail.com'; // SMTP kullanıcı adı
            $mail->Password = 'sazjvbfajhwnketb'; // SMTP şifresi
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Gönderici ve alıcı bilgileri
            $mail->setFrom('samet.saray.06@gmail.com', 'Asistik');
            $mail->addAddress($email);

            // E-posta içeriği
            $mail->isHTML(true); // E-postayı HTML formatında gönder
            $mail->CharSet = 'UTF-8'; // Türkçe karakter desteği
            $mail->Subject = 'Şifre Sıfırlama Talebi';
            $mail->Body = "
                <p>Merhaba,</p>
                <p>Şifre sıfırlama bağlantınız:</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>Eğer bu işlemi siz başlatmadıysanız, lütfen bu e-postayı görmezden gelin.</p>
            ";

            $mail->send();
            echo json_encode(["status" => "success", "message" => "Şifre sıfırlama bağlantısı e-posta adresinize gönderildi."]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "E-posta gönderiminde bir sorun oluştu: " . $mail->ErrorInfo]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Bir hata oluştu: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Bu sayfaya doğrudan erişim yapılamaz."]);
    exit;
}
?>
