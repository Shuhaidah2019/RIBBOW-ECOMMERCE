<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';  // Composer autoload

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/index.php#contact");
    exit();
}

// Sanitize inputs
$name = trim(filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING));
$email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
$message = trim(filter_var($_POST['message'] ?? '', FILTER_SANITIZE_STRING));

// Basic validation
if (!$name || !$email || !$message) {
    $_SESSION['contact_error'] = "Please fill in all fields.";
    header("Location: ../public/index.php#contact");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = "Invalid email address.";
    header("Location: ../public/index.php#contact");
    exit();
}

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'shuhaidahrabiu@gmail.com'; // Your Gmail
    $mail->Password   = 'gbatcztvipragdzy';         // Your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // --- 1️⃣ Send to Admin ---
    $mail->setFrom('no-reply@ribbow.com', 'RIBBOW Contact Form');
    $mail->addAddress('shuhaidahrabiu@gmail.com'); // Your admin email
    $mail->addReplyTo($email, $name);

    $mail->isHTML(false);
    $mail->Subject = "New Contact Message from $name via RIBBOW";
    $body  = "You got a new message from the RIBBOW contact form!\n\n";
    $body .= "Name: $name\n";
    $body .= "Email: $email\n\n";
    $body .= "Message:\n$message\n";
    $mail->Body = $body;
    $mail->send();

    // --- 2️⃣ Send Confirmation to Customer ---
$mail->clearAddresses();
$mail->addAddress($email, $name);

$mail->isHTML(true);
$mail->Subject = "Thanks for contacting RIBBOW 💌";
$mail->Body = "
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; background-color: #faf8ff; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5d9ff; }
    .header { background: #c8a2ff; padding: 20px; text-align: center; }
    .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
    .content { padding: 20px; color: #4a4a4a; }
    .message { border-left: 4px solid #c8a2ff; padding-left: 10px; margin: 15px 0; color: #555; background: #f6f0ff; border-radius: 4px; }
    .button { display: inline-block; padding: 10px 20px; background: #c8a2ff; color: #ffffff; text-decoration: none; border-radius: 5px; }
    .footer { text-align: center; font-size: 12px; color: #999; padding: 15px; }
  </style>
</head>
<body>
  <div class='container'>
    <div class='header'>
      <h1>RIBBOW</h1>
    </div>
    <div class='content'>
      <p>Hi <strong>{$name}</strong>,</p>
      <p>Thanks for reaching out to <strong>RIBBOW</strong>! 💜<br>
      We’ve received your message and will get back to you soon.</p>
      <p>Here’s a copy of what you sent:</p>
      <div class='message'>{$message}</div>
      <p>If this wasn’t you, please ignore this email.</p>
      <a href='http://localhost/RIBBOW/public/' class='button'>Visit Our Store</a>
    </div>
    <div class='footer'>
      &copy; " . date('Y') . " RIBBOW. All rights reserved.
    </div>
  </div>
</body>
</html>
";

$mail->AltBody = "Hi $name,\n\nThanks for reaching out to RIBBOW! We received your message and will get back to you ASAP.\n\nMessage:\n$message";
$mail->send();


    $mail->AltBody = "Hi $name,\n\nThanks for reaching out to RIBBOW! We received your message and will get back to you ASAP.\n\nMessage:\n$message";

    $mail->send();

    echo '<div class="contact-success">Thank you for reaching out! We\'ll get back to you soon.</div>';
} catch (Exception $e) {
    $_SESSION['contact_error'] = "Oops! Something went wrong: {$mail->ErrorInfo}";
}

header("Location: ../public/index.php#contact");
exit();



