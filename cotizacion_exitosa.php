<?php
session_start();

// Si el usuario llega aquí sin haber creado una cotización, lo redirigimos a su cuenta.
if (!isset($_SESSION['whatsapp_redirect_url']) || !isset($_SESSION['user_id'])) {
    header('Location: cuenta.php');
    exit();
}

// Recuperamos la URL de WhatsApp y luego la eliminamos de la sesión para que no se reutilice.
$whatsapp_url = $_SESSION['whatsapp_redirect_url'];
unset($_SESSION['whatsapp_redirect_url']);
unset($_SESSION['last_cotizacion_id']);

$page_title = "Cotización Enviada";
include 'includes/header.php';
?>

<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg p-5">
                <i class="bi bi-check-circle-fill text-success display-1 mb-4"></i>
                <h1 class="mb-3">¡Cotización Creada con Éxito!</h1>
                <p class="lead text-muted">Hemos guardado tu solicitud en la sección "Mi Cuenta".</p>
                <hr class="my-4">
                <p class="mb-4">El último paso es notificarnos por WhatsApp para que podamos atenderte lo antes posible. Haz clic en el botón de abajo para enviar el resumen de tu cotización.</p>

                <div class="d-grid gap-2 col-8 mx-auto">
                    <a href="<?php echo htmlspecialchars($whatsapp_url); ?>" target="_blank" class="btn btn-success btn-lg">
                        <i class="bi bi-whatsapp me-2"></i>Notificar al Taller
                    </a>
                    <a href="cuenta.php" class="btn btn-outline-secondary">Volver a Mi Cuenta</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
