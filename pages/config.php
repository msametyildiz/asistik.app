<?php
// Veritabanı bağlantı bilgileri
$host = 'localhost';        // Veritabanı sunucusu
$dbname = 'asistik';        // Veritabanı adı
$username = 'root';         // Veritabanı kullanıcı adı
$password = '';             // Veritabanı şifresi

try {
    // PDO ile veritabanı bağlantısı oluştur
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Hata durumunda kullanıcıya genel bir mesaj göster
    die("Veritabanı bağlantısı başarısız. Lütfen daha sonra tekrar deneyin.");
    // Hata ayıklama için log dosyasına hata mesajını yazabilirsiniz
    // error_log($e->getMessage());
}
?>
