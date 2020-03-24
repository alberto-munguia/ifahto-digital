import Vue from 'vue'
import { eq, isEmpty } from 'lodash'

const fetchCliente = async ({ commit }, id) => {
    try {
        await Vue.axios.get(`/clientes/view/${ id }`, {
            headers: { 'Content-Type': 'application/json' }
        }).then(response => commit('Cliente/setCliente', response.data, { root: true }))
    } catch (error) {
    }
}

const getAll = async ({ commit }) => {
    try {
        await Vue.axios.get('/clientes/get-all', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => commit('Cliente/setClientes', response.data, { root: true }))
    } catch (error) {
    }
}

const agregar = async ({ commit, dispatch }, cliente) => {
    let formData  = new FormData()
    let rfc       = !isEmpty(cliente.rfc) ? cliente.rfc : ''
    let direccion = !isEmpty(cliente.direccion) ? cliente.direccion : ''

    formData.append('razon_social', cliente.razon_social)
    formData.append('nombre', cliente.nombre)
    formData.append('rfc', rfc)
    formData.append('direccion', direccion)

    try {
        await Vue.axios.post('/clientes/add', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Cliente/isSaved', isSaved, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

const editar = async ({ commit, dispatch }, cliente) => {
    let formData  = new FormData()
    let rfc       = !isEmpty(cliente.rfc) ? cliente.rfc : ''
    let direccion = !isEmpty(cliente.direccion) ? cliente.direccion : ''

    formData.append('razon_social', cliente.razon_social)
    formData.append('nombre', cliente.nombre)
    formData.append('rfc', rfc)
    formData.append('direccion', direccion)

    try {
        await Vue.axios.post(`/clientes/edit/${ cliente.id }`, formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Cliente/isSaved', isSaved, { root: true })
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
        await Vue.axios.post('/clientes/delete', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon      = eq(response.data.code, 1) ? 'success' : 'error'
            let text      = response.data.message
            let isDeleted = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Cliente/isDeleted', isDeleted, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

const getByProyecto = async ({ commit }, idProyecto) => {
    try {
        await Vue.axios.get(`/clientes/get-by-proyecto/${ idProyecto }`, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => commit('Cliente/setCliente', response.data, { root: true }))
    } catch (error) {
    }
}

const getTipoClientes = async ({ commit }) => {
    try {
        await Vue.axios.get('/clientes/get-tipo-clientes', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => commit('Cliente/setTipoClientes', response.data, { root: true }))
    } catch (error) {
    }
}

export {
    getAll,
    fetchCliente,
    agregar,
    editar,
    eliminar,
    getByProyecto,
    getTipoClientes,
}
