<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "root";
$dbName = "projectwebfinal";
$conn =mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Could not connect to database");
}

?>