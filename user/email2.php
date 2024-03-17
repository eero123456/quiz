<?php


$headers = array(
    "Content-type" => "text/html; charset=UTF-8",
    'From' => 'webmaster@example.com',
    'Reply-To' => 'webmaster@example.com',
);

$to = "aku@ankka.com";
$subject = "Aihe";

$message = "<p>Hei</p>";

mail($to, $subject, $message, $headers);
