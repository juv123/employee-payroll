<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'env.php'; // Load environment variables
require 'vendor/autoload.php'; // If using Composer
// require 'path/to/PHPMailer/src/PHPMailer.php'; // If using manually downloaded files
// require 'path/to/PHPMailer/src/Exception.php';
// require 'path/to/PHPMailer/src/SMTP.php';

function sendEmailNotification($employeeEmail, $employeeName, $date, $status) {
    $mail = new PHPMailer(true);
    $date = date('d/m/Y', strtotime($date));

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       =  getenv('MAIL_HOST'); // Fetch from .env
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('MAIL_USERNAME'); // Fetch from .env
        $mail->Password   = getenv('MAIL_PASSWORD'); // Fetch from .env
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = getenv('MAIL_PORT');


        // Sender & recipient
        $mail->setFrom(getenv('MAIL_FROM_ADDRESS'), 'Admin Department');
        $mail->addAddress($employeeEmail, $employeeName);

        // Email subject & body
        $subject = "Leave Request Update";
        $message = "Dear $employeeName,<br><br>";
        $message .= "Your leave request on ".$date." has been <strong>$status</strong>.<br><br>";
        $message .= "If you have any questions, please contact Admin.<br><br>";
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
