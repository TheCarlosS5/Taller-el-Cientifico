<?php 
$page_title = "Gestionar Usuarios";
include 'admin_header.php';
require_once 'functions.php';

// --- LÓGICA PARA ACTUALIZAR EL ESTADO (ACTIVAR/DESACTIVAR) DE UN USUARIO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $user_id_update = intval($_POST['user_id']);
    $new_status = intval($_POST['new_status']);

    $stmt = $conn->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $user_id_update);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Estado del usuario actualizado correctamente.</div>";
        $status_text = $new_status ? 'activado' : 'desactivado';
        log_activity($conn, $_SESSION['admin_id'], 'Actualización de Usuario', "Se ha {$status_text} al usuario #{$user_id_update}.");
    } else {
        $message = "<div class='alert alert-danger'>Error al actualizar el estado del usuario.</div>";
    }
    $stmt->close();
}

// --- LÓGICA PARA ELIMINAR UN USUARIO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id_delete = intval($_POST['user_id']);

    // Eliminar usuario
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id_delete);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Usuario eliminado permanentemente.</div>";
        log_activity($conn, $_SESSION['admin_id'], 'Eliminación de Usuario', "Se eliminó al usuario #{$user_id_delete}.");
    } else {
        $message = "<div class='alert alert-danger'>Error al eliminar el usuario.</div>";
    }
    $stmt->close();
}


// --- OBTENER TODOS LOS USUARIOS DE LA BASE DE DATOS ---
$sql = "SELECT id, nombre, apellido, email, telefono, fecha_registro, activo FROM usuarios ORDER BY fecha_registro DESC";
$result = $conn->query($sql);
$usuarios = $result->fetch_all(MYSQLI_ASSOC);

?>

<h1 class="mb-4"><?php echo $page_title; ?></h1>
<p>Aquí puedes ver y administrar todas las cuentas de clientes registradas en el sitio web.</p>

<?php if (isset($message)) { echo $message; } ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Registrado</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay usuarios registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                                <td><?php echo htmlspecialchars(trim($usuario['nombre'] . ' ' . $usuario['apellido'])); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($usuario['email']); ?>
                                    <br>
                                    <span class="text-muted"><?php echo htmlspecialchars($usuario['telefono'] ?? 'No provisto'); ?></span>
                                </td>
                                <td>
                                    <?php if ($usuario['activo']): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <form method="POST" action="view_usuarios.php" class="me-2">
                                            <input type="hidden" name="user_id" value="<?php echo $usuario['id']; ?>">
                                            <?php if ($usuario['activo']): ?>
                                                <input type="hidden" name="new_status" value="0">
                                                <button type="submit" name="update_status" class="btn btn-sm btn-warning" title="Desactivar Usuario">
                                                    <i class="bi bi-person-fill-slash"></i>
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="new_status" value="1">
                                                <button type="submit" name="update_status" class="btn btn-sm btn-success" title="Activar Usuario">
                                                    <i class="bi bi-person-fill-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                        <form method="POST" action="view_usuarios.php" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este usuario y todas sus cotizaciones asociadas? Esta acción es PERMANENTE.');">
                                            <input type="hidden" name="user_id" value="<?php echo $usuario['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn btn-sm btn-danger" title="Eliminar Usuario">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
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
