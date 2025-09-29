<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import { useI18n } from 'vue-i18n';

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
        mail: {
            type: Object,
            required: true,
        },
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
            enable_recaptcha: props.settings.enable_recaptcha || '',
            reacptcha_site_key: props.settings.reacptcha_site_key || '',
            reacptcha_secret_key: props.settings.reacptcha_secret_key || '',

        });

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };


        const handleSubmit = async () => {

            try {
                let formData = new FormData();
                formData.append('enable_recaptcha', form.enable_recaptcha);
                formData.append('reacptcha_site_key', form.reacptcha_site_key);
                formData.append('reacptcha_secret_key', form.reacptcha_secret_key);

                
                let response = await axios.post('/recaptcha/update', formData);

                if (response.status === 201) {
                    successMessage.value = t('recaptcha_updated_successfully');
                    form.reset();
                    router.get('/recaptcha');
                } else {
                    alertMessage.value = t('failed_to_update_recaptcha');
                }
            } catch (error) {
                console.error(t('error_updating_recaptcha'), error);
                alertMessage.value = t('failed_to_update_recaptcha_catch');
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
        const siteKey = document.getElementById('reacptcha_site_key');
        const secretKey = document.getElementById('reacptcha_secret_key');
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'type' && siteKey.type !== 'password') {
                  siteKey.type = 'password'; // Reset to password type
                }
                if (mutation.attributeName === 'type' && secretKey.type !== 'password') {
                  secretKey.type = 'password'; // Reset to password type
                }
            });
        });
        observer.observe(siteKey, { attributes: true });
        observer.observe(secretKey, { attributes: true });
    }
};
</script>
<template>
    <Layout>

        <Head title="Recaptcha" />
        <PageHeader :title="$t('recaptcha')" :pageTitle="$t('recaptcha')" />
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
                      <label for="enable_recaptcha" class="form-label">{{$t("enable_recaptcha")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select :disabled="app_for === 'demo'" id="enable_recaptcha" class="form-select" v-model="form.enable_recaptcha">
                        <option disabled value="">{{$t("select")}}</option>
                        <option value="1">{{$t("yes")}}</option>
                        <option value="0">{{$t("no")}}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="reacptcha_site_key" class="form-label">{{$t("site_key")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="password" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_site_key')" id="reacptcha_site_key" v-model="form.reacptcha_site_key" />
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div> 
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="reacptcha_secret_key" class="form-label">{{$t("secret_key")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="password" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_secret_key')" id="reacptcha_secret_key"  v-model="form.reacptcha_secret_key" />
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div>
                  </div>
                    <div class="mt-3 mb-3">
                        <div class="row">
                            <div class="col">
                                <p>
                                    <i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> {{$t("go_to_the_credentials_page")}}
                                    <a href="https://www.google.com/recaptcha/admin/create" target="_blank" class="text-decoration-underline fs-14 ms-1" style="color:#405189;">{{$t("click_here")}}</a>                            
                                </p>
                                <p><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i>{{$t("add_a_label")}}</p>
                                <p><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> {{$t("select_reCAPTCHA_v2_as_reCAPTCHA_type")}}</p>
                                <p><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> {{$t("add_domain")}}</p>
                            </div>
                            <div class="col">                                
                                <p><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> {{$t("check_in_accept_the_reCAPTCHA_terms_of_service")}}</p>
                                <p><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> {{$t("press_submit")}}</p>
                                <p><i class="mdi mdi-check-bold align-middle lh-1 me-2"></i> {{$t("copy_site_key_and_secret_key")}}</p>                                
                            </div>
                        </div>  
                    </div>  
                  <div class="col-lg-12 ">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ settings ? $t('update') : $t('save') }}</button>
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
</style>