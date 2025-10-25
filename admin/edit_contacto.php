<?php
$page_title = "Editar Página de Contacto";
include 'admin_header.php';
require_once 'db_config.php'; // Asegúrate de tener la conexión a la BD
require_once 'functions.php'; // Y las funciones necesarias

// Lógica para procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = '';
    $error = false;
    
    $stmt = $conn->prepare("UPDATE contenido_editable SET valor = ? WHERE id = ?");

    if (isset($_POST['content']) && is_array($_POST['content'])) {
        foreach ($_POST['content'] as $id => $value) {
            $id = intval($id);
            // Limpiamos los datos para evitar inyecciones de código
            $sanitized_value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

            $stmt->bind_param("si", $sanitized_value, $id);
            if (!$stmt->execute()) {
                $error = true;
                $message = "<div class='alert alert-danger'>Error al actualizar el contenido.</div>";
                break;
            }
        }
    }

    if (!$error) {
        log_activity($conn, $_SESSION['admin_id'], 'Actualización de Contenido', "Se guardaron cambios en la Página de Contacto.");
        $message = "<div class='alert alert-success'>¡Información de contacto actualizada correctamente!</div>";
    }
}

// --- ÚNICO CAMBIO AQUÍ ---
// Lógica para obtener el contenido actual de la base de datos
// Ahora la consulta trae los campos de 'contacto' Y los campos 'direccion' y 'barrio' de la sección 'ubicacion'
$sql = "
    SELECT id, campo, valor, descripcion, tipo 
    FROM contenido_editable 
    WHERE seccion = 'contacto' OR (seccion = 'ubicacion' AND campo IN ('direccion', 'barrio'))
    ORDER BY FIELD(campo, 'direccion', 'barrio', 'telefono', 'email', 'whatsapp')
";
$result = $conn->query($sql);
$contents = $result->fetch_all(MYSQLI_ASSOC);

?>

<h1 class="mb-4"><?php echo $page_title; ?></h1>
<p>Desde aquí puedes cambiar los datos de contacto y la dirección que se muestran en la página.</p>

<?php if (isset($message)) { echo $message; } ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="edit_contacto.php">
            <?php foreach ($contents as $item): ?>
                <div class="mb-3">
                    <label for="content-<?php echo $item['id']; ?>" class="form-label">
                        <strong><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $item['campo']))); ?></strong>
                        <?php if (!empty($item['descripcion'])): ?>
                            <small class="text-muted d-block"><?php echo htmlspecialchars($item['descripcion']); ?></small>
                        <?php endif; ?>
                    </label>
                    
                    <?php if (isset($item['tipo']) && $item['tipo'] == 'textarea'): ?>
                        <textarea class="form-control" id="content-<?php echo $item['id']; ?>" name="content[<?php echo $item['id']; ?>]" rows="3"><?php echo htmlspecialchars($item['valor']); ?></textarea>
                    <?php else: ?>
                        <input type="text" class="form-control" id="content-<?php echo $item['id']; ?>" name="content[<?php echo $item['id']; ?>]" value="<?php echo htmlspecialchars($item['valor']); ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
</div>

<?php include 'admin_footer.php'; ?>