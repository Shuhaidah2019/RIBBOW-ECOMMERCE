<?php
// email-helper.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php'; // Adjust path to your vendor folder

/**
 * Send an email via Gmail SMTP using PHPMailer
 * 
 * @param string $to         Recipient email
 * @param string $toName     Recipient name (optional)
 * @param string $subject    Email subject
 * @param string $body       Email body (HTML or plain text)
 * @param bool   $isHTML     Whether body is HTML
 * @param string $replyTo    Reply-to email (optional)
 * @param string $replyToName Reply-to name (optional)
 * 
 * @return array ['success' => bool, 'error' => string|null]
 */
function sendEmail($to, $toName, $subject, $body, $isHTML = false, $replyTo = null, $replyToName = null) {
    $mail = new PHPMailer(true);

    try {
        // Gmail SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shuhaidahrabiu@gmail.com';       // Your Gmail
        $mail->Password   = 'gbatcztvipragdzy';         // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender
        $mail->setFrom('shuhaidahrabiu@gmail.com', 'RIBBOW');

        // Recipient
        $mail->addAddress($to, $toName);

        // Optional reply-to
        if ($replyTo) {
            $mail->addReplyTo($replyTo, $replyToName ?? '');
        }

        // Email content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();

        return ['success' => true, 'error' => null];
    } catch (Exception $e) {
        // Log error for debugging
        error_log("Email sending failed to $to: " . $mail->ErrorInfo);
        return ['success' => false, 'error' => $mail->ErrorInfo];
    }
}

/**
 * Send contact form email to admin
 */
function sendContactFormEmail($name, $email, $message) {
    $adminEmail = 'shuhaidahrabiu@gmail.com'; // Admin Gmail

    $subject = "New Contact Message from $name via RIBBOW";

    $body = "
        <h3>New message via RIBBOW contact form</h3>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
    ";

    return sendEmail($adminEmail, 'RIBBOW Admin', $subject, $body, true, $email, $name);
}
