<?php 
// 1. Conectar a la DB y obtener contenido de ubicación
require 'admin/db_config.php';
$sql = "SELECT campo, valor FROM contenido_editable WHERE seccion = 'ubicacion'";
$result = $conn->query($sql);
$ubicacion_content = [];
while($row = $result->fetch_assoc()) {
    $ubicacion_content[$row['campo']] = $row['valor'];
}
$conn->close();

$page_title = "Nuestras Sedes";
include 'includes/header.php'; 
?>

<div class="sedes-page py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-4">Nuestra Ubicación</h1>
            <p class="lead">Visítanos y conoce nuestras instalaciones.</p>
        </div>

        <div class="card shadow-lg location-card">
            <div class="row g-0">
                <div class="col-lg-6">
                    <!-- Usaremos un placeholder para la imagen del taller -->
                    <img src="https://placehold.co/600x450/333/FFF?text=Taller+El+Científico" class="img-fluid rounded-start h-100" alt="Foto del Taller" style="object-fit: cover;">
                </div>
                <div class="col-lg-6 d-flex align-items-center">
                    <div class="card-body">
                        <h3 class="card-title mb-4">
                            <i class="bi bi-geo-alt-fill me-2"></i> 
                            Encuéntranos
                        </h3>
                        
                        <p><strong><i class="bi bi-pin-map-fill me-2"></i> Dirección:</strong> <?php echo htmlspecialchars($ubicacion_content['direccion'] ?? 'No disponible'); ?></p>
                        <p><strong><i class="bi bi-signpost-split-fill me-2"></i> Barrio:</strong> <?php echo htmlspecialchars($ubicacion_content['barrio'] ?? 'No disponible'); ?></p>

                        <hr>

                        <p class="mb-1"><strong><i class="bi bi-clock-fill me-2"></i> Horario:</strong></p>
                        <ul class="list-unstyled ms-4">
                            <li><?php echo htmlspecialchars($ubicacion_content['horario_lunes_viernes'] ?? 'No disponible'); ?></li>
                            <li><?php echo htmlspecialchars($ubicacion_content['horario_sabados'] ?? 'No disponible'); ?></li>
                        </ul>

                        <hr>
                        
                        <p class="mt-4">
                            <i class="bi bi-info-circle-fill me-2"></i> 
                            <?php echo htmlspecialchars_decode($ubicacion_content['descripcion'] ?? 'No disponible'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

         <!-- Mapa de Google -->
        <div class="map-container mt-5 shadow">
             <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.45785023908!2d-75.2974246852427!3d2.973887197834018!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3b7596a775a6a3%3A0x6b359f131508985c!2sCampoalegre%2C%20Huila!5e0!3m2!1ses!2sco!4v1664057195511!5m2!1ses!2sco" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>


