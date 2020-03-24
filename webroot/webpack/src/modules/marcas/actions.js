import Vue from 'vue'
import { eq } from 'lodash'

const fetchMarca = async ({ commit }, id) => {
    try {
        Vue.axios.get(`/marcas/view/${ id }`, {
            headers: { 'Content-Type': 'application/json' }
        }).then(response => commit('Marca/setMarca', response.data, { root: true }))
    } catch (error) {
        
    }
}

const getAllByCliente = async ({ commit }, idCliente) => {
    try {
        await Vue.axios.get(`/marcas/get-all-by-cliente/${ idCliente }`, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => commit('Marca/setMarcas', response.data, { root: true }))
    } catch (error) {
    }
}

const agregar = async ({ commit, dispatch }, marca) => {
    let formData = new FormData()

    formData.append('cliente_id', marca.cliente_id)
    formData.append('nombre', marca.nombre)

    try {
        Vue.axios.post('/marcas/add', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Marca/isSaved', isSaved, { root: true })
                    dispatch('getAllByCliente', marca.cliente_id)
                }
            })
        })
    } catch (error) {
    }
}

export {
    getAllByCliente,
    fetchMarca,
    agregar,
}
