import Vue from 'vue'
import { mapActions } from 'vuex'

Vue.component('FormUsuarioEditar', {
    props: {
        usuario : { type: [Array, Object], required: true },
        puestos : { type: Array, required: true },
        perfiles: { type: Array, required: true },
    },
    data() {
        return {
        }
    },
    methods: {
        ...mapActions('Usuario', ['editar']),

        handleSubmit() {
            this.$refs.formEditarUsuario.validate().then(isValid => {
                if (isValid) {
                    this.editar(this.usuario)
                    this.$emit('close-modal', true)
                }
            })
        },
    },
    template: `
        <validation-observer
            tag="form"
            class="form-horizontal form-bordered"
            v-slot="{ invalid, valid }"
            ref="formEditarUsuario"
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
                    <b-form-input v-model="usuario.nombre" :class="classes"></b-form-input>
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
                    <b-form-input v-model="usuario.apellido_paterno" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- apellido materno -->
            <validation-provider tag="div" class="form-group row" v-slot="{ classes }" name="Apellido Materno">
                <label class="col-sm-4 control-label text-sm-right pt-2">Apellido materno</label>
                <b-col sm="8">
                    <b-form-input v-model="usuario.apellido_materno" :class="classes"></b-form-input>
                </b-col>
            </validation-provider>

            <!-- puesto -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Puesto"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Puesto <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <v-select
                        :options="puestos"
                        placeholder="Selecciona..."
                        :clearable="false"
                        v-model="usuario.puesto_id"
                        :reduce="option => option.id"
                        label="nombre"
                    ></v-select>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
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
                    <b-form-input type="email" v-model="usuario.email" :class="classes"></b-form-input>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>

            <!-- costo mes -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                :rules="{ regex:/^([0-9]{1}[0-9]{0,2})$|^([1-9]{1}[0-9]{0,2})(\d{3})*(\.\d{2})?$|^(\d)?(\.)(\d{2})?$/ }"
                name="Costo del Mes"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2"> Costo del mes</label>
                <b-col sm="8">
                    <div class="input-group">
                        <b-input-group-prepend is-text><b>$</b></b-input-group-prepend>
                        <b-form-input v-model="usuario.costo_mes" :class="classes"></b-form-input>
                        <div class="invalid-feedback">{{ errors[0] }}</div>
                    </div>
                </b-col>
            </validation-provider>

            <!-- perfil -->
            <validation-provider
                tag="div"
                class="form-group row"
                v-slot="{ errors, classes }"
                rules="required"
                name="Perfil"
            >
                <label class="col-sm-4 control-label text-sm-right pt-2">
                    Perfil <span class="required">*</span>
                </label>
                <b-col sm="8">
                    <v-select
                        placeholder="Selecciona..."
                        v-model="usuario.perfil_id"
                        label="nombre"
                        :options="perfiles"
                        :clearable="false"
                        :reduce="option => option.id"
                        :select-on-tab="true"
                    ></v-select>
                    <div class="invalid-feedback">{{ errors[0] }}</div>
                </b-col>
            </validation-provider>
        </validation-observer>
    `,
})
