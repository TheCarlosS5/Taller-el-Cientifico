<?php
session_start();
require 'admin/db_config.php';

$errors = [];

if (isset($_SESSION['user_id'])) {
    header('Location: cuenta.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validaciones
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo electrónico no es válido.";
    }
    if (empty($password)) {
        $errors[] = "La contraseña es obligatoria.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, nombre, apellido, password_hash, provider FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            // Si el hash de la contraseña está vacío, significa que es un inicio de sesión social (ej. Google)
            if (empty($user['password_hash']) && $user['provider'] !== 'local') {
                $errors[] = "Esta cuenta fue registrada usando un proveedor social. Por favor, utiliza ese método para iniciar sesión.";
            } 
            // Si hay hash, verificar la contraseña
            elseif (password_verify($password, $user['password_hash'])) {
                // Inicio de sesión exitoso
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nombre_completo'] = trim($user['nombre'] . ' ' . $user['apellido']);
                header('Location: cuenta.php');
                exit();
            } else {
                $errors[] = "La contraseña es incorrecta. Por favor, inténtalo de nuevo.";
            }
        } else {
            $errors[] = "No se encontró una cuenta con ese correo electrónico.";
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
                <div class="login-form-card shadow-lg p-5">
                    <h1 class="text-center mb-4">Iniciar Sesión</h1>

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
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-cientifico btn-lg">Ingresar</button>
                        </div>
                    </form>

                    <p class="text-center mt-4">
                        ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
                    </p>

                    <div class="text-center mt-4">
                        <hr>
                        <a href="admin/" class="admin-link">¿Eres administrador? Ingresa aquí</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

