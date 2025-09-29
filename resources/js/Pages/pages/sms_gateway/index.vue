<script>
import { Link, Head, useForm, router, usePage } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, watch } from "vue";
import axios from "axios";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import { useI18n } from 'vue-i18n';

export default {
    data() {
        return {
            rightOffcanvas: false,
        };
    },
    components: {
        Layout,
        PageHeader,
        Head,
        Pagination,
        flatPickr,
        Link,
        search,
        searchbar,
    },
    props: {
        successMessage: String,
        alertMessage: String,
        app_for: String,
        settings: Object,
    },
    setup(props) {
        const { t } = useI18n();
        const form = useForm({
            enable_firebase_otp: props.settings?.enable_firebase_otp ?? false,

            enable_twilio: props.settings?.enable_twilio ?? false,
            twilio_sid: props.settings?.twilio_sid ?? '',
            twilio_token: props.settings?.twilio_token ?? '',
            twilio_mobile_number: props.settings?.twilio_mobile_number ?? '',

            enable_sms_ala: props.settings?.enable_sms_ala ?? false,
            sms_ala_api_key: props.settings?.sms_ala_api_key ?? '',
            sms_ala_api_secret_key: props.settings?.sms_ala_api_secret_key ?? '',
            sms_ala_token: props.settings?.sms_ala_token ?? '',
            sms_ala_mobile_number: props.settings?.sms_ala_mobile_number ?? '',

            enable_msg91: props.settings?.enable_msg91 ?? false,
            msg91_template_id: props.settings?.msg91_template_id ?? '',
            msg91_auth_key: props.settings?.msg91_auth_key ?? '',

            enable_sparrow: props.settings?.enable_sparrow ?? false,
            sparrow_sender_id: props.settings?.sparrow_sender_id ?? '',
            sparrow_token: props.settings?.sparrow_token ?? '',

            enable_sms_india_hub: props.settings?.enable_sms_india_hub ?? false,
            sms_india_hub_api_key: props.settings?.sms_india_hub_api_key ?? '',
            sms_india_hub_sid: props.settings?.sms_india_hub_sid ?? '',
        });

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const handleCheckboxChange = (key) => {
            if(props.app_for == "demo"){
                form[key] = !form[key];
                Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
                return;
            }
            Object.keys(form).forEach((formKey) => {
                if (formKey.startsWith('enable_')) {
                    form[formKey] = (formKey === key);
                }
            });
            handleSubmit();
        };

        const handleSubmit = async () => {
            if(props.app_for == "demo"){
                Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
                return;
            }
            try {
                let formData = new FormData();
                for (let key in form) {
                    if(key.startsWith('enable')){
                        formData.append(key, form[key] ? 1 : 0);
                    }else{
                        formData.append(key, form[key] ?? '');
                    }
                }

                let response = await axios.post('/sms-gateway/update', formData);

                if (response.status === 201) {
                    successMessage.value = t('sms_configuration_updated_successfully');
                    setTimeout(() => {
                        router.get('/sms-gateway');
                        form.reset();
                    }, 5000);
                } else {
                    alertMessage.value = t('failed_to_update_sms_configuration');
                }
            } catch (error) {
                console.error(t('error_updating_sms_configuration'), error);
                alertMessage.value = t('failed_to_update_sms_configuration_catch');
            }
        };

        watch(() => props.settings, (newSettings) => {
            Object.assign(form, newSettings);
        }, { immediate: true });

        return {
            form,
            successMessage,
            alertMessage,
            dismissMessage,
            handleCheckboxChange,
            handleSubmit,
        };
    },
};
</script>

