<?php 
// 1. Conectar a la DB y obtener servicios
require 'admin/db_config.php';
// Se modifica la consulta para traer 'imagen_url' en lugar de 'icono_bs'
$sql = "SELECT nombre, descripcion, imagen_url FROM servicios WHERE activo = 1 ORDER BY id";
$result = $conn->query($sql);
$servicios = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();

$page_title = "Servicios";
include 'includes/header.php'; 
?>

<div class="services-page-background">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 text-white">Nuestros Servicios</h1>
            <p class="lead text-white-50">Ofrecemos soluciones integrales para el cuidado de tu vehículo.</p>
        </div>

        <div class="row g-4">
            <?php if (count($servicios) > 0): ?>
                <?php foreach ($servicios as $servicio): ?>
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
                    <div class="card service-card-v2 w-100">
                        <!-- Se reemplaza el div del icono por una etiqueta de imagen -->
                        <img 
                            src="<?php echo htmlspecialchars(!empty($servicio['imagen_url']) ? $servicio['imagen_url'] : 'https://placehold.co/600x400/0d2c4f/fbc108?text=Servicio'); ?>" 
                            class="service-card-img" 
                            alt="<?php echo htmlspecialchars($servicio['nombre']); ?>"
                            onerror="this.onerror=null;this.src='https://placehold.co/600x400/0d2c4f/fbc108?text=Error';"
                        >
                        <div class="card-body text-center d-flex flex-column">
                            <h3 class="card-title mt-3"><?php echo htmlspecialchars($servicio['nombre']); ?></h3>
                            <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($servicio['descripcion']); ?></p>
                            <a href="/TALLER_EL_CIENTIFICO/crear_cotizacion.php" class="btn btn-cientifico mt-auto">Cotizar Servicio</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center text-white">No hay servicios disponibles en este momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
