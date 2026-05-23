<?php
/**
 * HematoLearn — Inicio de sesión
 * Recibe POST desde pages/login.php
 */

session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/login.html');
    exit;
}

// ── Recoger datos ───────────────────────────────────────────
$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';
$recordar = isset($_POST['recordar']);

// ── Validar ─────────────────────────────────────────────────
if (!$email || !$password) {
    $_SESSION['login_error'] = 'Por favor completa todos los campos.';
    header('Location: ../../pages/login.php');
    exit;
}

// ── Buscar usuario ───────────────────────────────────────────
$db   = getDB();
$stmt = $db->prepare(
    'SELECT id, nombre, email, password_hash, activo FROM usuarios WHERE email = ? LIMIT 1'
);
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
    $_SESSION['login_error'] = 'Correo o contraseña incorrectos.';
    header('Location: ../../pages/login.php');
    exit;
}

if (!$usuario['activo']) {
    $_SESSION['login_error'] = 'Esta cuenta ha sido desactivada.';
    header('Location: ../../pages/login.php');
    exit;
}

// ── Crear sesión ─────────────────────────────────────────────
$_SESSION['usuario_id']     = $usuario['id'];
$_SESSION['usuario_nombre'] = $usuario['nombre'];
$_SESSION['usuario_email']  = $usuario['email'];

// ── "Recordarme": cookie persistente 30 días ────────────────
if ($recordar) {
    $token    = bin2hex(random_bytes(32));   // 64 chars hex
    $expira   = new DateTime('+30 days');

    // Guardar token en BD
    $ins = $db->prepare(
        'INSERT INTO sesiones (usuario_id, token, expira_en) VALUES (?, ?, ?)'
    );
    $ins->execute([$usuario['id'], $token, $expira->format('Y-m-d H:i:s')]);

    // Enviar cookie segura
    setcookie(
        'hl_recordar',
        $token,
        [
            'expires'  => $expira->getTimestamp(),
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            // 'secure' => true,  // ← descomenta si tienes HTTPS
        ]
    );
}

header('Location: ../../index.php');
exit;
