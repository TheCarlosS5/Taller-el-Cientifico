<?php
// ¡SOLUCIÓN! Iniciamos la sesión aquí para que esté disponible en TODAS las páginas.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Usaremos rutas absolutas para garantizar que los archivos siempre se encuentren.
$page_title = $page_title ?? "Taller El Científico";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Taller El Científico</title>
    
    <link rel="stylesheet" href="/TALLER_EL_CIENTIFICO/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="/TALLER_EL_CIENTIFICO/assets/css/style.css">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/TALLER_EL_CIENTIFICO/index.php">
                <img src="/TALLER_EL_CIENTIFICO/assets/img/logo.jpg" alt="Logo Taller El Científico" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/TALLER_EL_CIENTIFICO/index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/TALLER_EL_CIENTIFICO/servicios.php">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/TALLER_EL_CIENTIFICO/sedes.php">Nuestras Sedes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/TALLER_EL_CIENTIFICO/contacto.php">Contacto</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <a href="#" class="btn btn-outline-dark dropdown-toggle" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-2"></i>
                                <?php echo htmlspecialchars($_SESSION['user_nombre_completo']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                                <li><a class="dropdown-item" href="/TALLER_EL_CIENTIFICO/cuenta.php">Mi Cuenta</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/TALLER_EL_CIENTIFICO/logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/TALLER_EL_CIENTIFICO/login.php" class="btn btn-cientifico">Iniciar Sesión</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
<main>

