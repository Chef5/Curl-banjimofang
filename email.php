<?php

include('lib/Mailer.php');
/**
 * 发送邮件
 */
function sendEmail($isEmail, $smtpServer, $smtpPort, $email, $password, $name, $reName, $reEmail, $title, $body){
    if($isEmail && $smtpServer && $smtpPort && $email && $password && $reEmail && $body){
        $name ? $name : $name = "自动助手";
        $reName ? $reName : $reName = "收件人昵称";
        $title ? $title : $title = "健康日报自动报告";
        $ok = (new Tx\Mailer())
        ->setServer($smtpServer, $smtpPort)
        ->setAuth($email, $password)
        ->setFrom($name, $email)
        ->addTo($reName, $reEmail)
        ->setSubject($title)
        ->setBody($body)
        ->send();
        return $ok;
    }else{
        return false;
    }
}

?>