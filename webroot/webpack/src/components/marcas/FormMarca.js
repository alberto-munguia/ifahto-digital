import Vue from 'vue'
import { mapActions } from 'vuex'

Vue.component('FormMarca', {
    props: {
        clientes: { type: [Array, Object], required: true },
        marca   : { type: [Array, Object], required: true },
    },
    data() {
        return {
        }
    },
    methods: {
        ...mapActions({
            agregar: 'Marca/agregar',
            editar : 'Marca/editar',
        }),

        clearForm() {
            this.$refs.formMarca.reset()
        },

        handleSubmit(method) {
            this.$refs.formMarca.validate().then(isValid => {
                if (isValid) {
                    switch (method) {
                        case 'add':
                            this.agregar(this.marca)
                            break;

                        case 'edit':
                            this.editar(this.marca)
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
            ref="formMarca"
        >
            <!-- cliente -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Cliente"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Cliente <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <v-select
                        placeholder="Selecciona..."
                        v-model="marca.cliente_id"
                        label="razon_social"
                        :options="clientes"
                        :clearable="false"
                        :reduce="option => option.id"
                        :select-on-tab="true"
                    >
                        <template slot="no-options">No se han encontrado opciones.</template>
                    </v-select>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

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
                    <b-form-input v-model="marca.nombre" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>
        </validation-observer>
    `,
})