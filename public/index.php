<?php

ob_start();

require_once __DIR__ . '/../includes/app.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use MVC\Router;
use Controllers\LoginController;
use Controllers\CitaController;
use Controllers\APIController;
use Controllers\AdminController;
use Controllers\ServicioController;

$router = new Router();

// ********** AREA PUBLICA **********

// Iniciar Sesión
$router->get('/', [Controllers\LoginController::class, 'login']);
$router->post('/', [Controllers\LoginController::class, 'login']);
// Cerrar Sesión
$router->get('/logout', [Controllers\LoginController::class, 'logout']);


// Recuperar Password
$router->get('/olvide', [Controllers\LoginController::class, 'olvide']);
$router->post('/olvide', [Controllers\LoginController::class, 'olvide']);
$router->get('/reestablecer', [Controllers\LoginController::class, 'reestablecer']);
$router->post('/reestablecer', [Controllers\LoginController::class, 'reestablecer']);

// Crear Cuenta
$router->get('/crear-cuenta', [Controllers\LoginController::class, 'crearCuenta']);
$router->post('/crear-cuenta', [Controllers\LoginController::class, 'crearCuenta']);

// Confirmar Cuenta
$router->get('/confirmar-cuenta', [Controllers\LoginController::class, 'confirmarCuenta']);

$router->get('/mensaje', [Controllers\LoginController::class, 'mensaje']);



//********* AREA PRIVADA **********

// Cita
$router->get('/cita', [Controllers\CitaController::class, 'index']);
$router->get('/admin', [Controllers\AdminController::class, 'index']);



// API Citas
$router->get('/api/servicios', [Controllers\APIController::class, 'index']);
$router->post('/api/citas', [Controllers\APIController::class, 'guardar']);
$router->post('/api/eliminar', [Controllers\APIController::class, 'eliminar']);


// CRUD Servicios
$router->get('/servicios', [Controllers\ServicioController::class, 'index']);
$router->get('/servicios/crear', [Controllers\ServicioController::class, 'crear']);
$router->post('/servicios/crear', [Controllers\ServicioController::class, 'crear']);
$router->get('/servicios/actualizar', [Controllers\ServicioController::class, 'actualizar']);
$router->post('/servicios/actualizar', [Controllers\ServicioController::class, 'actualizar']);
$router->post('/servicios/eliminar', [Controllers\ServicioController::class, 'eliminar']);






// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();

ob_end_flush();