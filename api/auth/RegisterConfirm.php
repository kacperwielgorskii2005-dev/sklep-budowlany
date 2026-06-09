<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../frameworks/PHPMailer/src/Exception.php';
require '../../frameworks/PHPMailer/src/PHPMailer.php';
require '../../frameworks/PHPMailer/src/SMTP.php';

/**
 * Sends an account creation confirmation email
 *
 * @param string $email  - user's email
 * @param string $login  - user's login/username
 * @return bool          - true if sent, false if failed
 */
function sendAccountCreationEmail(string $email, string $login, string $body): bool
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'specmarketinfo@gmail.com';
        $mail->Password = 'vxba kvld ugau epfr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('specmarketinfo@gmail.com', 'SklepInternetowy');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Potwierdzenie utworzenia konta';
        $mail->Body = $body;
        $mail->AltBody = "Kliknij w link aby potwierdzić rejestrację: $body";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}



?>
