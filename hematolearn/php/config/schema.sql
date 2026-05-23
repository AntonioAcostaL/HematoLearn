--  HematoLearn — Esquema de base de datos MySQL


CREATE DATABASE IF NOT EXISTS hematolearn
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE hematolearn;

--  Tabla: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(80)  NOT NULL,
    apellido      VARCHAR(80)  NOT NULL,
    email         VARCHAR(160) NOT NULL UNIQUE,
    institucion   VARCHAR(160) DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    creado_en     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo        TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB;


--  Tabla: sesiones  (para "recordarme")
CREATE TABLE IF NOT EXISTS sesiones (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id    INT UNSIGNED NOT NULL,
    token         CHAR(64)     NOT NULL UNIQUE,
    expira_en     DATETIME     NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

--  Tabla: progreso_usuario  (futuro uso)
CREATE TABLE IF NOT EXISTS progreso_usuario (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id    INT UNSIGNED NOT NULL,
    linea         ENUM('mieloide','linfoide') NOT NULL,
    etapa         TINYINT UNSIGNED NOT NULL DEFAULT 1,
    actualizado   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_usuario_linea (usuario_id, linea),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

--  Tabla: celulas  (catálogo de células)
CREATE TABLE IF NOT EXISTS celulas (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(160) NOT NULL,
    linea         ENUM('mieloide','linfoide') NOT NULL,
    etapa         TINYINT UNSIGNED NOT NULL,
    descripcion   TEXT,
    funcion       TEXT,
    localizacion  VARCHAR(120),
    tamanio_um    DECIMAL(5,1),
    creado_en     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

--  Tabla: progreso_celulas
CREATE TABLE IF NOT EXISTS progreso_celulas (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT UNSIGNED NOT NULL,
    celula_id   INT UNSIGNED NOT NULL,
    visto_en    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_usuario_celula (usuario_id, celula_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (celula_id)  REFERENCES celulas(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

--  Tabla: examenes
CREATE TABLE IF NOT EXISTS examenes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo      VARCHAR(160) NOT NULL,
    descripcion TEXT,
    dificultad  ENUM('basico','intermedio','avanzado') NOT NULL DEFAULT 'basico',
    tiempo_min  TINYINT UNSIGNED NOT NULL DEFAULT 15,
    activo      TINYINT(1) NOT NULL DEFAULT 1,
    creado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

--  Tabla: intentos_examen
CREATE TABLE IF NOT EXISTS intentos_examen (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id   INT UNSIGNED NOT NULL,
    examen_id    INT UNSIGNED NOT NULL,
    puntaje      TINYINT UNSIGNED NOT NULL DEFAULT 0,
    completado   TINYINT(1) NOT NULL DEFAULT 0,
    iniciado_en  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    terminado_en DATETIME,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (examen_id)  REFERENCES examenes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

--  Datos: células
INSERT IGNORE INTO celulas (nombre, linea, etapa, descripcion, funcion, localizacion, tamanio_um) VALUES
('Célula Madre Hematopoyética','mieloide',1,'Célula pluripotencial capaz de diferenciarse en todas las líneas celulares sanguíneas.','Origen de todas las células sanguíneas','Médula ósea',14.0),
('Progenitor Mieloide','mieloide',2,'Célula progenitora comprometida con la línea mieloide.','Progenitor de granulocitos, monocitos y eritrocitos','Médula ósea',15.0),
('Mieloblasto','mieloide',3,'Primera célula morfológicamente reconocible de la serie granulocítica.','Precursor de los granulocitos','Médula ósea',18.0),
('Promielocito','mieloide',4,'Célula con abundantes gránulos primarios azurófilos.','Contiene enzimas líticas en gránulos primarios','Médula ósea',20.0),
('Mielocito','mieloide',5,'Primera célula reconocible como neutrófilo, eosinófilo o basófilo.','Diferenciación final de granulocitos','Médula ósea',16.0),
('Metamielocito','mieloide',6,'Célula con núcleo en forma de herradura o riñón.','Etapa de maduración granulocítica','Médula ósea',14.0),
('Granulocito en Banda','mieloide',7,'Núcleo en forma de banda o bastón sin segmentación.','Forma inmadura circulante de neutrófilo','Sangre periférica',12.0),
('Neutrófilo Segmentado','mieloide',8,'Granulocito maduro con núcleo multilobulado.','Fagocitosis de bacterias y defensa innata','Sangre periférica',12.0),
('Célula Madre Hematopoyética','linfoide',1,'Célula pluripotencial capaz de diferenciarse en todas las líneas celulares sanguíneas.','Origen de todas las células sanguíneas','Médula ósea',14.0),
('Progenitor Linfoide Común','linfoide',2,'Progenitor comprometido con la línea linfoide.','Progenitor de linfocitos B, T y NK','Médula ósea',12.0),
('Linfoblasto','linfoide',3,'Precursor inmaduro de los linfocitos B y T.','Inicio de la diferenciación linfocítica','Médula ósea',15.0),
('Prolinfocito','linfoide',4,'Célula en proceso de maduración entre linfoblasto y linfocito.','Maduración linfocítica intermedia','Médula ósea',13.0),
('Linfocito B Inmaduro','linfoide',5,'Linfocito B que aún no ha salido de la médula ósea.','Selección negativa y maduración B','Médula ósea',10.0),
('Linfocito B Maduro','linfoide',6,'Linfocito B naïve que ha completado su maduración.','Reconocimiento de antígenos y activación','Sangre periférica',10.0),
('Plasmoblasto','linfoide',7,'Linfocito B activado que ha reconocido su antígeno.','Inicio de la producción de anticuerpos','Ganglios linfáticos',15.0),
('Célula Plasmática','linfoide',8,'Diferenciación terminal del linfocito B especializada en secreción de anticuerpos.','Producción masiva de inmunoglobulinas','Médula ósea',20.0);

--  Datos: exámenes
INSERT IGNORE INTO examenes (titulo, descripcion, dificultad, tiempo_min) VALUES
('Fundamentos de Hematopoyesis','Evalúa tus conocimientos básicos sobre la formación de células sanguíneas.','basico',15),
('Línea Mieloide Completa','Examen completo sobre las etapas de maduración de la línea mieloide.','intermedio',20),
('Línea Linfoide Completa','Evalúa tu comprensión de la diferenciación linfocítica.','intermedio',20),
('Diagnóstico Celular Avanzado','Identifica células sanguíneas en diferentes etapas evolutivas.','avanzado',30);
