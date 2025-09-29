<template>
    <Layout>
        <Head title="Firebase" />
        <PageHeader :title="$t('firebase')" :pageTitle="$t('firebase')" />
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
                    <BCardHeader class="border-0"></BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="firebase_database_url" class="form-label">{{$t("firebase_database_url")}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input :readonly="app_for === 'demo'" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('enter_firebase_database_url')" v-model="form.firebase_database_url" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="firebase_api_key" class="form-label">{{$t("firebase_api_key")}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input :readonly="app_for === 'demo'" type="password" class="form-control" :placeholder="$t('enter_firebase_api_key')" v-model="form.firebase_api_key" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="firebase_auth_domain" class="form-label">{{$t("firebase_auth_domain")}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input :readonly="app_for === 'demo'" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('enter_firebase_auth_domain')" v-model="form.firebase_auth_domain" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="firebase_project_id" class="form-label">{{$t("firebase_project_id")}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input :readonly="app_for === 'demo'" type="password" class="form-control" :placeholder="$t('enter_firebase_project_id')" v-model="form.firebase_project_id" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="firebase_storage_bucket" class="form-label">{{$t("firebase_storage_bucket")}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input :readonly="app_for === 'demo'" type="password" class="form-control" :placeholder="$t('enter_firebase_storage_bucket')" v-model="form.firebase_storage_bucket" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="firebase_messaging_sender_id" class="form-label">{{$t("firebase_messaging_sender_id")}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input :readonly="app_for === 'demo'" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('enter_firebase_messaging_sender_id')" v-model="form.firebase_messaging_sender_id" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="firebase_app_id" class="form-label">{{$t("firebase_app_id")}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input :readonly="app_for === 'demo'"  :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('enter_firebase_app_id')" v-model="form.firebase_app_id" />
                                    </div>
                                </div>
                                <!-- <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="firebase_measurement_id" class="form-label">{{$t("firebase_management_id")}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input :readonly="app_for === 'demo'" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('enter_firebase_management_id')" v-model="form.firebase_measurement_id" />
                                    </div>
                                </div> -->
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="firebase_json" class="form-label">{{$t("firebase_json")}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input :disabled="app_for === 'demo'" type="file" class="form-control" @change="handleFileUpload" />
                                        <div v-if="jsonFileError" class="text-danger">{{$t("please_upload_a_json_file")}}</div>
                                        <div v-if="existingJsonFile" class="mt-2">
                                            <strong>{{ $t("existing_file") }}</strong> {{ existingJsonFile }}
                                        </div>
                                    </div>
                                    <!-- Firebase validation message -->
                                    <span v-if="!firebase_json_validation" class="text-danger">
                                        Kindly Upload Correct firebase.json
                                    </span>
                                    <span v-else class="text-success">
                                        firebase.json Uploaded 
                                    </span>
                                 </div>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">{{ firebase ? $t('update') : $t('save') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <div>
            <!-- Success Message -->
            <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" data="alert" id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Success</strong> - {{ successMessage }}
                    <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
                </div>
            </div>

            <!-- Alert Message -->
            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" data="alert" id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage" aria-label="Close Alert Message"></button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";
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
        firebase: {
            type: Object,
            required: true,
        },
        app_for: String,
        firebase_json_validation : Boolean,
        settings: {
            type: Object,
            required: false,
            default: () => ({})
        },
        existingJsonFile: {
            type: String,
            required: false,
            default: null
        }
    },

    setup(props) {
        const { t } = useI18n();
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');
        const form = useForm({
            firebase_database_url: props.settings.firebase_database_url || '',
            firebase_api_key: props.settings.firebase_api_key || '',
            firebase_auth_domain: props.settings.firebase_auth_domain || '',
            firebase_project_id: props.settings.firebase_project_id || '',
            firebase_storage_bucket: props.settings.firebase_storage_bucket || '',
            firebase_messaging_sender_id: props.settings.firebase_messaging_sender_id || '',
            firebase_app_id: props.settings.firebase_app_id || '',
            // firebase_measurement_id: props.settings.firebase_measurement_id || '',
            firebase_json: null,
        });
        const jsonFileError = ref(false);
        const existingJsonFile = ref(props.existingJsonFile);

        const firebase_json_validation = ref(props.firebase_json_validation);

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const handleFileUpload = (e) => {
            if(props.app_for == "demo"){
                Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
                return;
            }
            const file = e.target.files[0];
            
            // Validate file type
            if (file && file.type !== 'application/json') {
                jsonFileError.value = true;
                form.firebase_json = null; // Clear the file input
                return;
            }

            // If valid JSON file, update form data
            jsonFileError.value = false;
            form.firebase_json = file;
        };

        const handleSubmit = async () => {
            if(props.app_for == "demo"){
                Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
                return;
            }
            if (form.firebase_json && !validateJsonFile(form.firebase_json)) {
                jsonFileError.value = true;
                return;
            }

            try {
                let formData = new FormData();
                if (form.firebase_json) {
                    formData.append('firebase_json', form.firebase_json);
                }
                formData.append('firebase_database_url', form.firebase_database_url);
                formData.append('firebase_api_key', form.firebase_api_key);
                formData.append('firebase_auth_domain', form.firebase_auth_domain);
                formData.append('firebase_project_id', form.firebase_project_id);
                formData.append('firebase_storage_bucket', form.firebase_storage_bucket);
                formData.append('firebase_messaging_sender_id', form.firebase_messaging_sender_id);
                formData.append('firebase_app_id', form.firebase_app_id);
                // formData.append('firebase_measurement_id', form.firebase_measurement_id);
                
                let response = await axios.post('/firebase/update', formData);

                if (response.status === 201) {
                    successMessage.value = t('firebase_updated_successfully');
                    form.reset();
                    router.get('/firebase');
                } else {
                    alertMessage.value = t('failed_to_update_firebase')
                }
            } catch (error) {
                console.error(t('error_updating_firebase'), error);
                alertMessage.value =t('failed_to_update_firebase');
            }
        };

        const validateJsonFile = (file) => {
            return file && file.type === 'application/json';
        };

        return {
            successMessage,
            alertMessage,
            dismissMessage,
            handleFileUpload,
            handleSubmit,
            form,
            validateJsonFile,
            jsonFileError,
            existingJsonFile,
            firebase_json_validation,
        };
    },
};
</script>

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