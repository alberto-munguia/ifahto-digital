import Vue from 'vue'
import { eq } from 'lodash'

const fetchProveedor = async ({ commit }, id) => {
    try {
        Vue.axios.get(`/proveedores/view/${ id }`, {
            headers: { 'Content-Type': 'application/json' }
        }).then(response => commit('Proveedor/setProveedor', response.data, { root: true }))
    } catch (error) {
        
    }
}

const getAll = async ({ commit }) => {
    try {
        await Vue.axios.get('/proveedores/get-all', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            commit('Proveedor/setProveedores', response.data, { root: true })
        })
    } catch (error) {
    }
}

const agregar = async ({ commit, dispatch }, proveedor) => {
    let formData = new FormData()

    formData.append('razon_social', proveedor.razon_social)
    formData.append('nombre', proveedor.nombre)
    formData.append('tipo', proveedor.tipo)

    try {
        Vue.axios.post('/proveedores/add', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Proveedor/isSaved', isSaved, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

const editar = async ({ commit, dispatch }, proveedor) => {
    let formData = new FormData()

    formData.append('razon_social', proveedor.razon_social)
    formData.append('nombre', proveedor.nombre)
    formData.append('tipo', proveedor.tipo)

    try {
        Vue.axios.post(`/proveedores/edit/${ proveedor.id }`, formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Proveedor/isSaved', isSaved, { root: true })
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
        await Vue.axios.post('/proveedores/delete', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon      = eq(response.data.code, 1) ? 'success' : 'error'
            let text      = response.data.message
            let isDeleted = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Proveedor/isDeleted', isDeleted, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

export {
    fetchProveedor,
    getAll,
    agregar,
    editar,
    eliminar,
}
