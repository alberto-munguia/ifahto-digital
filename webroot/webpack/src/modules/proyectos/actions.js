import Vue from 'vue'
import moment from 'moment'
import { eq, isEmpty } from 'lodash'

const getAll = async ({ commit }, query) => {
    try {
        await Vue.axios.get('/proyectos/get-all', {
            headers: { 'Content-Type': 'application/json' },
            params : { query: query},
        }).then(response => commit('Proyecto/setProyectos', response.data, { root: true }))
    } catch (error) {
    }
}

const getById = async ({ commit },  id) => {
    try {
        await Vue.axios.get(`/proyectos/get-by-id/${ id }`, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => commit('Proyecto/setProyecto', response.data, { root: true }))
    } catch (error) {
    }
}

const agregar = async ({ commit }, proyecto) => {
    let formData           = new FormData()
    let fechaEntrega       = !isEmpty(proyecto.fecha_entrega) ? proyecto.fecha_entrega : ''
    let horaEntrega        = !isEmpty(proyecto.hora_entrega) ? proyecto.hora_entrega : ''
    let fechaFinalizacion  = !isEmpty(proyecto.fecha_finalizacion) ? proyecto.fecha_finalizacion : ''
    let horaFinalizacion   = !isEmpty(proyecto.hora_finalizacion) ? proyecto.hora_finalizacion : ''
    let porcentajeAnticipo = !isEmpty(proyecto.porcentaje_anticipo) ? proyecto.porcentaje_anticipo : ''
    let anticipo           = !isEmpty(proyecto.anticipo) ? proyecto.anticipo : ''

    formData.append('nombre', proyecto.nombre)
    formData.append('tipo_servicio_id', proyecto.tipo_servicio_id)
    formData.append('responsable_id', proyecto.responsable_id)
    formData.append('entidad_federativa_id', proyecto.entidad_federativa_id)
    formData.append('ciudad_id', proyecto.ciudad_id)
    formData.append('cliente_id', proyecto.cliente_id)
    formData.append('marca_id', proyecto.marca_id)
    formData.append('tipo_cliente_id', proyecto.tipo_cliente_id)
    formData.append('contacto_responsable_id', proyecto.contacto_responsable_id)
    formData.append('contacto_facturacion_id', proyecto.contacto_facturacion_id)
    formData.append('periodicidad_pago_id', proyecto.periodicidad_pago_id)
    formData.append('cliente_autorizacion', proyecto.cliente_autorizacion)
    formData.append('fecha_autorizacion', proyecto.fecha_autorizacion)
    formData.append('fecha_entrega', fechaEntrega)
    formData.append('hora_entrega', horaEntrega)
    formData.append('fecha_finalizacion', fechaFinalizacion)
    formData.append('hora_finalizacion', horaFinalizacion)
    formData.append('descripcion', proyecto.descripcion)
    formData.append('monto', proyecto.monto)
    formData.append('numero_pago', proyecto.numero_pago)
    formData.append('requiere_anticipo', Number(proyecto.requiere_anticipo))
    formData.append('porcentaje_anticipo', porcentajeAnticipo)
    formData.append('anticipo', anticipo)
    formData.append('usuarios_relacionados', JSON.stringify(proyecto.usuaios_relacionados))
    formData.append('multimedia_ids', proyecto.multimedia_ids)

    try {
        await Vue.axios.post('/proyectos/add', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value && isSaved) {
                    location.replace('/')
                }
            })
        })
    } catch (error) {
    }
}

const cambiarEstatus = async ({ commit, dispatch }, proyecto) => {
    let formData = new FormData()
    formData.append('estatus', proyecto.estatus)

    try {
        await Vue.axios.post(`/proyectos/cambiar-estatus/${ proyecto.id }`, formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value && isSaved) {
                    commit('Proyecto/isSaved', isSaved, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

const getPeriodicidadPagos = async ({ commit }) => {
    try {
        await Vue.axios.get('/proyectos/get-periodicidades-pago', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => commit('Proyecto/setPeriodicidadPagos', response.data, { root: true }))
    } catch (error) {
    }
}

const getTipoServicios = async ({ commit }) => {
    try {
        await Vue.axios.get('/proyectos/get-tipo-servicios', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => commit('Proyecto/setTipoServicios', response.data, { root: true }))
    } catch (error) {
    }
}

export {
    getAll,
    getById,
    agregar,
    getPeriodicidadPagos,
    getTipoServicios,
    cambiarEstatus,
}
