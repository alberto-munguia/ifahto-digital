import Vue from 'vue'

const getAll = async ({ commit }) => {
    try {
        await Vue.axios.get('/perfiles/get-all').then(response => {
            commit('Perfil/setPerfiles', response.data, { root: true })
        })
    } catch (error) {
    }
}

export {
    getAll,
}
