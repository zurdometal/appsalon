let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion(); // muestra y oculta las secciones
    tabs(); // Cambia la sección cuando se presionen los tabs
    botonesPaginador(); //agrega o quita los botones del paginador
    paginaAnterior(); // mueve la sección a la pagina anterior
    paginaSiguiente(); // mueve la sección a la pagina siguiente

    consultarAPI(); // consulta la API en el backend de PHP


    idCliente(); // asigna el id del cliente al objeto de cita
    nombreCliente(); // Añade el nombre del cliente al objeto de cita

    seleccionarFecha(); // añade la fecha de la cita en el objeto
    seleccionarHora(); // añade la hora de la cita en el objeto

    mostrarResumen(); // muestra el resumen de la cita creada al final del proceso
}

function mostrarSeccion() {

    // Ocultar la seccion que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar'); //busco elemento el cual tenga la class "mostrar"
    if(seccionAnterior) {   //si existe un elemento con la class mostrar
        seccionAnterior.classList.remove('mostrar'); //le elimino la class al elemento
    }
    
    // Seleccionar la sección con el paso
    const pasoSelector = `#paso-${paso}`; //asigno a una variable el valor que voy a buscar
    const seccion = document.querySelector(pasoSelector); //busco en el documento el elemento que tenga el valor de la var
    seccion.classList.add('mostrar'); //al elemento encontrado le agrego la class "mostrar"
    
    // Quita la class actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    // Resalta el tab actual
    const tab=document.querySelector(`[data-paso="${paso}"]`)
    tab.classList.add('actual');
}    

function tabs() {
   const botones = document.querySelectorAll('.tabs button'); //busco los botones que esten incluidos en una class "tabs" 
   
   botones.forEach( boton => { //por cada elementro encontrado
        boton.addEventListener('click', function(e) { //cuando hago click en el elemento
            paso = parseInt (e.target.dataset.paso); // devuelvo el valor en entero de target.dataset.paso

            mostrarSeccion(); //corro funcion
            botonesPaginador(); // muestra u oculta los botones del paginador
        });
    });
}

function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    // Resetear estado primero
    paginaAnterior.classList.remove('ocultar');
    paginaSiguiente.classList.remove('ocultar');

    // Lógica según paso
    if (paso === 1) {
        paginaAnterior.classList.add('ocultar');
    } 
    
    if (paso === 3) {
        paginaSiguiente.classList.add('ocultar');
        mostrarResumen(); // muestra el resumen de la cita creada al final del proceso

    }

    mostrarSeccion();
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function(){
        if(paso <= pasoInicial) return;
        paso--;
        botonesPaginador();
    })
}

function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function(){
        if(paso >= pasoFinal) return;
        paso++;
        botonesPaginador();
    })
}

async function consultarAPI() {
    try {
        const url = '/api/servicios';
        const resultado = await fetch(url);
        const servicios = await resultado.json();

        mostrarServicios(servicios);
                
    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios){
    servicios.forEach( servicio => {
        const { id, nombre, precio } = servicio;
        //Creando los parrafos contenedores de la información
        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;
        //Creando el DIV que va a contener los parrafos con los servicios....
        const servicioDiv = document.createElement('div');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        //Cuando hago click en el div de cada servicio corro una funcion callback seleccionarServicio para seleccionarla
        servicioDiv.onclick = function() {
            seleccionarServicio(servicio);
        }
        //agregando al div los P
        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);
        //asignando al elemento cuyo id es "servicios" (div) en cita/index.php ...un div con cada servicio... osea mostrandolo en pantalla
        document.querySelector('#servicios').appendChild(servicioDiv);
    });
}

function seleccionarServicio(servicio) {
    // extraigo el id del servicio seleccionado
    const {id} = servicio;
    // extraer el arreglo de servicios
    const {servicios} = cita;
    
    // identifico el elemento al que hago click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);


    //****************** Lo cambio por algo mejor
    //******************
    
    //******************//Comprobar si un servicio ya fue agregado, buscando "some" id (del arreglo servicios de citas) que sea igual al seleccionado.
    //******************if(servicios.some(agregado => agregado.id === id)) {
    //******************    // Eliminarlo. filtro 
    //******************    cita.servicios = servicios.filter(agregado => agregado.id !== id);
    //******************} else {
    //******************    // Agrego a servicio
    //******************    cita.servicios = [...servicios, servicio]; // (...servicios) tomo una copia del arreglo y le voy agregando los servicios a medida que hago click
    //******************    // le asigno la class al div "seleccionado"
    //******************    divServicio.classList.add('seleccionado');
    //******************}
    //****************** Lo cambio por algo mejor

    //Comprobar si un servicio ya fue agregado, buscando "some" id (del arreglo servicios de citas) que sea igual al seleccionado.
    const existe = servicios.some(agregado => agregado.id === id);
    // si existe
    if(existe) {
        //Filter recorre el arreglo servicios y deja solo los que sean distintos a id... osea el seleccionado... osea que lo quita
        cita.servicios = servicios.filter(agregado => agregado.id !== id);
        // le elimino la class al div con el id filtrado
        divServicio.classList.remove('seleccionado');
    } else {
        // Agrego a servicio
        cita.servicios = [...servicios, servicio]; //(...servicios) toma una copia del arreglo y le voy agregando los servicios a medida que hago pasa la comprobación
        // le asigno la class al div "seleccionado"
        divServicio.classList.add('seleccionado');
    }

}

