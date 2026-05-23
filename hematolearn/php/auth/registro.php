<?php
/**
 * HematoLearn — Registro de usuario
 * Recibe POST desde pages/signup.php
 */

session_start();
require_once __DIR__ . '/../config/db.php';

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/signup.php');
    exit;
}

// ── Recoger y limpiar datos ─────────────────────────────────
$nombre      = trim($_POST['nombre']      ?? '');
$apellido    = trim($_POST['apellido']    ?? '');
$email       = trim($_POST['email']       ?? '');
$institucion = trim($_POST['institucion'] ?? '');
$password    = $_POST['password']         ?? '';
$confirm     = $_POST['confirm']          ?? '';

// ── Validaciones ────────────────────────────────────────────
$errores = [];

if ($nombre === '')   $errores[] = 'El nombre es obligatorio.';
if ($apellido === '') $errores[] = 'El apellido es obligatorio.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                      $errores[] = 'El correo no es válido.';
if (strlen($password) < 8)
                      $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
if ($password !== $confirm)
                      $errores[] = 'Las contraseñas no coinciden.';

if ($errores) {
    $_SESSION['signup_errores'] = $errores;
    $_SESSION['signup_datos']   = compact('nombre', 'apellido', 'email', 'institucion');
    header('Location: ../../pages/signup.php');
    exit;
}

// ── Verificar email duplicado ────────────────────────────────
$db   = getDB();
$stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
$stmt->execute([$email]);

if ($stmt->fetch()) {
    $_SESSION['signup_errores'] = ['Ese correo electrónico ya está registrado.'];
    $_SESSION['signup_datos']   = compact('nombre', 'apellido', 'email', 'institucion');
    header('Location: ../../pages/signup.php');
    exit;
}

// ── Insertar usuario ─────────────────────────────────────────
$hash = password_hash($password, PASSWORD_BCRYPT);

$insert = $db->prepare(
    'INSERT INTO usuarios (nombre, apellido, email, institucion, password_hash)
     VALUES (?, ?, ?, ?, ?)'
);
$insert->execute([$nombre, $apellido, $email, $institucion ?: null, $hash]);
$nuevoId = (int) $db->lastInsertId();

// Inicializar progreso (solo si la tabla existe — no falla si no)
try {
    $prog = $db->prepare(
        'INSERT INTO progreso_usuario (usuario_id, linea, etapa) VALUES (?, ?, 1)'
    );
    $prog->execute([$nuevoId, 'mieloide']);
    $prog->execute([$nuevoId, 'linfoide']);
} catch (PDOException $e) {
    // Si la tabla no existe, ignorar — no bloquea el registro
}

// ── Iniciar sesión ───────────────────────────────────────────
session_regenerate_id(true);
$_SESSION['usuario_id']     = $nuevoId;
$_SESSION['usuario_nombre'] = $nombre;
$_SESSION['usuario_email']  = $email;

header('Location: ../../index.php');
exit;
