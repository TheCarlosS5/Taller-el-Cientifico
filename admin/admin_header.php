<?php
// Usamos require_once para evitar errores de doble inclusión.
require_once 'session_handler.php';
require_once 'db_config.php';

// Obtenemos el nombre del script actual para marcar el enlace activo
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin'; ?> - Taller El Científico</title>
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts para un look más pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-page">
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/img/logo.jpg" alt="Logo" class="admin-logo">
                <h5>Admin Panel</h5>
            </div>
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                            <i class="bi bi-grid-1x2-fill me-3"></i>Dashboard
                        </a>
                    </li>

                    <li class="nav-item-header px-3 mt-3">GESTIÓN</li>
                    <li class="nav-item">
                        <a href="view_cotizaciones.php" class="nav-link <?php echo ($current_page == 'view_cotizaciones.php') ? 'active' : ''; ?>">
                            <i class="bi bi-file-earmark-text-fill me-3"></i>Cotizaciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="view_usuarios.php" class="nav-link <?php echo ($current_page == 'view_usuarios.php') ? 'active' : ''; ?>">
                            <i class="bi bi-people-fill me-3"></i>Usuarios
                        </a>
                    </li>

                    <li class="nav-item-header px-3 mt-3">CONTENIDO WEB</li>
                    <li class="nav-item">
                        <a href="edit_home.php" class="nav-link <?php echo ($current_page == 'edit_home.php') ? 'active' : ''; ?>">
                            <i class="bi bi-house-door-fill me-3"></i>Editar Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="edit_servicios.php" class="nav-link <?php echo ($current_page == 'edit_servicios.php') ? 'active' : ''; ?>">
                            <i class="bi bi-tools me-3"></i>Editar Servicios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="edit_sedes.php" class="nav-link <?php echo ($current_page == 'edit_sedes.php') ? 'active' : ''; ?>">
                            <i class="bi bi-geo-alt-fill me-3"></i>Editar Sedes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="edit_contacto.php" class="nav-link <?php echo ($current_page == 'edit_contacto.php') ? 'active' : ''; ?>">
                            <i class="bi bi-telephone-fill me-3"></i>Editar Contacto
                        </a>
                    </li>

                    <li class="nav-item-header px-3 mt-3">SISTEMA</li>
                    <li class="nav-item">
                        <a href="view_logs.php" class="nav-link <?php echo ($current_page == 'view_logs.php') ? 'active' : ''; ?>">
                            <i class="bi bi-shield-lock-fill me-3"></i>Registro de Actividad
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="sidebar-footer">
                <a href="edit_admin_profile.php" class="sidebar-profile-link">
                    <div class="d-flex align-items-center">
                        <?php if (!empty($_SESSION['admin_avatar_url'])): ?>
                            <img src="../<?php echo htmlspecialchars($_SESSION['admin_avatar_url']); ?>" ... class="sidebar-avatar">
                        <?php else: ?>
                            <div class="sidebar-initials">
                                <?php echo strtoupper(substr($_SESSION['admin_nombre'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <span class="ms-2 user-name"><?php echo htmlspecialchars($_SESSION['admin_nombre']); ?></span>
                    </div>
                </a>
                <a href="logout.php" class="btn btn-logout w-100 mt-3">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                </a>
            </div>
        </aside>
        
        <div class="main-content">
            <main class="container-fluid">

