import Vue from 'vue'
import { mapState, mapActions } from 'vuex'
import { has, isEmpty } from 'lodash'

Vue.component('ListadoUsuario', {
    props: {
        usuarios: { type: Array, required: true },
    },
    data() {
        return {
            theme    : 'bootstrap',
            selection: 'row',
            height   : 500,
            paging   : {
                pageSizeData: [20, 50, 100],
                pageSize    : 30,
            },
            defaults: {
                sortable : true,
                resizable: true,
                align: 'center',
            },
            tbar: [
                { type: 'search', width: 332, emptyText: 'Buscar', paramsMenu: true, paramsText: 'Parámetros' },
            ],
            buttons: [
                {
                    type: 'button',
                    text: 'Editar',
                    tip: 'Editar usuario',
                    handler: this.editar,
                }, '|', 
                {
                    type: 'button',
                    text: 'Eliminar',
                    tip: 'Eliminar usuario',
                    handler: this.eliminar,
                },
                // {
                //     type: 'button',
                //     text: 'Exportar Costos Usuario',
                //     tip: 'Exportar costos de cada usuario',
                //     handler: () => {
                //         window.open(`/reportes/proyectos-horas/${ localStorage.getItem('selected') }`, '_blank')
                //     },
                // },
            ],
            columns: [
                { index: 'id', title: 'ID', width: 45 },
                { index: 'nombre_completo', title: 'Nombre', flex: 2 },
                { index: 'email', title: 'Correo Electrónico', flex: 1 },
                { index: 'puesto', title: 'Puesto', width: 200 },
                { index: 'perfil', title: 'Perfil', cellAlign: 'center' },
                { index: 'costo', title: 'Costo Hora', cellAlign: 'rigth' },
                { index: 'fecha_inicio', title: 'Fecha Inicio de Actividades', cellAlign: 'center', width: 160 },
            ],
        }
    },
    computed: {
        ...mapState('Usuario', ['usuario'])
    },
    methods: {
        ...mapActions({
            'getUsuario': 'Usuario/fetchUser',
        }),

        onInit() {
            localStorage.removeItem('selected')
            document.querySelector('a[href="http://www.fancygrid.com"]').remove()
        },

        onSelect(grid, selection) {
            if (!has(selection, 0)) {
                return
            }

            localStorage.setItem('selected', selection[0].id)
        },

        editar(grid) {
            let id = localStorage.getItem('selected')
            
            if (!isEmpty(id)) {
                this.$emit('editar', id)
            }
        },

        eliminar(grid) {
            let id = localStorage.getItem('selected')

            if (!isEmpty(id)) {
                this.$swal({
                    text: '¿Deseas eliminar el usuario?',
                    icon: 'warning',
                    showCancelButton: true,
                }).then(response => {
                    if (response.value) {
                        this.$emit('eliminar', id)
                        localStorage.removeItem('selected')
                    }
                })
            }
        },
    },
    template: `
        <b-col>
            <fancy-grid-vue
                :defaults="defaults"
                :theme="theme"
                :height="height"
                :data="usuarios"
                :resizable="true"
                :columns="columns"
                :sel-model="selection"
                :paging="paging"
                :tbar="tbar"
                :subTBar="buttons"
                :i18n="'es'"
                @select="onSelect"
                @init="onInit"
            >
            </fancy-grid-vue>
        </b-col>
    `,
})
