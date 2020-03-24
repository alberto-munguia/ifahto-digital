import Vue from 'vue'

const getByEntidad = async ({ commit }, id) => {
    try {
        await Vue.axios.get(`/ciudades/get-all-by-entidad-federativa/${ id }`, {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => commit('Ciudad/setCiudades', response.data, { root: true }))
    } catch (error) {
    }
}

export {
    getByEntidad,
}
