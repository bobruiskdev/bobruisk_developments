<?php

$servername = "localhost";
$username_db = "pryhazha";
$password_db = "Tis*7291911";
$dbname = "pryhazha";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
