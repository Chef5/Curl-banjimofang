<?php

require 'lib/PHPMailer/Exception.php';
require 'lib/PHPMailer/PHPMailer.php';
require 'lib/PHPMailer/SMTP.php';
ini_set('date.timezone','Asia/Shanghai');

/**
 * 发送邮件
 */
function sendEmail($isEmail, $smtpServer, $smtpPort, $email, $password, $name, $reName, $reEmail, $title, $body){
    if($isEmail && $smtpServer && $smtpPort && $email && $password && $reEmail && $body){$name ? $name : $name = "自动助手";
        $reName ? $reName : $reName = "尊贵的主人";
        $title ? $title : $title = "健康日报自动填写完成(".date('m-d').")";
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 1;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = $smtpServer;                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;       
            $mail->Username   = $email;                     // SMTP username
            $mail->Password   = $password;                               // SMTP password
            $mail->SMTPSecure = 'ssl';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = $smtpPort;                                    // TCP port to connect to
        
            //Recipients
            $mail->setFrom($email, $name);
            $mail->addAddress($reEmail, $reName);     // Add a recipient
            // $mail->addAddress('ellen@example.com');               // Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');
        
            // Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $title;
            $mail->Body    = $body;
            $mail->AltBody = htmlentities($body);
        
            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }else{
        return false;
    }
}

// 旧方法
// include('lib/Mailer.php');
// function sendEmail($isEmail, $smtpServer, $smtpPort, $email, $password, $name, $reName, $reEmail, $title, $body){
//     if($isEmail && $smtpServer && $smtpPort && $email && $password && $reEmail && $body){
//         $name ? $name : $name = "自动助手";
//         $reName ? $reName : $reName = "收件人昵称";
//         $title ? $title : $title = "健康日报自动报告";
//         $ok = (new Tx\Mailer())
//         ->setServer($smtpServer, $smtpPort)
//         ->setAuth($email, $password)
//         ->setFrom($name, $email)
//         ->addTo($reName, $reEmail)
//         ->setSubject($title)
//         ->setBody($body)
//         ->send();
//         return $ok;
//     }else{
//         return false;
//     }
// }

?>