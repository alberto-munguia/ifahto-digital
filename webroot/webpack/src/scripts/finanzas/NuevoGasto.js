import Vue from 'vue'
import store from '../../store'
import { mapActions, mapMutations, mapState } from 'vuex'
import { eq, isEmpty } from 'lodash'

import FormProveedor from '../../components/proveedores/FormProveedor'
import FormGasto from '../../components/finanzas/FormGasto'
import ListadoGasto from '../../components/finanzas/ListadoGasto'

import '../../main'
import '../../plugins/vue-dropzone'

new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    store,
    data() {
        return {
            formWait: false,
            invalid : true,
        }
    },
    created() {
        this.$store.subscribe((mutation, state) => {
            switch (mutation.type) {
                case 'Proveedor/isSaved':
                    this.setProveedor([])
                    this.closeModal('modalAddProveedor')
                    break;

                case 'Gasto/isSaved':
                    this.clearForm()

                    if (this.$refs.modalEditarEstatus !== undefined) {
                        this.$refs.modalEditarEstatus.hide()
                    }
                    break;

                default:
                    break;
            }
        })
    },
    beforeMount() {
        this.getAllProyectos()
        this.getAllProveedores()
        this.getTipoGasto()
        this.getTipoPago()
        this.getUsuarios()
        this.getGastos()
    },
    mounted() {
        this.$watch(
            ()      => this.$refs['form-gasto'].$refs.formGasto.flags.invalid,
            (value) => this.invalid = value,
        )
    },
    computed: {
        ...mapState('Gasto', ['gasto', 'gastos', 'tipoGastos', 'tipoPagos']),
        ...mapState('Proveedor', ['proveedores', 'proveedor']),
        ...mapState('Proyecto', ['proyectos']),
        ...mapState('Usuario', ['usuarios']),
    },
    methods: {
        ...mapActions({
            getGasto         : 'Gasto/fetchGasto',
            getGastos        : 'Gasto/getAll',
            getTipoGasto     : 'Gasto/getTipoGasto',
            getTipoPago      : 'Gasto/getTipoPago',
            deleteGasto      : 'Gasto/eliminar',
            getAllProyectos  : 'Proyecto/getAll',
            getAllProveedores: 'Proveedor/getAll',
            getUsuarios      : 'Usuario/getAll',
        }),

        ...mapMutations('Gasto', ['setGasto']),
        ...mapMutations('Proveedor', ['setProveedor']),

        showModal(element) {
            this.$refs[`${ element }`].show()
        },

        showModalEditar(id) {
            this.getGasto(id)
            this.$refs.modalEditarGasto.show()
        },

        showModalArchivos(id) {
            this.getGasto(id)
            this.$refs.modalVerArchivos.show()
        },

        closeModal(element = null) {
            if (!isEmpty(element)) {
                this.$refs[`${ element }`].hide()
            } else {
                this.$refs.modalEditarGasto.hide()
            }
        },

        nuevoProveedor() {
            this.$refs['form-proveedor'].handleSubmit('add')
        },

        nuevoTipoGasto() {
            this.$refs['form-tipo-gasto'].handleSubmit('add')
        },

        clearForm() {
            this.$refs['form-gasto'].clearForm()
            this.setGasto([])
        },

        editarGasto() {
            this.$refs['form-gasto-editar'].handleSubmit('edit')
        },

        nuevoGasto() {
            this.$refs['form-gasto'].handleSubmit('add')
        },
    },
})
