import Vue from 'vue'
import Vuex from 'vuex'

import Ciudad from './modules/ciudades'
import Cliente from './modules/clientes'
import Contacto from './modules/contactos'
import EntidadFederativa from './modules/entidades_federativas'
import Facturacion from './modules/facturaciones'
import Gasto from './modules/gastos'
import Licencia from './modules/licencias'
import Marca from './modules/marcas'
import Perfil from './modules/perfiles'
import Puesto from './modules/puestos'
import Proveedor from './modules/proveedores'
import Proyecto from './modules/proyectos'
import Usuario from './modules/usuarios'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
    },
    modules: {
        Ciudad,
        Cliente,
        Contacto,
        EntidadFederativa,
        Facturacion,
        Gasto,
        Licencia,
        Marca,
        Perfil,
        Puesto,
        Proveedor,
        Proyecto,
        Usuario,
    },
})