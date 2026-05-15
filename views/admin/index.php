<?php
    include_once __DIR__ . '/../templates/barra.php';
?>

<h1 class="nombre-pagina">Gestión de Citas</h1>


<div class="busqueda">
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input 
                type="date" 
                id="fecha" 
                name="fecha" 
                value="<?php echo $fecha; ?>"
            >
        </div>
    </form>
</div>

<?php
    if(count($citas) === 0) {
        echo "<h2>No hay citas en esta fecha</h2>";
    }
?>


<div id="citas-admin">
    <ul class="citas">
        <?php
            $idCita = 0; // la defino la 1era vez con cualquier valor, luego se va a actualizar con el id de cada cita para comparar y mostrar los datos del cliente solo una vez por cada cita
            foreach($citas as $key => $cita) {
                if($idCita !== $cita->id) {
                    $total = 0;
        ?>
                    
                    <li>
                        <h3>Cita</h3>
                        <p>ID: <span><?php echo $cita->id; ?></span></p>
                        <p>Hora: <span><?php echo $cita->hora; ?></span></p>
                        <p>Cliente: <span><?php echo $cita->cliente; ?></span></p>
                        <p>Email: <span><?php echo $cita->email; ?></span></p>
                        <p>Teléfono: <span><?php echo $cita->telefono; ?></span></p>
                    

                        <h3>Servicios</h3>
                        
        <?php
                        } // Fin del if

                        $idCita = $cita->id;// Actualizo el idCita con el id de cada cita para comparar y mostrar los datos del cliente solo una vez por cada cita
                        $total += $cita->precio;// Sumo el precio de cada servicio para mostrar el total al final de cada cita
        ?>      
                        <p class="servicio"><?php echo $cita->servicio . ": $" . $cita->precio; ?></p>
                   
        <?php
                        $actual = $cita->id;
                        $proximo = $citas[$key + 1]->id ?? 0;
                        if(esUltimo($actual, $proximo)) {
        ?>
                        <div class="botoneliminar-precio">
                            <p class="total">Total:<span>$<?php echo $total;?></span></p>
                            <!-- Para eliminar la cita, se envía el id de la cita a través de un formulario POST a la ruta /api/eliminar, que es manejada por el método eliminar() del APIController -->
                            <form action="/api/eliminar" method="POST">
                                <input type="hidden" name="id" value="<?php echo $cita->id; ?>">
                                <input type="submit" class="boton-eliminar" value="Eliminar">
                            </form>
                        </div>
                    </li>
        <?php        
                }
            } // fin del foreach
        ?>
    </ul>
</div>

<?php
    $script = "<script src='build/js/buscador.js'></script>";
?>