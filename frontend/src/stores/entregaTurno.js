import { defineStore } from 'pinia';
import { alertaError, alertaExito } from '@/helpers/AlertasSweetAlert';
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
axios.defaults.headers.common["Authorization"] = "Bearer " + authStore.authToken;

export const useEntregaTurnoStore = defineStore('entregaTurno', {
    state: () => ({
        turno: null,
        turnos: [],
        entregados: [],
        traslados: [],
        fallecidos: [],
        cirugias: [],
        error: null,
        loading: false,
        loading_obtener: false,
        total_rows: 0,
        sin_informacion: 'Sin información',
        form: {
            id_turno: '',
            entregados: [],
            traslados: [],
            fallecidos: [],
            cirugias: [],
            novedades: '',
            reemplazante: false,
            medico_entrega: '',
            medico_recibe: '',
            fecha_llegada: '',
            fecha_salida: '',
        },
        medicos: [],
        unidades: {}
    }),
    actions: {
        async obtenerTurno(id_turno) {
            this.loading_obtener = true;
            try {
                const response = await axios.get(`/obtenerTurno/${id_turno}`);
                this.turno = response.data.turno;
                console.log(this.turno);
                await this.obtenerEntregados(id_turno);
                await this.obtenerTraslados(id_turno);
                await this.obtenerFallecidos(id_turno);
                await this.obtenerCirugias(id_turno);
            } catch (err) {
                this.manejarError(err, "Error al obtener el listado de los turnos.");
            } finally {
                this.loading_obtener = false;
            }
        },
        async getMisTurnos() {
            this.loading = true;
            try {
                const response = await axios.get("/misTurnos");
                this.turnos = response.data.turnos;
            } catch (err) {
                this.manejarError(err, "Error al obtener el listado de los turnos.");
            } finally {
                this.loading = false;
            }
        },
        async getListadoTurnos(params) {
            this.loading = true;
            try {
                const response = await axios.get("/listadoTurnos", {
                    params: { ...params }
                });
                console.log(response);
                this.turnos = response.data.turnos.data;
                this.total_rows = response.data.turnos.total;
            } catch (err) {
                this.manejarError(err, "Error al obtener el listado de los turnos.");
            } finally {
                this.loading = false;
            }
        },
        async generarPdf(id_turno) {
            try {
                const response = await axios.get(`/generarPdfTurno/${id_turno}`, {
                    responseType: "blob",
                });

                const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
                const link = document.createElement("a");
                link.href = url;
                link.target = "_blank";
                link.click();
            } catch (err) {
                console.log(err);
                console.log("Error al generar el pdf ");
            }
        },
        async borrarTurno(id_turno) {
            try {
                const response = await axios.delete(`/entregaTurno/borrarTurno/${id_turno}`);
                let data = response.data;
                if (data.success) {
                    alertaExito(data.success);
                    // Actualizar la lista de turnos después de eliminar el turno
                    this.turnos = this.turnos.filter(turno => turno.id !== id_turno);
                } else if (data.error) {
                    alertaError(data.error);
                }
            } catch (err) {
                this.manejarError(err, "Error al intentar borrar el turno.");
            }
        },
        async obtenerEntregados(id_turno) {
            try {
                const response = await axios.get(`/obtenerEntregados/${id_turno}`);
                this.entregados = response.data.entregados;
                console.log(this.entregados);
            } catch (err) {
                this.manejarError(err, "Error al obtener el listado de los pacientes entregados.");
            }
        },
        async obtenerTraslados(id_turno) {
            try {
                const response = await axios.get(`/obtenerTraslados/${id_turno}`);
                this.traslados = response.data.traslados;
            } catch (err) {
                this.manejarError(err, "Error al obtener el listado de los pacientes trasladados.");
            }
        },
        async obtenerFallecidos(id_turno) {
            try {
                const response = await axios.get(`/obtenerFallecidos/${id_turno}`);
                this.fallecidos = response.data.fallecidos;
            } catch (err) {
                this.manejarError(err, "Error al obtener el listado de los pacientes fallecidos.");
            }
        },
        async obtenerCirugias(id_turno) {
            try {
                const response = await axios.get(`/obtenerCirugias/${id_turno}`);
                this.cirugias = response.data.cirugias;
            } catch (err) {
                this.manejarError(err, "Error al obtener el listado de las cirugías.");
            }
        },
        manejarError(err, mensaje) {
            console.log(err);
            if (err.response && err.response.data) {
                this.error = err.response.data.error || mensaje;
                alertaError(this.error);
            } else {
                this.error = mensaje;
                alertaError(this.error);
            }
        }
    }
})