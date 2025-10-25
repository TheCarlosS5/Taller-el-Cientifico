<?php
// --- LÓGICA DE PROCESAMIENTO ANTES DE CUALQUIER HTML ---
require_once 'session_handler.php';
require_once 'db_config.php';
require_once 'functions.php';

$page_title = "Gestionar Servicios";
$message = '';
$alert_type = 'info';
$upload_dir = __DIR__ . '/../uploads/servicios/';

// --- ACCIÓN: ELIMINAR UN SERVICIO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_service'])) {
    $id_to_delete = intval($_POST['id_to_delete']);
    if ($id_to_delete > 0) {
        // Opcional: Eliminar la imagen del servidor para no guardar basura
        $stmt_get_img = $conn->prepare("SELECT imagen_url FROM servicios WHERE id = ?");
        $stmt_get_img->bind_param("i", $id_to_delete);
        $stmt_get_img->execute();
        $result = $stmt_get_img->get_result();
        if ($row = $result->fetch_assoc()) {
            $image_path = __DIR__ . '/../' . $row['imagen_url'];
            if (file_exists($image_path) && !empty($row['imagen_url']) && strpos($row['imagen_url'], 'placehold.co') === false) {
                unlink($image_path);
            }
        }
        $stmt_get_img->close();

        // Eliminar el registro de la base de datos
        $stmt_delete = $conn->prepare("DELETE FROM servicios WHERE id = ?");
        $stmt_delete->bind_param("i", $id_to_delete);
        if ($stmt_delete->execute()) {
            log_activity($conn, $_SESSION['admin_id'], 'Eliminación de Servicio', "Se eliminó el servicio ID #{$id_to_delete}.");
            $message = "Servicio eliminado correctamente.";
            $alert_type = 'success';
        } else {
            $message = "Error al eliminar el servicio.";
            $alert_type = 'danger';
        }
        $stmt_delete->close();
    }
}

// --- ACCIÓN: GUARDAR CAMBIOS Y AÑADIR NUEVOS SERVICIOS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_all_services'])) {
    
    // 1. Actualizar servicios existentes
    if (isset($_POST['service'])) {
        $stmt_update = $conn->prepare("UPDATE servicios SET nombre = ?, descripcion = ?, imagen_url = ? WHERE id = ?");
        foreach ($_POST['service'] as $id => $data) {
            $id = intval($id);
            $nombre = htmlspecialchars($data['nombre']);
            $descripcion = htmlspecialchars($data['descripcion']);
            $imagen_url = $data['current_image'];

            if (isset($_FILES['service_image']) && $_FILES['service_image']['error'][$id] == UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($_FILES['service_image']['name'][$id], PATHINFO_EXTENSION));
                $new_file_name = 'servicio_' . $id . '_' . time() . '.' . $file_ext;
                if (move_uploaded_file($_FILES['service_image']['tmp_name'][$id], $upload_dir . $new_file_name)) {
                    $imagen_url = 'uploads/servicios/' . $new_file_name;
                }
            }
            $stmt_update->bind_param("sssi", $nombre, $descripcion, $imagen_url, $id);
            $stmt_update->execute();
        }
        $stmt_update->close();
    }

    // 2. Añadir nuevos servicios
    if (isset($_POST['new_service'])) {
        $stmt_insert = $conn->prepare("INSERT INTO servicios (nombre, descripcion, imagen_url) VALUES (?, ?, ?)");
        $stmt_update_new = $conn->prepare("UPDATE servicios SET imagen_url = ? WHERE id = ?");
        foreach ($_POST['new_service'] as $key => $data) {
            if (!empty(trim($data['nombre']))) { // Solo añadir si tiene nombre
                $nombre = htmlspecialchars($data['nombre']);
                $descripcion = htmlspecialchars($data['descripcion']);
                $imagen_url_new = '';

                $stmt_insert->bind_param("sss", $nombre, $descripcion, $imagen_url_new);
                $stmt_insert->execute();
                $new_id = $conn->insert_id;

                if (isset($_FILES['new_service_image']) && $_FILES['new_service_image']['error'][$key] == UPLOAD_ERR_OK) {
                    $file_ext = strtolower(pathinfo($_FILES['new_service_image']['name'][$key], PATHINFO_EXTENSION));
                    $new_file_name = 'servicio_' . $new_id . '_' . time() . '.' . $file_ext;
                    if (move_uploaded_file($_FILES['new_service_image']['tmp_name'][$key], $upload_dir . $new_file_name)) {
                        $imagen_url_new = 'uploads/servicios/' . $new_file_name;
                        $stmt_update_new->bind_param("si", $imagen_url_new, $new_id);
                        $stmt_update_new->execute();
                    }
                }
            }
        }
        $stmt_insert->close();
        $stmt_update_new->close();
    }

    log_activity($conn, $_SESSION['admin_id'], 'Gestión de Servicios', 'Se guardaron cambios en los servicios.');
    $message = "¡Todos los cambios han sido guardados!";
    $alert_type = 'success';
}

