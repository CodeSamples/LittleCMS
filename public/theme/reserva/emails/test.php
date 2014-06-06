<?php
ini_set("display_errors", true);
error_reporting(E_ALL);
define('BASE_URL', $_SERVER["HTTP_HOST"]);
$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
$mail_body = file_get_contents("http://".BASE_URL."/theme/reserva/emails/coupon_email.html", false, $context);
echo $mail_body;
?>