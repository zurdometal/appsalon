<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Classes\Email;

class LoginController {

    public static function login(Router $router) {
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            

            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);

                if($usuario) {
                    //verificar si el usuario está confirmado
                    $confirmado = $usuario->confirmado();
                                        
                    if($confirmado) {
                        // Verificar el password
                        $passwordCorrecto = $usuario->comprobarPassword($auth->password);
                        
                        if($passwordCorrecto) {
                            // Autenticar el usuario
                            if (session_status() === PHP_SESSION_NONE) {
                                session_start();
                            }

                            $_SESSION['id'] = $usuario->id;
                            $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                            $_SESSION['email'] = $usuario->email;
                            $_SESSION['login'] = true;

                            // Comprobar si el usuario es admin
                            if($usuario->admin === "1") {
                                $_SESSION['admin'] = $usuario->admin ?? null;
                                
                                // Redirecciono a la zona de admin
                                header('Location: /admin');
                                
                            } else {
                                // Redirecciono a la zona de usuarios
                                header('Location: /cita');
                            }
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }                    
            }               

            $alertas = Usuario::getAlertas();
        }
    
        $router->render('auth/login', [
            'alertas' => $alertas,
        ]);
    }
    
    public static function logout() {
        session_start();
        $_SESSION = [];
        session_destroy();
        header('Location: /');
        exit;
    }

    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            
            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);
                if($usuario && $usuario->confirmado === "1") {
                    // Generar un nuevo token
                    $usuario->crearToken();
                    $usuario->guardar();
                    
                    // enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta de exito
                    Usuario::setAlerta('exito', 'Revisa tu email para las instrucciones');
                    
                    
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                    
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide', [
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router) {
        $alertas = [];
        // Variable para no mostrar el formulario si el token no es válido
        $error = false;

        $token = s($_GET['token']);

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            $error = true;
        
        } else {
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                // Leer el nuevo password y guardarlo
                $newPassword = new Usuario($_POST);
                $alertas = $newPassword->validarPassword();

                if(empty($alertas)) {
                    $usuario->password = $newPassword->password;
                    // Hashear el nuevo password                
                    $usuario->hashPassword();
                    // Eliminar el token para que no se pueda volver a usar
                    $usuario->token = null;
                    // Guardar los cambios en el usuario
                    $resultado = $usuario->guardar();

                    // Si se guardó correctamente redirecciono al login
                    if($resultado) {
                        header('Location: /');
                        exit;
                    }
                                        
                }
            }
        }

        
        $alertas = Usuario::getAlertas();
        
        $router->render('auth/reestablecer', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crearCuenta(Router $router) {
        $usuario = new Usuario($_POST);
        
        //Arreglo con mensajes de alertas
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();

            // Revisar que alertas esté vacío
            if(empty($alertas)) {     
                // Verificar que el usuario no esté registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();
                    
                    // Generar un token único
                    $usuario->crearToken();

                    // Enviar el email de confirmación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar();
                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }   
                
                
            }
           
        }


        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');
    }

    public static function confirmarCuenta(Router $router) {
        $alertas = [];
        $token = s($_GET['token']);

        $usuario=Usuario::where('token', $token);

        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no válido');
        } else {
            // Modificar a usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = false;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');
        }

        // Obtener alertas
        $alertas = Usuario::getAlertas();

        // Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}