import Vue from 'vue'
import store from '../../store'
import { mapActions, mapMutations, mapState } from 'vuex'
import { eq, isEmpty, toNumber } from 'lodash'

import FormProveedor from '../../components/proveedores/FormProveedor'
import ListadoLicencia from '../../components/licencias/Listado'
import FormLicencia from '../../components/licencias/FormLicencia'

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
            proyectosRelacionar: []
        }
    },
    created() {
        this.$store.subscribe((mutation, state) => {
            switch (mutation.type) {
                case 'Proveedor/isSaved':
                    this.setProveedor([])
                    this.closeModal('modalAddProveedor')
                    break;

                case 'Licencia/isSaved':
                    this.clearForm()

                    if (this.$refs.modalEditarEstatus !== undefined) {
                        this.$refs.modalEditarEstatus.hide()
                    }

                    if (this.$refs.modalAsociarProyecto !== undefined) {
                        this.$refs.modalAsociarProyecto.hide()
                        this.proyectosRelacionar = []
                        this.setLicencia([])
                    }
                    break;

                default:
                    break;
            }
        })
    },
    beforeMount() {
        this.getLicencias()
        this.getTipoPago()
        this.getAllProveedores()
        this.getUsuarios()
    },
    mounted() {
        this.$watch(
            ()      => this.$refs['form-licencia'].$refs.formLicencia.flags.invalid,
            (value) => this.invalid = value,
        )
    },
    computed: {
        ...mapState('Gasto', ['tipoPagos']),
        ...mapState('Licencia', ['licencia', 'licencias']),
        ...mapState('Proveedor', ['proveedores', 'proveedor']),
        ...mapState('Proyecto', ['proyectos']),
        ...mapState('Usuario', ['usuarios']),
    },
    methods: {
        ...mapMutations('Licencia', ['setLicencia']),
        ...mapMutations('Proveedor', ['setProveedor']),

        ...mapActions({
            getTipoPago        : 'Gasto/getTipoPago',
            getLicencias       : 'Licencia/getAll',
            getLicencia        : 'Licencia/fetchLicencia',
            deleteLicencia     : 'Licencia/eliminar',
            relacionarProyectos: 'Licencia/relacionarProyectos',
            getAllProveedores  : 'Proveedor/getAll',
            getProyectos       : 'Proyecto/getAll',
            getUsuarios        : 'Usuario/getAll',
        }),

        showModal(element) {
            this.$refs[`${ element }`].show()
        },

        clearForm() {
            this.$refs['form-licencia'].clearForm()
            this.setLicencia([])
        },

        nuevoProveedor() {
            this.$refs['form-proveedor'].handleSubmit('add')
        },

        nuevaLicencia() {
            this.$refs['form-licencia'].handleSubmit('add')
        },

        editarLicencia() {
            this.$refs['form-licencia-editar'].handleSubmit('edit')
        },

        showModalEditar(id) {
            this.getLicencia(id)
            this.$refs.modalEditarLicencia.show()
        },

        showModalArchivos(id) {
            this.getLicencia(id)
            this.$refs.modalVerArchivos.show()
        },

        showModalAsociar(id) {
            this.getLicencia(id)
            this.getProyectos()
            this.$refs.modalAsociarProyecto.show()
        },

        closeModal(element = null) {
            if (!isEmpty(element)) {
                this.$refs[`${ element }`].hide()
            } else {
                this.$refs.modalEditarLicencia.hide()
            }
        },

        asociarProyectoLicencia() {
            this.$refs.formAsociarProyecto.validate().then(isValid => {
                if (isValid && !this.formWait) {
                    this.formWait = true
                    let data = {
                        id       : this.licencia.id,
                        proyectos: this.proyectosRelacionar
                    }

                    this.relacionarProyectos(data)
                    this.formWait = false
                }
            })
        },
    },
})
