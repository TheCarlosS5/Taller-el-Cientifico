<?php
// --- ANTI-CACHE HEADERS ---
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 1. Iniciar sesión y conectar a la BD
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'admin/db_config.php';

// --- LÓGICA PARA PROCESAR EL FORMULARIO DE COTIZACIÓN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cotizar'])) {
    $errors = [];
    
    // Recoger datos del formulario
    $ciudad = trim($_POST['ciudad'] ?? '');
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    
    $usuario_id = $_SESSION['user_id'] ?? null;
    $nombre_cliente = '';
    $telefono_cliente = '';
    $email_cliente = '';

    if ($usuario_id) { // Si el usuario ha iniciado sesión
        $stmt_user = $conn->prepare("SELECT nombre, email, telefono FROM usuarios WHERE id = ?");
        $stmt_user->bind_param("i", $usuario_id);
        $stmt_user->execute();
        $user_result = $stmt_user->get_result()->fetch_assoc();
        $nombre_cliente = $user_result['nombre'];
        $email_cliente = $user_result['email'];
        $telefono_cliente = $user_result['telefono'] ?? 'No provisto';
        $stmt_user->close();
    } else { // Si es un visitante
        $nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
        $telefono_cliente = trim($_POST['telefono_cliente'] ?? '');
        if (empty($nombre_cliente)) $errors[] = "Tu nombre es requerido.";
        if (empty($telefono_cliente)) $errors[] = "Tu teléfono es requerido.";
    }

    if (empty($ciudad) || $ciudad == 'Selecciona tu ciudad') $errors[] = "Por favor, selecciona una ciudad.";
    if (empty($marca)) $errors[] = "La marca del vehículo es requerida.";
    if (empty($modelo)) $errors[] = "El modelo del vehículo es requerido.";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO cotizaciones (usuario_id, nombre_cliente, telefono_cliente, email_cliente, ciudad, marca_vehiculo, modelo_vehiculo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $usuario_id, $nombre_cliente, $telefono_cliente, $email_cliente, $ciudad, $marca, $modelo);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "¡Tu cotización ha sido enviada con éxito! Nos pondremos en contacto contigo pronto.";
        } else {
            $_SESSION['error_message'] = "Hubo un error al enviar tu cotización. Por favor, inténtalo de nuevo.";
        }
        $stmt->close();
        
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['form_errors'] = $errors;
        header("Location: index.php");
        exit();
    }
}


// 2. Obtener todo el contenido editable de la página de inicio
$sql = "SELECT campo, valor FROM contenido_editable WHERE seccion = 'home'";
$result = $conn->query($sql);
$home_content = [];
while($row = $result->fetch_assoc()) {
    $home_content[$row['campo']] = $row['valor'];
}
$conn->close();

$page_title = "Inicio";
include 'includes/header.php'; 
?>

