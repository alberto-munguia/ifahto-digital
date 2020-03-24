import Vue from 'vue'
import { eq, isEmpty, toNumber } from 'lodash'

const getTipoFacturacion = async ({ commit }) => {
    try {
        await Vue.axios.get('/facturaciones/get-tipo-facturaciones', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            commit('Facturacion/setTipoFacturaciones', response.data, { root: true })
        })
    } catch (error) {
    }
}

const fetchFacturacion = async ({ commit }, id) => {
    try {
        await Vue.axios.get(`/facturaciones/view/${ id }`, {
            headers: { 'Content-Type': 'application/json' }
        }).then(response => commit('Facturacion/setFacturacion', response.data, { root: true }))
    } catch (error) {
    }
}

const getAll = async ({ commit }) => {
    try {
        await Vue.axios.get('/facturaciones/get-all', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            commit('Facturacion/setFacturaciones', response.data, { root: true })
        })
    } catch (error) {
    }
}

const agregar = async ({ commit, dispatch }, facturacion) => {
    let formData          = new FormData()
    let descripcion       = !isEmpty(facturacion.descripcion) ? facturacion.descripcion : ''
    let fechaEstimadaPago = !isEmpty(facturacion.fecha_estimada_pago) ? facturacion.fecha_estimada_pago : ''
    let fechaExpedicion   = !isEmpty(facturacion.fecha_expedicion) ? facturacion.fecha_expedicion : ''
    let fechaPago         = !isEmpty(facturacion.fecha_pago) ? facturacion.fecha_pago : ''
    let numeroFactura     = !isEmpty(facturacion.numero_factura) ? facturacion.numero_factura : ''
    let numeroProyecto    = !isEmpty(facturacion.numero_proyecto_cliente) ? facturacion.numero_proyecto_cliente : ''
    let ordenCompra       = !isEmpty(facturacion.orden_compra) ? facturacion.orden_compra : ''

    formData.append('proyecto_id', facturacion.proyecto_id)
    formData.append('tipo_facturacion_id', facturacion.tipo_facturacion_id)
    formData.append('descripcion', descripcion)
    formData.append('fecha_estimada_pago', fechaEstimadaPago)
    formData.append('fecha_expedicion', fechaExpedicion)
    formData.append('fecha_pago', fechaPago)
    formData.append('moneda', facturacion.moneda)
    formData.append('numero_factura', numeroFactura)
    formData.append('numero_proyecto_cliente', numeroProyecto)
    formData.append('orden_compra', ordenCompra)
    formData.append('importe', toNumber(facturacion.importe))
    formData.append('iva', toNumber(facturacion.iva))
    formData.append('total', toNumber(facturacion.total))
    formData.append('multimedia_ids', JSON.stringify(facturacion.multimedia_ids))

    try {
        await Vue.axios.post('/facturaciones/add', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success' : 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Facturacion/isSaved', isSaved, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

const editar = async ({ commit, dispatch }, facturacion) => {
    let formData          = new FormData()
    let descripcion       = !isEmpty(facturacion.descripcion) ? facturacion.descripcion : ''
    let fechaEstimadaPago = !isEmpty(facturacion.fecha_estimada_pago) ? facturacion.fecha_estimada_pago : ''
    let fechaExpedicion   = !isEmpty(facturacion.fecha_expedicion) ? facturacion.fecha_expedicion : ''
    let fechaPago         = !isEmpty(facturacion.fecha_pago) ? facturacion.fecha_pago : ''
    let numeroFactura     = !isEmpty(facturacion.numero_factura) ? facturacion.numero_factura : ''
    let numeroProyecto    = !isEmpty(facturacion.numero_proyecto_cliente) ? facturacion.numero_proyecto_cliente : ''
    let ordenCompra       = !isEmpty(facturacion.orden_compra) ? facturacion.orden_compra : ''

    formData.append('proyecto_id', facturacion.proyecto_id)
    formData.append('tipo_facturacion_id', facturacion.tipo_facturacion_id)
    formData.append('descripcion', descripcion)
    formData.append('fecha_estimada_pago', fechaEstimadaPago)
    formData.append('fecha_expedicion', fechaExpedicion)
    formData.append('fecha_pago', fechaPago)
    formData.append('moneda', facturacion.moneda)
    formData.append('numero_factura', numeroFactura)
    formData.append('numero_proyecto_cliente', numeroProyecto)
    formData.append('orden_compra', ordenCompra)
    formData.append('importe', toNumber(facturacion.importe))
    formData.append('iva', toNumber(facturacion.iva))
    formData.append('total', toNumber(facturacion.total))
    formData.append('multimedia_ids', JSON.stringify(facturacion.multimedia_ids))

    try {
        await Vue.axios.post(`/facturaciones/edit/${ facturacion.id }`, formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Facturacion/isSaved', isSaved, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

const eliminar = async ({ commit, dispatch }, id) => {
    let formData = new FormData()

    formData.append('id', id)

    try {
        await Vue.axios.post('/facturaciones/delete', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon      = eq(response.data.code, 1) ? 'success' : 'error'
            let text      = response.data.message
            let isDeleted = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Facturacion/isDeleted', isDeleted, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

const cambiarEstatus = async ({ commit, dispatch }, facturacion) => {
    let formData = new FormData()
    formData.append('estatus', facturacion.estatus)

    try {
        await Vue.axios.post(`/facturaciones/cambiar-estatus/${ facturacion.id }`, formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value && isSaved) {
                    commit('Facturacion/isSaved', isSaved, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

export {
    getTipoFacturacion,
    fetchFacturacion,
    getAll,
    agregar,
    editar,
    eliminar,
    cambiarEstatus,
}
