import Vue from 'vue'
import store from '../store'
import { mapActions, mapState } from 'vuex'
import { eq } from 'lodash'

import ListadoProyecto from '../components/proyectos/Listado'

import '../main'

new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    store,
    data() {
        return {
            formWait: false,
            estatus : ['En Desarrollo', 'Terminado', 'Facturado', 'Pagado', 'Iguala', 'Permanente'],
        }
    },
    created() {
        this.$store.subscribe((mutation, state) => {
            if (eq(mutation.type, 'Proyecto/isSaved')) {
                this.$refs.modalEditarEstatus.hide()
            }
        })
    },
    beforeMount() {
        this.getProyectos()
    },
    computed: {
        ...mapState('Proyecto', ['proyectos', 'proyecto']),
    },
    methods: {
        ...mapActions({
            getProyecto   : 'Proyecto/getById',
            getProyectos  : 'Proyecto/getAll',
            cambiarEstatus: 'Proyecto/cambiarEstatus',
        }),

        showModalEstatus(id) {
            this.getProyecto(id)
            this.$refs.modalEditarEstatus.show()
        },
    },
})
