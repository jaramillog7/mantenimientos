CREATE TABLE IF NOT EXISTS usuarios_sistema (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  correo VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  rol VARCHAR(50) NOT NULL DEFAULT 'admin',
  estado TINYINT(1) NOT NULL DEFAULT 1,
  ultimo_acceso DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_usuarios_sistema_correo (correo),
  KEY idx_usuarios_sistema_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios_sistema (nombre, correo, password_hash, rol, estado)
VALUES (
  'Administrador TI',
  'admin@mantenimientos.local',
  '$2y$10$zdCWhZFwEtCL34hU8IOVLe8qeg6/f88KJMjXCBacKdthU2zQfpqki',
  'admin',
  1
)
ON DUPLICATE KEY UPDATE
  nombre = VALUES(nombre),
  password_hash = VALUES(password_hash),
  rol = VALUES(rol),
  estado = VALUES(estado),
  updated_at = CURRENT_TIMESTAMP;
