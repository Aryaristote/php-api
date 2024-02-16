<?php

$host = "localhost";
$username = "root";
$passwoord = "";
$dbname = "php-api";

$conn = mysqli_connect($host, $username, $passwoord, $dbname);
if(!$conn){
    die("Could not connect to". mysqli_connect_error());
}
