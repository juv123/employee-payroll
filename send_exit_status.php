<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // If using Composer
// require 'path/to/PHPMailer/src/PHPMailer.php'; // If using manually downloaded files
// require 'path/to/PHPMailer/src/Exception.php';
// require 'path/to/PHPMailer/src/SMTP.php';

function sendExitNotification($employeeEmail,$employeeName,$status) {
    $mail = new PHPMailer(true);
    

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       =  getenv('MAIL_HOST'); // Fetch from .env
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('MAIL_USERNAME'); // Fetch from .env
        $mail->Password   = getenv('MAIL_PASSWORD'); // Fetch from .env
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       =  getenv('MAIL_PORT');

        // Sender & recipient
        $mail->setFrom(getenv('MAIL_FROM_ADDRESS'), 'Admin Department');
        $mail->addAddress($employeeEmail,$employeeName, $status);

        // Email subject & body
        $subject = "Exit Request Initiated";
        $message = "Dear $employeeName,<br><br>";
        $message .= "Your Exit request has been  ".$status." <br><br>";
       $message .= "Best Regards,<br>Admin Department";

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Send email
        if ($mail->send()) {
            return true;
        }
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
