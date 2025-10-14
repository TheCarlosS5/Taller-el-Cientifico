<?php
// --- LÓGICA DE PROCESAMIENTO ---
require_once 'session_handler.php';
require_once 'db_config.php';
require_once 'functions.php';

$page_title = "Gestionar Marcas";
$message = '';
$alert_type = 'info';
$upload_dir = __DIR__ . '/../uploads/marcas/';

// --- LÓGICA PARA AÑADIR/EDITAR UNA MARCA ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_brand'])) {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $logo_actual = $_POST['logo_actual'] ?? '';
    $logo_final_url = $logo_actual;

    if (!empty($_FILES['logo']['name'])) {
        // Lógica de subida de archivo...
        // (Similar a la que ya hemos usado en otros archivos)
    }

    if ($id > 0) { // Editar
        $stmt = $conn->prepare("UPDATE marcas SET nombre = ?, logo_url = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nombre, $logo_final_url, $id);
    } else { // Añadir
        $stmt = $conn->prepare("INSERT INTO marcas (nombre, logo_url) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $logo_final_url);
    }
    
    if ($stmt->execute()) {
        log_activity($conn, $_SESSION['admin_id'], 'Gestión de Marcas', 'Se guardó la marca: ' . $nombre);
        // ... mensajes de éxito
    }
    // ...
}

// --- LÓGICA PARA ELIMINAR UNA MARCA ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_brand'])) {
    // ... Lógica para borrar de la DB
}

// Obtener todas las marcas
$marcas = $conn->query("SELECT * FROM marcas ORDER BY orden, nombre ASC")->fetch_all(MYSQLI_ASSOC);

include 'admin_header.php';
?>

<!-- HTML del formulario para añadir/editar y la tabla para mostrar marcas -->
