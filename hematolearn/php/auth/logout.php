<?php
session_start();

// Limpiar cookie "recordarme"
if (!empty($_COOKIE['hl_recordar'])) {
    require_once __DIR__ . '/../config/db.php';
    $db = getDB();
    $db->prepare('DELETE FROM sesiones WHERE token = ?')->execute([$_COOKIE['hl_recordar']]);
    setcookie('hl_recordar', '', time() - 3600, '/');
}

session_unset();
session_destroy();
header('Location: ../../index.php');
exit;
