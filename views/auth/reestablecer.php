<h1 class="nombre-pagina">Reestablecer Password</h1>
<?php 
    if($error) {
        echo '<div class="alerta error">Token no válido</div>';
        echo '<div class="acciones">';
        echo '    <a href="/">Iniciar Sesión</a>';
        echo '    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>';
        echo '</div>';
        return; // Detener la ejecución del código si el token no es válido
        
    } else {
        // Mostrar el formulario para ingresar la nueva contraseña
        echo '<p class="descripcion-pagina">Introduce tu nueva contraseña</p>';
    }
?>

<?php 
    include_once __DIR__ . '/../templates/alertas.php';
?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="password">Password</label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Tu Password"
        />
    </div>
    <input type="submit" class="boton" value="Reestablecer Password">
</form> 

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
</div>