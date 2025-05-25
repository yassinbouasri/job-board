<?php
$host = "smtp.mailtrap.io";
$port = 2525;

$socket = @fsockopen($host, $port, $errno, $errstr, 5);

if (!$socket) {
    die("Connection failed: $errstr ($errno)");
}

echo "Connected successfully!\n";
fclose($socket);
