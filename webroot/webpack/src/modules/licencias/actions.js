import Vue from 'vue'
import { eq, isEmpty, toNumber } from 'lodash'

const fetchLicencia = async ({ commit }, id) => {
    try {
        await Vue.axios.get(`/licencias/view/${ id }`, {
            headers: { 'Content-Type': 'application/json' }
        }).then(response => commit('Licencia/setLicencia', response.data, { root: true }))
    } catch (error) {
    }
}

const getAll = async ({ commit }) => {
    try {
        await Vue.axios.get('/licencias/get-all', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            commit('Licencia/setLicencias', response.data, { root: true })
        })
    } catch (error) {
    }
}

const agregar = async ({ commit, dispatch }, licencia) => {
    let formData = new FormData()
    let usuario  = licencia.usuario_id !== undefined ? toNumber(licencia.usuario_id) : 0

    formData.append('proveedor_id', licencia.proveedor_id)
    formData.append('tipo_pago_id', licencia.tipo_pago_id)
    formData.append('usuario_id', usuario)
    formData.append('nombre', licencia.nombre)
    formData.append('descripcion', licencia.descripcion)
    formData.append('importe', toNumber(licencia.importe))
    formData.append('fecha', licencia.fecha)
    formData.append('multimedia_ids', JSON.stringify(licencia.multimedia_ids))

    try {
        await Vue.axios.post('/licencias/add', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success' : 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Licencia/isSaved', isSaved, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

const editar = async ({ commit, dispatch }, licencia) => {
    let formData = new FormData()
    let usuario  = licencia.usuario_id !== undefined ? toNumber(licencia.usuario_id) : 0

    formData.append('proveedor_id', licencia.proveedor_id)
    formData.append('tipo_pago_id', licencia.tipo_pago_id)
    formData.append('usuario_id', usuario)
    formData.append('nombre', licencia.nombre)
    formData.append('descripcion', licencia.descripcion)
    formData.append('importe', toNumber(licencia.importe))
    formData.append('fecha', licencia.fecha)
    formData.append('multimedia_ids', JSON.stringify(licencia.multimedia_ids))

    try {
        await Vue.axios.post(`/licencias/edit/${ licencia.id }`, formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Licencia/isSaved', isSaved, { root: true })
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
        await Vue.axios.post('/licencias/delete', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon      = eq(response.data.code, 1) ? 'success' : 'error'
            let text      = response.data.message
            let isDeleted = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Licencia/isDeleted', isDeleted, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

const relacionarProyectos = async ({ commit }, data) => {
    console.log(data);
    let formData = new FormData()
    
    formData.append('proyecto_ids', JSON.stringify(data.proyectos))

    try {
        await Vue.axios.post(`/licencias/relacionar-proyectos/${ data.id }`, formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success' : 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Licencia/isSaved', isSaved, { root: true })
                }
            })
        })
    } catch (error) {
        
    }
}

export {
    fetchLicencia,
    getAll,
    agregar,
    editar,
    eliminar,
    relacionarProyectos,
}
