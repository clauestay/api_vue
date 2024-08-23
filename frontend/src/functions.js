import { alertaError } from '@/helpers/AlertasSweetAlert';

export function manejarError(err, mensaje) {
  let errorMessage = mensaje;

  if (err.response && err.response.data) {
    errorMessage = err.response.data.error || mensaje;
  }

  alertaError(errorMessage);
}

export async function generarPdf(url) {
  try {
    const response = await axios.get(url, {
      responseType: 'blob',
    });

    const _url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = _url;
    link.target = '_blank';
    link.click();
  } catch (err) {
    console.log(err);
    manejarError(err, 'Error al generar el PDF');
  }
}