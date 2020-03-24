import Vue from 'vue'

const getAll = async ({ commit }) => {
    try {
        await Vue.axios.get('/entidad-federativas/get-all', {
            headers: { 'Content-Type': 'application/json' },
        }).then(response => commit('EntidadFederativa/setEntidades', response.data, { root: true }))
    } catch (error) {
    }
}

export {
    getAll,
}
