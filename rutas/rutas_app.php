<?php

declare(strict_types=1);

use Aplicacion\Controladores\ControladorAutenticacion;
use Aplicacion\Controladores\ControladorInicio;
use Aplicacion\Controladores\ControladorPanel;

$controladorInicio = new ControladorInicio();
$controladorAutenticacion = new ControladorAutenticacion();
$controladorPanel = new ControladorPanel();

$enrutador->get('/', [$controladorInicio, 'index']);
$enrutador->get('/inicio', [$controladorInicio, 'index']);
$enrutador->get('/login', [$controladorAutenticacion, 'formularioLogin']);
$enrutador->post('/login', [$controladorAutenticacion, 'procesarLogin']);
$enrutador->get('/logout', [$controladorAutenticacion, 'logout']);
$enrutador->get('/panel', [$controladorPanel, 'index']);
$enrutador->post('/panel/proximos/programar', [$controladorPanel, 'marcarProximoComoProgramado']);
$enrutador->post('/panel/programados/hecho', [$controladorPanel, 'marcarProgramadoComoHecho']);
$enrutador->post('/panel/tecnicos/crear', [$controladorPanel, 'crearTecnico']);
$enrutador->post('/panel/reversiones/revertir', [$controladorPanel, 'revertirUltimaAccion']);
$enrutador->post('/panel/usuarios/actualizar', [$controladorPanel, 'actualizarUsuario']);
$enrutador->post('/panel/usuarios/estado', [$controladorPanel, 'cambiarEstadoUsuario']);
$enrutador->post('/panel/usuarios/eliminar', [$controladorPanel, 'eliminarUsuario']);
