<?php
require 'vendor/autoload.php'; // Composer autoload dosyasını dahil et

use Samet\Asistik\Mailer; // Mailer sınıfını dahil et

// Mailer sınıfını başlat
$mailer = new Mailer();

// Test e-posta gönderimi
$result = $mailer->sendMail(
    'recipient@example.com', // Hedef e-posta adresi (burayı kendi e-postanızla değiştirin)
    'Test E-posta',          // Konu
    '<p>Bu bir test e-postasıdır. Eğer bu mesajı görüyorsanız SMTP ayarlarınız başarılıdır!</p>' // Mesaj
);

// Sonucu yazdır
echo $result;
