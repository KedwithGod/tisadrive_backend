<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import { useI18n } from 'vue-i18n';
import Swal from "sweetalert2";

export default {
    components: {
        Layout,
        PageHeader,
        Head,
    },
    props: {
        successMessage: String,
        alertMessage: String,
        app_for: String,
        settings: {
            type: Object,
            required: false,
            default: () => ({})
        },

    },

    setup(props) {
      const { t } = useI18n();
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');
        const form = useForm({
            mail_mailer: props.settings.mail_mailer || '',
            mail_host: props.settings.mail_host || '',
            mail_port: props.settings.mail_port || '',
            mail_username: props.settings.mail_username || '',
            mail_password: props.settings.mail_password || '',
            mail_encryption: props.settings.mail_encryption || '',
            mail_from_address: props.settings.mail_from_address || '',
            mail_from_name: props.settings.mail_from_name || '',

        });

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };


        const handleSubmit = async () => {
            if(props.app_for == "demo"){
                Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
                return;
            }

            try {
                let formData = new FormData();
                formData.append('mail_mailer', form.mail_mailer);
                formData.append('mail_host', form.mail_host);
                formData.append('mail_port', form.mail_port);
                formData.append('mail_username', form.mail_username);
                formData.append('mail_password', form.mail_password);
                formData.append('mail_encryption', form.mail_encryption);
                formData.append('mail_from_address', form.mail_from_address);
                formData.append('mail_from_name', form.mail_from_name);

                
                let response = await axios.post('/mail-configuration/update', formData);

                if (response.status === 201) {
                    successMessage.value = t('mail_configuration_updated_successfully');
                    form.reset();
                    router.get('/mail-configuration');
                } else {
                    alertMessage.value = t('failed_to_update_mail_configuration');
                }
            } catch (error) {
                console.error(t('error_updating_mail_configuration'), error);
                alertMessage.value = t('failed_to_update_mail_configuration_catch');
            }
        };
        

        return {
            successMessage,
            alertMessage,
            dismissMessage,
            handleSubmit,
            form,
        };
    },
    mounted() {
        const passwordField = document.getElementById('mail_password');
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'type' && passwordField.type !== 'password') {
                    passwordField.type = 'password'; // Reset to password type
                }
            });
        });
        observer.observe(passwordField, { attributes: true });
    }
};
</script>
<template>
    <Layout>

        <Head title="Mail Configuration" />
        <PageHeader :title="$t('mail-configuration')" :pageTitle="$t('mail-configuration')" />
        <BRow>
        <BCard v-if="app_for === 'demo'" no-body id="tasksList">
          <BCardHeader class="border-0">
            <div class="alert bg-warning border-warning fs-18" role="alert">
              <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
          </div>
        </BCardHeader>
      </BCard>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">                        
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <form @submit.prevent="handleSubmit">
              <!-- <FormValidation :form="form" :rules="validationRules" ref="validationRef"> -->
                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="mail_mailer" class="form-label">{{$t("mailer_name")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_mailer_name')" id="mail_mailer" v-model="form.mail_mailer" />
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div> 
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="mail_host" class="form-label">{{$t("mail_host")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_mail_host')" id="mail_host"  v-model="form.mail_host" />
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="mail_port" class="form-label">{{$t("mail_port")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_mail_port')" id="mail_port" v-model="form.mail_port" 
                      />
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="mail_username" class="form-label">{{$t("mail_username")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_mail_username')" id="mail_username" v-model="form.mail_username"/>
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="mail_password" class="form-label">{{$t("mail_password")}}
                        <span class="text-danger">*</span>
                      </label>
                      <div class="input-group">
                        <input  type="password" :readonly="app_for === 'demo'" autocomplete="off" class="form-control" :placeholder="$t('enter_mail_password')" id="mail_password"  v-model="form.mail_password"/>
                      </div>                      
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="mail_encryption" class="form-label">{{$t("mail_encryption")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_mail_encryption')" id="mail_encryption" v-model="form.mail_encryption"/>
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="mail_from_address" class="form-label">{{$t("mail_from_address")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_mail_from_address')" id="mail_from_address"  v-model="form.mail_from_address"/>
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="mail_from_name" class="form-label">{{$t("mail_from_name")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_mail_from_name')" id="mail_from_name" v-model="form.mail_from_name"/>
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ settings ? $t('update') : $t('save') }}</button>
                    </div>
                  </div>
                </div>               
                
              <!-- </FormValidation> -->
            </form>                        
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <div>
            <!-- Success Message -->
            <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" data="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Success</strong> - {{
                        successMessage }}
                    <button type="button" class="btn-close btn-close-success" @click="dismissMessage"
                        aria-label="Close Success Message"></button>
                </div>
            </div>

            <!-- Alert Message -->
            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" data="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage
                    }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage"
                        aria-label="Close Alert Message"></button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>
.custom-alert {
    max-width: 600px;
    float: right;
    position: fixed;
    top: 90px;
    right: 20px;
}
.rtl .custom-alert {
  max-width: 600px;
  float: left;
  top: -300px;
  right: 10px;
}
@media only screen and (max-width: 1024px) {
  .custom-alert {
  max-width: 600px;
  float: right;
  position: fixed;
  top: 90px;
  right: 20px;
}
.rtl .custom-alert {
  max-width: 600px;
  float: left;
  top: -230px;
  right: 10px;
}
}
.toggle-password-icon i{
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #6c757d; /* Optional: Change the color of the icon */
}
</style>