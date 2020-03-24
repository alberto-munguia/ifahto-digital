import Vue from 'vue'
import store from '../../store'
import { mapActions, mapMutations, mapState } from 'vuex'
import { eq } from 'lodash'

import '../../main'

import FormProveedor from '../../components/proveedores/FormProveedor'
import ListadoProveedor from '../../components/proveedores/Listado'

new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    store,
    data() {
        return {
            formWait     : false,
            invalid      : true,
            actionButtons: [],
        }
    },
    created() {
        this.$store.subscribe((mutation, state) => {
            if (eq(mutation.type, 'Proveedor/isSaved')) {
                this.setProveedor([])
            }
        })
    },
    beforeMount() {
        this.getProveedores()
    },
    mounted() {
        this.$watch(
            ()      => this.$refs['form-proveedor'].$refs.formProveedor.flags.invalid,
            (value) => this.invalid = value
        )
    },
    computed: {
        ...mapState('Proveedor', ['proveedor', 'proveedores']),
    },
    methods: {
        ...mapActions({
            'getProveedores': 'Proveedor/getAll',
            'eliminar'      : 'Proveedor/eliminar',
            'getProveedor'  : 'Proveedor/fetchProveedor',
        }),

        ...mapMutations('Proveedor', ['setProveedor']),

        clearForm() {
            this.$refs['form-proveedor'].clearForm()
            this.setProveedor([])
        },

        nuevoProveedor() {
            this.$refs['form-proveedor'].handleSubmit('add')
        },

        editarProveedor() {
            this.$refs['form-proveedor-editar'].handleSubmit('edit')
        },

        showModal(id) {
            this.getProveedor(id)
            this.$refs.modalEditarProveedor.show()
        },

        closeModal() {
            this.$refs.modalEditarProveedor.hide()
        },
    },
})
