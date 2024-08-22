import { alertaError } from '@/helpers/AlertasSweetAlert';

export function manejarError(err, mensaje) {
  let errorMessage = mensaje;

  if (err.response && err.response.data) {
    errorMessage = err.response.data.error || mensaje;
  }

  alertaError(errorMessage);
}