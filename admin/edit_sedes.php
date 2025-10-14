<?php
// --- LÓGICA DE PROCESAMIENTO PRIMERO ---
require_once 'session_handler.php';
require_once 'db_config.php';
require_once 'functions.php';

$message = '';
$alert_type = 'info';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lógica para procesar la subida de la nueva imagen
    if (isset($_FILES['imagen_sede']) && $_FILES['imagen_sede']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/sedes/';
        
        if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
            $message = "Error: El directorio 'uploads/sedes/' no existe o no tiene permisos de escritura.";
            $alert_type = 'danger';
        } else {
            $file_tmp_path = $_FILES['imagen_sede']['tmp_name'];
            $file_name = basename($_FILES['imagen_sede']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($file_ext, $allowed_ext)) {
                $new_file_name = 'sede_' . time() . '.' . $file_ext;
                $dest_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp_path, $dest_path)) {
                    $imagen_url = 'uploads/sedes/' . $new_file_name;
                    // Actualizar la URL de la imagen en la base de datos
                    $stmt_img = $conn->prepare("UPDATE contenido_editable SET valor = ? WHERE seccion = 'ubicacion' AND campo = 'imagen_url'");
                    $stmt_img->bind_param("s", $imagen_url);
                    $stmt_img->execute();
                    $stmt_img->close();
                } else {
                    $message = "Error al mover la imagen subida.";
                    $alert_type = 'danger';
                }
            } else {
                $message = "Formato de archivo no permitido.";
                $alert_type = 'danger';
            }
        }
    }

    // Lógica para actualizar los campos de texto
    $stmt_text = $conn->prepare("UPDATE contenido_editable SET valor = ? WHERE id = ?");
    $changes_made = false;
    if (isset($_POST['content']) && is_array($_POST['content'])) {
        foreach ($_POST['content'] as $id => $value) {
            $id = intval($id);
            $sanitized_value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $stmt_text->bind_param("si", $sanitized_value, $id);
            if ($stmt_text->execute()) {
                $changes_made = true;
            }
        }
    }
    $stmt_text->close();
    
    if (empty($message)) {
        log_activity($conn, $_SESSION['admin_id'], 'Actualización de Contenido', "Se guardaron cambios en la Página de Sedes.");
        $message = "¡Contenido de la página de sedes actualizado correctamente!";
        $alert_type = 'success';
    }
}

// --- EMPIEZA LA PARTE DE DIBUJO DE LA PÁGINA ---
$page_title = "Editar Página de Sedes";
include 'admin_header.php';

// Obtener el contenido actual de la base de datos
$sql = "SELECT id, campo, valor, descripcion, tipo FROM contenido_editable WHERE seccion = 'ubicacion' ORDER BY orden";
$result = $conn->query($sql);
$contents = [];
$imagen_sede_url = '';
while($row = $result->fetch_assoc()) {
    if ($row['campo'] == 'imagen_url') {
        $imagen_sede_url = $row['valor'];
    } else {
        $contents[] = $row;
    }
}
?>

<h1 class="mb-4"><?php echo $page_title; ?></h1>
<p>Desde aquí puedes cambiar los textos y la imagen principal de la página de ubicación.</p>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $alert_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" action="edit_sedes.php" enctype="multipart/form-data">
    <div class="card mb-4">
        <div class="card-header">
            <h4>Imagen Principal</h4>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <p><strong>Imagen Actual:</strong></p>
                    <img src="../<?php echo htmlspecialchars($imagen_sede_url); ?>" alt="Vista previa de la sede" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                </div>
                <div class="col-md-8">
                    <label for="imagen_sede" class="form-label">Subir nueva imagen</label>
                    <input class="form-control" type="file" id="imagen_sede" name="imagen_sede">
                    <div class="form-text">Sube una imagen para reemplazar la actual. Se recomienda una imagen apaisada (horizontal).</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Información de Contacto y Horarios</h4>
        </div>
        <div class="card-body">
            <?php foreach ($contents as $item): ?>
                <div class="mb-3">
                    <label for="content-<?php echo $item['id']; ?>" class="form-label">
                        <strong><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $item['campo']))); ?></strong>
                        <?php if (!empty($item['descripcion'])): ?>
                            <small class="text-muted d-block"><?php echo htmlspecialchars($item['descripcion']); ?></small>
                        <?php endif; ?>
                    </label>
                    
                    <?php if ($item['tipo'] == 'textarea'): ?>
                        <textarea class="form-control" id="content-<?php echo $item['id']; ?>" name="content[<?php echo $item['id']; ?>]" rows="3"><?php echo htmlspecialchars($item['valor']); ?></textarea>
                    <?php else: ?>
                        <input type="text" class="form-control" id="content-<?php echo $item['id']; ?>" name="content[<?php echo $item['id']; ?>]" value="<?php echo htmlspecialchars($item['valor']); ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-4">Guardar Todos los Cambios</button>
</form>

<?php include 'admin_footer.php'; ?>

