import Vue from 'vue'
import store from '../../store'
import { eq } from 'lodash'

import '../../main'

new Vue({
    delimiters: ['${', '}'],
    el: '#app',
    store,
    data() {
        return {
            email        : '',
            password     : '',
            emailRecovery: '',
            forgotPwd    : false,
            formWait     : false,
            showMessage  : false,
            message      : '',
        }
    },
    beforeMount() {
        localStorage.clear()
    },
    watch: {
        forgotPwd(value) {
            if (eq(value, true)) {
                this.$refs.formLogin.reset()
            } else {
                this.showMessage   = false
                this.emailRecovery = ''
                this.$refs.formRecovery.reset()
            }
        },
    },
    methods: {
        login() {
            this.$refs.formLogin.validate().then(isValid => {
                if (isValid && !this.formWait) {
                    this.formWait = true
                    let formData  = new FormData()

                    formData.append('email', this.email)
                    formData.append('password', this.password)

                    try {
                        this.$http.post('/usuarios/login', formData, {
                            headers: { 'Content-Type': 'application/json' },
                        }).then(response => {
                            if (eq(response.data.code, 1)) {
                                localStorage.setItem('usuario_id', response.data.id)
                                location.replace('/')
                            } else {
                                this.message     = response.data.message
                                this.showMessage = true
                            }
                        })
                    } catch (error) {
                    }

                    this.formWait = false
                }
            })
        },

        passwordRecovery() {
            let formData = new FormData()

            formData.append('email', this.emailRecovery)

            this.$http.post('/usuarios/recovery', formData, {
                headers: { 'Content-Type': 'application/json' },
            }).then(response => {
                if (eq(response.data.code, 202)) {
                    this.showMessage = true
                }

                this.message = response.data.message
            })
        },
    },
})
