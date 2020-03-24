import Vue from 'vue'
import { eq, isEmpty } from 'lodash'

const fetchUser = async ({ commit }, id) => {
    try {
        Vue.axios.get(`/usuarios/view/${ id }`, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            commit('Usuario/setUsuario', response.data, { root: true })
        })
    } catch (error) {
    }
}

const getAll = async ({ commit }) => {
    try {
        await Vue.axios.get('/usuarios/get-all', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            commit('Usuario/setUsuarios', response.data, { root: true })
        })
    } catch (error) {
    }
}

const agregar = async ({ commit, dispatch }, usuario) => {
    let formData        = new FormData()
    let apellidoMaterno = !isEmpty(usuario.apellido_materno) ? usuario.apellido_materno : ''
    let costoHora       = !isEmpty(usuario.costo_hora) ? usuario.costo_hora : ''

    formData.append('puesto_id', usuario.puesto_id)
    formData.append('perfil_id', usuario.perfil_id)
    formData.append('nombre', usuario.nombre)
    formData.append('apellido_paterno', usuario.apellido_paterno)
    formData.append('apellido_materno', apellidoMaterno)
    formData.append('email', usuario.email)
    formData.append('costo_hora', costoHora)
    formData.append('password', usuario.password)
    formData.append('re_password', usuario.re_password)
    formData.append('fecha_inicio', usuario.fecha_inicio)

    try {
        await Vue.axios.post('/usuarios/add', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true: false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Usuario/isSaved', isSaved, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

const editar = async ({ commit, dispatch }, usuario) => {
    let formData        = new FormData()
    let apellidoMaterno = !isEmpty(usuario.apellido_materno) ? usuario.apellido_materno: ''
    let costoHora       = !isEmpty(usuario.costo_hora) ? usuario.costo_hora            : ''

    formData.append('puesto_id', usuario.puesto_id)
    formData.append('perfil_id', usuario.perfil_id)
    formData.append('nombre', usuario.nombre)
    formData.append('apellido_paterno', usuario.apellido_paterno)
    formData.append('apellido_materno', apellidoMaterno)
    formData.append('email', usuario.email)
    formData.append('costo_hora', costoHora)

    try {
        Vue.axios.post(`/usuarios/edit/${ usuario.id }`, formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Usuario/isSaved', isSaved, { root: true })
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
        Vue.axios.post('/usuarios/delete', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon      = eq(response.data.code, 1) ? 'success' : 'error'
            let text      = response.data.message
            let isDeleted = eq(response.data.code, 1) ? true : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Usuario/isDeleted', isDeleted, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

export {
    fetchUser,
    agregar,
    getAll,
    editar,
    eliminar,
}
