import Swal from 'sweetalert2'
import { router } from '@inertiajs/vue3';

// aca declarar todos los tipos de sweetalert a utilizar.
const alertaExito = (mensaje) => {
    Swal.fire({
        title: '¡Excelente!',
        text: mensaje,
        icon: 'success',
        confirmButtonColor: 'blue'
    });
}

const alertaError = (mensaje) => {
    Swal.fire({
        title: '¡Error!',
        html: mensaje,
        icon: 'error',
        confirmButtonColor: 'blue'
    });
}

const alertaInfo = (mensaje) => {
    Swal.fire({
        title: '¡Información!',
        html: mensaje,
        icon: 'info',
        confirmButtonColor: 'blue'
    });
}

const alertaErrores = (mensajes) => {
    let mensajeAlerta = '';
    for (const mensaje in mensajes) {
        if (mensajes.hasOwnProperty(mensaje)) {
            mensajeAlerta += '<p class="text-justify">'+ mensajes[mensaje] + '</p><br>';
        }
    }

    Swal.fire({
        title: '¡Error!',
        html: mensajeAlerta,
        icon: 'error',
        confirmButtonColor: 'blue'
    });
}

const alertaPregunta = (ruta, titulo, form) => {
    Swal.fire({
        title: titulo,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        confirmButtonColor: 'green',
        denyButtonText: `Cancelar`,
        cancelButtonColor: 'red'
    }).then((result) => {
        if (result.isConfirmed) {
            router.get(route(ruta, form));
        }
    })
}

export {
    alertaExito,
    alertaError,
    alertaInfo,
    alertaErrores,
    alertaPregunta
}