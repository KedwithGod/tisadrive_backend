<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import { useI18n } from 'vue-i18n';
import FormValidation from "@/Components/FormValidation.vue";

export default {
    components: {
        Layout,
        PageHeader,
        Head,
        Multiselect,
        FormValidation
    },
    props: {
        category: Object,
        successMessage: String,
        alertMessage: String,
        app_for: String,
    },

    setup(props) {
        const { t } = useI18n();
        const errors = ref({});
        const validationRef = ref(null);

        const form = useForm({
            category: props.category ? props.category.category || "" : "",
        });


        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const validationRules = {
            category: { required: true },
        };
        // Construct the full URL for the vehicle type icon
        const searchQuery = ref('');


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
            errors.value = validationRef.value.validate();
            if (Object.keys(errors.value).length > 0) {
                return;
            }

            try {
                let response;
                if (props.category && props.category.id) {
                response = await axios.post(`/category/update/${props.category.id}`, form.data());
                } else {
                response = await axios.post('store', form.data());
                }

                if (response.status === 201) {
                successMessage.value = t('category_created_successfully');
                form.reset();
                router.get('/category');
                } else {
                alertMessage.value = t('failed_to_create_category');
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
                } else {
                console.error(t('error_creating_category'), error);
                alertMessage.value = t('failed_to_create_category_catch');
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
            searchQuery,
        };
    }
};

</script>

<template>
    <Layout>
        <Head title="Category" />
        <PageHeader :title="category ? $t('edit') : $t('create')" :pageTitle="$t('category')" pageLink="/category" />
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
                                    <div class="col-sm-12">
                                        <div class="mb-3">
                                            <label for="category" class="form-label">{{$t("category")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" :placeholder="$t('enter_category')" id="category" v-model="form.category">
                                            <span v-if="errors.category" class="text-danger">{{ errors.category }}</span>
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
    