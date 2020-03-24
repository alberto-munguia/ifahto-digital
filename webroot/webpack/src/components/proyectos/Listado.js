import Vue from 'vue'
import { has, isEmpty } from 'lodash'

Vue.component('ListadoProyecto', {
    props: {
        proyectos: { type: Array, required: true },
    },
    component: {
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
                align    : 'center',
            },
            tbar: [
                { type: 'search', width: 332, emptyText: 'Buscar', paramsMenu: true, paramsText: 'Parámetros' },
            ],
            buttons: [
                {
                    type: 'button',
                    text: 'Detalle',
                    tip: 'Ver detalles del proyecto',
                    handler: () => location.replace(`/proyectos/view/${ localStorage.getItem('selected') }`),
                }, '|', 
                // {
                //     type: 'button',
                //     text: 'Editar',
                //     tip: 'Editar el proyecto',
                //     handler: () => location.replace(`/proyectos/edit/${ localStorage.getItem('selected') }`),
                // }, '|', 
                {
                    type: 'button',
                    text: 'Estatus',
                    tip: 'Cambiar el estatus del proyecto',
                    handler: this.cambiarEstatus,
                }, '|', 
                {
                    type: 'button',
                    text: 'Exportar Proyectos',
                    tip: 'Exportar todos los proyectos',
                    handler: () => window.open('/reportes/proyectos', '_blank'),
                }, '|', 
                {
                    type: 'button',
                    text: 'Exportar Horas Laboradas',
                    tip: 'Exportar relación horas laboradas - cotizadas',
                    handler: this.exportarHoras,
                }
            ],
            columns: [
                { index: 'id', title: 'ID', width: 45 },
                { index: 'clave', title: 'Clave',  },
                { index: 'nombre', title: 'Nombre', flex: 2 },
                { index: 'cliente.nombre', title: 'Cliente' },
                { index: 'marca.nombre', title: 'Marca' },
                { index: 'fecha_registro', title: 'Fecha Registro', cellAlign: 'center' },
                { index: 'usuario.nombre_completo', title: 'Usuario', flex: 1 },
                { index: 'estatus', title: 'Estatus', cellAlign: 'center' },
            ],
        }
    },
    methods: {
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

        cambiarEstatus(grid) {
            let id = localStorage.getItem('selected')
            
            if (!isEmpty(id)) {
                this.$emit('cambiar-estatus', id)
            }
        },

        exportarHoras(grid) {
            let id = localStorage.getItem('selected')

            if (!isEmpty(id)) {
                window.open(`/reportes/timesheet-proyectos/${ id }`, '_blank')
            }
        },
    },
    template: `
        <b-col>
            <fancy-grid-vue
                :defaults="defaults"
                :theme="theme"
                :height="height"
                :data="proyectos"
                :resizable="true"
                :columns="columns"
                :sel-model="selection"
                :paging="paging"
                :tbar="tbar"
                :subTBar="buttons"
                @select="onSelect"
                @init="onInit"
            >
            </fancy-grid-vue>
        </b-col>
    `,
})
