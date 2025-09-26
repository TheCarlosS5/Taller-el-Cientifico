<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require 'admin/db_config.php';
$user_id = $_SESSION['user_id'];

// Obtener datos del usuario, incluyendo el estado de verificación
$stmt_user = $conn->prepare("SELECT nombre, apellido, is_verified FROM usuarios WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();
$_SESSION['user_nombre_completo'] = trim($user['nombre'] . ' ' . $user['apellido']);


// Obtener las cotizaciones del usuario
$stmt = $conn->prepare("
    SELECT c.id, c.marca_vehiculo, c.modelo_vehiculo, c.fecha_creacion, c.estado, c.total_estimado
    FROM cotizaciones c
    WHERE c.usuario_id = ?
    ORDER BY c.fecha_creacion DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cotizaciones_result = $stmt->get_result();
$cotizaciones = $cotizaciones_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();

$page_title = "Mi Cuenta";
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-5">Bienvenido a tu cuenta, <?php echo htmlspecialchars($_SESSION['user_nombre_completo']); ?>!</h1>
        <a href="editar_perfil.php" class="btn btn-outline-dark"><i class="bi bi-pencil-square me-2"></i>Editar Perfil</a>
    </div>

    <p class="lead mb-4">Desde aquí podrás ver y gestionar tus cotizaciones y servicios agendados con nosotros.</p>
    
    <!-- Alerta de correo no verificado -->
    <?php if (!$user['is_verified']): ?>
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
        <div>
            <strong>Tu correo electrónico no ha sido verificado.</strong> Por favor, revisa tu bandeja de entrada para encontrar el enlace de verificación.
            <a href="#" class="alert-link ms-2">Reenviar correo</a>
        </div>
    </div>
    <?php endif; ?>


    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4><i class="bi bi-file-earmark-text me-2"></i>Mis Cotizaciones</h4>
            <a href="crear_cotizacion.php" class="btn btn-cientifico"><i class="bi bi-plus-circle-fill me-2"></i>Crear una nueva cotización</a>
        </div>
        <div class="card-body">
            <?php if (empty($cotizaciones)): ?>
                <div class="text-center p-4">
                    <p class="mb-0">Actualmente no tienes cotizaciones registradas.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Vehículo</th>
                                <th>Total Estimado</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cotizaciones as $cotizacion): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($cotizacion['fecha_creacion'])); ?></td>
                                    <td><?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?></td>
                                    <td>$<?php echo number_format($cotizacion['total_estimado'], 0); ?></td>
                                    <td><span class="badge status-<?php echo htmlspecialchars($cotizacion['estado']); ?> text-capitalize"><?php echo str_replace('_', ' ', htmlspecialchars($cotizacion['estado'])); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

