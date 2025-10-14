<?php 
// --- LÓGICA DE PROCESAMIENTO PRIMERO ---
// Iniciamos la sesión y cargamos los archivos necesarios ANTES de cualquier HTML.
// SOLUCIÓN: Usamos require_once para que no haya conflictos.
require_once 'session_handler.php';
require_once 'db_config.php';
require_once 'functions.php';

// Verificamos si se envió el formulario para vaciar los logs
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_empty_logs'])) {
    // Registramos la acción
    log_activity($conn, $_SESSION['admin_id'], 'Vaciado de Logs', 'Se eliminó todo el historial de actividad.');

    // Vaciamos la tabla
    if ($conn->query("TRUNCATE TABLE logs_actividad")) {
        $_SESSION['flash_message'] = "<div class='alert alert-success'>¡El registro de actividad ha sido vaciado exitosamente!</div>";
    } else {
        $_SESSION['flash_message'] = "<div class='alert alert-danger'>Error al intentar vaciar el registro de actividad.</div>";
    }

    // Redirigimos. Como no se ha impreso HTML, esto ahora funciona.
    header('Location: view_logs.php');
    exit(); // Detenemos el script después de redirigir.
}

// --- COMIENZO DE LA PÁGINA VISUAL ---
$page_title = "Registro de Actividad";
include 'admin_header.php'; // Ahora sí, incluimos la parte visual de la página

$message = ''; // Para mostrar mensajes de éxito o error

// Comprobamos si hay un mensaje flash en la sesión para mostrarlo
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // Lo borramos para que no se muestre de nuevo
}

// --- LÓGICA DE PAGINACIÓN (sin cambios) ---
$logs_per_page = 15;
$total_logs_result = $conn->query("SELECT COUNT(id) as total FROM logs_actividad");
$total_logs = $total_logs_result->fetch_assoc()['total'];
$total_pages = ceil($total_logs / $logs_per_page);
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

if ($current_page < 1) {
    $current_page = 1;
}
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

$offset = ($current_page - 1) * $logs_per_page;

// --- OBTENER LOS LOGS DE LA PÁGINA ACTUAL (sin cambios) ---
$sql = "SELECT l.fecha, a.nombre as admin_nombre, l.actividad, l.descripcion, l.ip_address 
        FROM logs_actividad l
        LEFT JOIN administradores a ON l.admin_id = a.id
        ORDER BY l.fecha DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $logs_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
$logs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0"><?php echo $page_title; ?></h1>
        <p class="mb-0">Aquí puedes ver un historial de todas las acciones importantes realizadas.</p>
    </div>
    <div>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmEmptyModal">
            <i class="bi bi-trash3-fill me-2"></i>Vaciar Registros
        </button>
    </div>
</div>

<?php echo $message; // Muestra el mensaje de éxito/error ?>

<!-- El resto del archivo (tabla, paginación, modal, script) no cambia -->
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
                            <td colspan="5" class="text-center p-4">No hay registros de actividad todavía.</td>
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
    
    <?php if($total_pages > 1): ?>
    <div class="card-footer d-flex justify-content-end">
        <nav aria-label="Navegación de logs">
            <ul class="pagination mb-0">
                <li class="page-item <?php if($current_page <= 1){ echo 'disabled'; } ?>">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Anterior</a>
                </li>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if($current_page == $i) {echo 'active'; } ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?php if($current_page >= $total_pages) { echo 'disabled'; } ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Modal de Confirmación para Vaciar Logs -->
<div class="modal fade" id="confirmEmptyModal" tabindex="-1" aria-labelledby="confirmEmptyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-confirm-danger">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmEmptyModalLabel">Confirmación Requerida</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <div class="warning-icon mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h4>¡Acción Irreversible!</h4>
        <p>Estás a punto de borrar <strong>permanentemente</strong> todo el historial de actividad. Esta acción no se puede deshacer.</p>
        <p>Para confirmar, por favor escribe el siguiente código:</p>
        <div class="captcha-code my-3" id="captchaCode"></div>
        
        <form id="emptyLogsForm" action="view_logs.php" method="POST">
            <input type="hidden" name="confirm_empty_logs" value="1">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="captchaInput" placeholder="Código de confirmación" autocomplete="off">
                <label for="captchaInput">Escribe el código aquí</label>
            </div>
            <button type="submit" id="confirmEmptyBtn" class="btn btn-danger w-100" disabled>
                Sí, entiendo, vaciar todo
            </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const confirmModal = document.getElementById('confirmEmptyModal');
    const captchaCodeEl = document.getElementById('captchaCode');
    const captchaInput = document.getElementById('captchaInput');
    const confirmBtn = document.getElementById('confirmEmptyBtn');
    let generatedCode = '';

    // Cuando el modal se muestra, generamos un nuevo código
    confirmModal.addEventListener('show.bs.modal', function () {
        generatedCode = Math.random().toString(36).substring(2, 8).toUpperCase();
        captchaCodeEl.textContent = generatedCode;
        captchaInput.value = ''; // Limpiamos el input
        confirmBtn.disabled = true; // Deshabilitamos el botón
    });

    // Validamos el input en tiempo real
    captchaInput.addEventListener('input', function () {
        if (captchaInput.value === generatedCode) {
            confirmBtn.disabled = false;
        } else {
            confirmBtn.disabled = true;
        }
    });
});
</script>

<?php include 'admin_footer.php'; ?>

