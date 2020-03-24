import Vue from 'vue'
import store from '../../store'
import { mapActions, mapMutations, mapState } from 'vuex'
import { eq } from 'lodash'

import '../../main'

import ListadoCliente from '../../components/clientes/Listado'
import FormCliente from '../../components/clientes/FormCliente'

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
            if (eq(mutation.type, 'Cliente/isSaved')) {
                this.setCliente([])
            }
        })
    },
    beforeMount() {
        this.getClientes()
    },
    mounted() {
        this.$watch(
            ()      => this.$refs['form-cliente'].$refs.formCliente.flags.invalid,
            (value) => this.invalid = value,
        )
    },
    computed: {
        ...mapState('Cliente', ['cliente', 'clientes']),

    },
    methods: {
        ...mapActions({
            getClientes: 'Cliente/getAll',
            eliminar   : 'Cliente/eliminar',
            getCliente : 'Cliente/fetchCliente',
        }),

        ...mapMutations('Cliente', ['setCliente']),

        clearForm() {
            this.$refs['form-cliente'].clearForm()
            this.setCliente([])
        },

        nuevoCliente() {
            this.$refs['form-cliente'].handleSubmit('add')
        },

        editarCliente() {
            this.$refs['form-cliente-editar'].handleSubmit('edit')
        },

        showModal(id) {
            this.getCliente(id)
            this.$refs.modalEditarCliente.show()
        },

        closeModal() {
            this.$refs.modalEditarCliente.hide()
        },
    },
})
