<?php 
$page_title = "Editar Servicios";
include 'admin_header.php';

// --- Lógica para procesar el formulario de actualización de servicios con subida de imágenes ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_services'])) {
    $message = '';
    $error = false;
    
    // Directorio donde se guardarán las imágenes.
    $upload_dir = __DIR__ . '/../uploads/servicios/';

    // --- NUEVA VERIFICACIÓN DE PERMISOS Y DIRECTORIO ---
    // Primero, verificamos si el directorio de subida existe y si tenemos permisos para escribir en él.
    // Esta es la causa más común de fallos silenciosos en la subida de archivos.
    if (!is_dir($upload_dir)) {
        $error = true;
        $message = "<div class='alert alert-danger'><strong>Error Crítico:</strong> El directorio de subida no existe en <code>taller_el_cientifico/uploads/servicios/</code>. Por favor, créalo manualmente.</div>";
    } elseif (!is_writable($upload_dir)) {
        $error = true;
        $message = "<div class='alert alert-danger'><strong>Error Crítico:</strong> No tengo permisos para escribir en el directorio <code>/uploads/servicios/</code>. Por favor, ajusta los permisos de la carpeta.</div>";
    }
    
    // Solo continuamos con el procesamiento si no hay errores de directorio
    if (!$error) {
        // Preparar la consulta para actualizar un servicio
        $stmt = $conn->prepare("UPDATE servicios SET nombre = ?, descripcion = ?, imagen_url = ? WHERE id = ?");

        if(isset($_POST['service'])) {
            foreach ($_POST['service'] as $id => $data) {
                $id = intval($id);
                $nombre = htmlspecialchars($data['nombre']);
                $descripcion = htmlspecialchars($data['descripcion']);
                $current_image_url = $data['current_image']; // Obtenemos la URL actual por si no se sube una nueva imagen

                // --- LÓGICA DE SUBIDA DE ARCHIVO ---
                if (isset($_FILES['service_image']) && $_FILES['service_image']['error'][$id] == UPLOAD_ERR_OK) {
                    
                    $file_tmp_path = $_FILES['service_image']['tmp_name'][$id];
                    $file_name = $_FILES['service_image']['name'][$id];
                    
                    // Limpiar el nombre del archivo y hacerlo único para evitar sobreescrituras
                    $file_name_parts = explode(".", $file_name);
                    $file_ext = strtolower(end($file_name_parts));
                    $new_file_name = 'servicio_' . $id . '_' . time() . '.' . $file_ext;
                    
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (in_array($file_ext, $allowed_ext)) {
                        $dest_path = $upload_dir . $new_file_name;
                        
                        if(move_uploaded_file($file_tmp_path, $dest_path)) {
                            // Si la subida es exitosa, la nueva URL será la ruta a este archivo
                            $current_image_url = 'uploads/servicios/' . $new_file_name;
                        } else {
                            $error = true;
                            $message .= "<div class='alert alert-danger'>Error al mover el archivo para el servicio ID: $id. Revisa los permisos.</div>";
                        }
                    } else {
                        $error = true;
                        $message .= "<div class='alert alert-danger'>Tipo de archivo no permitido para el servicio ID: $id.</div>";
                    }
                }
                // --- FIN DE LÓGICA DE SUBIDA ---
                
                if (!$error) {
                    $stmt->bind_param("sssi", $nombre, $descripcion, $current_image_url, $id);
                    if (!$stmt->execute()) {
                        $error = true;
                        $message = "<div class='alert alert-danger'>Error al actualizar la base de datos para el servicio ID: $id.</div>";
                        break;
                    }
                }
            }
        }

        if (!$error) {
            $message = "<div class='alert alert-success'>¡Servicios actualizados correctamente!</div>";
        }
    }
}

// --- Lógica para obtener todos los servicios de la DB (ahora trae imagen_url) ---
$sql_servicios = "SELECT id, nombre, descripcion, imagen_url FROM servicios ORDER BY id";
$result_servicios = $conn->query($sql_servicios);
$servicios = $result_servicios->fetch_all(MYSQLI_ASSOC);
?>

<h1 class="mb-4"><?php echo $page_title; ?></h1>
<p>Gestiona los servicios que se muestran en la página pública. Puedes cambiar sus nombres, descripciones y subir una nueva imagen para cada uno.</p>

<?php if (isset($message)) { echo $message; } ?>

<div class="card">
    <div class="card-header">
        <h3>Lista de Servicios</h3>
    </div>
    <div class="card-body">
        <!-- ¡IMPORTANTE! Añadimos enctype="multipart/form-data" para permitir la subida de archivos -->
        <form method="POST" action="edit_servicios.php" enctype="multipart/form-data">
            <input type="hidden" name="update_services" value="1">
            
            <?php foreach ($servicios as $servicio): ?>
                <div class="service-edit-item card mb-3">
                    <div class="card-body">
                         <div class="row align-items-center">
                            <!-- Columna de la imagen -->
                            <div class="col-md-3 text-center">
                                <label class="form-label fw-bold">Imagen Actual</label>
                                <img src="../<?php echo !empty($servicio['imagen_url']) ? htmlspecialchars($servicio['imagen_url']) : 'https://placehold.co/300x200?text=Sin+Imagen'; ?>" 
                                     alt="Vista previa de <?php echo htmlspecialchars($servicio['nombre']); ?>" 
                                     class="img-fluid rounded service-img-preview mb-2">
                                <input type="hidden" name="service[<?php echo $servicio['id']; ?>][current_image]" value="<?php echo htmlspecialchars($servicio['imagen_url']); ?>">
                                <input type="file" class="form-control form-control-sm" name="service_image[<?php echo $servicio['id']; ?>]">
                                <small class="form-text text-muted">Sube una nueva para reemplazarla.</small>
                            </div>

                            <!-- Columna de los datos -->
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="service_nombre_<?php echo $servicio['id']; ?>" class="form-label fw-bold">Nombre del Servicio</label>
                                    <input type="text" class="form-control" id="service_nombre_<?php echo $servicio['id']; ?>" name="service[<?php echo $servicio['id']; ?>][nombre]" value="<?php echo htmlspecialchars($servicio['nombre']); ?>">
                                </div>
                                <div class="mb-3">
                                     <label for="service_desc_<?php echo $servicio['id']; ?>" class="form-label fw-bold">Descripción</label>
                                     <textarea class="form-control" id="service_desc_<?php echo $servicio['id']; ?>" name="service[<?php echo $servicio['id']; ?>][descripcion]" rows="4"><?php echo htmlspecialchars($servicio['descripcion']); ?></textarea>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary btn-lg mt-3"><i class="bi bi-save-fill me-2"></i>Guardar Todos los Cambios</button>
        </form>
    </div>
</div>

<?php include 'admin_footer.php'; ?>

