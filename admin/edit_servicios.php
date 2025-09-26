<?php 
$page_title = "Editar Página de Servicios";
include 'admin_header.php';

// Array de iconos preseleccionados para facilitar la vida al admin
$icons = [
    'bi-tools' => 'Herramientas',
    'bi-car-front-fill' => 'Carro',
    'bi-gear-wide-connected' => 'Engranajes',
    'bi-wrench-adjustable-circle' => 'Llave y Tuerca',
    'bi-speedometer2' => 'Velocímetro',
    'bi-battery-charging' => 'Batería',
    'bi-paint-bucket' => 'Pintura',
    'bi-lightbulb' => 'Electricidad',
    'bi-wind' => 'Aire Acondicionado',
    'bi-piston' => 'Pistón (Motor)',
    'bi-align-bottom' => 'Alineación',
    'bi-shield-check' => 'Revisión/Seguridad',
    'bi-truck-front' => 'Camioneta',
    'bi-bicycle' => 'Motos'
];


// --- Lógica para procesar el formulario de actualización de servicios ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_services'])) {
    // ... (El resto de la lógica PHP no cambia)
    $message = '';
    $error = false;
    
    // Preparar la consulta para actualizar un servicio
    $stmt = $conn->prepare("UPDATE servicios SET nombre = ?, descripcion = ?, icono_bs = ? WHERE id = ?");

    // Iterar sobre cada servicio enviado
    if(isset($_POST['service'])) {
        foreach ($_POST['service'] as $id => $data) {
            $id = intval($id);
            $nombre = htmlspecialchars($data['nombre']);
            $descripcion = htmlspecialchars($data['descripcion']);
            $icono = htmlspecialchars($data['icono']);

            $stmt->bind_param("sssi", $nombre, $descripcion, $icono, $id);
            if (!$stmt->execute()) {
                $error = true;
                $message = "<div class='alert alert-danger'>Error al actualizar el servicio ID: $id.</div>";
                break;
            }
        }
    }

    if (!$error) {
        $message = "<div class='alert alert-success'>¡Servicios actualizados correctamente!</div>";
    }
}


// --- Lógica para obtener todos los servicios de la DB ---
$sql_servicios = "SELECT id, nombre, descripcion, icono_bs FROM servicios ORDER BY id";
$result_servicios = $conn->query($sql_servicios);
$servicios = $result_servicios->fetch_all(MYSQLI_ASSOC);

?>

<h1 class="mb-4"><?php echo $page_title; ?></h1>
<p>Gestiona los servicios que se muestran en la página pública. Puedes cambiar sus nombres, descripciones e iconos.</p>

<?php if (isset($message)) { echo $message; } ?>

<div class="card">
    <div class="card-header">
        <h3>Lista de Servicios</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="edit_servicios.php">
            <input type="hidden" name="update_services" value="1">
            
            <?php foreach ($servicios as $servicio): ?>
                <div class="service-edit-item card mb-3">
                    <div class="card-body">
                         <h5 class="card-title">Editando: <?php echo htmlspecialchars($servicio['nombre']); ?></h5>
                         <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="service_nombre_<?php echo $servicio['id']; ?>" class="form-label">Nombre del Servicio</label>
                                <input type="text" class="form-control" id="service_nombre_<?php echo $servicio['id']; ?>" name="service[<?php echo $servicio['id']; ?>][nombre]" value="<?php echo htmlspecialchars($servicio['nombre']); ?>">
                            </div>
                            <div class="col-md-5 mb-3">
                                 <label for="service_desc_<?php echo $servicio['id']; ?>" class="form-label">Descripción</label>
                                 <textarea class="form-control" id="service_desc_<?php echo $servicio['id']; ?>" name="service[<?php echo $servicio['id']; ?>][descripcion]" rows="1"><?php echo htmlspecialchars($servicio['descripcion']); ?></textarea>
                            </div>
                             <div class="col-md-3 mb-3">
                                <label for="service_icono_<?php echo $servicio['id']; ?>" class="form-label">Icono</label>
                                <select class="form-select" id="service_icono_<?php echo $servicio['id']; ?>" name="service[<?php echo $servicio['id']; ?>][icono]">
                                    <?php foreach ($icons as $class => $name): ?>
                                        <option value="<?php echo $class; ?>" <?php echo ($servicio['icono_bs'] == $class) ? 'selected' : ''; ?>>
                                            <?php echo $name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                         </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Guardar Todos los Cambios</button>
        </form>
    </div>
</div>


<?php include 'admin_footer.php'; ?>

