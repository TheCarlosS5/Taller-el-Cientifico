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
    $ip_address = get_real_ip(); // Usamos nuestra nueva función para obtener la IP.
    
    $stmt = $conn->prepare("INSERT INTO logs_actividad (admin_id, actividad, descripcion, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $admin_id, $actividad, $descripcion, $ip_address);
    $stmt->execute();
    $stmt->close();
}

/**
 * Obtiene la dirección IP real del visitante, considerando proxies y balanceadores de carga.
 *
 * @return string La dirección IP del visitante.
 */
function get_real_ip() {
    // Revisa si la IP viene de un proxy compartido
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    // Revisa si la IP viene de un proxy
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    // Método estándar para obtener la IP remota
    return $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
}
