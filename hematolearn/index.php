<?php
require_once __DIR__ . '/php/auth/sesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HematoLearn | Inicio</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        .linea-activa-badge {
            display: inline-block;
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            padding: 0.15rem 0.5rem;
            border-radius: 20px;
            margin-left: 0.4rem;
            vertical-align: middle;
            text-transform: uppercase;
        }
        .badge-mieloide { background: var(--morado-suave); color: var(--morado-oscuro); }
        .badge-linfoide  { background: #fde8e7; color: var(--rojo-fuerte); }

        /* Visor 3D */
        .visor-celula {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        #canvas3d {
            border-radius: 16px;
            border: 1.5px solid var(--gris-borde);
            background: transparent;
            cursor: grab;
            display: block;
        }
        #canvas3d:active { cursor: grabbing; }

        .celula-tamanio {
            background: var(--gris-fondo);
            border: 1px solid var(--gris-borde);
            border-radius: var(--radio);
            padding: 0.4rem 1.2rem;
            text-align: center;
        }
        .celula-tamanio .etiqueta { font-size: 0.72rem; color: var(--gris-texto); display: block; }
        .celula-tamanio .valor    { font-size: 1.2rem; font-weight: 700; color: var(--morado-oscuro); transition: color 0.3s; }

        .celula-hint { font-size: 0.7rem; color: var(--gris-texto); opacity: 0.7; }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <div class="logo-icono">🩸</div>
        <div class="logo-texto">
            <span class="logo-nombre">HematoLearn</span>
            <span class="logo-subtitulo">Plataforma educativa de células sanguíneas</span>
        </div>
    </div>
</header>

<div class="layout">
    <aside class="sidebar">
        <ul class="sidebar-nav">
            <li><a href="index.php" class="sidebar-link activo" title="Inicio"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
  <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
</svg>
</a></li>
            <li><a href="pages/galeria.php" class="sidebar-link" title="Galería"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" />
</svg>
</a></li>
            <li><a href="pages/examenes.php" class="sidebar-link" title="Exámenes"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M9 1.5H5.625c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5Zm6.61 10.936a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 14.47a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
  <path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
</svg>
</a></li>
            <li><a href="pages/progreso.php" class="sidebar-link" title="Progreso"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z" />
</svg>
</a></li>
        </ul>
                    <div class="sidebar-sesion">
                <a href="pages/configuracion.php" class="sidebar-link" title="Configuración"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 0 0-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 0 0-2.282.819l-.922 1.597a1.875 1.875 0 0 0 .432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 0 0 0 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 0 0-.432 2.385l.922 1.597a1.875 1.875 0 0 0 2.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 0 0 2.28-.819l.923-1.597a1.875 1.875 0 0 0-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 0 0 0-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 0 0-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 0 0-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 0 0-1.85-1.567h-1.843ZM12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z" clip-rule="evenodd" />
</svg>
</a>
                <?php if ($usuario): ?>
                <a href="php/auth/logout.php" class="btn-sesion" title="Cerrar sesión"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M7.5 3.75A1.5 1.5 0 0 0 6 5.25v13.5a1.5 1.5 0 0 0 1.5 1.5h6a1.5 1.5 0 0 0 1.5-1.5V15a.75.75 0 0 1 1.5 0v3.75a3 3 0 0 1-3 3h-6a3 3 0 0 1-3-3V5.25a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3V9A.75.75 0 0 1 15 9V5.25a1.5 1.5 0 0 0-1.5-1.5h-6Zm10.72 4.72a.75.75 0 0 1 1.06 0l3 3a.75.75 0 0 1 0 1.06l-3 3a.75.75 0 1 1-1.06-1.06l1.72-1.72H9a.75.75 0 0 1 0-1.5h10.94l-1.72-1.72a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
</svg>
<br>Salir</a>
                <?php else: ?>
                <a href="pages/login.php" class="btn-sesion" title="Iniciar sesión"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
</svg>
<br>Sesión</a>
                <a href="pages/signup.php" class="btn-registro" title="Registrarse"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path d="M5.25 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM2.25 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM18.75 7.5a.75.75 0 0 0-1.5 0v2.25H15a.75.75 0 0 0 0 1.5h2.25v2.25a.75.75 0 0 0 1.5 0v-2.25H21a.75.75 0 0 0 0-1.5h-2.25V7.5Z" />
</svg>
<br>Registro</a>
                <?php endif; ?>
            </div>
        </aside>

    <main class="contenido">

        <!-- Visualizador 3D -->
        <section class="seccion-celula">

            <div class="visor-celula">
                <canvas id="canvas3d" width="220" height="220"></canvas>
                <div class="celula-tamanio">
                    <span class="etiqueta">Tamaño celular</span>
                    <span class="valor" id="celula-valor-tam">20.0 μm</span>
                </div>
                <span class="celula-hint">⟳ Arrastra para rotar</span>
            </div>

            <aside class="panel-info">
                <h2 class="panel-titulo">
                    ℹ️ Información de la célula
                    <span id="badge-linea" class="linea-activa-badge badge-mieloide">MIELOIDE</span>
                </h2>

                <div class="tarjeta">
                    <h3>Detalles de la célula</h3>
                    <p class="etiqueta-campo">Tipo de célula</p>
                    <p id="info-tipo">Célula Madre Hematopoyética</p>
                    <p class="etiqueta-campo">Función principal</p>
                    <p id="info-funcion">Origen de todas las células sanguíneas</p>
                    <p class="etiqueta-campo">Localización</p>
                    <p id="info-localizacion">Médula ósea</p>
                </div>

                <div class="tarjeta">
                    <h3>Datos morfológicos</h3>
                    <ul>
                        <li id="info-etapa-li">Etapa 1 de 8</li>
                        <li id="info-linea-li">Línea celular: Mieloide</li>
                        <li id="info-nucleo-li">Núcleo: grande, redondo</li>
                        <li id="info-extra-li">Alta relación N/C</li>
                    </ul>
                </div>
            </aside>
        </section>

        <!-- Línea Mieloide -->
        <section class="seccion-linea">
            <div class="linea-encabezado mieloide"><h2>LÍNEA MIELOIDE</h2></div>
            <div class="linea-navegacion">
                <button class="btn-nav" id="mieloide-prev" disabled>‹ Anterior</button>
                <div class="linea-centro">
                    <p class="linea-nombre" id="mieloide-nombre">Célula Madre Hematopoyética</p>
                    <div class="puntos-progreso" id="mieloide-puntos">
                        <span class="punto-prog activo"></span><span class="punto-prog"></span>
                        <span class="punto-prog"></span><span class="punto-prog"></span>
                        <span class="punto-prog"></span><span class="punto-prog"></span>
                        <span class="punto-prog"></span><span class="punto-prog"></span>
                    </div>
                    <p class="linea-etapa" id="mieloide-etapa">Etapa 1 de 8</p>
                </div>
                <button class="btn-nav activo" id="mieloide-next">Siguiente ›</button>
            </div>
        </section>

        <!-- Línea Linfoide -->
        <section class="seccion-linea">
            <div class="linea-encabezado linfoide"><h2>LÍNEA LINFOIDE</h2></div>
            <div class="linea-navegacion">
                <button class="btn-nav" id="linfoide-prev" disabled>‹ Anterior</button>
                <div class="linea-centro linfoide">
                    <p class="linea-nombre" id="linfoide-nombre">Célula Madre Hematopoyética</p>
                    <div class="puntos-progreso linfoide" id="linfoide-puntos">
                        <span class="punto-prog activo"></span><span class="punto-prog"></span>
                        <span class="punto-prog"></span><span class="punto-prog"></span>
                        <span class="punto-prog"></span><span class="punto-prog"></span>
                        <span class="punto-prog"></span><span class="punto-prog"></span>
                    </div>
                    <p class="linea-etapa" id="linfoide-etapa">Etapa 1 de 8</p>
                </div>
                <button class="btn-nav activo" id="linfoide-next">Siguiente ›</button>
            </div>
        </section>

    </main>
</div>

<script>

//  DATOS DE MADURACIÓN

const DATOS = {
  mieloide: [
    { nombre:"Célula Madre Hematopoyética", tam:20, radioC:1.0,  radioN:0.55, nucleoloR:0.18, nOrg:1,  anucleado:false, segm:false,
      tipo:"Célula Madre Hematopoyética (HSC)", funcion:"Origen de todas las células sanguíneas", loc:"Médula ósea",
      nucleoDesc:"Grande, redondo, cromatina laxa", extra:"Alta relación N/C · Nucléolo prominente" },
    { nombre:"Mieloblasto", tam:18, radioC:0.95, radioN:0.52, nucleoloR:0.16, nOrg:2,  anucleado:false, segm:false,
      tipo:"Mieloblasto", funcion:"Primera célula reconocible de la serie granulocítica", loc:"Médula ósea",
      nucleoDesc:"Grande, oval, 2-5 nucléolos", extra:"Citoplasma basófilo · Sin gránulos" },
    { nombre:"Promielocito", tam:20, radioC:1.0,  radioN:0.48, nucleoloR:0.13, nOrg:4,  anucleado:false, segm:false,
      tipo:"Promielocito", funcion:"Producción de gránulos primarios azurófilos", loc:"Médula ósea",
      nucleoDesc:"Oval excéntrico, nucléolo visible", extra:"Gránulos primarios abundantes" },
    { nombre:"Mielocito", tam:16, radioC:0.88, radioN:0.42, nucleoloR:0,    nOrg:6,  anucleado:false, segm:false,
      tipo:"Mielocito", funcion:"Gránulos secundarios; inicio de segmentación nuclear", loc:"Médula ósea",
      nucleoDesc:"Oval achatado, cromatina moderada", extra:"Sin nucléolo · Gránulos secundarios" },
    { nombre:"Metamielocito", tam:14, radioC:0.80, radioN:0.36, nucleoloR:0,    nOrg:7,  anucleado:false, segm:false, rinon:true,
      tipo:"Metamielocito", funcion:"Célula en tránsito; no se divide", loc:"Médula ósea / Sangre periférica",
      nucleoDesc:"Indentado en forma de riñón", extra:"Precursor de granulocito en banda" },
    { nombre:"Granulocito en Banda", tam:13, radioC:0.76, radioN:0.32, nucleoloR:0,    nOrg:8,  anucleado:false, segm:false, rinon:true,
      tipo:"Granulocito en Banda (Cayado)", funcion:"Forma inmadura circulante; sale a sangre", loc:"Médula ósea / Sangre periférica",
      nucleoDesc:"Herradura; lóbulos sin filamento", extra:"Respuesta a infección aguda" },
    { nombre:"Neutrófilo Segmentado", tam:12, radioC:0.72, radioN:0,    nucleoloR:0,    nOrg:9,  anucleado:false, segm:true,
      tipo:"Neutrófilo Segmentado (PMN)", funcion:"Fagocitosis y destrucción de bacterias", loc:"Sangre periférica / Tejidos",
      nucleoDesc:"3-5 lóbulos unidos por filamentos", extra:"Gránulos terciarios · Vida media 6-8h" },
    { nombre:"Eritrocito", tam:8, radioC:0.50, radioN:0,    nucleoloR:0,    nOrg:0,  anucleado:true,  segm:false,
      tipo:"Eritrocito Maduro", funcion:"Transporte de oxígeno mediante hemoglobina", loc:"Sangre periférica",
      nucleoDesc:"Anucleado (núcleo expulsado)", extra:"Forma bicóncava · Vida media 120 días" }
  ],
  linfoide: [
    { nombre:"Célula Madre Hematopoyética", tam:20, radioC:1.0,  radioN:0.55, nucleoloR:0.18, nOrg:1,  anucleado:false, segm:false,
      tipo:"Célula Madre Hematopoyética (HSC)", funcion:"Precursora común de todas las líneas", loc:"Médula ósea",
      nucleoDesc:"Grande, redondo, cromatina laxa", extra:"Alta relación N/C · Nucléolo prominente" },
    { nombre:"Linfoblasto", tam:16, radioC:0.88, radioN:0.52, nucleoloR:0.15, nOrg:1,  anucleado:false, segm:false,
      tipo:"Linfoblasto", funcion:"Primera célula reconocible de la línea linfoide", loc:"Médula ósea / Timo",
      nucleoDesc:"Grande, redondo, 1-2 nucléolos", extra:"Citoplasma escaso y basófilo" },
    { nombre:"Prolinfocito", tam:14, radioC:0.80, radioN:0.46, nucleoloR:0.13, nOrg:2,  anucleado:false, segm:false,
      tipo:"Prolinfocito", funcion:"Expansión clonal; mitosis frecuente", loc:"Médula ósea / Ganglios",
      nucleoDesc:"Redondo, nucléolo prominente", extra:"Cromatina densa" },
    { nombre:"Linfocito Pequeño", tam:9, radioC:0.56, radioN:0.44, nucleoloR:0,    nOrg:1,  anucleado:false, segm:false,
      tipo:"Linfocito Pequeño", funcion:"Vigilancia inmune; forma en reposo", loc:"Sangre periférica / Tejido linfoide",
      nucleoDesc:"Núcleo grande relativo al citoplasma", extra:"Cromatina condensada · Citoplasma escaso" },
    { nombre:"Linfocito Activado", tam:13, radioC:0.78, radioN:0.48, nucleoloR:0.12, nOrg:3,  anucleado:false, segm:false,
      tipo:"Linfocito Activado (Inmunoblasto)", funcion:"Respuesta a antígeno; produce linfocinas", loc:"Ganglios / Bazo",
      nucleoDesc:"Nucléolo reaparece; cromatina laxa", extra:"Citoplasma basófilo expandido" },
    { nombre:"Linfocito T Efector", tam:11, radioC:0.68, radioN:0.40, nucleoloR:0,    nOrg:3,  anucleado:false, segm:false,
      tipo:"Linfocito T Efector (CD8+/CD4+)", funcion:"Citotoxicidad / cooperación con linfocitos B", loc:"Sangre periférica / Tejidos",
      nucleoDesc:"Oval, cromatina moderada", extra:"Gránulos citotóxicos (CD8+) · Sin nucléolo" },
    { nombre:"Plasmoblasto", tam:14, radioC:0.82, radioN:0.35, nucleoloR:0.10, nOrg:5,  anucleado:false, segm:false,
      tipo:"Plasmoblasto", funcion:"Transición hacia producción de anticuerpos", loc:"Ganglios linfáticos",
      nucleoDesc:"Excéntrico; rueda de carro incipiente", extra:"RER abundante · Inicio secreción IgM" },
    { nombre:"Célula Plasmática", tam:15, radioC:0.85, radioN:0.30, nucleoloR:0,    nOrg:7,  anucleado:false, segm:false,
      tipo:"Célula Plasmática (Plasmocito)", funcion:"Secreción masiva de anticuerpos", loc:"Médula ósea / Tejido linfoide",
      nucleoDesc:"Muy excéntrico; rueda de carro clásica", extra:"RER muy desarrollado · Vida media larga" }
  ]
};

// Paletas de color por línea
const COL = {
  mieloide: { cuerpo:0xD6C3F0, borde:0x7B3FB5, nucleo:0x5B2D8E, nucleolo:0x9B6BC5, org:0xB48DD8 },
  linfoide:  { cuerpo:0xF5C4C0, borde:0xC0392B, nucleo:0x8B1A10, nucleolo:0xD05040, org:0xE07060 }
};

//  ESTADO

let estadoM = 0, estadoL = 0;
let lineaActiva = 'mieloide';


//  THREE.JS

let scene, camera, renderer, grupo;

function initThree() {
  const canvas = document.getElementById('canvas3d');

  renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
  renderer.setPixelRatio(window.devicePixelRatio || 1);
  renderer.setSize(220, 220);
  renderer.setClearColor(0x000000, 0);

  scene = new THREE.Scene();
  camera = new THREE.PerspectiveCamera(42, 1, 0.1, 100);
  camera.position.set(0, 0, 4.8);

  scene.add(new THREE.AmbientLight(0xffffff, 0.7));
  const d1 = new THREE.DirectionalLight(0xffffff, 0.9);
  d1.position.set(3, 4, 5); scene.add(d1);
  const d2 = new THREE.DirectionalLight(0xffffff, 0.3);
  d2.position.set(-3, -2, -3); scene.add(d2);

  grupo = new THREE.Group();
  scene.add(grupo);

  addDragRotate(canvas);
  construirCelula();

  (function loop() {
    requestAnimationFrame(loop);
    grupo.rotation.y += 0.007;
    renderer.render(scene, camera);
  })();
}

// ── Construir geometría según etapa 
function construirCelula() {
  // Limpiar grupo
  while (grupo.children.length) grupo.remove(grupo.children[0]);

  const etapa = lineaActiva === 'mieloide' ? estadoM : estadoL;
  const d   = DATOS[lineaActiva][etapa];
  const col = COL[lineaActiva];

  if (d.anucleado)   buildEritrocito(d, col);
  else if (d.segm)   buildSegmentado(d, col);
  else               buildNormal(d, col);

  actualizarPanel(d, etapa);
}

function buildNormal(d, col) {
  // Cuerpo celular (esfera translúcida)
  const cuerpoMat = new THREE.MeshPhongMaterial({
    color: col.cuerpo, transparent: true, opacity: 0.52,
    side: THREE.FrontSide, shininess: 50
  });
  grupo.add(new THREE.Mesh(new THREE.SphereGeometry(d.radioC, 52, 52), cuerpoMat));

  // Wireframe sutil
  const wireMat = new THREE.MeshBasicMaterial({ color: col.borde, wireframe: true, transparent: true, opacity: 0.10 });
  grupo.add(new THREE.Mesh(new THREE.SphereGeometry(d.radioC + 0.01, 24, 24), wireMat));

  // Núcleo
  if (d.radioN > 0) {
    const scY = d.rinon ? 0.60 : 1.0;
    const scZ = d.rinon ? 0.72 : 1.0;
    const nMat = new THREE.MeshPhongMaterial({ color: col.nucleo, shininess: 25 });
    const nMesh = new THREE.Mesh(new THREE.SphereGeometry(d.radioN, 36, 36), nMat);
    nMesh.scale.set(1, scY, scZ);
    grupo.add(nMesh);

    // Nucléolo
    if (d.nucleoloR > 0) {
      const nlMat = new THREE.MeshPhongMaterial({ color: col.nucleolo });
      const nlMesh = new THREE.Mesh(new THREE.SphereGeometry(d.nucleoloR, 16, 16), nlMat);
      nlMesh.position.set(d.radioN * 0.3, d.radioN * 0.2, d.radioN * 0.2);
      grupo.add(nlMesh);
    }
  }

  // Organelos
  const posOrg = [
    [ 0.55,  0.35,  0.40], [-0.55,  0.35,  0.40],
    [ 0.50, -0.40,  0.30], [-0.50, -0.40,  0.30],
    [ 0.10,  0.62,  0.35], [-0.10, -0.62,  0.35],
    [ 0.62,  0.00,  0.28], [-0.62,  0.00,  0.28],
    [ 0.00,  0.30,  0.65], [ 0.00, -0.30,  0.65]
  ];
  const orgMat = new THREE.MeshPhongMaterial({ color: col.org, shininess: 70, transparent: true, opacity: 0.88 });
  for (let i = 0; i < Math.min(d.nOrg, posOrg.length); i++) {
    const [ox, oy, oz] = posOrg[i];
    if (Math.sqrt(ox*ox + oy*oy + oz*oz) > d.radioC - 0.12) continue;
    const s = 0.068 + (i % 3) * 0.018;
    const om = new THREE.Mesh(new THREE.SphereGeometry(s, 10, 10), orgMat);
    om.position.set(ox * (d.radioC - 0.14), oy * (d.radioC - 0.14), oz * (d.radioC - 0.14));
    grupo.add(om);
  }
}

function buildSegmentado(d, col) {
  // Cuerpo
  const mat = new THREE.MeshPhongMaterial({ color: col.cuerpo, transparent: true, opacity: 0.52, shininess: 50 });
  grupo.add(new THREE.Mesh(new THREE.SphereGeometry(d.radioC, 52, 52), mat));
  const wm = new THREE.MeshBasicMaterial({ color: col.borde, wireframe: true, transparent: true, opacity: 0.10 });
  grupo.add(new THREE.Mesh(new THREE.SphereGeometry(d.radioC + 0.01, 24, 24), wm));

  // 3 lóbulos nucleares
  const nm = new THREE.MeshPhongMaterial({ color: col.nucleo, shininess: 20 });
  const lobulos = [[-0.26, 0.17, 0], [0, 0.28, 0], [0.26, 0.17, 0]];
  lobulos.forEach(([lx, ly, lz], i) => {
    const lm = new THREE.Mesh(new THREE.SphereGeometry(0.16, 22, 22), nm);
    lm.position.set(lx, ly, lz);
    grupo.add(lm);
    if (i < lobulos.length - 1) {
      const [nx, ny, nz] = lobulos[i + 1];
      const cm = new THREE.Mesh(new THREE.SphereGeometry(0.055, 8, 8), nm);
      cm.position.set((lx+nx)/2, (ly+ny)/2, (lz+nz)/2);
      grupo.add(cm);
    }
  });

  // Organelos
  const orgMat = new THREE.MeshPhongMaterial({ color: col.org, transparent: true, opacity: 0.88 });
  [[ 0.48,-0.30, 0.38],[-0.48,-0.22, 0.38],[ 0.10,-0.54, 0.38],
   [-0.10, 0.54, 0.30],[ 0.54, 0.10, 0.28],[-0.54, 0.10, 0.28],
   [ 0.00,-0.22, 0.60],[ 0.36, 0.40, 0.30],[-0.36, 0.40, 0.30]
  ].slice(0, d.nOrg).forEach(([ox, oy, oz]) => {
    if (Math.sqrt(ox*ox+oy*oy+oz*oz) > d.radioC - 0.10) return;
    const om = new THREE.Mesh(new THREE.SphereGeometry(0.072, 10, 10), orgMat);
    om.position.set(ox, oy, oz);
    grupo.add(om);
  });
}

function buildEritrocito(d, col) {
  const geo = new THREE.SphereGeometry(0.5, 52, 52);
  const pos = geo.attributes.position;
  for (let i = 0; i < pos.count; i++) {
    const x = pos.getX(i), y = pos.getY(i), z = pos.getZ(i);
    const r2 = x*x + z*z;
    const indent = 0.26 * Math.exp(-r2 / 0.20);
    pos.setY(i, y > 0 ? y - indent : y + indent);
  }
  pos.needsUpdate = true;
  geo.computeVertexNormals();
  const mat = new THREE.MeshPhongMaterial({ color: col.cuerpo, transparent: true, opacity: 0.72, shininess: 70, side: THREE.DoubleSide });
  grupo.add(new THREE.Mesh(geo, mat));
  const wm = new THREE.MeshBasicMaterial({ color: col.borde, wireframe: true, transparent: true, opacity: 0.15 });
  grupo.add(new THREE.Mesh(new THREE.SphereGeometry(0.51, 24, 24), wm));
}

// Drag para rotar 
function addDragRotate(canvas) {
  let drag = false, lx = 0, ly = 0;
  canvas.addEventListener('mousedown',  e => { drag=true; lx=e.clientX; ly=e.clientY; });
  window.addEventListener('mousemove',  e => { if (!drag) return; grupo.rotation.y += (e.clientX-lx)*0.012; grupo.rotation.x += (e.clientY-ly)*0.012; lx=e.clientX; ly=e.clientY; });
  window.addEventListener('mouseup',    () => drag = false);
  canvas.addEventListener('touchstart', e => { drag=true; lx=e.touches[0].clientX; ly=e.touches[0].clientY; }, { passive:true });
  canvas.addEventListener('touchmove',  e => { if (!drag) return; grupo.rotation.y += (e.touches[0].clientX-lx)*0.012; grupo.rotation.x += (e.touches[0].clientY-ly)*0.012; lx=e.touches[0].clientX; ly=e.touches[0].clientY; }, { passive:true });
  canvas.addEventListener('touchend',   () => drag = false);
}

//  UI — PANEL DE INFO + BADGES + PUNTOS

function actualizarPanel(d, etapa) {
  document.getElementById('info-tipo').textContent        = d.tipo;
  document.getElementById('info-funcion').textContent     = d.funcion;
  document.getElementById('info-localizacion').textContent= d.loc;
  document.getElementById('info-nucleo-li').textContent   = 'Núcleo: ' + d.nucleoDesc;
  document.getElementById('info-extra-li').textContent    = d.extra;
  document.getElementById('info-etapa-li').textContent    = 'Etapa ' + (etapa+1) + ' de ' + DATOS[lineaActiva].length;
  document.getElementById('info-linea-li').textContent    = 'Línea: ' + (lineaActiva === 'mieloide' ? 'Mieloide' : 'Linfoide');
  document.getElementById('celula-valor-tam').textContent = d.tam.toFixed(1) + ' μm';
  document.getElementById('celula-valor-tam').style.color = lineaActiva === 'mieloide' ? 'var(--morado-oscuro)' : 'var(--rojo-fuerte)';

  const badge = document.getElementById('badge-linea');
  badge.className = 'linea-activa-badge ' + (lineaActiva === 'mieloide' ? 'badge-mieloide' : 'badge-linfoide');
  badge.textContent = lineaActiva === 'mieloide' ? 'MIELOIDE' : 'LINFOIDE';
}

function actualizarLinea(linea, etapa) {
  const total = DATOS[linea].length;
  document.getElementById(linea + '-nombre').textContent = DATOS[linea][etapa].nombre;
  document.getElementById(linea + '-etapa').textContent  = 'Etapa ' + (etapa+1) + ' de ' + total;

  Array.from(document.getElementById(linea+'-puntos').querySelectorAll('.punto-prog'))
    .forEach((p, i) => p.classList.toggle('activo', i === etapa));

  const prev = document.getElementById(linea + '-prev');
  const next = document.getElementById(linea + '-next');
  prev.disabled = etapa === 0;
  prev.classList.toggle('activo', etapa > 0);
  next.disabled = etapa === total - 1;
  next.classList.toggle('activo', etapa < total - 1);
}
//  EVENTOS DE NAVEGACIÓN

['mieloide', 'linfoide'].forEach(linea => {
  document.getElementById(linea + '-prev').addEventListener('click', () => {
    if (linea === 'mieloide' && estadoM > 0) estadoM--;
    if (linea === 'linfoide' && estadoL > 0) estadoL--;
    lineaActiva = linea;
    const e = linea === 'mieloide' ? estadoM : estadoL;
    actualizarLinea(linea, e);
    construirCelula();
  });
  document.getElementById(linea + '-next').addEventListener('click', () => {
    const max = DATOS[linea].length - 1;
    if (linea === 'mieloide' && estadoM < max) estadoM++;
    if (linea === 'linfoide' && estadoL < max) estadoL++;
    lineaActiva = linea;
    const e = linea === 'mieloide' ? estadoM : estadoL;
    actualizarLinea(linea, e);
    construirCelula();
  });
});

// Botones linfoide en rojo
['linfoide-prev','linfoide-next'].forEach(id => {
  const btn = document.getElementById(id);
  btn.style.setProperty('--nav-active-bg', 'var(--rojo-fuerte)');
});


//  ARRANQUE

window.addEventListener('load', () => {
  initThree();
  actualizarLinea('mieloide', 0);
  actualizarLinea('linfoide', 0);
});
</script>

</body>
</html>
