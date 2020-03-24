import Vue from 'vue'
import { mapActions } from 'vuex'
import { eq, toNumber } from 'lodash'

Vue.component('FormGasto', {
    props: {
        proveedores: { type: [Array, Object], required: true },
        tipoGastos : { type: [Array, Object], required: true },
        tipoPagos  : { type: [Array, Object], required: true },
        usuarios   : { type: [Array, Object], required: true },
        proyectos  : { type: [Array, Object], required: true },
        gasto      : { type: [Array, Object], required: true },
        isModal    : { type: Boolean, required: true },
    },
    data() {
        return {
            idsFiles: [],
            importe: 0,
            dropzoneOptions:  {
                url: '/multimedias/upload',
                thumbnailWidth: 150,
            },
            locale: {
                labelTodayButton: 'Hoy',
                labelNowButton: 'Ahora',
                labelCloseButton: 'Cerrar',
                labelNoTimeSelected: 'No has seleccionado la hora',
                labelNoDateSelected: 'No has seleccionado la fecha',
                labelToday: 'Hoy',
                labelPrevYear: 'Año anterior',
                labelPrevMonth: 'Mes anterior',
                labelCurrentMonth: 'Mes actual',
                labelNextMonth: 'Siguiente mes',
                labelNextYear: 'Siguiente año',
                labelHours: 'Horas',
                labelMinutes: 'Minutos',
                labelSeconds: 'Segundos',
                labelIncrement: 'Incrementar',
                labelDecrement: 'Disminuir',
            },
        }
    },
    methods: {
        ...mapActions({
            agregar: 'Gasto/agregar',
            editar : 'Gasto/editar',
        }),

        clearForm() {
            this.idsFiles = []
            this.$refs.dropzoneGastos.removeAllFiles()
            this.$refs.formGasto.reset()
        },

        handleSubmit(method) {
            this.$refs.formGasto.validate().then(isValid => {
                if (isValid) {
                    this.gasto.multimedia_ids = this.idsFiles
                    this.gasto.importe        = toNumber(this.importe)

                    switch (method) {
                        case 'add':
                            this.agregar(this.gasto)
                            break;

                        case 'edit':
                            this.editar(this.gasto)
                            this.$emit('close-modal', true)
                            break;
                    }

                    this.clearForm()
                }
            })
        },

        onSendingDropzone(file, xhr, formData) {
            formData.append('tipo', 'factura')
        },

        onSuccessDropzone(file, response) {
            if (eq(response.code, 1)) {
                this.idsFiles.push(response.idMultimedia)
            }
        },
    },
    template: `
        <validation-observer
            tag="form"
            class="form-horizontal form-bordered"
            v-slot="{ invalid, valid }"
            ref="formGasto"
        >
            <!-- proyecto -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Proyecto"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Proyecto <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <v-select
                        placeholder="Selecciona..."
                        v-model="gasto.proyecto_id"
                        label="nombre"
                        :options="proyectos"
                        :clearable="false"
                        :reduce="option => option.id"
                        :select-on-tab="true"
                    >
                        <template slot="no-options">No se han encontrado opciones.</template>
                    </v-select>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- proveedor -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Proveedor"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Proveedor <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <div class="input-group">
                        <v-select
                            placeholder="Selecciona..."
                            v-model="gasto.proveedor_id"
                            label="nombre"
                            :options="proveedores"
                            :clearable="false"
                            :reduce="option => option.id"
                            :select-on-tab="true"
                        >
                            <template slot="no-options">No se han encontrado opciones.</template>
                        </v-select>
                        <b-input-group-append v-if="!isModal">
                            <b-button
                                variant="outline-danger"
                                v-b-tooltip.hover
                                title="Nuevo Proveedor"
                                @click="$emit('add-proveedor', true)"
                            >
                                <i class="fas fa-plus"></i>
                            </b-button>
                        </b-input-group-append>
                        <div class="invalid-feedback">{{ errors[0] }}</div>
                    </div>
                </b-col>
            </validation-provider>

            <!-- tipo gasto -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Tipo de Gasto"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Tipo de gasto <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <div class="input-group">
                        <v-select
                            placeholder="Selecciona..."
                            v-model="gasto.tipo_gasto_id"
                            label="nombre"
                            :options="tipoGastos"
                            :clearable="false"
                            :reduce="option => option.id"
                            :select-on-tab="true"
                        >
                            <template slot="no-options">No se han encontrado opciones.</template>
                        </v-select>
                        <div class="invalid-feedback">{{ errors[0] }}</div>
                    </div>
                </b-col>
            </validation-provider>

            <!-- importe total -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                name="Importe Total"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">Importe total</label>
                <b-col sm="8">
                    <div class="input-group">
                        <b-input-group-prepend is-text><b>$</b></b-input-group-prepend>
                        <b-form-input type="number" step=".01" v-model="importe" :class="classes"></b-form-input>
                        <div class="invalid-feedback">{{ errors[0] }}</div>
                    </div>
                </b-col>
            </validation-provider>

            <!-- tipo pago -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Tipo de Pago"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Tipo de pago <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <v-select
                        placeholder="Selecciona..."
                        v-model="gasto.tipo_pago_id"
                        label="nombre"
                        :options="tipoPagos"
                        :clearable="false"
                        :reduce="option => option.id"
                        :select-on-tab="true"
                    ></v-select>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- descripción -->
            <validation-provider tag="div" class="form-group row" v-slot="{ errors, classes }">
                <label class="col-sm-4 control-label text-sm-right pt-2">Descripción</label>
                <div class="col-sm-8">
                    <b-form-textarea v-model="gasto.descripcion" rows="4" :class="classes"></b-form-textarea>
                </div>
            </validation-provider>

            <!-- usuario -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Usuario"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Usuario <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <v-select
                        placeholder="Selecciona..."
                        v-model="gasto.recurso_id"
                        label="nombre_completo"
                        :options="usuarios"
                        :clearable="false"
                        :reduce="option => option.id"
                        :select-on-tab="true"
                    >
                        <template slot="no-options">No se han encontrado opciones.</template>
                    </v-select>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- fecha -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Fecha"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Fecha <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <div class="input-group">
                        <b-form-datepicker
                            close-button
                            today-button
                            local="es"
                            placeholder="Selecciona la fecha"
                            class="mb-2"
                            close-button-variant="outline-dark"
                            v-model="gasto.fecha"
                            v-bind="locale"
                            :required="true"
                            :state="classes['is-valid']"
                            :class="classes"
                        ></b-form-datepicker>
                        <div class="invalid-feedback">{{ errors[0] }}</div>
                    </div>
                </b-col>
            </validation-provider>

            <vue-dropzone
                ref="dropzoneGastos"
                id="dropzone"
                :options="dropzoneOptions"
                :use-custom-slot="true"
                @vdropzone-sending="onSendingDropzone"
                @vdropzone-success="onSuccessDropzone"
            >
                <div class="dropzone-custom-content">
                    <h4 class="dropzone-custom-title">Arrastra los archivos aquí para subirlos</h4>
                    <div class="subtitle">...o da clic para seleccionarlos</div>
                </div>
            </vue-dropzone>
        </validation-observer>
    `,
})