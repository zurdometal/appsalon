document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
    buscarPorFecha();

}

function buscarPorFecha() {
    const fechaInput = document.querySelector('#fecha');
    fechaInput.addEventListener('input', function(e) {
        const fechaSeleccionada = e.target.value;
        // Redirigir a la página de resultados con la fecha seleccionada como parámetro
        window.location = `?fecha=${fechaSeleccionada}`;
    });
}