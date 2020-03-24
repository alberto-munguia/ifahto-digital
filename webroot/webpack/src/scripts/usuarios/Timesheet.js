import Vue from 'vue'
import store from '../../store'
import moment from 'moment'
import { mapActions, mapState, mapGetters } from 'vuex'
import { isEmpty, eq } from 'lodash'

import '../../main'

new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    store,
    data() {
        return {
            totalHoras      : 0,
            formWait        : false,
            disableTimesheet: false,
            showExtraOptions: false,
            startDate       : '',
            fields          : [
                { key: 'cliente', label: 'Cliente' },
                { key: 'proyecto', label: 'Proyecto' },
                { key: 'tipo', label: 'Tipo' },
                { key: 'total_hora', label: 'Horas Totales' },
                'acciones'
            ],
            items       : [],
            proyecto    : [],
            cliente     : [],
            total       : 0,
            tipo        : '',
            extraOptions: [
                'Enfermedad',
                'Maternidad',
                'Vacaciones',
                'Feriados',
                'Capacitación',
                'Junta',
                'Convivio',
                'Día Inhábil',
                'Permiso',
            ],
        }
    },
    created() {
        this.$store.subscribe((mutation, state) => {
            if (eq(mutation.type, 'Proyecto/setProyectos')) {
                this.proyectos.push({ id: 0, nombre: 'Otro' })
            }
        })
    },
    beforeMount() {
        this.getProyectos({ 'Proyectos.estatus': 'En Desarrollo' })
        this.getFechaTimesheet()
    },
    mounted() {
    },
    computed: {
        ...mapState('Proyecto', ['proyectos']),

        ...mapGetters({
            'findProyecto': 'Proyecto/getProyecto',
        }),

        showTable() {
            return !isEmpty(this.items) ? true : false
        },
    },
    methods: {
        ...mapActions({
            getCliente     : 'Cliente/fetchCliente',
            getProyectos   : 'Proyecto/getAll',
            getProyectoById: 'Proyecto/getById',
        }),

        getFechaTimesheet() {
            this.$http.get('/usuarios/get-date-timesheet', {
                headers: { 'Content-Type': 'application/json' },
                params : { idUsuario: localStorage.getItem('usuario_id') },
            }).then(response => {
                this.startDate        = moment(response.data.startDate, 'YYYY-MM-DD', 'es').format('dddd, LL')
                this.disableTimesheet = response.data.isToday
            })
        },

        getTotalHoras() {
            let total = 0

            this.items.forEach(element => {
                total += Number(element.total_hora)
            });

            this.totalHoras = total
        },

        agregarHoras() {
            let timesheet = this.items
            timesheet.push({
                cliente_id : this.cliente.id,
                cliente    : this.cliente.nombre,
                proyecto_id: this.proyecto.id,
                proyecto   : eq(this.proyecto.id, 0) ? 'Otro' : this.proyecto.nombre,
                total_hora : this.total,
                tipo       : this.tipo,
            })

            this.items = timesheet
            this.getTotalHoras()
            this.clearForm()
        },

        eliminarHora(index) {
            this.items.splice(index, 1)
            this.getTotalHoras()
        },

        guardarHoras() {
            let fecha    = moment(this.startDate, 'dddd, LL', 'es').format('YYYY-MM-DD')
            let formData = new FormData()

            formData.append('fecha', fecha)
            formData.append('timesheet', JSON.stringify(this.items))

            this.$http.post('/usuarios/timesheet', formData, {
                headers: { 'Content-Type': 'application/json' },
            }).then(response => {
                let icon              = eq(response.data.code, 1) ? 'success' : 'error'
                let text              = response.data.message
                let siguienteFecha    = response.data.siguienteFecha
                this.disableTimesheet = response.data.isToday

                this.$swal({ text, icon }).then(response => {
                    if (response.value) {
                        this.totalHoras = 0
                        this.items      = []
                        this.startDate  = moment(siguienteFecha, 'YYYY-MM-DD', 'es').format('dddd, LL')
                    }
                })
            })
        },

        fetchProyecto() {
            if (isEmpty(this.proyecto)) {
                return
            }

            if (eq(this.proyecto.id, 0)) {
                this.showExtraOptions = true
            } else {
                this.showExtraOptions = false
                this.cliente          = this.proyecto.cliente
            }
        },

        clearForm() {
            this.cliente          = []
            this.proyecto         = []
            this.total            = 0
            this.showExtraOptions = false
            this.tipo             = ''

            this.$refs.formTimesheet.reset()
        },
    },
})
