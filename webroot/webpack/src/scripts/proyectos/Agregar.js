import Vue from 'vue'
import store from '../../store'
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex'
import { eq, isEmpty } from 'lodash'

import FormCliente from '../../components/clientes/FormCliente'
import FormMarca from '../../components/marcas/FormMarca'
import FormContacto from '../../components/contactos/FormContacto'

import '../../main'
import '../../plugins/vue-dropzone'

new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    store,
    data() {
        const now     = new Date()
        const today   = new Date(now.getFullYear(), now.getMonth(), now.getDate())
        const minDate = new Date(today)

        return {
            formWait    : false,
            usuario     : [],
            responsable : [],
            facturacion : [],
            horaCotizada: 0,
            minDate     : minDate,
            idsFiles    : [],
            locale      : {
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
            dropzoneOptions: {
                url: '/multimedias/upload',
                thumbnailWidth: 150,
            },
            fieldsContacto: [
                { key: 'nombre_completo', label: 'Contacto' },
                { key: 'email', label: 'Correo Electrónico' },
                { key: 'telefono', label: 'Teléfono' },
                { key: 'extension', label: 'Extensión' },
            ],
            items : [],
            fields: [
                { key: 'nombre_completo', label: 'Recurso', class: 'align-middle' },
                { key: 'total_hora', label: 'Horas Cotizadas', class: 'align-middle' },
                'acciones'
            ],
        }
    },
    created() {
        this.$store.subscribe((mutation, state) => {
            switch (mutation.type) {
                case 'Cliente/isSaved':
                    this.closeModal('modalAddCliente')
                    break;

                case 'Contacto/isSaved':
                    this.closeModal('modalAddContacto')
                    break;

                case 'Marca/isSaved':
                    this.closeModal('modalAddMarca')
                    break;
            }
        })
    },
    beforeMount() {
        this.getTipoServicios()
        this.getEntidades()
        this.getUsuarios()
    },
    mounted() {
        this.getClientes()
        this.getPeriodicidadPagos()
        this.getTipoClientes()
        this.getContactos()
    },
    computed: {
        ...mapState('Ciudad', ['ciudades']),
        ...mapState('Cliente', ['cliente', 'clientes', 'tipoClientes']),
        ...mapState('Contacto', ['contacto', 'contactos']),
        ...mapState('EntidadFederativa', ['entidades']),
        ...mapState('Marca', ['marcas', 'marca']),
        ...mapState('Proyecto', ['proyecto', 'periodicidadPagos', 'tipoServicios']),
        ...mapState('Usuario', ['usuarios']),

        ...mapGetters({
            getUsuariosPM: 'Usuario/getUsuariosByPuesto',
            getContacto  : 'Contacto/getContacto',
        }),

        disableUserBtn() {
            return !isEmpty(this.usuario) && !isEmpty(this.horaCotizada) ? false : true
        },
    },
    methods: {
        ...mapMutations({
            setCliente            : 'Cliente/setCliente',
            setContacto           : 'Contacto/setContacto',
            setMarca              : 'Marca/setMarca',
            setContactoResponsable: 'Contacto/setContactoResponsable',
            setContactoFacturacion: 'Contacto/setContactoFacturacion',
            setProyecto           : 'Proyecto/setProyecto',
        }),

        ...mapActions({
            getCiudadesByEntidad: 'Ciudad/getByEntidad',
            getClientes         : 'Cliente/getAll',
            getTipoClientes     : 'Cliente/getTipoClientes',
            getContactos        : 'Contacto/getAll',
            getEntidades        : 'EntidadFederativa/getAll',
            getMarcasByCliente  : 'Marca/getAllByCliente',
            getUsuarios         : 'Usuario/getAll',
            getPeriodicidadPagos: 'Proyecto/getPeriodicidadPagos',
            getTipoServicios    : 'Proyecto/getTipoServicios',
            addProyecto         : 'Proyecto/agregar',
        }),

        showModal(element) {
            this.$refs[`${ element }`].show()
        },

        closeModal(element) {
            this.$refs[`${ element }`].hide()
        },

        nuevoCliente() {
            this.$refs['form-cliente'].handleSubmit('add')
        },

        nuevaMarca() {
            this.$refs['form-marca'].handleSubmit('add')
        },

        nuevoContacto() {
            this.$refs['form-contacto'].handleSubmit('add')
        },

        getContactoResponsable(id) {
            this.responsable = this.getContacto(id)
        },

        getContactoFacturacion(id) {
            this.facturacion = this.getContacto(id)
        },

        agregarHoras() {
            let horasCotizadas = this.items
            horasCotizadas.push({
                usuario_id     : this.usuario.id,
                nombre_completo: this.usuario.nombre_completo,
                total_hora     : this.horaCotizada,
            })

            this.items        = horasCotizadas
            this.usuario      = {}
            this.horaCotizada = 0
        },

        clearForm() {
            this.items       = []
            this.responsable = []
            this.facturacion = []

            this.setProyecto([])
            this.$refs['form-proyecto'].reset()
        },

        nuevoProyecto() {
            this.proyecto.multimedia_ids       = this.idsFiles
            this.proyecto.usuaios_relacionados = this.items

            this.addProyecto(this.proyecto)
        },

        onSendingDropzone(file, xhr, formData) {
            formData.append('tipo', 'factura')
        },

        onSuccessDropzone(file, response) {
            if (eq(response.code, 1)) {
                this.idsFiles.push(response.idMultimedia)
            }
        },
    },
})
