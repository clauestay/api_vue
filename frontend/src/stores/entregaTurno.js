import { defineStore } from 'pinia';
import { alertaError, alertaExito, alertaErrores } from '@/helpers/AlertasSweetAlert';
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
        errors: null,
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
        async getMedicoEntrega(cod_prof){
            this.loading = true;
            try {
                const response = await axios.get(`/medicoEntregaTurno/${cod_prof}`);
                this.form.medico_entrega = response.data.medico_entrega;
            } catch (err) {
                this.manejarError(err, "Error al obtener el listado de los turnos.");
            } finally {
                this.loading = false;
            }
        },
        async getMedicos() {
            this.loading = true;
            try {
                const response = await axios.get("/medicosEntregaTurno");
                this.medicos = response.data.medicos;
            } catch (err) {
                this.manejarError(err, "Error al obtener el listado de los turnos.");
            } finally {
                this.loading = false;
            }
        },
        async getUnidades() {
            this.loading = true;
            try {
                const response = await axios.get("/unidades");
                this.unidades = response.data.unidades;
            } catch (err) {
                this.manejarError(err, "Error al obtener el listado de las unidades.");
            } finally {
                this.loading = false;
            }
        },
        // async guardarCambioTurno(router) {
        //     this.loading = true;
        //     try {
        //         const response = await axios.post('/guardarCambioTurno', this.form, {
        //             headers: {
        //                 'Content-Type': 'application/json'
        //             }
        //         });

        //         const responseData = response.data;

        //         if (responseData.exito) {
        //             alertaExito(responseData.exito);
        //             this.resetForm();
        //             router.push('/misTurnos');
        //         } else if (responseData.error) {
        //             alertaError(responseData.error);
        //         }
        //     } catch (err) {
        //         console.error(err);
        //         const responseData = err.response?.data;

        //         if (responseData) {
        //             if (err.response.status === 409) {
        //                 alertaError(responseData.info);
        //             } else if (responseData.errors) {
        //                 this.errors = responseData.errors;
        //                 alertaErrores(this.errors);
        //             } else if (responseData.error) {
        //                 alertaError(responseData.error);
        //             } else {
        //                 alertaError("Se ha producido un error desconocido.");
        //             }
        //         } else {
        //             alertaError("Se ha producido un error en la red o un error inesperado.");
        //         }
        //     } finally {
        //         this.loading = false;
        //     }
        // },
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
        async actualizarCambioTurno(router) {
            this.loading = true;
            try {
                const response = await axios.post('/actualizarCambioTurno', this.form, {
                    headers: {
                        'Content-type': 'application/json',
                    }
                });

                const responseData = response.data;

                if (responseData.exito) {
                    alertaExito(responseData.exito);
                    this.resetForm();
                    router.push('/misTurnos');
                } else if (responseData.error) {
                    alertaError(responseData.error);
                }
            } catch (err) {
                // this.manejarError(err, "Error al actualizar el turno.");
                console.error(err);
                const responseData = err.response?.data;

                if (responseData) {
                    if (err.response.status === 409) {
                        alertaError(responseData.info);
                    } else if (responseData.errors) {
                        this.errors = responseData.errors;
                        alertaErrores(this.errors);
                    } else if (responseData.error) {
                        alertaError(responseData.error);
                    } else {
                        alertaError("Se ha producido un error desconocido.");
                    }
                } else {
                    alertaError("Se ha producido un error en la red o un error inesperado.");
                }
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.form = {
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
            };
            this.errors = {};
        },
        async getMisTurnos() {
            this.loading = true;
            try {
                const response = await axios.get("/misTurnos");
                console.log(response.data);
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