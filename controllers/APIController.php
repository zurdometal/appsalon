<?php

namespace Controllers;

use Model\Servicio;
use Model\Cita;
use Model\CitaServicio;

class APIController {

    public static function index() {
        // Consulto los servicios de la tabla servicios mediante el modelo Servicio
        $servicios = Servicio::all();
        // muestro los resultados en formato json
        echo json_encode($servicios);
    }

    public static function guardar() {

        header('Content-Type: application/json');

        // Almacena la cita en la base de datos y devuelve una respuesta en formato JSON   
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        $id = $resultado['id'];
        
        // Almacena la cita y servicios relacionados en la base de datos
        $idServicios = explode(",", $_POST['servicios']);

        foreach($idServicios as $idServicio) {

            $args = [
                'citaId' => $id,
                'servicioId' => trim($idServicio)
            ];
            
            $citaservicio = new CitaServicio($args);
            $citaservicio->guardar();
        }
        // Retorna una respuesta en formato JSON con el resultado de la operación
        echo json_encode([
            'resultado' => $resultado
        ]);
    }

    
    // Elimina una cita de la base de datos y devuelve una respuesta en formato JSON
    public static function eliminar() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $cita = Cita::find($id);
            $cita->eliminar();
            header('location:' . $_SERVER['HTTP_REFERER']);
        }
    }
}