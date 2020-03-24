import Vue from 'vue'
import es from 'vee-validate/dist/locale/es.json'
import * as rules from 'vee-validate/dist/rules'
import { localize } from 'vee-validate'
import { ValidationProvider, ValidationObserver, extend, configure } from 'vee-validate'

localize({ es })
configure({
    classes: {
        invalid : 'is-invalid',
        valid   : 'is-valid',
        required: 'is-required',
    }
})

for (const rule in rules) {
    extend(rule, {
        ...rules[rule],
        message: es.messages[rule]
    })
}

Vue.component('validation-provider', ValidationProvider)
Vue.component('validation-observer', ValidationObserver)