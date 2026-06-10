-- Carga demo de areas/equipos para repositorio publico
START TRANSACTION;
CREATE TEMPORARY TABLE tmp_areas_usuario (codigo_activo INT PRIMARY KEY, area_nombre VARCHAR(120) NOT NULL);
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1001, 'OPERACIONES');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1002, 'OPERACIONES');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1003, 'OPERACIONES');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1004, 'COMERCIAL');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1005, 'COMERCIAL');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1006, 'COMERCIAL');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1007, 'FINANZAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1008, 'FINANZAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1009, 'FINANZAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1010, 'TALENTO HUMANO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1011, 'TALENTO HUMANO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1012, 'TALENTO HUMANO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1013, 'SISTEMAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1014, 'SISTEMAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1015, 'SISTEMAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1016, 'DIRECCION');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1017, 'DIRECCION');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1018, 'DIRECCION');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1019, 'CALIDAD');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1020, 'CALIDAD');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1021, 'CALIDAD');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1022, 'SOPORTE EXTERNO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1023, 'SOPORTE EXTERNO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1024, 'SOPORTE EXTERNO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1025, 'OPERACIONES');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1026, 'OPERACIONES');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1027, 'OPERACIONES');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1028, 'COMERCIAL');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1029, 'COMERCIAL');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1030, 'COMERCIAL');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1031, 'FINANZAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1032, 'FINANZAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1033, 'FINANZAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1034, 'TALENTO HUMANO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1035, 'TALENTO HUMANO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1036, 'TALENTO HUMANO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1037, 'SISTEMAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1038, 'SISTEMAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1039, 'SISTEMAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1040, 'DIRECCION');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1041, 'DIRECCION');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1042, 'DIRECCION');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1043, 'CALIDAD');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1044, 'CALIDAD');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1045, 'CALIDAD');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1046, 'SOPORTE EXTERNO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1047, 'SOPORTE EXTERNO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1048, 'SOPORTE EXTERNO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1049, 'OPERACIONES');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1050, 'COMERCIAL');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1051, 'FINANZAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1052, 'TALENTO HUMANO');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1053, 'SISTEMAS');
INSERT INTO tmp_areas_usuario (codigo_activo, area_nombre) VALUES (1054, 'SOPORTE EXTERNO');
INSERT INTO areas (nombre, estado)
SELECT DISTINCT area_nombre, 1 FROM tmp_areas_usuario
ON DUPLICATE KEY UPDATE estado = VALUES(estado), updated_at = CURRENT_TIMESTAMP;
UPDATE usuarios u
INNER JOIN tmp_areas_usuario t ON t.codigo_activo = u.codigo_activo
INNER JOIN areas a ON a.nombre = t.area_nombre
SET u.area_id = a.id;
DROP TEMPORARY TABLE tmp_areas_usuario;
COMMIT;
