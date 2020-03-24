import Vue from 'vue'
import { has, isEmpty } from 'lodash'

Vue.component('ListadoLicencia', {
    props: {
        licencias: { type: Array, required: true },
    },
    data() {
        return {
            theme: 'bootstrap',
            selection: 'row',
            height: 500,
            paging: {
                pageSizeData: [20, 50, 100],
                pageSize: 30,
            },
            defaults: {
                sortable: true,
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
                    tip: 'Editar licencia',
                    handler: this.editar,
                }, '|',
                {
                    type: 'button',
                    text: 'Eliminar',
                    tip: 'Eliminar licencia',
                    handler: this.eliminar,
                }, '|',
                {
                    type: 'button',
                    text: 'Ver Archivos',
                    tip: 'Ver archivos relacionados',
                    handler: this.verArchivos,
                }, '|',
                {
                    type: 'button',
                    text: 'Asociar Proyectos',
                    tip: 'Asociar proyectos a la licencia',
                    handler: this.asociarProyectos,
                },
            ],
            columns: [
                { index: 'id', title: 'ID', width: 45 },
                { index: 'nombre', title: 'Nombre de la Licencia', width: 140 },
                { index: 'descripcion', title: 'Descripción', flex: 2 },
                { index: 'proveedor.nombre', title: 'Proveedor' },
                { index: 'importe', title: 'Importe', cellAlign: 'right', width: 90 },
                { index: 'tipo_pago', title: 'Tipo de Pago', cellAlign: 'center', width: 100 },
                { index: 'fecha', title: 'Fecha', cellAlign: 'center', width: 80 },
                { index: 'usuario.nombre_completo', title: 'Usuario', flex: 1 },
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
                    text: '¿Deseas eliminar la licencia?',
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

        verArchivos(grid) {
            let id = localStorage.getItem('selected')

            if (!isEmpty(id)) {
                this.$emit('ver-archivos', id)
            }
        },

        asociarProyectos(grid) {
            let id = localStorage.getItem('selected')

            if (!isEmpty(id)) {
                this.$emit('asociar-proyectos', id)
            }
        },
    },
    template: `
        <b-col>
            <fancy-grid-vue
                :defaults="defaults"
                :theme="theme"
                :height="height"
                :data="licencias"
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
