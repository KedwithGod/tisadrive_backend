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
            enable_vase_map: props.settings.enable_vase_map || '',
            google_map_key: props.settings.google_map_key || '',
            google_map_key_for_distance_matrix: props.settings.google_map_key_for_distance_matrix || '',
            google_sheet_id: props.settings.google_sheet_id || '',

        });

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };


        const handleSubmit = async () => {

            try {
                let formData = new FormData();
                formData.append('enable_vase_map', form.enable_vase_map);
                formData.append('google_map_key', form.google_map_key);
                formData.append('google_map_key_for_distance_matrix', form.google_map_key_for_distance_matrix);
                formData.append('google_sheet_id', form.google_sheet_id);

                
                let response = await axios.post('/map-apis/update', formData);

                if (response.status === 201) {
                    successMessage.value = t('google_mapApis_updated_successfully');
                    form.reset();
                    router.get('/map-apis');
                } else {
                    alertMessage.value = t('failed_to_update_google_mapApis');
                }
            } catch (error) {
                console.error(t('error_updating_google_mapApis'), error);
                alertMessage.value = t('failed_to_update_google_mapApis_catch');
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
};
</script>
<template>
    <Layout>

        <Head title=" Google Map APIs" />
        <PageHeader :title="$t('google_map_apis')" :pageTitle=" $t('google_map_apis')" />
        <BRow>
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
                      <label for="enable_vase_map" class="form-label">{{$t("enable_vase_map")}}</label>
                      <select id="enable_vase_map" class="form-select" v-model="form.enable_vase_map">
                        <option disabled value="">{{$t("select")}}</option>
                        <option value="1">{{$t("yes")}}</option>
                        <option value="0">{{$t("no")}}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="google_map_key" class="form-label">{{$t("google_map_key_for_web_apps")}}</label>
                      <input type="text" class="form-control" :placeholder="$t('enter_google_map_key_for_web_apps')" id="google_map_key" v-model="form.google_map_key" />
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div> 
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="google_map_key_for_distance_matrix" class="form-label">{{$t("google_map_key_for_distance_matrix")}}</label>
                      <input type="text" class="form-control" :placeholder="$t('enter_google_map_key_for_distance_matrix')" id="google_map_key_for_distance_matrix"  v-model="form.google_map_key_for_distance_matrix" />
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="google_sheet_id" class="form-label">{{$t("google_sheet_id")}}</label>
                      <input type="text" class="form-control" :placeholder="$t('enter_google_sheet_id')" id="google_sheet_id" v-model="form.google_sheet_id" 
                      />
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div>
                  </div>
                  <div class="col-lg-12">
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