<template>
    <Layout>
        <Head title="SMS Gateway" />
        <PageHeader :title="$t('sms_gateway')" :pageTitle="$t('sms_gateway')" />
        <form @submit.prevent="handleSubmit">
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
                            <BRow class="mt-3">
                                <BCol lg="6">
                                    <BCard no-body id="tasksList shadow">
                                        <BCardHeader class="border-0">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{$t("enable_firebase_otp")}}</h5>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check form-switch form-switch-lg float-end me-3">
                                                        <input v-model="form.enable_firebase_otp" class="form-check-input" type="checkbox" role="switch" id="enable_firebase_otp" @change="handleCheckboxChange('enable_firebase_otp')" />
                                                    </div>
                                                </div>
                                            </div>
                                        </BCardHeader>
                                    </BCard>
                                </BCol>
                            </BRow>

                            <BRow class="mt-4">
                                <BCol lg="6">
                                    <BCard no-body id="tasksList" class="border">
                                        <BCardHeader class="border-0 mt-2 p-4 border-bottom">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{$t("twilio")}}</h5>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check form-switch form-switch-lg float-end me-3">
                                                        <input v-model="form.enable_twilio" class="form-check-input" type="checkbox" role="switch" id="enable_twilio" @change="handleCheckboxChange('enable_twilio')" />
                                                    </div>
                                                </div>
                                            </div>
                                        </BCardHeader>
                                        <BCardBody>
                                            <div class="text-center mb-4">
                                                <img src="@assets/images/twilio.png" style="width: 200px;" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="twilio_sid" class="form-label">{{$t("sid")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.twilio_sid" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_twilio_sid')" id="twilio_sid" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="twilio_token" class="form-label">{{$t("token")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.twilio_token" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_twilio_token')" id="twilio_token" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="twilio_mobile_number" class="form-label">{{$t("twilio_mobile_number")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.twilio_mobile_number" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_twilio_mobile_number')" id="twilio_mobile_number" />
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">{{ $t('save') }}</button>
                                                </div>
                                            </div>
                                        </BCardBody>
                                    </BCard>
                                </BCol>

                                <BCol lg="6">
                                    <BCard no-body id="tasksList" class="border">
                                        <BCardHeader class="border-0 mt-2 p-4 border-bottom">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{$t("sms_ala")}}</h5>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check form-switch form-switch-lg float-end me-3">
                                                        <input v-model="form.enable_sms_ala" class="form-check-input" type="checkbox" role="switch" id="enable_sms_ala" @change="handleCheckboxChange('enable_sms_ala')" />
                                                    </div>
                                                </div>
                                            </div>
                                        </BCardHeader>
                                        <BCardBody>
                                            <div class="text-center mb-4">
                                                <img src="@assets/images/smsala.webp" style="width: 200px;" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="sms_ala_api_key" class="form-label">{{$t("api_key")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.sms_ala_api_key" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_sms_ala_api_key')" id="sms_ala_api_key" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="sms_ala_api_secret_key" class="form-label">{{$t("api_secret_key")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.sms_ala_api_secret_key" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_sms_ala_api_secret_key')" id="sms_ala_api_secret_key" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="sms_ala_token" class="form-label">{{$t("token")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.sms_ala_token" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_sms_ala_token')" id="sms_ala_token" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="sms_ala_mobile_number" class="form-label">{{$t("sms_ala_mobile_number")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.sms_ala_mobile_number" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_sms_ala_mobile_number')" id="sms_ala_mobile_number" />
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">{{ $t('save') }}</button>
                                                </div>
                                            </div>
                                        </BCardBody>
                                    </BCard>
                                </BCol>
                                <BCol lg="6">
                                    <BCard no-body id="tasksList" class="border">
                                        <BCardHeader class="border-0 mt-2 p-4 border-bottom">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{$t("msg91")}}</h5>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check form-switch form-switch-lg float-end me-3">
                                                        <input v-model="form.enable_msg91" class="form-check-input" type="checkbox" role="switch" id="enable_msg91" @change="handleCheckboxChange('enable_msg91')" />
                                                    </div>
                                                </div>
                                            </div>
                                        </BCardHeader>
                                        <BCardBody>
                                            <div class="text-center mb-4">
                                                <img src="@assets/images/msg91.png" style="width: 200px;" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="msg91_template_id" class="form-label">{{$t("msg91_template_id")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.msg91_template_id" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_msg91_template_id')" id="msg91_template_id" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="msg91_auth_key" class="form-label">{{$t("msg91_token")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.msg91_auth_key" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_msg91_token')" id="msg91_auth_key" />
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">{{ $t('save') }}</button>
                                                </div>
                                            </div>
                                        </BCardBody>
                                    </BCard>
                                </BCol>
                                <BCol lg="6">
                                    <BCard no-body id="tasksList" class="border">
                                        <BCardHeader class="border-0 mt-2 p-4 border-bottom">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{$t("sparrow")}}</h5>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check form-switch form-switch-lg float-end me-3">
                                                        <input v-model="form.enable_sparrow" class="form-check-input" type="checkbox" role="switch" id="enable_sparrow" @change="handleCheckboxChange('enable_sparrow')" />
                                                    </div>
                                                </div>
                                            </div>
                                        </BCardHeader>
                                        <BCardBody>
                                            <div class="text-center mb-4">
                                                <img src="@assets/images/sparrow.png" style="width: 200px;" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="sparrow_sender_id" class="form-label">{{$t("sparrow_sender_id")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.sparrow_sender_id" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_sparrow_sender_id')" id="sparrow_sender_id" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="sparrow_token" class="form-label">{{$t("sparrow_token")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.sparrow_token" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_sparrow_token')" id="sparrow_token" />
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">{{ $t('save') }}</button>
                                                </div>
                                            </div>
                                        </BCardBody>
                                    </BCard>
                                </BCol>
                                <BCol lg="6">
                                    <BCard no-body id="tasksList" class="border">
                                        <BCardHeader class="border-0 mt-2 p-4 border-bottom">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5>{{$t("sms_india_hub")}}</h5>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check form-switch form-switch-lg float-end me-3">
                                                        <input v-model="form.enable_sms_india_hub" class="form-check-input" type="checkbox" role="switch" id="enable_sms_india_hub" @change="handleCheckboxChange('enable_sms_india_hub')" />
                                                    </div>
                                                </div>
                                            </div>
                                        </BCardHeader>
                                        <BCardBody>
                                            <div class="text-center mb-4">
                                                <img src="@assets/images/SMSINDIAHUB.png" style="width: 200px;" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="sms_india_hub_api_key" class="form-label">{{ $t("sms_india_hub_api_key") }}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.sms_india_hub_api_key" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_sms_india_hub_api_key')" id="sms_india_hub_api_key" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="sms_india_hub_sid" class="form-label">{{$t("sms_india_hub_sid")}}</label>
                                                <input :readonly="app_for === 'demo'" v-model="form.sms_india_hub_sid" :type="app_for === 'demo' ? 'password' : 'text'" class="form-control" :placeholder="$t('your_sms_india_hub_sid')" id="sms_india_hub_sid" />
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">{{ $t('save') }}</button>
                                                </div>
                                            </div>
                                        </BCardBody>
                                    </BCard>
                                </BCol>
                            </BRow>
                        </BCardBody>
                    </BCard>
                </BCol>
            </BRow>
        </form>
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
