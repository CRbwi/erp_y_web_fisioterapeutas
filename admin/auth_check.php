<?php
session_start();

// Verificar si el usuario está logueado
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Verificar si el usuario está logueado y redirigir si no
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Obtener el nombre de usuario del admin
function getAdminUsername() {
    return $_SESSION['admin_username'] ?? 'Admin';
}

// Cerrar sesión
function logout() {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Verificar si se solicitó logout
if (isset($_GET['logout'])) {
    logout();
}
?>
