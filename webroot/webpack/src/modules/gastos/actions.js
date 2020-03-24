import Vue from 'vue'
import { eq, isEmpty, toNumber } from 'lodash'

const fetchGasto = async ({ commit }, id) => {
    try {
        await Vue.axios.get(`/gastos/view/${ id }`, {
            headers: { 'Content-Type': 'application/json' }
        }).then(response => commit('Gasto/setGasto', response.data, { root: true }))
    } catch (error) {
    }
}

const getAll = async ({ commit }) => {
    try {
        await Vue.axios.get('/gastos/get-all', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => commit('Gasto/setGastos', response.data, { root: true }))
    } catch (error) {
    }
}

const getTipoGasto = async ({ commit }) => {
    try {
        await Vue.axios.get('/gastos/get-tipo-gastos', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            commit('Gasto/setTipoGastos', response.data, { root: true })
        })
    } catch (error) {
    }
}

const getTipoPago = async ({ commit }) => {
    try {
        await Vue.axios.get('/gastos/get-tipo-pagos', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            commit('Gasto/setTipoPagos', response.data, { root: true })
        })
    } catch (error) {
    }
}

const agregar = async ({ commit, dispatch }, gasto) => {
    let formData    = new FormData()
    let descripcion = !isEmpty(gasto.descripcion) ? gasto.descripcion : ''

    formData.append('proyecto_id', gasto.proyecto_id)
    formData.append('proveedor_id', gasto.proveedor_id)
    formData.append('tipo_gasto_id', gasto.tipo_gasto_id)
    formData.append('importe', toNumber(gasto.importe))
    formData.append('tipo_pago_id', gasto.tipo_pago_id)
    formData.append('descripcion', descripcion)
    formData.append('recurso_id', gasto.recurso_id)
    formData.append('fecha', gasto.fecha)
    formData.append('multimedia_ids', JSON.stringify(gasto.multimedia_ids))

    try {
        await Vue.axios.post('/gastos/add', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success' : 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Gasto/isSaved', isSaved, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

const editar = async ({ commit, dispatch }, gasto) => {
    let formData    = new FormData()
    let descripcion = !isEmpty(gasto.descripcion) ? gasto.descripcion : ''

    formData.append('proyecto_id', gasto.proyecto_id)
    formData.append('proveedor_id', gasto.proveedor_id)
    formData.append('tipo_gasto_id', gasto.tipo_gasto_id)
    formData.append('importe', toNumber(gasto.importe))
    formData.append('tipo_pago_id', gasto.tipo_pago_id)
    formData.append('descripcion', descripcion)
    formData.append('recurso_id', gasto.recurso_id)
    formData.append('fecha', gasto.fecha)
    formData.append('multimedia_ids', JSON.stringify(gasto.multimedia_ids))

    try {
        await Vue.axios.post(`/gastos/edit/${ gasto.id }`, formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success' : 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Gasto/isSaved', isSaved, { root: true })
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
        await Vue.axios.post('/gastos/delete', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon      = eq(response.data.code, 1) ? 'success' : 'error'
            let text      = response.data.message
            let isDeleted = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Gasto/isDeleted', isDeleted, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

export {
    fetchGasto,
    getAll,
    getTipoGasto,
    getTipoPago,
    agregar,
    editar,
    eliminar,
}
