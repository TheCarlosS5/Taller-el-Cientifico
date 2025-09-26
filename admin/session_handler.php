<?php
session_start();

// Si la variable de sesión del admin no existe,
// lo redirigimos a la página de login.
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
?>
