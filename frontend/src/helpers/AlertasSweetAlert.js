import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';

const router = useRouter();

// Declarar todos los tipos de SweetAlert a utilizar
const alertaExito = (mensaje) => {
    Swal.fire({
        title: '¡Excelente!',
        text: mensaje,
        icon: 'success',
        confirmButtonColor: 'blue'
    });
};

const alertaError = (mensaje) => {
    Swal.fire({
        title: '¡Error!',
        html: mensaje,
        icon: 'error',
        confirmButtonColor: 'blue'
    });
};

const alertaInfo = (mensaje) => {
    Swal.fire({
        title: '¡Información!',
        html: mensaje,
        icon: 'info',
        confirmButtonColor: 'blue'
    });
};

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
};

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
            router.push({ name: ruta, params: form });
        }
    });
};

const alertaConfirmacion = (name, url, redirect) => {
    const alert = Swal.mixin({buttonsStyling: true});
    alert.fire({
        title: titulo,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // sendRequest('DELETE')
        }
    });
};

export {
    alertaExito,
    alertaError,
    alertaInfo,
    alertaErrores,
    alertaPregunta,
    alertaConfirmacion
};
