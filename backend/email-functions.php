<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Composer's autoloader

function sendRibbowEmail($to, $subject, $htmlMessage) {
  $mail = new PHPMailer(true);

  try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'shuhaidahrabiu@gmail.com';   // my Gmail
    $mail->Password   = 'gbatcztvipragdzy';           // Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('shuhaidahrabiu@gmail.com', 'RIBBOW');
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $htmlMessage;

    $mail->send();
    return true;

  } catch (Exception $e) {
    error_log("RIBBOW Email Error: " . $mail->ErrorInfo);
    return false;
  }
}
?>
