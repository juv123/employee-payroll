<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        $mail->Host       = 'smtp.gmail.com'; // Change this to your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'deegaaug'; // Your email
        $mail->Password   = 'jatjwjzzokvqswoj'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender & recipient
        $mail->setFrom('deegaaug@gmail.com', 'Admin Department');
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