// --- OBTENER DATOS PARA MOSTRAR ---
$servicios = $conn->query("SELECT * FROM servicios ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);

include 'admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0"><?php echo $page_title; ?></h1>
    <div>
        <button type="button" id="add-service-btn" class="btn btn-success"><i class="bi bi-plus-circle-fill me-2"></i>Añadir Servicio</button>
    </div>
</div>
<p>Gestiona los servicios que se muestran en la página pública. Puedes añadir, editar y eliminar servicios dinámicamente.</p>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $alert_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" action="edit_servicios.php" enctype="multipart/form-data" id="services-form">
    <div id="services-container">
        <!-- Las tarjetas de servicios existentes se cargarán aquí -->
        <?php foreach ($servicios as $servicio): ?>
            <div class="service-edit-item card mb-3" data-id="<?php echo $servicio['id']; ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-danger btn-sm delete-service-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-id="<?php echo $servicio['id']; ?>" data-name="<?php echo htmlspecialchars($servicio['nombre']); ?>">
                            <i class="bi bi-trash-fill"></i> Eliminar
                        </button>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <label class="form-label fw-bold">Imagen Actual</label>
                            <img src="../<?php echo !empty($servicio['imagen_url']) ? htmlspecialchars($servicio['imagen_url']) : 'https://placehold.co/300x200?text=Sin+Imagen'; ?>" alt="Vista previa de <?php echo htmlspecialchars($servicio['nombre']); ?>" class="img-fluid rounded service-img-preview mb-2">
                            <input type="hidden" name="service[<?php echo $servicio['id']; ?>][current_image]" value="<?php echo htmlspecialchars($servicio['imagen_url']); ?>">
                            <input type="file" class="form-control form-control-sm" name="service_image[<?php echo $servicio['id']; ?>]">
                            <small class="form-text text-muted">Sube una nueva para reemplazarla.</small>
                        </div>
                        <div class="col-md-9">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre del Servicio</label>
                                <input type="text" class="form-control" name="service[<?php echo $servicio['id']; ?>][nombre]" value="<?php echo htmlspecialchars($servicio['nombre']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control" name="service[<?php echo $servicio['id']; ?>][descripcion]" rows="4"><?php echo htmlspecialchars($servicio['descripcion']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" name="save_all_services" class="btn btn-primary btn-lg mt-3"><i class="bi bi-save-fill me-2"></i>Guardar Todos los Cambios</button>
</form>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que quieres eliminar permanentemente el servicio "<strong id="serviceNameToDelete"></strong>"? Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <form method="POST" action="edit_servicios.php">
            <input type="hidden" name="id_to_delete" id="idToDeleteInput">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" name="delete_service" class="btn btn-danger">Sí, Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Plantilla para nuevos servicios (oculta) -->
<template id="new-service-template">
    <div class="service-edit-item card mb-3 is-new">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-2">
                <button type="button" class="btn btn-outline-danger btn-sm delete-new-service-btn">
                    <i class="bi bi-x-lg"></i> Quitar
                </button>
            </div>
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <label class="form-label fw-bold">Imagen</label>
                    <img src="https://placehold.co/300x200?text=Nueva+Imagen" alt="Vista previa de nuevo servicio" class="img-fluid rounded service-img-preview mb-2">
                    <input type="file" class="form-control form-control-sm" name="new_service_image[0]">
                </div>
                <div class="col-md-9">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Servicio</label>
                        <input type="text" class="form-control" name="new_service[0][nombre]" placeholder="Escribe el nombre del nuevo servicio">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" name="new_service[0][descripcion]" rows="4" placeholder="Describe el nuevo servicio"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const servicesContainer = document.getElementById('services-container');
    const addServiceBtn = document.getElementById('add-service-btn');
    const newServiceTemplate = document.getElementById('new-service-template');

    // Lógica para añadir una nueva tarjeta de servicio
    addServiceBtn.addEventListener('click', function() {
        const templateContent = newServiceTemplate.content.cloneNode(true);
        const newCard = templateContent.querySelector('.service-edit-item');
        
        // Usar un timestamp para asegurar claves únicas para los nuevos elementos
        const uniqueId = Date.now();
        newCard.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace('[0]', `[${uniqueId}]`);
        });

        servicesContainer.appendChild(newCard);
    });

    // Lógica para eliminar la tarjeta del DOM al hacer clic en el botón "Quitar" de una tarjeta nueva
    servicesContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-new-service-btn') || e.target.closest('.delete-new-service-btn')) {
            e.target.closest('.service-edit-item.is-new').remove();
        }
    });

    // Lógica para manejar la eliminación de un servicio existente desde el modal
    const deleteModal = document.getElementById('deleteConfirmModal');
    const idToDeleteInput = document.getElementById('idToDeleteInput');
    const serviceNameToDelete = document.getElementById('serviceNameToDelete');

    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        
        idToDeleteInput.value = id;
        serviceNameToDelete.textContent = name;
    });
});
</script>

<?php include 'admin_footer.php'; ?>

