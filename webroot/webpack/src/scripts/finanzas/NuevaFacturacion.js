import Vue from 'vue'
import store from '../../store'
import { mapActions, mapMutations, mapState } from 'vuex'
import { eq } from 'lodash'

import ListadoFacturacion from '../../components/finanzas/ListadoFacturacion'
import FormFacturacion from '../../components/finanzas/FormFacturacion'

import '../../main'
import '../../plugins/vue-dropzone'

new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    store,
    data() {
        return {
            formWait: false,
            invalid: true,
            estatus: ['Pagado', 'Solicitada', 'Facturada', 'Espera de ODC', 'Cancelada'],
        }
    },
    created() {
        this.$store.subscribe((mutation, state) => {
            if (eq(mutation.type, 'Facturacion/isSaved')) {
                this.clearForm()

                if (this.$refs.modalEditarEstatus !== undefined) {
                    this.$refs.modalEditarEstatus.hide()
                }
            }
        })
    },
    beforeMount() {
        this.getFacturaciones()
        this.getTipoFacturacion()
        this.getAllProyectos()
        this.getUsuarios()
    },
    mounted() {
        this.$watch(
            ()      => this.$refs['form-facturacion'].$refs.formFacturacion.flags.invalid,
            (value) => this.invalid = value,
        )
    },
    computed: {
        ...mapState('Facturacion', ['facturacion', 'tipoFacturaciones', 'facturaciones']),
        ...mapState('Proyecto', ['proyectos']),
        ...mapState('Usuario', ['usuarios']),
    },
    watch: {
    },
    methods: {
        ...mapMutations('Facturacion', ['setFacturacion']),

        ...mapActions({
            getFacturacion    : 'Facturacion/fetchFacturacion',
            getFacturaciones  : 'Facturacion/getAll',
            getTipoFacturacion: 'Facturacion/getTipoFacturacion',
            deleteFacturacion : 'Facturacion/eliminar',
            cambiarEstatus    : 'Facturacion/cambiarEstatus',
            getAllProyectos   : 'Proyecto/getAll',
            getUsuarios       : 'Usuario/getAll',
        }),

        clearForm() {
            this.$refs['form-facturacion'].clearForm()
            this.setFacturacion([])
        },

        nuevaFacturacion() {
            this.$refs['form-facturacion'].handleSubmit('add')
        },

        editarFacturacion() {
            this.$refs['form-facturacion-editar'].handleSubmit('edit')
        },

        showModalEditar(id) {
            this.getFacturacion(id)
            this.$refs.modalEditarFacturacion.show()
        },

        showModalArchivos(id) {
            this.getFacturacion(id)
            this.$refs.modalVerArchivos.show()
        },

        closeModal() {
            this.$refs.modalEditarFacturacion.hide()
        },

        showModalEstatus(id) {
            this.getFacturacion(id)
            this.$refs.modalEditarEstatus.show()
        },
    },
})
