<?php

namespace Samet\Asistik;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    public function sendMail($to, $subject, $message)
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP ayarları
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP sunucusu
            $mail->SMTPAuth = true;
            $mail->Username = 'info.asistik@gmail.com'; // SMTP kullanıcı adı
            $mail->Password = 'mody xldl biyv hfmg'; // SMTP şifresi veya uygulama şifresi
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Şifreleme yöntemi
            $mail->Port = 587; // SMTP portu

            // Gönderici ve Alıcı Bilgileri
            $mail->setFrom('msametyildiz.msy@gmail.com', 'Asistik');
            $mail->addAddress($to);

            // İçerik
            $mail->isHTML(true); // HTML formatında e-posta gönderimi
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            return 'E-posta başarıyla gönderildi.';
        } catch (Exception $e) {
            return "E-posta gönderilemedi: {$mail->ErrorInfo}";
        }
    }
}
