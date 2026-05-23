<?php
/**
 * HematoLearn — Helper de sesión
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

$usuario = null;

// ── 1. Sesión activa ──────────────────────────────────────────
if (!empty($_SESSION['usuario_id'])) {
    $usuario = [
        'id'     => $_SESSION['usuario_id'],
        'nombre' => $_SESSION['usuario_nombre'],
        'email'  => $_SESSION['usuario_email'],
    ];
}
// ── 2. Intentar restaurar desde cookie "recordarme" ──────────
elseif (!empty($_COOKIE['hl_recordar'])) {
    $token = $_COOKIE['hl_recordar'];

    // Solo intentar BD si la conexión es posible
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT u.id, u.nombre, u.email
             FROM sesiones s
             JOIN usuarios u ON u.id = s.usuario_id
             WHERE s.token = ? AND s.expira_en > NOW() AND u.activo = 1
             LIMIT 1'
        );
        $stmt->execute([$token]);
        $fila = $stmt->fetch();

        if ($fila) {
            $_SESSION['usuario_id']     = $fila['id'];
            $_SESSION['usuario_nombre'] = $fila['nombre'];
            $_SESSION['usuario_email']  = $fila['email'];
            $usuario = $fila;
        }
    } catch (Exception $e) {
        // BD no disponible — continuar sin sesión, no mostrar error
    }
}

/**
 * Redirige a login si no hay sesión activa.
 * Calcula la ruta de login automáticamente según dónde está el archivo que llama.
 */
function require_login(): void {
    global $usuario;
    if ($usuario === null) {
        // Detectar si estamos en /pages/ o en la raíz
        $script = $_SERVER['SCRIPT_FILENAME'] ?? '';
        if (str_contains($script, DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR)) {
            $redirect = 'login.php';
        } else {
            $redirect = 'pages/login.php';
        }
        header('Location: ' . $redirect);
        exit;
    }
}
