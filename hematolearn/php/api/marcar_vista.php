<?php
require_once __DIR__ . '/../auth/sesion.php';
if (!$usuario) { http_response_code(401); exit; }

$celulaId = (int)($_GET['celula_id'] ?? 0);
if ($celulaId < 1) { http_response_code(400); exit; }

$db = getDB();
$db->prepare('INSERT IGNORE INTO progreso_celulas (usuario_id, celula_id) VALUES (?, ?)')
   ->execute([$usuario['id'], $celulaId]);

echo json_encode(['ok' => true]);
