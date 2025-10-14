<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_title = $page_title ?? "Taller El Científico";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Taller El Científico</title>
    <link rel="stylesheet" href="/TALLER_EL_CIENTIFICO/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/TALLER_EL_CIENTIFICO/assets/css/style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/TALLER_EL_CIENTIFICO/index.php">
                <img src="/TALLER_EL_CIENTIFICO/assets/img/logo.jpg" alt="Logo Taller El Científico" style="height: 80px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="/TALLER_EL_CIENTIFICO/index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="/TALLER_EL_CIENTIFICO/servicios.php">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="/TALLER_EL_CIENTIFICO/sedes.php">Nuestras Sedes</a></li>
                    <li class="nav-item"><a class="nav-link" href="/TALLER_EL_CIENTIFICO/contacto.php">Contacto</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if (!empty($_SESSION['user_avatar_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($_SESSION['user_avatar_url']); ?>" alt="Avatar" class="user-avatar me-2">
                                <?php else: ?>
                                    <div class="user-initial me-2">
                                        <?php echo strtoupper(substr($_SESSION['user_nombre_completo'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                                <span class="d-none d-sm-inline"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_nombre_completo'])[0]); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser">
                                <li><a class="dropdown-item" href="/TALLER_EL_CIENTIFICO/cuenta.php"><i class="bi bi-grid-fill me-2"></i>Mi Panel</a></li>
                                <li><a class="dropdown-item" href="/TALLER_EL_CIENTIFICO/editar_perfil.php"><i class="bi bi-person-fill-gear me-2"></i>Editar Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/TALLER_EL_CIENTIFICO/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
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
<main style="padding-top: 80px;"> <!-- Padding to offset fixed navbar -->

