import Vue from 'vue'
import VueSweetalert2 from 'vue-sweetalert2'

import 'sweetalert2/dist/sweetalert2.min.css'

const options = {
    customClass: {
        confirmButton: 'btn btn-outline-success mr-1',
        cancelButton : 'btn btn-outline-danger mr-1'
    },
    allowEscapeKey   : false,
    allowOutsideClick: false,
    buttonsStyling   : false,
    confirmButtonText: 'Aceptar',
    cancelButtonText : 'Cancelar',
}

Vue.use(VueSweetalert2, options)
