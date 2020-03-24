import Vue from 'vue'
import { eq, isEmpty } from 'lodash'

const getAll = async ({ commit }) => {
    try {
        await Vue.axios.get('/contactos/get-all', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => commit('Contacto/setContactos', response.data, { root: true }))
    } catch (error) {
    }
}

const agregar = async ({ commit, dispatch }, contacto) => {
    let formData = new FormData()

    let apellidoMaterno = !isEmpty(contacto.apellido_materno) ? contacto.apellido_materno : ''
    let telefono        = !isEmpty(contacto.telefono) ? contacto.telefono : ''
    let extension       = !isEmpty(contacto.extension) ? contacto.extension : ''

    formData.append('nombre', contacto.nombre)
    formData.append('apellido_paterno', contacto.apellido_paterno)
    formData.append('apellido_materno', apellidoMaterno)
    formData.append('email', contacto.email)
    formData.append('telefono', telefono)
    formData.append('extension', extension)

    try {
        await Vue.axios.post('/contactos/add', formData, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => {
            let icon    = eq(response.data.code, 1) ? 'success': 'error'
            let text    = response.data.message
            let isSaved = eq(response.data.code, 1) ? true     : false

            Vue.swal({ text, icon }).then(response => {
                if (response.value) {
                    commit('Contacto/isSaved', isSaved, { root: true })
                    dispatch('getAll')
                }
            })
        })
    } catch (error) {
    }
}

export {
    getAll,
    agregar,
}
