import Vue from 'vue'
import store from '../../store'
import moment from 'moment'
import { mapActions, mapMutations, mapState } from 'vuex'
import { eq } from 'lodash'

import ListadoUsuario from '../../components/usuarios/Listado'
import FormUsuarioEditar from '../../components/usuarios/FormEditar'

import '../../main'

new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    store,
    data() {
        return {
            locale: {
                labelTodayButton   : 'Hoy',
                labelNowButton     : 'Ahora',
                labelCloseButton   : 'Cerrar',
                labelNoTimeSelected: 'No has seleccionado la hora',
                labelNoDateSelected: 'No has seleccionado la fecha',
                labelToday         : 'Hoy',
                labelPrevYear      : 'Año anterior',
                labelPrevMonth     : 'Mes anterior',
                labelCurrentMonth  : 'Mes actual',
                labelNextMonth     : 'Siguiente mes',
                labelNextYear      : 'Siguiente año',
                labelHours         : 'Horas',
                labelMinutes       : 'Minutos',
                labelSeconds       : 'Segundos',
                labelIncrement     : 'Incrementar',
                labelDecrement     : 'Disminuir',
            },
            formWait     : false,
            disabledDates: { from: new Date() },
        }
    },
    created() {
        this.$store.subscribe((mutation, state) => {
            if (eq(mutation.type, 'Usuario/isSaved')) {
                this.setUsuario([])
                this.clearForm()
            }
        })
    },
    beforeMount() {
        this.getUsuarios()
        this.getPerfiles()
        this.getPuestos()
    },
    computed: {
        ...mapState('Usuario', ['usuario', 'usuarios']),
        ...mapState('Perfil', ['perfiles']),
        ...mapState('Puesto', ['puestos']),
    },
    methods: {
        ...mapMutations('Usuario', ['setUsuario']),

        ...mapActions({
            getPerfiles   : 'Perfil/getAll',
            getPuestos    : 'Puesto/getPuestos',
            agregarUsuario: 'Usuario/agregar',
            getUsuarios   : 'Usuario/getAll',
            getUsuario    : 'Usuario/fetchUser',
            eliminar      : 'Usuario/eliminar',
        }),

        clearForm() {
            this.$refs['form-usuario'].reset()
        },

        nuevoUsuario() {
            this.$refs['form-usuario'].validate().then(isValid => {
                if (isValid && !this.formWait) {
                    this.formWait = true

                    this.usuario.fecha_inicio = moment(this.usuario.fecha_timesheet).format('YYYY-MM-DD')
                    this.agregarUsuario(this.usuario)
                    this.formWait = false
                }
            })
        },

        editarUsuario() {
            this.$refs['form-usuario-editar'].handleSubmit()
        },

        showModal(id) {
            this.getUsuario(id)
            this.$refs.modalEditarUsuario.show()
        },

        closeModal(element) {
            this.$refs[`${ element }`].hide()
        },

        editarCosto() {},
    },
})
