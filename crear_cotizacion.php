<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'admin/db_config.php';

// Obtener la información del usuario logueado
$user_id = $_SESSION['user_id'];
$stmt_user = $conn->prepare("SELECT nombre, apellido, email, telefono FROM usuarios WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

$user_nombre_completo = trim($user['nombre'] . ' ' . $user['apellido']);
$user_email = $user['email'];
$user_telefono = $user['telefono'] ?? 'No especificado';

// Obtener la lista de servicios activos
$servicios_result = $conn->query("SELECT id, nombre, precio_desde FROM servicios WHERE activo = 1 ORDER BY nombre ASC");
$servicios = $servicios_result->fetch_all(MYSQLI_ASSOC);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger datos del formulario
    $marca = trim($_POST['marca_vehiculo'] ?? '');
    $modelo = trim($_POST['modelo_vehiculo'] ?? '');
    $año = trim($_POST['año_vehiculo'] ?? '');
    $tipo_vehiculo = $_POST['tipo_vehiculo'] ?? '';
    $descripcion_problema = trim($_POST['descripcion_problema'] ?? '');
    $servicios_seleccionados = $_POST['servicios'] ?? [];
    $total_estimado = 0;

    // Validaciones
    if (empty($marca)) $errors[] = "La marca del vehículo es obligatoria.";
    if (empty($modelo)) $errors[] = "El modelo del vehículo es obligatorio.";
    if (empty($año)) $errors[] = "El año del vehículo es obligatorio.";
    if (empty($tipo_vehiculo)) $errors[] = "Debes seleccionar el tipo de vehículo.";
    if (empty($servicios_seleccionados)) $errors[] = "Debes seleccionar al menos un servicio.";

    if (empty($errors)) {
        // Guardar la cotización principal en la base de datos
        $stmt = $conn->prepare("INSERT INTO cotizaciones (usuario_id, nombre_cliente, telefono_cliente, email_cliente, marca_vehiculo, modelo_vehiculo, año_vehiculo, tipo_vehiculo, descripcion_problema) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssss", $user_id, $user_nombre_completo, $user_telefono, $user_email, $marca, $modelo, $año, $tipo_vehiculo, $descripcion_problema);
        
        if ($stmt->execute()) {
            $cotizacion_id = $stmt->insert_id; // Obtener el ID de la cotización recién creada
            $servicios_nombres = [];

            // Guardar los servicios seleccionados y calcular el total
            $stmt_service = $conn->prepare("INSERT INTO cotizacion_servicios (cotizacion_id, servicio_id, precio_cotizado) VALUES (?, ?, ?)");
            foreach ($servicios_seleccionados as $servicio_id) {
                // Buscamos el precio del servicio para guardarlo
                $precio_servicio = 0;
                foreach($servicios as $servicio) {
                    if ($servicio['id'] == $servicio_id) {
                        $precio_servicio = $servicio['precio_desde'];
                        $servicios_nombres[] = $servicio['nombre'];
                        break;
                    }
                }
                $total_estimado += $precio_servicio;
                $stmt_service->bind_param("iid", $cotizacion_id, $servicio_id, $precio_servicio);
                $stmt_service->execute();
            }
            $stmt_service->close();

            // Actualizar la cotización con el total estimado
            $stmt_update_total = $conn->prepare("UPDATE cotizaciones SET total_estimado = ? WHERE id = ?");
            $stmt_update_total->bind_param("di", $total_estimado, $cotizacion_id);
            $stmt_update_total->execute();
            $stmt_update_total->close();

            // Preparar y guardar el mensaje de WhatsApp en la sesión
            $numero_taller = WHATSAPP_TALLER;
            $fecha_hora = date('d/m/Y H:i:s');

            $mensaje_whatsapp = "*¡Nueva Cotización Detallada!* %0A%0A";
            $mensaje_whatsapp .= "*Cliente:* " . urlencode($user_nombre_completo) . "%0A";
            $mensaje_whatsapp .= "*Contacto:* " . urlencode($user_telefono) . " | " . urlencode($user_email) . "%0A";
            $mensaje_whatsapp .= "*Fecha:* " . urlencode($fecha_hora) . "%0A%0A";
            $mensaje_whatsapp .= "*Vehículo:* " . urlencode($marca . ' ' . $modelo . ' (' . $año . ')') . "%0A";
            $mensaje_whatsapp .= "*Tipo:* " . urlencode(ucfirst($tipo_vehiculo)) . "%0A%0A";
            $mensaje_whatsapp .= "*Servicios Solicitados:*%0A" . urlencode("- " . implode("%0A- ", $servicios_nombres)) . "%0A%0A";
            if (!empty($descripcion_problema)) {
                 $mensaje_whatsapp .= "*Descripción Adicional:*%0A" . urlencode($descripcion_problema) . "%0A%0A";
            }
            $mensaje_whatsapp .= "*Total Estimado:* $" . number_format($total_estimado, 0, ',', '.') . "%0A%0A";
            $mensaje_whatsapp .= "--------------------%0A";
            $mensaje_whatsapp .= "ID de Cotización para seguimiento: *" . $cotizacion_id . "*";

            $_SESSION['whatsapp_redirect_url'] = "https://wa.me/{$numero_taller}?text=" . $mensaje_whatsapp;
            
            header('Location: cotizacion_exitosa.php');
            exit();

        } else {
            $errors[] = "Error al procesar la cotización: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();

$page_title = "Crear Nueva Cotización";
include 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="display-5 mb-4">Crear Nueva Cotización</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="crear_cotizacion.php" method="POST">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-car-front-fill me-2"></i>Información del Vehículo</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="marca_vehiculo" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca_vehiculo" name="marca_vehiculo" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="modelo_vehiculo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo_vehiculo" name="modelo_vehiculo" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="año_vehiculo" class="form-label">Año</label>
                        <input type="number" class="form-control" id="año_vehiculo" name="año_vehiculo" min="1950" max="<?php echo date('Y') + 1; ?>" required>
                    </div>
                </div>
                 <div class="mb-3">
                    <label class="form-label">Tipo de Vehículo</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipo_vehiculo" id="tipo_carro" value="carro" checked>
                            <label class="form-check-label" for="tipo_carro">Carro</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipo_vehiculo" id="tipo_moto" value="moto">
                            <label class="form-check-label" for="tipo_moto">Moto</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4><i class="bi bi-tools me-2"></i>Servicios Requeridos</h4>
            </div>
            <div class="card-body">
                <p>Selecciona uno o más servicios de la lista.</p>
                <div class="row">
                    <?php foreach ($servicios as $servicio): ?>
                        <div class="col-md-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="servicios[]" value="<?php echo $servicio['id']; ?>" id="servicio_<?php echo $servicio['id']; ?>">
                                <label class="form-check-label" for="servicio_<?php echo $servicio['id']; ?>">
                                    <?php echo htmlspecialchars($servicio['nombre']); ?> ($<?php echo number_format($servicio['precio_desde'], 0); ?>)
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <hr>
                <div class="mb-3">
                    <label for="descripcion_problema" class="form-label">Describe tu problema (Opcional)</label>
                    <textarea class="form-control" id="descripcion_problema" name="descripcion_problema" rows="4" placeholder="Ej: El carro hace un ruido extraño al frenar..."></textarea>
                </div>
            </div>
        </div>
        
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-cientifico btn-lg"><i class="bi bi-send-fill me-2"></i>Enviar Cotización</button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

