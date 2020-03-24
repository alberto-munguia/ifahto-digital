import Vue from 'vue'
import { mapActions } from 'vuex'
import { eq, toNumber, isEmpty } from 'lodash'

Vue.component('FormFacturacion', {
    props: {
        facturacion      : { type: [Array, Object], required: true },
        proyectos        : { type: [Array, Object], required: true },
        tipoFacturaciones: { type: [Array, Object], required: true },
    },
    data() {
        return {
            idsFiles: [],
            tipoMoneda: ['MXN', 'USD'],
            estatus: ['Pagado', 'Solicitada', 'Facturada', 'Espera de ODC', 'Cancelada'],
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
            agregar: 'Facturacion/agregar',
            editar : 'Facturacion/editar',
        }),

        clearForm() {
            this.idsFiles = []
            this.importe  = 0
            this.$refs.dropzoneFacturaciones.removeAllFiles()
            this.$refs.formFacturacion.reset()
        },

        handleSubmit(method) {
            this.$refs.formFacturacion.validate().then(isValid => {
                if (isValid) {
                    this.facturacion.multimedia_ids = this.idsFiles

                    switch (method) {
                        case 'add':
                            this.agregar(this.facturacion)
                            break;

                        case 'edit':
                            this.editar(this.facturacion)
                            this.$emit('close-modal', true)
                            break;
                    }

                    this.clearForm()
                }
            })
        },

        calculateIva() {
            let subtotal           = toNumber(this.facturacion.importe)
            this.facturacion.iva   = subtotal * .16
            this.facturacion.total = this.facturacion.iva + subtotal
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
            ref="formFacturacion"
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
                        v-model="facturacion.proyecto_id"
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

            <!-- tipo facturación -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Tipo de Facturación"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Tipo de facturación <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <v-select
                        placeholder="Selecciona..."
                        v-model="facturacion.tipo_facturacion_id"
                        label="nombre"
                        :options="tipoFacturaciones"
                        :clearable="false"
                        :reduce="option => option.id"
                        :select-on-tab="true"
                    >
                        <template slot="no-options">No se han encontrado opciones.</template>
                    </v-select>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- número de factura -->
            <validation-provider tag="div" class="form-group row" v-slot="{ classes }" >
                <label class="col-sm-4 control-label text-sm-right pt-2">Número de factura</label>
                <b-col sm="8">
                    <b-form-input v-model="facturacion.numero_factura" :class="classes"></b-form-input>
                </b-col>
            </validation-provider>

            <!-- orden de compra -->
            <validation-provider tag="div" class="form-group row" v-slot="{ classes }">
                <label class="col-sm-4 control-label text-sm-right pt-2">Orden de compra</label>
                <b-col sm="8">
                    <b-form-input v-model="facturacion.orden_compra" :class="classes"></b-form-input>
                </b-col>
            </validation-provider>

            <!-- número de proyecto -->
            <validation-provider tag="div" class="form-group row" v-slot="{ classes }">
                <label class="col-sm-4 control-label text-sm-right pt-2">Número de proyecto</label>
                <b-col sm="8">
                    <b-form-input v-model="facturacion.numero_proyecto_cliente" :class="classes"></b-form-input>
                    <span class="hel-block">Número de proyecto del cliente</span>
                </b-col>
            </validation-provider>

            <!-- descripción -->
            <validation-provider tag="div" class="form-group row" v-slot="{ classes }">
                <label class="col-sm-4 control-label text-sm-right pt-2">Descripción</label>
                <div class="col-sm-8">
                    <b-form-textarea v-model="facturacion.descripcion" rows="4" :class="classes"></b-form-textarea>
                </div>
            </validation-provider>

            <!-- fecha expedición -->
            <validation-provider tag="div" class="form-group row" v-slot="{ classes }">
                <label class="col-sm-4 control-label text-sm-right pt-2">Fecha de expedición</label>
                <b-col sm="8">
                    <div class="input-group">
                        <b-form-datepicker
                            close-button
                            today-button
                            local="es"
                            placeholder="Selecciona la fecha"
                            class="mb-2"
                            close-button-variant="outline-dark"
                            v-model="facturacion.fecha_expedicion"
                            v-bind="locale"
                            :required="true"
                            :state="classes['is-valid']"
                            :class="classes"
                        ></b-form-datepicker>
                    </div>
                </b-col>
            </validation-provider>

            <!-- fecha estimada pago -->
            <validation-provider tag="div" class="form-group row" v-slot="{ classes }">
                <label class="col-sm-4 control-label text-sm-right pt-2">Fecha estimada de pago</label>
                <b-col sm="8">
                    <div class="input-group">
                        <b-form-datepicker
                            close-button
                            today-button
                            local="es"
                            placeholder="Selecciona la fecha"
                            class="mb-2"
                            close-button-variant="outline-dark"
                            v-model="facturacion.fecha_estimada_pago"
                            v-bind="locale"
                            :required="true"
                            :state="classes['is-valid']"
                            :class="classes"
                        ></b-form-datepicker>
                    </div>
                </b-col>
            </validation-provider>

            <!-- fecha pago -->
            <validation-provider tag="div" class="form-group row" v-slot="{ classes }">
                <label class="col-sm-4 control-label text-sm-right pt-2">Fecha de pago</label>
                <b-col sm="8">
                    <div class="input-group">
                        <b-form-datepicker
                            close-button
                            today-button
                            local="es"
                            placeholder="Selecciona la fecha"
                            class="mb-2"
                            close-button-variant="outline-dark"
                            v-model="facturacion.fecha_pago"
                            v-bind="locale"
                            :required="true"
                            :state="classes['is-valid']"
                            :class="classes"
                        ></b-form-datepicker>
                    </div>
                </b-col>
            </validation-provider>

            <!-- moneda -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Moneda"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Moneda <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <v-select
                        placeholder="Selecciona..."
                        v-model="facturacion.moneda"
                        :options="tipoMoneda"
                        :clearable="false"
                        :select-on-tab="true"
                    ></v-select>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- importe -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                name="Importe"
                rules="required"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Importe <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <div class="input-group">
                        <b-input-group-prepend is-text><b>$</b></b-input-group-prepend>
                        <b-form-input
                            type="number"
                            v-model="facturacion.importe"
                            :class="classes"
                            @change="calculateIva"
                        ></b-form-input>
                        <div class="invalid-feedback">{{ errors[0] }}</div>
                    </div>
                </b-col>
            </validation-provider>

            <!-- iva -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                name="I.V.A."
                rules="required"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    I.V.A. <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <div class="input-group">
                        <b-input-group-prepend is-text><b>$</b></b-input-group-prepend>
                        <b-form-input type="number" v-model="facturacion.iva" :class="classes"></b-form-input>
                        <div class="invalid-feedback">{{ errors[0] }}</div>
                    </div>
                </b-col>
            </validation-provider>

            <!-- total -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                name="Total"
                rules="required"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Total <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <div class="input-group">
                        <b-input-group-prepend is-text><b>$</b></b-input-group-prepend>
                        <b-form-input type="number" v-model="facturacion.total" :state="classes['is-valid']" :class="classes"></b-form-input>
                        <div class="invalid-feedback">{{ errors[0] }}</div>
                    </div>
                </b-col>
            </validation-provider>

            <vue-dropzone
                ref="dropzoneFacturaciones"
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