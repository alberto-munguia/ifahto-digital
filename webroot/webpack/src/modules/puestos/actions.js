import Vue from 'vue'

const getPuestos = async ({ commit }) => {
    try {
        await Vue.axios.get('/puestos/get-all').then(response => {
            commit('Puesto/setPuestos', response.data, { root: true })
        })
    } catch (error) {
    }
}

export {
    getPuestos,
}
