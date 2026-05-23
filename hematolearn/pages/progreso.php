<?php
require_once __DIR__ . '/../php/auth/sesion.php';

try {


$db  = getDB();
$uid = $usuario['id'];

// Células vistas por línea
$stmt = $db->prepare('
    SELECT c.linea,
           COUNT(pc.celula_id) AS vistas,
           (SELECT COUNT(*) FROM celulas c2 WHERE c2.linea = c.linea) AS total
    FROM celulas c
    LEFT JOIN progreso_celulas pc ON pc.celula_id = c.id AND pc.usuario_id = ?
    GROUP BY c.linea
');
$stmt->execute([$uid]);
$porLinea = $stmt->fetchAll();

// Transformar en array clave => datos
$progLinea = [];
foreach ($porLinea as $fila) {
    $progLinea[$fila['linea']] = $fila;
}

// Exámenes
$stmtE = $db->prepare('
    SELECT COUNT(*) AS completados,
           COALESCE(ROUND(AVG(puntaje),1), 0) AS promedio,
           (SELECT COUNT(*) FROM examenes WHERE activo=1) AS total
    FROM intentos_examen
    WHERE usuario_id = ? AND completado = 1
');
$stmtE->execute([$uid]);
$examStats = $stmtE->fetch();

// Porcentaje general
$totalCelulas = array_sum(array_column($porLinea, 'total'));
$vistasTotal  = array_sum(array_column($porLinea, 'vistas'));
$pctGeneral   = $totalCelulas > 0 ? round($vistasTotal / $totalCelulas * 100) : 0;

// Últimas células vistas
$stmtU = $db->prepare('
    SELECT c.nombre, c.linea, c.etapa, pc.visto_en
    FROM progreso_celulas pc
    JOIN celulas c ON c.id = pc.celula_id
    WHERE pc.usuario_id = ?
    ORDER BY pc.visto_en DESC
    LIMIT 5
');
$stmtU->execute([$uid]);
$ultimasVistas = $stmtU->fetchAll();

} catch (\Exception $e) {
    // BD no disponible - usar arrays vacíos
    $lineas = []; $celulas = []; $intentos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HematoLearn | Mi Progreso</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body<?php if ($usuario): ?> data-sesion="1"<?php endif; ?>>
<!-- Modal: acceso restringido -->
<div class="modal-acceso-overlay" id="modalAcceso">
    <div class="modal-acceso-card">
        <div class="modal-acceso-icono">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3A5.25 5.25 0 0 0 12 1.5Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
            </svg>
        </div>
        <h2>Contenido exclusivo</h2>
        <p>Necesitas una cuenta para acceder a esta sección. Es gratis y solo tarda un momento.</p>
        <div class="modal-acceso-botones">
            <a href="login.php" class="modal-btn-login">Iniciar sesión</a>
            <a href="signup.php" class="modal-btn-registro">Registrarse</a>
        </div>
    </div>
</div>

    <header>
        <div class="logo">
            <div class="logo-icono">🩸</div>
            <div class="logo-texto">
                <span class="logo-nombre">HematoLearn</span>
                <span class="logo-subtitulo">Plataforma educativa de células sanguíneas</span>
            </div>
        </div>
        <div class="header-usuario">
            <span class="usuario-saludo">👋 <strong><?= htmlspecialchars($usuario['nombre']) ?></strong></span>
        </div>
    </header>
    <div class="layout">
        <aside class="sidebar">
            <ul class="sidebar-nav">
                <li><a href="../index.php" class="sidebar-link" title="Inicio"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
  <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
</svg></a></li>
                <li><a href="galeria.php" class="sidebar-link" title="Galería"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" />
</svg></a></li>
                <li><a href="examenes.php" class="sidebar-link" title="Exámenes"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M9 1.5H5.625c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5Zm6.61 10.936a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 14.47a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
  <path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
</svg></a></li>
                <li><a href="progreso.php" class="sidebar-link activo" title="Progreso"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z" />
</svg></a></li>
            </ul>
                        <div class="sidebar-sesion">
                <a href="configuracion.php" class="sidebar-link" title="Configuración"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 0 0-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 0 0-2.282.819l-.922 1.597a1.875 1.875 0 0 0 .432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 0 0 0 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 0 0-.432 2.385l.922 1.597a1.875 1.875 0 0 0 2.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 0 0 2.28-.819l.923-1.597a1.875 1.875 0 0 0-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 0 0 0-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 0 0-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 0 0-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 0 0-1.85-1.567h-1.843ZM12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z" clip-rule="evenodd" />
</svg>
</a>
                <?php if ($usuario): ?>
                <a href="../php/auth/logout.php" class="btn-sesion" title="Cerrar sesión"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M7.5 3.75A1.5 1.5 0 0 0 6 5.25v13.5a1.5 1.5 0 0 0 1.5 1.5h6a1.5 1.5 0 0 0 1.5-1.5V15a.75.75 0 0 1 1.5 0v3.75a3 3 0 0 1-3 3h-6a3 3 0 0 1-3-3V5.25a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3V9A.75.75 0 0 1 15 9V5.25a1.5 1.5 0 0 0-1.5-1.5h-6Zm10.72 4.72a.75.75 0 0 1 1.06 0l3 3a.75.75 0 0 1 0 1.06l-3 3a.75.75 0 1 1-1.06-1.06l1.72-1.72H9a.75.75 0 0 1 0-1.5h10.94l-1.72-1.72a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
</svg>
<br>Salir</a>
                <?php else: ?>
                <a href="login.php" class="btn-sesion" title="Iniciar sesión"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
</svg>
<br>Sesión</a>
                <a href="signup.php" class="btn-registro" title="Registrarse"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path d="M5.25 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM2.25 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM18.75 7.5a.75.75 0 0 0-1.5 0v2.25H15a.75.75 0 0 0 0 1.5h2.25v2.25a.75.75 0 0 0 1.5 0v-2.25H21a.75.75 0 0 0 0-1.5h-2.25V7.5Z" />
</svg>
<br>Registro</a>
                <?php endif; ?>
            </div>
        </aside>
        <main class="contenido">
            <div style="margin-bottom:0.5rem;">
                <h1 class="page-titulo">Mi Progreso</h1>
                <p class="page-subtitulo">Seguimiento de tu aprendizaje en hematología</p>
            </div>

            <!-- Resumen circular + barras -->
            <div class="progreso-resumen">
                <div class="progreso-circulo-wrap">
                    <div class="progreso-circulo">
                        <svg class="progreso-svg" viewBox="0 0 100 100" width="110" height="110">
                            <circle class="progreso-track" cx="50" cy="50" r="45"/>
                            <circle class="progreso-fill" cx="50" cy="50" r="45"
                                style="stroke-dashoffset:<?= 283 - round(283 * $pctGeneral / 100) ?>"/>
                        </svg>
                        <div class="progreso-pct"><?= $pctGeneral ?>%</div>
                    </div>
                    <p class="progreso-label">Progreso general</p>
                </div>
                <div class="progreso-lineas">
                    <?php foreach ($porLinea as $l):
                        $pct = $l['total'] > 0 ? round($l['vistas'] / $l['total'] * 100) : 0;
                        $esMieloide = $l['linea'] === 'mieloide';
                    ?>
                    <div class="prog-linea-item">
                        <div class="prog-linea-label">
                            <span><?= $esMieloide ? '🔵 Línea Mieloide' : '🔴 Línea Linfoide' ?></span>
                            <span><?= $l['vistas'] ?>/<?= $l['total'] ?> células</span>
                        </div>
                        <div class="prog-bar-bg">
                            <div class="prog-bar-fill <?= $esMieloide ? '' : 'rojo' ?>"
                                 style="width:<?= $pct ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="prog-linea-item">
                        <div class="prog-linea-label">
                            <span>📋 Exámenes completados</span>
                            <span><?= $examStats['completados'] ?>/<?= $examStats['total'] ?></span>
                        </div>
                        <div class="prog-bar-bg">
                            <div class="prog-bar-fill"
                                 style="width:<?= $examStats['total'] > 0 ? round($examStats['completados'] / $examStats['total'] * 100) : 0 ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats rápidas -->
            <div class="examenes-stats" style="margin-top:0;">
                <div class="stat-card">
                    <div class="stat-icono morado">🔬</div>
                    <div>
                        <div class="stat-numero"><?= $vistasTotal ?></div>
                        <div class="stat-label">Células estudiadas</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icono rojo">📋</div>
                    <div>
                        <div class="stat-numero"><?= $examStats['completados'] ?></div>
                        <div class="stat-label">Exámenes completados</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icono verde">🎯</div>
                    <div>
                        <div class="stat-numero"><?= $examStats['promedio'] ?>%</div>
                        <div class="stat-label">Promedio en exámenes</div>
                    </div>
                </div>
            </div>

            <!-- Últimas vistas -->
            <?php if ($ultimasVistas): ?>
            <div style="background:var(--blanco); border:1px solid var(--gris-borde); border-radius:var(--radio-lg); padding:1.2rem; box-shadow:var(--sombra);">
                <h2 style="font-size:0.95rem; font-weight:700; color:var(--morado-oscuro); margin-bottom:0.75rem;">
                    🕐 Células vistas recientemente
                </h2>
                <div style="display:flex; flex-direction:column; gap:0.5rem;">
                    <?php foreach ($ultimasVistas as $v):
                        $esMieloide = $v['linea'] === 'mieloide';
                    ?>
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:0.5rem 0; border-bottom:1px solid var(--gris-fondo);">
                        <div style="display:flex; align-items:center; gap:0.6rem;">
                            <span class="tag <?= $esMieloide ? 'tag-morado' : 'tag-rojo' ?>">
                                Etapa <?= $v['etapa'] ?>
                            </span>
                            <span style="font-size:0.85rem; font-weight:500;"><?= htmlspecialchars($v['nombre']) ?></span>
                        </div>
                        <span style="font-size:0.72rem; color:var(--gris-texto);">
                            <?= date('d/m/Y', strtotime($v['visto_en'])) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </main>
    </div>

<script>
// Mostrar modal si no hay sesión
(function() {
    if (!document.body.dataset.sesion) {
        var overlay = document.getElementById('modalAcceso');
        if (overlay) overlay.classList.add('visible');
    }
})();
</script>
</body>
</html>
