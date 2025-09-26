<?php
session_start(); // Inicia la sesión
require 'db_config.php';

$message = '';
$email = $_GET['email'] ?? $_POST['email'] ?? '';

if (empty($email)) {
    header('Location: index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];

    // 1. Buscar el código en la base de datos
    $stmt = $conn->prepare("SELECT * FROM admin_codes WHERE email = ? AND code = ? AND used = 0 AND expires_at > NOW()");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $code_data = $result->fetch_assoc();
        
        // 2. Marcar el código como usado para que no se pueda volver a utilizar
        $update_stmt = $conn->prepare("UPDATE admin_codes SET used = 1 WHERE id = ?");
        $update_stmt->bind_param("i", $code_data['id']);
        $update_stmt->execute();

        // 3. Obtener los datos del administrador
        $admin_stmt = $conn->prepare("SELECT id, nombre FROM administradores WHERE email = ?");
        $admin_stmt->bind_param("s", $email);
        $admin_stmt->execute();
        $admin_result = $admin_stmt->get_result();
        $admin = $admin_result->fetch_assoc();

        // 4. Iniciar la sesión del administrador
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nombre'] = $admin['nombre'];

        // 5. Redirigir al dashboard
        header("Location: dashboard.php");
        exit();

    } else {
        // Si el código es incorrecto, ha expirado o ya fue usado
        $message = "<div class='alert alert-danger'>El código es incorrecto, ha expirado o ya fue utilizado. Por favor, solicita uno nuevo.</div>";
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
    <style>
        .admin-login-page {
            background-color: var(--cientifico-dark-blue);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-login-card {
            background-color: var(--cientifico-white);
            border-radius: 15px;
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
            <div class="card admin-login-card shadow-lg p-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="../assets/img/logo.jpg" alt="Logo" style="max-height: 70px;">
                        <h1 class="h3 my-3">Verificar Código</h1>
                    </div>
                    
                    <?php echo $message; ?>

                    <p class="text-center text-muted">Hemos enviado un código de 6 dígitos a <strong><?php echo htmlspecialchars($email); ?></strong>. Por favor, ingrésalo a continuación.</p>
                    
                    <form action="verify_code.php" method="POST">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control code-input" id="code" name="code" placeholder="123456" maxlength="6" required autofocus>
                            <label for="code">Código de Acceso</label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-cientifico btn-lg">Verificar e Ingresar</button>
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

