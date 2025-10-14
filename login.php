<?php
session_start();
require 'admin/db_config.php';

$errors = [];
$email = ''; // Inicializamos la variable email

// Si el usuario ya está logueado, lo redirigimos a su cuenta
if (isset($_SESSION['user_id'])) {
    header('Location: cuenta.php');
    exit();
}

// Lógica para el login de cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_cliente'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo electrónico no es válido.";
    }
    if (empty($password)) {
        $errors[] = "La contraseña es obligatoria.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, nombre, apellido, password_hash, provider, avatar_url FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            if (password_verify($password, $user['password_hash'])) {
                // Inicio de sesión exitoso
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nombre_completo'] = trim($user['nombre'] . ' ' . $user['apellido']);
                $_SESSION['user_avatar_url'] = $user['avatar_url'];
                header('Location: cuenta.php');
                exit();
            } else {
                $errors[] = "La contraseña es incorrecta.";
            }
        } else {
            $errors[] = "No se encontró una cuenta activa con ese correo electrónico.";
        }
    }
}
$conn->close();

$page_title = "Iniciar Sesión";
include 'includes/header.php';
?>

<div class="login-page d-flex align-items-center justify-content-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                
                <div class="login-form-card shadow-lg">
                    <!-- Pestañas de Navegación -->
                    <ul class="nav nav-tabs nav-fill" id="loginTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="cliente-tab" data-bs-toggle="tab" data-bs-target="#cliente-pane" type="button" role="tab" aria-controls="cliente-pane" aria-selected="true">
                                <i class="bi bi-person-circle me-2"></i>Soy Cliente
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin-pane" type="button" role="tab" aria-controls="admin-pane" aria-selected="false">
                                <i class="bi bi-shield-lock-fill me-2"></i>Soy Administrador
                            </button>
                        </li>
                    </ul>

                    <!-- Contenido de las Pestañas -->
                    <div class="tab-content p-4 p-md-5">
                        <!-- Pestaña Cliente -->
                        <div class="tab-pane fade show active" id="cliente-pane" role="tabpanel" aria-labelledby="cliente-tab">
                            <h2 class="text-center mb-4">Iniciar Sesión</h2>

                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form action="login.php" method="POST">
                                <input type="hidden" name="login_cliente" value="1">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-cientifico btn-lg">Ingresar</button>
                                </div>
                            </form>
                            <p class="text-center mt-4">¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
                        </div>

                        <!-- Pestaña Administrador -->
                        <div class="tab-pane fade" id="admin-pane" role="tabpanel" aria-labelledby="admin-tab">
                            <div class="text-center">
                                <img src="assets/img/logo.jpg" alt="Logo" class="login-logo mb-4">
                                <h2 class="mb-3">Acceso de Administrador</h2>
                                <p class="text-muted mb-4">El acceso al panel de administración se realiza mediante un código de seguridad enviado a tu correo electrónico.</p>
                                <div class="d-grid">
                                    <a href="admin/" class="btn btn-primary btn-lg">
                                        <i class="bi bi-envelope-check-fill me-2"></i>
                                        Solicitar Código de Acceso
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

