import Vue from 'vue'
import { has, isEmpty } from 'lodash'

Vue.component('ListadoFacturacion', {
    props: {
        facturaciones: { type: Array, required: true },
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
                    tip: 'Editar facturación',
                    handler: this.editar,
                }, '|',
                {
                    type: 'button',
                    text: 'Eliminar',
                    tip: 'Eliminar facturación',
                    handler: this.eliminar,
                }, '|',
                {
                    type: 'button',
                    text: 'Estatus',
                    tip: 'Cambiar el estatus de la facturación',
                    handler: this.cambiarEstatus,
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
                { index: 'numero_factura', title: 'Número de Factura', cellAlign: 'center', width: 115 },
                { index: 'proyecto.clave', title: 'Clave de Proyecto', width: 110 },
                { index: 'proyecto.nombre', title: 'Proyecto', width: 150 },
                { index: 'orden_compra', title: 'Orden de Compra', cellAlign: 'center', width: 110 },
                { index: 'numero_proyecto_cliente', title: 'No. de Proyecto Cliente', cellAlign: 'center', width: 150 },
                { index: 'fecha_expedicion', title: 'Fecha de Expedición', cellAlign: 'center', width: 130 },
                { index: 'fecha_estimada_pago', title: 'Fecha Estimada de Pago', cellAlign: 'center', width: 150 },
                { index: 'estatus', title: 'Estatus', cellAlign: 'center', width: 80, render: this.getEstatusColor },
                { index: 'fecha_pago', title: 'Fecha de Pago', cellAlign: 'center', width: 100 },
                { index: 'moneda', title: 'Moneda', cellAlign: 'center', width: 60 },
                { index: 'importe', title: 'Importe', cellAlign: 'right', width: 90 },
                { index: 'iva', title: 'I.V.A.', cellAlign: 'right', width: 90 },
                { index: 'total', title: 'Total', cellAlign: 'right', width: 90 },
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
                    text: '¿Deseas eliminar la facturación?',
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

        cambiarEstatus(grid) {
            let id = localStorage.getItem('selected')
            
            if (!isEmpty(id)) {
                this.$emit('cambiar-estatus', id)
            }
        },
    },
    template: `
        <b-col>
            <fancy-grid-vue
                :defaults="defaults"
                :theme="theme"
                :height="height"
                :data="facturaciones"
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
