import Vue from 'vue'
import store from '../../store'
import { mapActions, mapMutations, mapState } from 'vuex'

import '../../main'

new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    store,
    data() {
        return {
            formWait    : false,
            usuario     : [],
            horaCotizada: 0
        }
    },
    beforeMount() {
        this.getUsuarios()
    },
    computed: {
        ...mapState('Proyecto', ['proyecto']),
        ...mapState('Usuario', ['usuarios']),
    },
    methods: {
        ...mapActions({
            'getUsuarios': 'Usuario/getAll'
        }),
    },
})
