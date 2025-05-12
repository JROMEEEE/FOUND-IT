<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require_once __DIR__ . '/vendor/autoload.php';

function sendClaimNotificationEmail($recipientEmail, $recipientName, $subject, $messageBody, $supporting_image = null) {
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      
        $mail->isSMTP();                                         
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                              
        $mail->Username   = 'patric.mapa@gmail.com';             
        $mail->Password   = 'zybr tzrw dkeq rkzj';             
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      
        $mail->Port       = 587;                                 

        // Recipients
        $mail->setFrom('patric.mapa@gmail.com', 'Lost & Found System');
        $mail->addAddress($recipientEmail, $recipientName);      // Add a recipient

        // Content
        $mail->isHTML(true);                                     
        $mail->Subject = $subject;
        $mail->Body    = $messageBody;
        $mail->AltBody = strip_tags($messageBody);              

        if (!empty($supporting_image)) {
            $image_path = __DIR__ . '/' . $supporting_image;
            if (file_exists($image_path)) {
                $mail->addEmbeddedImage($image_path, 'supporting_image');
            }
        }

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>
