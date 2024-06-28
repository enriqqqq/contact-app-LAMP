<?php // connect to database
$user = "username";           // change this to your username
$password = "password";       // change this to your password
$database = "contact_app";    // change this to your database name

$conn = new mysqli("localhost", $user, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
