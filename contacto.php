<?php 
// 1. Conectar a la DB y obtener info de contacto
require 'admin/db_config.php';
$sql = "SELECT campo, valor FROM contenido_editable WHERE seccion = 'contacto'";
$result = $conn->query($sql);
$contact_content = [];
while($row = $result->fetch_assoc()) {
    $contact_content[$row['campo']] = $row['valor'];
}
$conn->close();

$page_title = "Contacto";
include 'includes/header.php'; 
?>

<div class="contact-page py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-4">¿Tienes alguna pregunta?</h1>
            <p class="lead">Nuestro equipo está listo para ayudarte. Completa el formulario o utiliza nuestros medios de contacto directo.</p>
        </div>

        <div class="row g-5">
            <!-- Columna de Información de Contacto -->
            <div class="col-lg-5">
                <div class="contact-info-card h-100 p-4">
                    <h3 class="mb-4">Información de Contacto</h3>
                    
                    <div class="info-item d-flex align-items-start mb-4">
                        <i class="bi bi-geo-alt-fill me-3"></i>
                        <div>
                            <strong>Dirección</strong><br>
                            Carrera 13a #14-46, Barrio Los Molinos, Campoalegre, Huila.
                        </div>
                    </div>

                    <div class="info-item d-flex align-items-start mb-4">
                        <i class="bi bi-telephone-fill me-3"></i>
                        <div>
                            <strong>Teléfono</strong><br>
                            <a href="tel:<?php echo htmlspecialchars($contact_content['telefono'] ?? ''); ?>"><?php echo htmlspecialchars($contact_content['telefono'] ?? 'No disponible'); ?></a>
                        </div>
                    </div>

                    <div class="info-item d-flex align-items-start mb-4">
                        <i class="bi bi-envelope-fill me-3"></i>
                        <div>
                            <strong>Email</strong><br>
                             <a href="mailto:<?php echo htmlspecialchars($contact_content['email'] ?? ''); ?>"><?php echo htmlspecialchars($contact_content['email'] ?? 'No disponible'); ?></a>
                        </div>
                    </div>

                    <div class="info-item d-flex align-items-start">
                        <i class="bi bi-whatsapp me-3"></i>
                        <div>
                            <strong>WhatsApp</strong><br>
                            <a href="https://wa.me/<?php echo htmlspecialchars($contact_content['whatsapp'] ?? ''); ?>" target="_blank"><?php echo htmlspecialchars($contact_content['telefono'] ?? 'No disponible'); ?></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna del Formulario -->
            <div class="col-lg-7">
                 <div class="contact-form-card h-100 p-4">
                    <h3 class="mb-4">Envíanos un Mensaje</h3>
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="mensaje" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="mensaje" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-cientifico w-100">Enviar Mensaje</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