function idCliente() {
    cita.id = document.querySelector('#id').value;
}

function nombreCliente(){
    cita.nombre = document.querySelector('#nombre').value;
}

function seleccionarFecha() {
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e) {
        // const dia = new Date(e.target.value).getUTCDay();
        const [year, mes, dia] = e.target.value.split('-');
        const fechaLocal = new Date(year, mes - 1, dia);
        const diaSemana = fechaLocal.getDay();
        if ([6, 0].includes (diaSemana)){
            e.target.value = '';
            mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
        } else {
            cita.fecha = e.target.value;
        }
    });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {

    // Previene que se genere más de una alerta
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) {
        alertaPrevia.remove();
    }

       
    // Crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    // Eliminar la alerta después de 3 segundos
    if(desaparece) {
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }
    
    
}

function seleccionarHora() {
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e) {
        const horaCita = e.target.value;
        const hora = horaCita.split(':')[0]; // Obtiene la hora (parte antes de los dos puntos)
        const minutos = horaCita.split(':')[1]; // Obtiene los minutos (parte después de los dos puntos)

        if(hora < 9 || hora > 17) {
            mostrarAlerta('Horario de atención de 9:00 a 18:00', 'error', '.formulario');
            e.target.value = '';
        } else {  
            cita.hora = e.target.value;
        }
    });
}

function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');
    if(!resumen) return;

    // Limpiar el resumen previo
    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }
    
    // Destructuring del objeto de cita para extraer los valores de nombre, fecha, hora y servicios
    const {nombre, fecha, hora, servicios} = cita;

    //Si el objeto de cita tiene algun valor vacio o no tiene servicios seleccionados, mostrar un mensaje de error
    if(Object.values({nombre, fecha, hora}).includes('') || servicios.length === 0) {
        mostrarAlerta('Faltan datos de Servicios, fecha u hora', 'error', '.contenido-resumen', false);
        return;
    }

    // Heading para servicios en resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de servicios';
    resumen.appendChild(headingServicios);

    // Servicios seleccionados
    servicios.forEach(servicio => {
        const { precio, nombre } = servicio;

        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;
        
        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

    // Formatear el div con los datos del cliente, fecha y hora.
    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    // Formatear la hora para mostrarla en español y corregir el desfase de fecha que se produce cada vez que instanciamos un nuevo objeto Date con la fecha seleccionada, ya que se muestra un día menos al seleccionado. Esto se debe a que el objeto Date interpreta la fecha en formato UTC y al convertirla a la zona horaria local, se produce un desfase de un día menos. Al sumar 2 días, corregimos este desfase y mostramos la fecha correcta en el resumen de la cita.
    // const fechaObj = new Date(fecha);
    // const mes = fechaObj.getMonth();
    // const dia = fechaObj.getDate() + 2;
    // const year = fechaObj.getFullYear();

    // const fechaUtc = new Date(Date.UTC(year, mes, dia));

    // const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    // const fechaFormateada = fechaUtc.toLocaleDateString('es-ES', opciones);

    const [year, mes, dia] = fecha.split('-');
    const fechaObj = new Date(year, mes - 1, dia);

    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const fechaFormateada = fechaObj.toLocaleDateString('es-ES', opciones);
    
    
    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;
    
    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    // Boton para crear una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar cita';
    botonReservar.onclick = reservarCita;

    // Heading para servicios en resumen
    const headingCliente = document.createElement('H3');
    headingCliente.textContent = 'Resumen de cita';
    resumen.appendChild(headingCliente);

    //Agrego al resumen los datos de la cita
    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);

    resumen.appendChild(botonReservar);
    
}

async function reservarCita() {
    const datos = new FormData();

    const {id, nombre, fecha, hora, servicios} = cita;

    //const idServicios = servicios.map(servicio => servicio.id).join(', ');
    const idServicios = servicios.map(servicio => servicio.id).join(',');
    
    // Agregar los datos al FormData segun como los reciba la API de PHP en $_POST
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);

    //console.log([...datos]); //sirve para ver el contenido de un FormData de forma clara en la consola
    
    try {
        // Peticion hacia la API de PHP para guardar la cita
        const url = '/api/citas';

        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos,
            
        });

        const resultado = await respuesta.json();

        //const texto = await respuesta.text();

        //console.log(texto);

        //return;
        
        console.log(resultado.resultado);

        if(resultado.resultado) {
            Swal.fire({
                icon: "success",
                title: "Cita reservada",
                text: "Tu cita fue reservada correctamente",
                
            }).then( () => {
                setTimeout( () => {
                    window.location.reload();            
                }, 3000);
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al reservar tu cita, por favor intenta de nuevo",
        });
    }
        
}