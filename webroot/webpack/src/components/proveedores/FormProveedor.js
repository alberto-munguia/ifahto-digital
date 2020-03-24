import Vue from 'vue'
import { mapActions } from 'vuex'

Vue.component('FormProveedor', {
    props: {
        proveedor: { type: [Array, Object], required: true },
    },
    data() {
        return {
        }
    },
    methods: {
        ...mapActions({
            agregar: 'Proveedor/agregar',
            editar : 'Proveedor/editar',
        }),

        clearForm() {
            this.$refs.formProveedor.reset()
        },

        handleSubmit(method) {
            this.$refs.formProveedor.validate().then(isValid => {
                if (isValid) {
                    switch (method) {
                        case 'add':
                            this.agregar(this.proveedor)
                            break;

                        case 'edit':
                            this.editar(this.proveedor)
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
            ref="formProveedor"
            @submit.prevent="nuevoProveedor"
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
                    <b-form-input v-model="proveedor.razon_social" :class="classes"></b-form-input>
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
                    <b-form-input v-model="proveedor.nombre" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- tipo -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Tipo"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Tipo <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <b-form-input v-model="proveedor.tipo" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>
        </validation-observer>
    `,
})
