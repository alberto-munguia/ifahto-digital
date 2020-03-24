import Vue from 'vue'
import { has, isEmpty } from 'lodash'

Vue.component('ListadoGasto', {
    props: {
        gastos: { type: Array, required: true },
    },
    component: {
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
                    tip: 'Editar gasto',
                    handler: this.editar,
                }, '|',
                {
                    type: 'button',
                    text: 'Eliminar',
                    tip: 'Eliminar gasto',
                    handler: this.eliminar,
                }, '|',
                {
                    type: 'button',
                    text: 'Ver Archivos',
                    tip: 'Ver archivos relacionados',
                    handler: this.verArchivos,
                },
            ],
            columns: [
                { index: 'id', title: 'ID', width: 45 },
                { index: 'proyecto.clave', title: 'Clave de Proyecto', width: 110 },
                { index: 'proveedor.nombre', title: 'Proveedor' },
                { index: 'tipo_gasto', title: 'Tipo de Gasto', cellAlign: 'center' },
                { index: 'descripcion', title: 'Descripción', flex: 2 },
                { index: 'recurso.nombre_completo', title: 'Usuario', flex: 1 },
                { index: 'fecha', title: 'Fecha', cellAlign: 'center', width: 80 },
                { index: 'importe', title: 'Importe', cellAlign: 'right', width: 90 },
                { index: 'tipo_pago', title: 'Tipo de Pago', cellAlign: 'center', width: 100 },
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

        getEstatusColor(object) {
            switch (object.value) {
                case 'Pagado':
                    object.style = {
                        color: '#FFF',
                        'background-color': '#0A7D14',
                    };
                    break;

                case 'Solicitada':
                    object.style = {
                        color: '#FFF',
                        'background-color': '#B31B00',
                    };
                    break;

                case 'Facturada':
                    object.style = {
                        color: '#FFF',
                        'background-color': '#FF950E',
                    };
                    break;

                default:
                    object.style = {
                        color: '#FFF',
                        'background-color': '#283438',
                    };
                    break;
            }

            return object;
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
                    text: '¿Deseas eliminar el gasto?',
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
    },
    template: `
        <b-col>
            <fancy-grid-vue
                :defaults="defaults"
                :theme="theme"
                :height="height"
                :data="gastos"
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
