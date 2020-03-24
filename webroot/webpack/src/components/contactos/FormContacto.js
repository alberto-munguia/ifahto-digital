import Vue from 'vue'
import { mapActions } from 'vuex'

Vue.component('FormContacto', {
    props: {
        contacto: { type: [Array, Object], required: true },
    },
    data() {
        return {
        }
    },
    methods: {
        ...mapActions({
            agregar: 'Contacto/agregar',
            editar : 'Contacto/editar',
        }),

        clearForm() {
            this.$refs.formContacto.reset()
        },

        handleSubmit(method) {
            this.$refs.formContacto.validate().then(isValid => {
                if (isValid) {
                    switch (method) {
                        case 'add':
                            this.agregar(this.contacto)
                            break;

                        case 'edit':
                            this.editar(this.contacto)
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
            ref="formContacto"
        >
            <!-- nombre -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Nombre"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Nombre <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <b-form-input v-model="contacto.nombre" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- apellido paterno -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Apellido Paterno"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Apellido paterno <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <b-form-input v-model="contacto.apellido_paterno" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- apellido materno -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                name="Apellido Materno"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">Apellido materno</label>
                <b-col sm="8">
                    <b-form-input v-model="contacto.apellido_materno" :class="classes"></b-form-input>
                </b-col>
            </validation-provider>

            <!-- email -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required|email"
                name="Correo Electrónico"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Correo electrónico <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <b-form-input type="email" v-model="contacto.email" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- teléfono -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="numeric"
                name="Teléfono"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">Teléfono</label>
                <b-col sm="8">
                    <b-form-input maxlength="10" v-model="contacto.telefono" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- extensión -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="numeric"
                name="Extensión"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">Extensión</label>
                <b-col sm="8">
                    <b-form-input v-model="contacto.extension" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>
        </validation-observer>
    `,
})