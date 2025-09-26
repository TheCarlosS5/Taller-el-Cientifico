<?php
// --- CONFIGURACIÓN DE LA ZONA HORARIA ---
// Esto asegura que todas las fechas y horas se manejen con la hora de Colombia.
date_default_timezone_set('America/Bogota');

// --- NÚMERO DE WHATSAPP DEL TALLER ---
// Reemplaza este número por el número real del taller, incluyendo el código del país sin el '+'
define('WHATSAPP_TALLER', '573203150231'); 

// --- DATOS DE CONEXIÓN A LA BASE DE DATOS ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "taller_el_cientifico";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}

// --- ESTABLECER ZONA HORARIA Y JUEGO DE CARACTERES PARA LA CONEXIÓN ---
// Le decimos a MySQL que trabaje en la zona horaria de Colombia (UTC-5)
$conn->query("SET time_zone = '-05:00'");
// Aseguramos que los datos se guarden y lean con codificación UTF-8 (para tildes y ñ)
$conn->set_charset("utf8mb4");

?>

