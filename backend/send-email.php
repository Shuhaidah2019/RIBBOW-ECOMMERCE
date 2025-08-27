<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php'; // Composer's autoloader

function sendOrderEmail($toEmail, $toName, $orderHtml, $subject = "Your RIBBOW Order Confirmation") {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Or use another SMTP if not Gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shuhaidahrabiu@gmail.com'; // 
        $mail->Password   = 'gbatcztvipragdzy';     // 🔁 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom('shuhaidahrabiu@gmail.com', 'RIBBOW');
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $orderHtml;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Email error: {$mail->ErrorInfo}";
    }
}

