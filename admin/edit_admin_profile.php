<?php
// --- SOLUCIÓN DEFINITIVA: TODA LA LÓGICA ANTES DEL HTML ---

// 1. Cargar dependencias esenciales para la lógica.
// Usamos require_once para evitar conflictos.
require_once 'session_handler.php';
require_once 'db_config.php';
require_once 'functions.php';

$profile_message = '';
$password_message = '';
$profile_alert_type = '';
$password_alert_type = '';
$admin_id = $_SESSION['admin_id'];

// 2. Procesar el formulario de actualización de perfil SI FUE ENVIADO.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $avatar_url = $_POST['current_avatar_url'];

    $upload_dir = __DIR__ . '/../uploads/avatars/';

    if (isset($_FILES['avatar_image']) && $_FILES['avatar_image']['error'] == UPLOAD_ERR_OK) {
        if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
            $profile_message = "Error: El directorio 'uploads/avatars/' no existe o no tiene permisos de escritura.";
            $profile_alert_type = "danger";
        } else {
            $file_tmp_path = $_FILES['avatar_image']['tmp_name'];
            $file_name = $_FILES['avatar_image']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($file_ext, $allowed_ext)) {
                $new_file_name = 'admin_' . $admin_id . '_' . time() . '.' . $file_ext;
                $dest_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp_path, $dest_path)) {
                    $avatar_url = 'uploads/avatars/' . $new_file_name;
                } else {
                    $profile_message = "Hubo un error al mover la imagen subida.";
                    $profile_alert_type = "danger";
                }
            } else {
                $profile_message = "Formato de archivo no permitido.";
                $profile_alert_type = "danger";
            }
        }
    }

    if (empty($profile_message)) {
        $stmt = $conn->prepare("UPDATE administradores SET nombre = ?, email = ?, avatar_url = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $email, $avatar_url, $admin_id);
        
        if($stmt->execute()){
            $_SESSION['admin_nombre'] = $nombre;
            $_SESSION['admin_avatar_url'] = $avatar_url;
            log_activity($conn, $admin_id, 'Actualización de Perfil', 'El administrador actualizó sus datos de perfil.');
            
            // Como no hay HTML impreso, la redirección ahora SÍ FUNCIONA.
            header("Location: edit_admin_profile.php?updated=true");
            exit();
        } else {
            $profile_message = "Hubo un error al actualizar tus datos en la base de datos.";
            $profile_alert_type = "danger";
        }
        $stmt->close();
    }
}

// 3. Procesar el formulario de cambio de contraseña SI FUE ENVIADO.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $password_message = "La funcionalidad de cambio de contraseña no está implementada.";
    $password_alert_type = "info";
}

// 4. AHORA SÍ, empezamos a dibujar la página.
$page_title = "Editar Mi Perfil";
include 'admin_header.php'; 

// Verificamos si venimos de una redirección exitosa para mostrar el mensaje.
if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
    $profile_message = "¡Tus datos han sido actualizados correctamente!";
    $profile_alert_type = "success";
}

// Obtener los datos del admin para rellenar el formulario.
$stmt = $conn->prepare("SELECT nombre, email, avatar_url FROM administradores WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_data = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<h1 class="mb-4">Editar Mi Perfil</h1>

<!-- Pestañas de navegación -->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Mi Información</button>
    </li>
</ul>

<div class="tab-content card">
    <!-- Panel de Mi Información -->
    <div class="tab-pane fade show active p-4" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <h4 class="mb-3">Datos Personales y Avatar</h4>
        
        <?php if($profile_message): ?>
            <div class="alert alert-<?php echo $profile_alert_type; ?>"><?php echo $profile_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_admin_profile.php" enctype="multipart/form-data">
            <input type="hidden" name="update_profile" value="1">
            <input type="hidden" name="current_avatar_url" value="<?php echo htmlspecialchars($admin_data['avatar_url'] ?? ''); ?>">

            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre(s)</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($admin_data['nombre']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin_data['email']); ?>" readonly>
                        </div>
                    </div>
                     <div class="mb-3">
                        <label for="avatar_image" class="form-label">Foto de Perfil</label>
                        <input class="form-control" type="file" id="avatar_image" name="avatar_image">
                        <div class="form-text">Sube una nueva imagen para reemplazar la actual.</div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <label class="form-label">Vista Previa</label>
                    <div>
                        <?php if (!empty($admin_data['avatar_url'])): ?>
                            <img src="../<?php echo htmlspecialchars($admin_data['avatar_url']); ?>" alt="Foto de Perfil" class="avatar-preview">
                        <?php else: ?>
                            <div class="avatar-preview-initial user-initial">
                                <?php echo strtoupper(substr($admin_data['nombre'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
        </form>
    </div>
</div>

<?php include 'admin_footer.php'; ?>

