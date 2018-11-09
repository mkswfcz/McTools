<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/11/6
 * Time: 下午3:22
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require APP_ROOT . '/vendor/autoload.php';

class Mailer
{
    static function send($subject, $to_addresses, $body, $attachments = array(), $is_html = false)
    {
        try {
            $mail = new PHPMailer(true);
            $mail->SMTPdebug = 2;
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = 'smtp.qq.com';
            $mail->Username = sys('mail_username');
            $mail->Password = sys('mail_password');
            $mail->Port = 587;
            $mail->setFrom(sys('mail_username'), sys('mail_from'));

            if (is_array($to_addresses)) {
                foreach ($to_addresses as $address) {
                    $mail->addAddress($address);
                }
            } else {
                $mail->addAddress($to_addresses);
            }
            if (count($attachments) > 0) {
                foreach ($attachments as $attachment) {
                    $mail->addAttachment($attachment);
                }
            }
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->isHTML($is_html);
            $mail->send();
        } catch (\Exception $e) {
            echoTip("Mail send Fail: {$mail->ErrorInfo}");
        }
    }
}