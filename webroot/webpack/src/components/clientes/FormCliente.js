import Vue from 'vue'
import { mapActions } from 'vuex'

Vue.component('FormCliente', {
    props: {
        cliente: { type: [Array, Object], required: true },
    },
    data() {
        return {
        }
    },
    methods: {
        ...mapActions({
            agregar: 'Cliente/agregar',
            editar : 'Cliente/editar',
        }),

        clearForm() {
            this.$refs.formCliente.reset()
        },

        handleSubmit(method) {
            this.$refs.formCliente.validate().then(isValid => {
                if (isValid) {
                    switch (method) {
                        case 'add':
                            this.agregar(this.cliente)
                            break;

                        case 'edit':
                            this.editar(this.cliente)
                            this.$emit('close-modal', true)
                            break;
                    }

                    this.clearForm()
                }
            })
        },
    },
    template: `
        <validation-observer
            tag="form"
            class="form-horizontal form-bordered"
            v-slot="{ invalid, valid }"
            ref="formCliente"
        >
            <!-- razón social -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Razón Social"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Razón social <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <b-form-input v-model="cliente.razon_social" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- nombre comercial -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Nombre Comercial"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Nombre comercial <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <b-form-input v-model="cliente.nombre" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- rfc -->
            <validation-provider tag="div" class="form-group row" v-slot="{ classes }" >
                <label class="col-sm-4 control-label text-sm-right pt-2">R.F.C.</label>
                <b-col sm="8">
                    <b-form-input v-model="cliente.rfc" :class="classes"></b-form-input>
                </b-col>
            </validation-provider>

            <!-- dirección -->
            <validation-provider tag="div" class="form-group row" v-slot="{ classes }">
                <label class="col-sm-4 control-label text-sm-right pt-2">Dirección</label>
                <b-col sm="8">
                    <b-form-input v-model="cliente.direccion" :class="classes"></b-form-input>
                </b-col>
            </validation-provider>
        </validation-observer>
    `,
})