<?php
session_start();
// SOLUCIÓN: Usamos __DIR__ para garantizar que encuentre el archivo de configuración.
require_once __DIR__ . '/db_config.php';

$message = '';
$email = $_GET['email'] ?? $_POST['email'] ?? '';

if (empty($email)) {
    header('Location: index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];

    // La lógica de verificación se mantiene, ya era correcta.
    $stmt = $conn->prepare("SELECT * FROM admin_codes WHERE email = ? AND code = ? AND used = 0 AND expires_at > NOW()");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $code_data = $result->fetch_assoc();
        
        $update_stmt = $conn->prepare("UPDATE admin_codes SET used = 1 WHERE id = ?");
        $update_stmt->bind_param("i", $code_data['id']);
        $update_stmt->execute();

        // Actualizamos la consulta para traer también el avatar_url
        $admin_stmt = $conn->prepare("SELECT id, nombre, avatar_url FROM administradores WHERE email = ?");
        $admin_stmt->bind_param("s", $email);
        $admin_stmt->execute();
        $admin_result = $admin_stmt->get_result();
        $admin = $admin_result->fetch_assoc();

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nombre'] = $admin['nombre'];
        $_SESSION['admin_avatar_url'] = $admin['avatar_url']; // Guardamos la foto en la sesión

        header("Location: dashboard.php");
        exit();

    } else {
        $message = "<div class='alert alert-danger'>El código es incorrecto o ha expirado.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código - Admin</title>
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .admin-login-page {
            background-color: var(--cientifico-dark-blue);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .code-input {
            text-align: center;
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
        }
    </style>
</head>
<body class="admin-login-page">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg p-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="../assets/img/logo.jpg" alt="Logo" style="max-height: 70px;">
                        <h1 class="h3 my-3">Verificar Código</h1>
                    </div>
                    
                    <?php if(!empty($message)) echo $message; ?>

                    <p class="text-center text-muted">Hemos enviado un código de 6 dígitos a <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
                    
                    <form action="verify_code.php" method="POST">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control code-input" id="code" name="code" placeholder="123456" maxlength="6" required autofocus>
                            <label for="code">Código de Acceso</label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-cientifico btn-lg">
                                <i class="bi bi-key-fill me-2"></i>Verificar e Ingresar
                            </button>
                        </div>
                    </form>
                     <div class="text-center mt-3">
                        <a href="index.php">Solicitar otro código</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

