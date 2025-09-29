<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import imageUpload from "@/Components/widgets/vehicletypeIcon.vue";
import { useI18n } from 'vue-i18n';
import ImageUpload from '@/Components/ImageUpload.vue';
import FormValidation from "@/Components/FormValidation.vue";

export default {
    components: {
        Layout,
        PageHeader,
        Head,
        Multiselect,
        imageUpload,
        ImageUpload,
        FormValidation
    },
    props: {
        // user: Object,
        driver: Object,
        successMessage: String,
        alertMessage: String,
        app_for: String,
    },

    setup(props) {
        const { t } = useI18n();

        const errors = ref({});
        const validationRef = ref(null);

        const form = useForm({
            password: props.driver ? props.driver.password || "" : "",
            confirm_password: props.driver ? props.driver.password || "" : "",
        });


        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const validationRules = {
            password: { required: true },
            confirm_password: { required: true, sameAs: "password" },
        };




        const validateForm = () => {
            const errors = {};
            for (const key in validationRules) {
                if (validationRules[key].required && !form[key]) {
                    errors[key] = t('this_field_is_required');
                    // errors[key] = 'This field is required';
                }
            }
            return errors;
        };

        const handleSubmit = async () => {
    // Validate form fields locally first
    errors.value = validateForm();

    // Check if validation errors exist locally
    if (Object.keys(errors.value).length === 0) {
        try {
            // If no validation errors, proceed with form submission
            const formData = new FormData();
            formData.append('password', form.password);
            formData.append('confirm_password', form.confirm_password);

            let response;
            if (props.driver) {
                response = await axios.post(`/approved-drivers/password/update/${props.driver.id}`, formData);
            } else {
                response = await axios.post("store", formData);
            }

            if (response.status === 200 || response.status === 201) {
                successMessage.value = response.data.successMessage;
                form.reset(); // Reset form after successful submission
                router.get('/approved-drivers'); // Redirect or navigate to another route
            } else {
                console.error(t('unexpected_response_status'), response.status);
            }

        } catch (error) {
            console.error(t('error_handling_form_submission'), error);
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            } else {
                console.error(t('an_unexpected_error_occurred'), error);
            }
        }
    }
};


        return {
            form,
            successMessage,
            alertMessage,
            errors,
            handleSubmit,
            validationRef,
            validationRules,
        };
    }
};

</script>

<template>
    <Layout>
        <Head title="Driver" />
        <PageHeader :title="driver ? $t('edit') : $t('create')" :pageTitle="$t('driver')" pageLink="/drivers" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0">
                        <tab></tab>                        
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
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
                            <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                                <div class="row">
                                    <div  class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">{{$t("password")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" class="form-control" id="password" v-model="form.password" :placeholder="$t('enter_password')">
                                            <span v-if="errors.password" class="text-danger">{{ errors.password }}</span>
                                        </div>
                                    </div>
                                    <div  class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">{{$t("confirm_password")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" class="form-control" id="confirm_password" v-model="form.confirm_password" :placeholder="$t('confirm_password')">
                                            <span v-if="errors.confirm_password" class="text-danger">{{ errors.confirm_password }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 text-end">
                                        <button type="submit" class="btn btn-primary">{{$t("submit")}}</button>
                                    </div>
                                </div>
                                
                            </FormValidation>
                        </form>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
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
    