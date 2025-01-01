<?php
date_default_timezone_set("Asia/Jakarta");
session_start();

$databaseHost = 'localhost';
$databaseName = 'learning_management';
$databaseUsername = 'root';
$databasePassword = '';

$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

if (mysqli_connect_errno()) {
    echo''. mysqli_connect_error();
}
