<?php 
// 1. Conectar a la DB y obtener servicios
require 'admin/db_config.php';
$sql = "SELECT nombre, descripcion, icono_bs FROM servicios WHERE activo = 1 ORDER BY id";
$result = $conn->query($sql);
$servicios = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();

$page_title = "Servicios";
include 'includes/header.php'; 
?>

<div class="services-page">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4">Nuestros Servicios</h1>
            <p class="lead">Ofrecemos soluciones integrales para el cuidado de tu vehículo.</p>
        </div>

        <div class="row g-4">
            <?php if (count($servicios) > 0): ?>
                <?php foreach ($servicios as $servicio): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card service-card h-100 text-center">
                        <div class="card-body">
                            <div class="service-icon mx-auto mb-3">
                                <i class="bi <?php echo htmlspecialchars($servicio['icono_bs'] ?? 'bi-gear-wide-connected'); ?>"></i>
                            </div>
                            <h3 class="card-title"><?php echo htmlspecialchars($servicio['nombre']); ?></h3>
                            <p class="card-text"><?php echo htmlspecialchars($servicio['descripcion']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">No hay servicios disponibles en este momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

