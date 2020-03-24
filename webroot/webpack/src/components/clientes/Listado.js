import Vue from 'vue'
import { has, isEmpty } from 'lodash'

Vue.component('ListadoCliente', {
    props: {
        clientes: { type: Array, required: true },
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
                    text: 'Editar',
                    tip: 'Editar cliente',
                    handler: this.editar,
                }, '|', 
                {
                    type: 'button',
                    text: 'Eliminar',
                    tip: 'Eliminar cliente',
                    handler: this.eliminar,
                },
            ],
            columns: [
                { index: 'id', title: 'ID', width: 45 },
                { index: 'razon_social', title: 'Razón Social', flex: 1 },
                { index: 'nombre', title: 'Nombre Comercial', width: 150 },
                { index: 'rfc', title: 'RFC', cellAlign: 'center', width: 110 },
                { index: 'direccion', title: 'Dirección', flex: 2 },
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
                    text: '¿Deseas eliminar el cliente?',
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
                :data="clientes"
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
