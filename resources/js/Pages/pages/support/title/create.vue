<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch,onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import { useI18n } from 'vue-i18n';
import FormValidation from "@/Components/FormValidation.vue";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable

export default {
    components: {
        Layout,
        PageHeader,
        Head,
        Multiselect,
        FormValidation
    },
    props: {
        title: Object,
        successMessage: String,
        alertMessage: String,
        app_for: String,
        category: Array,
        languages: Array,
    },

    setup(props) {
        const { t } = useI18n();
        const { languages, fetchData } = useSharedState(); // Destructure the shared state
        const activeTab = ref('English');
        const errors = ref({});
        const validationRef = ref(null);
        const category = ref(props.category);

        const form = useForm({
            title:  props.title ? props.title.title || {} : {}, // To hold data from the Tab component
            // category_type: props.title ? props.title.category_type.split(',') || [] : [],
            title_type:  props.title ? props.title.title_type || " " : " ", 
            user_type:  props.title ? props.title.user_type || " " : " ", 
        });


        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const validationRules = {
            title: { required: true },
            // category_type: { required: true },
            title_type: { required: true },
            user_type: { requires: true},
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
            // console.log("data",form.data());
            // errors.value = validationRef.value.validate();
            // if (Object.keys(errors.value).length > 0) {
            //     console.log("error",errors.value);
            //     return;
            // }

            try {
                let response;
                if (props.title && props.title.id) {
                response = await axios.post(`/title/update/${props.title.id}`, form.data());
                } else {
                response = await axios.post('store', form.data());
                }

                if (response.status === 201) {
                successMessage.value = t('title_created_successfully');
                form.reset();
                router.get('/title');
                } else {
                alertMessage.value = t('failed_to_create_title');
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
                } else {
                console.error(t('error_creating_title'), error);
                alertMessage.value = t('failed_to_create_title_catch');
                }
            }
         };

         const setActiveTab = (tab) => {
        activeTab.value = tab;
        }
        onMounted(async () => {
        if (Object.keys(languages).length == 0) {
            await fetchData();
        }
        });


        return {
            form,
            successMessage,
            alertMessage,
            errors,
            handleSubmit,
            validationRef,
            validationRules,
            searchQuery,
            category,
            setActiveTab,
            activeTab,
            languages,
        };
    }
};

</script>

<template>
    <Layout>
        <Head title="Title" />
        <PageHeader :title="title ? $t('edit') : $t('create')" :pageTitle="$t('title')" pageLink="/title" />
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
                                    <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified" role="tablist">
                                        <BRow v-for="language in languages" :key="language.code">
                                        <BCol lg="12">
                                            <li class="nav-item" role="presentation">
                                            <a class="nav-link" @click="setActiveTab(language.label)"
                                                :class="{ active: activeTab === language.label }" role="tab" aria-selected="true">
                                                {{ language.label }}
                                            </a>
                                            </li>
                                        </BCol>
                                        </BRow>
                                    </ul>
                                    <div class="tab-content text-muted" v-for="language in languages" :key="language.code">
                                        <div v-if="activeTab === language.label" class="tab-pane active show" :id="`${language.label}`"
                                        role="tabpanel">
                                        <div class="row">
                                            <div class="col-sm-6 mt-3">
                                            <div class="mb-3">
                                            <label :for="`name-${language.code}`" class="form-label">{{$t("title")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" :placeholder="$t('enter_name_in', {language: `${language.label}`})"
                                                :id="`name-${language.code}`" v-model="form.title[language.code]"
                                                :required="language.code === 'en'">
                                            </div>
                                            <!-- Length validation error (client-side) -->
                                        <div v-if="form.title[language.code] && form.title[language.code].length > 100" class="text-danger mt-1">
                                            {{ $t('title_must_be_less_than_100_characters') }}
                                        </div>
                                        </div>
                                        
                                        <!-- <div class="col-sm-6">
                                            <div class="mb-3 mt-3">
                                                <label for="select_categories" class="form-label">{{$t("categories")}}
                                                </label>
                                                <Multiselect
                                                mode="tags" 
                                                id="select_categoires"
                                                v-model="form.category_type"
                                                :options="category.map(categories => ({value:categories.category,label:categories.category}))"
                                                multiple
                                                :close-on-select="false"
                                                :searchable="true"
                                                :create-option="false"
                                                :placeholder="$t('select_categoires')"
                                                />
                                                <span v-for="(error, index) in errors.category_type" :key="index" class="text-danger">{{ error }}</span>
                                            </div>
                                        </div> -->
                                        <div class="col-sm-6">
                                            <div class="mb-3 mt-3">
                                            <label for="title_type" class="form-label">{{$t("title_type")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select id="title_type" class="form-select" v-model="form.title_type">
                                                <option disabled value="">{{$t("choose_title_type")}}</option>
                                                <option value="general">{{$t("general")}}</option>
                                                <option value="request">{{$t("request")}}</option>
                                            </select>
                                            <span v-for="(error, index) in errors.title_type" :key="index" class="text-danger">{{ error }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                            <label for="user_type" class="form-label">{{$t("user_type")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select id="select__user_type" class="form-select" v-model="form.user_type">
                                                <option disabled value="">{{$t("select")}}</option>
                                                <option  value="user">{{$t("user")}}</option>
                                                <option  value="driver">{{$t("driver")}}</option>
                                            </select>
                                            <span v-for="(error, index) in errors.user_type" :key="index" class="text-danger">{{ error }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        
                                    </div>
                                  
                                    
                                    <div class="col-sm-12 text-end">
                                        <button type="submit" class="btn btn-primary" :disabled="Object.values(form.title).some(val => val && val.length > 100)">{{$t("submit")}}</button>
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
    