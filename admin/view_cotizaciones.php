<?php 
$page_title = "Gestionar Cotizaciones";
include 'admin_header.php';
require_once 'functions.php';

// --- LÓGICA PARA ELIMINAR UNA COTIZACIÓN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_cotizacion'])) {
    $cotizacion_id_delete = intval($_POST['cotizacion_id']);
    
    // Primero, eliminamos los servicios asociados para mantener la integridad de la base de datos
    $stmt_delete_services = $conn->prepare("DELETE FROM cotizacion_servicios WHERE cotizacion_id = ?");
    $stmt_delete_services->bind_param("i", $cotizacion_id_delete);
    $stmt_delete_services->execute();
    $stmt_delete_services->close();

    // Luego, eliminamos la cotización principal
    $stmt_delete_cotizacion = $conn->prepare("DELETE FROM cotizaciones WHERE id = ?");
    $stmt_delete_cotizacion->bind_param("i", $cotizacion_id_delete);
    
    if ($stmt_delete_cotizacion->execute()) {
        $message = "<div class='alert alert-success'>Cotización eliminada correctamente.</div>";
        // Registrar actividad
        log_activity($conn, $_SESSION['admin_id'], 'Eliminación de Cotización', "Se eliminó la cotización #{$cotizacion_id_delete}.");
    } else {
        $message = "<div class='alert alert-danger'>Error al eliminar la cotización.</div>";
    }
    $stmt_delete_cotizacion->close();
}


// --- LÓGICA PARA ACTUALIZAR EL ESTADO DE UNA COTIZACIÓN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
// ... (código existente sin cambios)
    $cotizacion_id = intval($_POST['cotizacion_id']);
    $nuevo_estado = $_POST['estado'];
    
    $estados_validos = ['pendiente', 'contactado', 'agendado', 'completado', 'cancelado'];

    if (in_array($nuevo_estado, $estados_validos)) {
        $stmt = $conn->prepare("UPDATE cotizaciones SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_estado, $cotizacion_id);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Estado de la cotización actualizado correctamente.</div>";
            $descripcion_log = "Se cambió el estado de la cotización #{$cotizacion_id} a '{$nuevo_estado}'.";
            log_activity($conn, $_SESSION['admin_id'], 'Actualización de Cotización', $descripcion_log);
        } else {
            $message = "<div class='alert alert-danger'>Error al actualizar el estado.</div>";
        }
        $stmt->close();
    }
}

// --- OBTENER TODAS LAS COTIZACIONES DE LA BASE DE DATOS ---
$sql = "SELECT c.id, c.nombre_cliente, c.telefono_cliente, c.email_cliente, c.marca_vehiculo, c.modelo_vehiculo, c.ciudad, c.estado, c.fecha_creacion, c.usuario_id, u.nombre as user_nombre, u.apellido as user_apellido
        FROM cotizaciones c
        LEFT JOIN usuarios u ON c.usuario_id = u.id
        ORDER BY c.fecha_creacion DESC";
$result = $conn->query($sql);
$cotizaciones = $result->fetch_all(MYSQLI_ASSOC);

?>

<h1 class="mb-4"><?php echo $page_title; ?></h1>
<p>Aquí puedes ver todas las solicitudes de cotización enviadas desde la página web y gestionar su estado.</p>

<?php if (isset($message)) { echo $message; } ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Contacto</th>
                        <th>Vehículo</th>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cotizaciones)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay cotizaciones registradas por el momento.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cotizaciones as $cotizacion): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($cotizacion['fecha_creacion'])); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?>
                                    <?php if ($cotizacion['usuario_id']): ?>
                                        <span class="badge bg-primary">Registrado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Visitante</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($cotizacion['telefono_cliente']); ?>
                                    <?php if ($cotizacion['email_cliente']): ?>
                                        <br><a href="mailto:<?php echo htmlspecialchars($cotizacion['email_cliente']); ?>"><?php echo htmlspecialchars($cotizacion['email_cliente']); ?></a>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?></td>
                                <td><?php echo htmlspecialchars($cotizacion['ciudad']); ?></td>
                                <td>
                                    <form method="POST" action="view_cotizaciones.php" class="d-flex">
                                        <input type="hidden" name="cotizacion_id" value="<?php echo $cotizacion['id']; ?>">
                                        <select name="estado" class="form-select form-select-sm me-2">
                                            <option value="pendiente" <?php if($cotizacion['estado'] == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                                            <option value="contactado" <?php if($cotizacion['estado'] == 'contactado') echo 'selected'; ?>>Contactado</option>
                                            <option value="agendado" <?php if($cotizacion['estado'] == 'agendado') echo 'selected'; ?>>Agendado</option>
                                            <option value="completado" <?php if($cotizacion['estado'] == 'completado') echo 'selected'; ?>>Completado</option>
                                            <option value="cancelado" <?php if($cotizacion['estado'] == 'cancelado') echo 'selected'; ?>>Cancelado</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-sm btn-outline-primary">OK</button>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="view_cotizaciones.php" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta cotización? Esta acción no se puede deshacer.');">
                                        <input type="hidden" name="cotizacion_id" value="<?php echo $cotizacion['id']; ?>">
                                        <button type="submit" name="delete_cotizacion" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?>

