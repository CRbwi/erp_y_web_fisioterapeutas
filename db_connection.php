<?php
$servername = "192.168.1.234";
$username = "casaos";
$password = "casaos";
$dbname = "jorge_fisioterapia";
$port = 3306;

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Comprobar conexión
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>