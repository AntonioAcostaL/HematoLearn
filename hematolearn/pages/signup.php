<?php
session_start();
if (!empty($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
$errores = $_SESSION['signup_errores'] ?? [];
$datos   = $_SESSION['signup_datos']   ?? [];
unset($_SESSION['signup_errores'], $_SESSION['signup_datos']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HematoLearn | Crear cuenta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="auth-page">
    <header>
        <div class="logo">
            <div class="logo-icono">🩸</div>
            <div class="logo-texto">
                <span class="logo-nombre">HematoLearn</span>
                <span class="logo-subtitulo">Plataforma educativa de células sanguíneas</span>
            </div>
        </div>
        <a href="../index.php" class="btn btn-secundario">← Volver al inicio</a>
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
                <li><a href="progreso.php" class="sidebar-link" title="Progreso"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z" />
</svg></a></li>
            </ul>
            <div class="sidebar-sesion">
                <a href="configuracion.php" class="sidebar-link" title="Configuración"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 0 0-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 0 0-2.282.819l-.922 1.597a1.875 1.875 0 0 0 .432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 0 0 0 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 0 0-.432 2.385l.922 1.597a1.875 1.875 0 0 0 2.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 0 0 2.28-.819l.923-1.597a1.875 1.875 0 0 0-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 0 0 0-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 0 0-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 0 0-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 0 0-1.85-1.567h-1.843ZM12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z" clip-rule="evenodd" />
</svg></a>
                <a href="login.php" class="btn-sesion" title="Iniciar sesión"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
</svg>
<br>Sesión</a>
                <a href="signup.php" class="btn-registro activo" title="Registrarse"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path d="M5.25 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM2.25 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM18.75 7.5a.75.75 0 0 0-1.5 0v2.25H15a.75.75 0 0 0 0 1.5h2.25v2.25a.75.75 0 0 0 1.5 0v-2.25H21a.75.75 0 0 0 0-1.5h-2.25V7.5Z" />
</svg>
<br>Registro</a>
            </div>
        </aside>
        <div class="auth-contenido">
            <div class="auth-card">
                <div class="auth-icono registro">👤</div>
                <h1 class="auth-titulo">Crear cuenta</h1>
                <p class="auth-subtitulo">Únete para acceder a contenido educativo exclusivo</p>

                <?php if ($errores): ?>
                <div class="auth-error">
                    <?php foreach ($errores as $e): ?>
                    <p>⚠️ <?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <form class="auth-form" method="POST" action="../php/auth/registro.php">
                    <div class="form-row">
                        <div class="form-grupo">
                            <label>Nombre <span class="req">*</span></label>
                            <input type="text" name="nombre" placeholder="Juan"
                                   value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>
                        </div>
                        <div class="form-grupo">
                            <label>Apellido <span class="req">*</span></label>
                            <input type="text" name="apellido" placeholder="Pérez"
                                   value="<?= htmlspecialchars($datos['apellido'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-grupo">
                        <label>Correo electrónico <span class="req">*</span></label>
                        <input type="email" name="email" placeholder="tu@email.com"
                               value="<?= htmlspecialchars($datos['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-grupo">
                        <label>Institución / Universidad</label>
                        <input type="text" name="institucion" placeholder="Universidad Autónoma de Baja California"
                               value="<?= htmlspecialchars($datos['institucion'] ?? '') ?>">
                    </div>
                    <div class="form-grupo">
                        <label>Contraseña <span class="req">*</span></label>
                        <input type="password" name="password" placeholder="Mínimo 8 caracteres" required>
                    </div>
                    <div class="form-grupo">
                        <label>Confirmar contraseña <span class="req">*</span></label>
                        <input type="password" name="confirm" placeholder="••••••••" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="terminos" name="terminos" required>
                        <label for="terminos">Acepto los <a href="#">términos y condiciones</a> y la <a href="#">política de privacidad</a> <span class="req">*</span></label>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;">
                        <a href="../index.php" class="btn btn-secundario" style="justify-content:center;">Cancelar</a>
                        <button type="submit" class="btn btn-rojo">Crear cuenta</button>
                    </div>
                    <p class="auth-pie">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
