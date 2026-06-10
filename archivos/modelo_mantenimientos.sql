CREATE DATABASE IF NOT EXISTS mantenimientos
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mantenimientos;

CREATE TABLE areas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_areas_nombre (nombre),
  KEY idx_areas_estado (estado)
) ENGINE=InnoDB;

CREATE TABLE usuarios (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  area_id BIGINT UNSIGNED NULL,
  nombre VARCHAR(150) NOT NULL,
  codigo_activo INT NOT NULL,
  serial_equipo VARCHAR(80) NOT NULL,
  estado_usuario TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_usuarios_area
    FOREIGN KEY (area_id) REFERENCES areas(id),
  UNIQUE KEY uq_usuarios_codigo_activo (codigo_activo),
  UNIQUE KEY uq_usuarios_serial_equipo (serial_equipo),
  KEY idx_usuarios_area (area_id),
  KEY idx_usuarios_estado_usuario (estado_usuario)
) ENGINE=InnoDB;


CREATE TABLE mantenimientos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id BIGINT UNSIGNED NOT NULL,
  fecha_programada DATE NOT NULL,
  hora_programada TIME NULL,
  estado ENUM('pendiente','programado','realizado','reprogramado','cancelado','no_asistio') NOT NULL DEFAULT 'pendiente',
  fecha_ejecucion DATE NULL,
  tecnico_responsable VARCHAR(150) NULL,
  observaciones VARCHAR(500) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_mantenimientos_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  KEY idx_mant_usuario_fecha (usuario_id, fecha_programada),
  KEY idx_mant_fecha_hora (fecha_programada, hora_programada),
  KEY idx_mant_estado_fecha (estado, fecha_programada)
) ENGINE=InnoDB;

CREATE TABLE tecnicos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_tecnicos_nombre (nombre),
  KEY idx_tecnicos_estado (estado)
) ENGINE=InnoDB;

CREATE TABLE notificaciones (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mantenimiento_id BIGINT UNSIGNED NOT NULL,
  usuario_id BIGINT UNSIGNED NOT NULL,
  tipo ENUM('preaviso_7d','vencido','recordatorio_hoy') NOT NULL DEFAULT 'preaviso_7d',
  canal ENUM('inapp','email') NOT NULL DEFAULT 'inapp',
  titulo VARCHAR(180) NOT NULL,
  mensaje TEXT NOT NULL,
  estado ENUM('pendiente','enviada','error') NOT NULL DEFAULT 'pendiente',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_notif_mantenimiento
    FOREIGN KEY (mantenimiento_id) REFERENCES mantenimientos(id),
  CONSTRAINT fk_notif_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  KEY idx_notif_estado_canal (estado, canal),
  KEY idx_notif_usuario_fecha (usuario_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE notificaciones_inapp (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  notificacion_id BIGINT UNSIGNED NOT NULL,
  destinatario_ti VARCHAR(120) NOT NULL DEFAULT 'TI',
  leida TINYINT(1) NOT NULL DEFAULT 0,
  leida_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notificaciones_inapp_notificacion
    FOREIGN KEY (notificacion_id) REFERENCES notificaciones(id)
    ON DELETE CASCADE,
  UNIQUE KEY uq_notificaciones_inapp_notificacion_dest (notificacion_id, destinatario_ti),
  KEY idx_notificaciones_inapp_leida (leida, created_at)
) ENGINE=InnoDB;

CREATE TABLE notificacion_queue (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  notificacion_id BIGINT UNSIGNED NOT NULL,
  canal ENUM('inapp','email') NOT NULL,
  estado ENUM('pendiente','procesando','enviado','error') NOT NULL DEFAULT 'pendiente',
  reintentos TINYINT UNSIGNED NOT NULL DEFAULT 0,
  ultimo_error VARCHAR(500) NULL,
  next_retry_at DATETIME NULL,
  sent_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_notificacion_queue_notificacion
    FOREIGN KEY (notificacion_id) REFERENCES notificaciones(id)
    ON DELETE CASCADE,
  KEY idx_notificacion_queue_estado_retry (estado, next_retry_at),
  KEY idx_notificacion_queue_canal_estado (canal, estado)
) ENGINE=InnoDB;