<!-- Hero Section -->
<section class="hero-section text-white d-flex align-items-center">
    <div class="container">
        <div class="row align-items-center">
            <!-- Columna de Texto -->
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h1 class="display-3 fw-bold">
                    <?php echo htmlspecialchars($home_content['titulo_principal'] ?? 'Título no encontrado'); ?>
                </h1>
                <div class="lead my-4">
                    <?php echo htmlspecialchars_decode($home_content['subtitulo'] ?? 'Subtítulo no encontrado'); ?>
                </div>
                <div class="d-flex stats-container">
                    <div class="stat-item me-5">
                        <i class="bi bi-car-front-fill me-2"></i>
                        <span class="fw-bold fs-5"><?php echo number_format($home_content['vehiculos_atendidos'] ?? 0); ?></span><br>
                        Vehículos atendidos
                    </div>
                    <div class="stat-item">
                        <i class="bi bi-tools me-2"></i>
                        <span class="fw-bold fs-5">+<?php echo htmlspecialchars($home_content['talleres_aliados'] ?? 0); ?></span><br>
                        Talleres aliados
                    </div>
                </div>
            </div>
            <!-- Columna de Cotización -->
            <div class="col-lg-5">
                <div class="card quote-card p-4">
                    <h3 class="text-center mb-4">Cotiza los servicios para tu vehículo</h3>
                    
                    <?php 
                    if (isset($_SESSION['success_message'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                        unset($_SESSION['success_message']);
                    }
                    if (isset($_SESSION['form_errors'])) {
                        echo '<div class="alert alert-danger"><ul>';
                        foreach ($_SESSION['form_errors'] as $error) {
                            echo '<li>' . htmlspecialchars($error) . '</li>';
                        }
                        echo '</ul></div>';
                        unset($_SESSION['form_errors']);
                    }
                    ?>

                    <form action="index.php" method="POST">
                        <input type="hidden" name="cotizar" value="1">
                        
                        <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre_cliente" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefono_cliente" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono_cliente" name="telefono_cliente" required>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <select class="form-select" id="ciudad" name="ciudad" required>
                                <option selected disabled value="">Selecciona tu ciudad</option>
                                <option value="Campoalegre">Campoalegre</option>
                                <option value="Neiva">Neiva</option>
                                <option value="Bogotá">Bogotá</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="marca" name="marca" placeholder="Ej: Toyota" required>
                        </div>
                        <div class="mb-3">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Ej: Corolla" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-cientifico btn-lg">Cotizar Servicios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección Sobre Nosotros -->
<section class="about-section py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="https://placehold.co/600x400/0d2c4f/fbc108?text=Imagen+del+Taller" class="img-fluid rounded shadow-lg" alt="Interior del Taller El Científico">
            </div>
            <div class="col-lg-6">
                <h5 class="section-subtitle text-uppercase"><?php echo htmlspecialchars($home_content['about_title'] ?? 'Título'); ?></h5>
                <h2 class="section-title display-5"><?php echo htmlspecialchars($home_content['about_title'] ?? 'Título Principal'); ?></h2>
                <div class="lead text-muted">
                    <?php echo htmlspecialchars_decode($home_content['about_text'] ?? 'Texto descriptivo no encontrado.'); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección ¿Por Qué Elegirnos? -->
<section class="why-choose-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h5 class="section-subtitle text-uppercase">Nuestras Fortalezas</h5>
            <h2 class="section-title display-5"><?php echo htmlspecialchars($home_content['why_title'] ?? 'Título no encontrado'); ?></h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="feature-box">
                    <div class="icon"><i class="bi bi-search-heart"></i></div>
                    <h4 class="mb-3"><?php echo htmlspecialchars($home_content['why_1_title'] ?? 'Ventaja 1'); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($home_content['why_1_text'] ?? 'Texto de ventaja 1.'); ?></p>
                </div>
            </div>
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="feature-box">
                    <div class="icon"><i class="bi bi-person-check-fill"></i></div>
                    <h4 class="mb-3"><?php echo htmlspecialchars($home_content['why_2_title'] ?? 'Ventaja 2'); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($home_content['why_2_text'] ?? 'Texto de ventaja 2.'); ?></p>
                </div>
            </div>
            <div class="col-lg-4 d-flex align-items-stretch">
                <div class="feature-box">
                    <div class="icon"><i class="bi bi-shield-fill-check"></i></div>
                    <h4 class="mb-3"><?php echo htmlspecialchars($home_content['why_3_title'] ?? 'Ventaja 3'); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($home_content['why_3_text'] ?? 'Texto de ventaja 3.'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección de Marcas (sin cambios) -->
<section class="brands-section">
    <div class="container py-5">
        <h2 class="text-center mb-5">Marcas que Atendemos</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-6 col-md-4 col-lg-2"><div class="brand-card p-3 text-center"><img src="https://placehold.co/100x50?text=Toyota" class="img-fluid" alt="Toyota"></div></div>
            <div class="col-6 col-md-4 col-lg-2"><div class="brand-card p-3 text-center"><img src="https://placehold.co/100x50?text=Chevrolet" class="img-fluid" alt="Chevrolet"></div></div>
            <div class="col-6 col-md-4 col-lg-2"><div class="brand-card p-3 text-center"><img src="https://placehold.co/100x50?text=Renault" class="img-fluid" alt="Renault"></div></div>
            <div class="col-6 col-md-4 col-lg-2"><div class="brand-card p-3 text-center"><img src="https://placehold.co/100x50?text=Mazda" class="img-fluid" alt="Mazda"></div></div>
            <div class="col-6 col-md-4 col-lg-2"><div class="brand-card p-3 text-center"><img src="https://placehold.co/100x50?text=Kia" class="img-fluid" alt="Kia"></div></div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

