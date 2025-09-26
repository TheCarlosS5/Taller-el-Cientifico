<?php
// Este archivo contendrá funciones útiles para nuestro panel de administración.

/**
 * Registra una actividad del administrador en la base de datos.
 *
 * @param mysqli $conn La conexión a la base de datos.
 * @param int $admin_id El ID del administrador que realiza la acción.
 * @param string $actividad Un título corto para la actividad (e.g., 'Inicio de Sesión').
 * @param string $descripcion Un texto más detallado de la acción.
 */
function log_activity($conn, $admin_id, $actividad, $descripcion = '') {
    // Usamos la variable global $_SERVER para obtener la IP. Es más fiable.
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
    
    $stmt = $conn->prepare("INSERT INTO logs_actividad (admin_id, actividad, descripcion, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $admin_id, $actividad, $descripcion, $ip_address);
    $stmt->execute();
    $stmt->close();
}
