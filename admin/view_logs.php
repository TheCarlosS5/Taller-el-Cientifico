<?php 
$page_title = "Registro de Actividad";
include 'admin_header.php';

// --- OBTENER TODOS LOS LOGS DE LA BASE DE DATOS ---
// Hacemos un JOIN con la tabla de administradores para mostrar el nombre
$sql = "SELECT l.fecha, a.nombre as admin_nombre, l.actividad, l.descripcion, l.ip_address 
        FROM logs_actividad l
        LEFT JOIN administradores a ON l.admin_id = a.id
        ORDER BY l.fecha DESC";
$result = $conn->query($sql);
$logs = $result->fetch_all(MYSQLI_ASSOC);

?>

<h1 class="mb-4"><?php echo $page_title; ?></h1>
<p>Aquí puedes ver un historial de todas las acciones importantes realizadas en el panel de administración.</p>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Administrador</th>
                        <th>Actividad</th>
                        <th>Descripción</th>
                        <th>Dirección IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay registros de actividad todavía.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($log['fecha'])); ?></td>
                                <td><?php echo htmlspecialchars($log['admin_nombre'] ?? 'Sistema'); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($log['actividad']); ?></span></td>
                                <td><?php echo htmlspecialchars($log['descripcion']); ?></td>
                                <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?>
