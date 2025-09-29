<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed,onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import ImageUp from "@/Components/ImageUp.vue";
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { useSharedState } from '@/composables/useSharedState';
import Swal from "sweetalert2";
import { useI18n } from 'vue-i18n';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation,
    imageUpload,
    ImageUp,
    ckeditor: CKEditor.component,
  },
  data() {
    return {
      editor: ClassicEditor,
      editorData: "",
    };
  },
  props: {
    successMessage: String,
    alertMessage: String,
    landingHome: Object,
    validate: Function,
    app_for: String,
    languages: {
      type: Array,
      required: true
    },
  },
  setup(props) {
    const { t } = useI18n();
    const { languages } = useSharedState();
    const storedLanguages = ref([]);  
    const form = useForm({
      hero_title: props.landingHome ? props.landingHome.hero_title || "" : "",
      hero_user_link_android: props.landingHome ? props.landingHome.hero_user_link_android || "" : "",
      hero_user_link_apple: props.landingHome ? props.landingHome.hero_user_link_apple || "" : "",
      hero_driver_link_android: props.landingHome ? props.landingHome.hero_driver_link_android || "" : "",
      hero_driver_link_apple: props.landingHome ? props.landingHome.hero_driver_link_apple || "" : "",
      feature_heading: props.landingHome ? props.landingHome.feature_heading || "" : "",
      feature_para: props.landingHome ? props.landingHome.feature_para || "" : "",
      feature_sub_heading_1: props.landingHome ? props.landingHome.feature_sub_heading_1 || "" : "",
      feature_sub_para_1: props.landingHome ? props.landingHome.feature_sub_para_1 || "" : "",
      feature_sub_heading_2: props.landingHome ? props.landingHome.feature_sub_heading_2 || "" : "",
      feature_sub_para_2: props.landingHome ? props.landingHome.feature_sub_para_2 || "" : "",
      feature_sub_heading_3: props.landingHome ? props.landingHome.feature_sub_heading_3 || "" : "",
      feature_sub_para_3: props.landingHome ? props.landingHome.feature_sub_para_3 || "" : "",
      feature_sub_heading_4: props.landingHome ? props.landingHome.feature_sub_heading_4 || "" : "",
      feature_sub_para_4: props.landingHome ? props.landingHome.feature_sub_para_4 || "" : "",
      service_heading_1: props.landingHome ? props.landingHome.service_heading_1 || "" : "",
      service_heading_2: props.landingHome ? props.landingHome.service_heading_2 || "" : "",
      service_para: props.landingHome ? props.landingHome.service_para || "" : "",
      services: props.landingHome ? props.landingHome.services || "" : "",
      service_img: props.landingHome ? props.landingHome.service_img || "" : "",
      about_title_1: props.landingHome ? props.landingHome.about_title_1 || "" : "",
      about_title_2: props.landingHome ? props.landingHome.about_title_2 || "" : "",
      about_img: props.landingHome ? props.landingHome.about_img || "" : "",
      about_para: props.landingHome ? props.landingHome.about_para || "" : "",
      about_lists: props.landingHome ? props.landingHome.about_lists || "" : "",
      box_img_1: props.landingHome ? props.landingHome.box_img_1 || "" : "",
      box_para_1: props.landingHome ? props.landingHome.box_para_1 || "" : "",
      box_img_2: props.landingHome ? props.landingHome.box_img_2 || "" : "",
      box_para_2: props.landingHome ? props.landingHome.box_para_2 || "" : "",
      box_img_3: props.landingHome ? props.landingHome.box_img_3 || "" : "",
      box_para_3: props.landingHome ? props.landingHome.box_para_3 || "" : "",
      drive_heading: props.landingHome ? props.landingHome.drive_heading || "" : "",
      drive_title_1: props.landingHome ? props.landingHome.drive_title_1 || "" : "",
      drive_para_1: props.landingHome ? props.landingHome.drive_para_1 || "" : "",
      drive_title_2: props.landingHome ? props.landingHome.drive_title_2 || "" : "",
      drive_para_2: props.landingHome ? props.landingHome.drive_para_2 || "" : "",
      drive_title_3: props.landingHome ? props.landingHome.drive_title_3 || "" : "",
      drive_para_3: props.landingHome ? props.landingHome.drive_para_3 || "" : "",
      service_area_img: props.landingHome ? props.landingHome.service_area_img || "" : "",
      service_area_title: props.landingHome ? props.landingHome.service_area_title || "" : "",
      service_area_para: props.landingHome ? props.landingHome.service_area_para || "" : "",
      service_para: props.landingHome ? props.landingHome.service_para || "" : "",
      locale: props.landingHome ? props.landingHome.locale || "" : "",
      language: props.landingHome ? props.landingHome.language || "" : "",
    });


    const validationRules = {
      hero_title: { required: true },
      hero_user_link_android: { required: true },
      hero_user_link_apple: { required: true },
      hero_driver_link_android: { required: true },
      hero_driver_link_apple: { required: true },
      feature_heading: { required: true },
      feature_para: { required: true },
      feature_sub_heading_1: { required: true },
      feature_sub_para_1: { required: true },
      feature_sub_heading_2: { required: true },
      feature_sub_para_2: { required: true },
      feature_sub_heading_3: { required: true },
      feature_sub_para_3: { required: true },
      feature_sub_heading_4: { required: true },
      feature_sub_para_4: { required: true },
      service_heading_1: { required: true },
      service_heading_2: { required: true },
      service_para: { required: true },
      services: { required: true } ,
      service_img: { required: true },
      about_title_1: { required: true },
      about_title_2: { required: true },
      about_img: { required: true } ,
      about_para: { required: true },
      about_lists: { required: true } ,
      box_img_1: { required: true },
      box_para_1: { required: true } ,
      box_img_2: { required: true },
      box_para_2: { required: true },
      box_img_3: { required: true } ,
      box_para_3: { required: true } ,
      drive_heading: { required: true } ,
      drive_title_1: { required: true } ,
      drive_para_1: { required: true } ,
      drive_title_2: { required: true } ,
      drive_para_2: { required: true } ,
      drive_title_3: { required: true },
      drive_para_3: { required: true } ,
      service_area_img: { required: true } ,
      service_area_title: { required: true } ,
      service_area_para: { required: true } ,
      locale: { required: true } ,
      language: { required: true } ,
    };

    const validateIconSize = () => {
      const fileInput = document.getElementById('iconInput');
      if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const img = new Image();
        img.onload = function () {
          if (img.width !== 512 || img.height !== 512) {
            alert('Icon must be 512x512 pixels.');
            fileInput.value = '';
          }
        };
        img.src = URL.createObjectURL(file);
      }
    };

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

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
        const formData = new FormData();
        for (const key in form) {
          if (key === 'iconFile' && form[key]) {
            formData.append('icon', form[key]);
          } else {
            formData.append(key, form[key]);
          }
        }

        let response;
        if (props.landingHome && props.landingHome.id) {
          response = await axios.post(`/landing-home/update/${props.landingHome.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });

        } else {
          response = await axios.post('/landing-home/store', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        }

        if (response.status === 201) {
          successMessage.value = t('home_created_successfully');
          form.reset();
          router.get('/landing-home');
        } else {
          alertMessage.value = t('failed_to_create_home');
        }
      } catch (error) {
        console.error(t('error_creating_home'), error);
        alertMessage.value = t('failed_to_create_home_catch');
        if (error.response && error.response.data.errors) {
          errors.value = error.response.data.errors;
        }
      }
    };

    onMounted(async () => {
      if (Object.keys(languages).length == 0) {
        await fetchData();
      }
      fetchStoredLanguages();
    });


    const fetchStoredLanguages = async () => {     
      try {
        const response = await axios.get('/landing-home/list');
        storedLanguages.value = response.data.results.map(lang => ({language: lang.language }) );

        const commonLanguages = storedLanguages.value.filter(storedLang =>
            languages.value.some(lang => lang.label === storedLang.language)
        );

          languages.value = languages.value.map(lang => ({
          ...lang,  // All existing properties of lang
          disabled: commonLanguages.some(commonLang => commonLang.language === lang.label),
        }));
      } catch (error) {
        console.error(t('error_fetching_stored_languages'), error);
      }
    };

      // Construct the full URL for the vehicle type icon
      const iconUrl = props.vehicleType ? props.vehicleType.icon :null;

      const handleImageSelected = (file, fieldName) => {
      if(props.app_for == "demo"){
          Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
          return;
      }
      form[fieldName] = file;
    };

    const handleImageRemoved = (fieldName) => {
      if(props.app_for == "demo"){
          Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
          return;
      }
      form[fieldName] = null;
    };


    const selectlanguages = async () =>{
      const selectedLanguage = languages.value.find(
        (lang) => lang.label === form.language,(lang)=>lang.direction === form.direction
      );
        form.language = selectedLanguage.label;
        form.locale = selectedLanguage.code;
        form.direction = selectedLanguage.direction;
    }

    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      validationRef,
      errors,
      validateIconSize,
      landingHome: props.landingHome,
      iconUrl,
      handleImageSelected,
      handleImageRemoved,
      languages,
      selectlanguages
    };
  }
};
</script>

<template>
  <Layout>
    <Head title="Manage LandingSite-Home" />
    <PageHeader :title="landingHome ? $t('edit') : $t('create')" :pageTitle="$t('landing_home')" pageLink="/landing-home"/>
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
            <div>
              <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert" id="alertMsg">
                <div class="alert-content">
                  <i class="ri-notification-off-line me-3 align-middle"></i>
                  <strong>Success</strong> - {{ successMessage }}
                  <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
                </div>
              </div>

              <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert" id="alertMsg">
                <div class="alert-content">
                  <i class="ri-notification-off-line me-3 align-middle"></i>
                  <strong>Alert</strong> - {{ alertMessage }}
                  <button type="button" class="btn-close btn-close-danger" @click="dismissMessage" aria-label="Close Alert Message"></button>
                </div>
              </div>
            </div>
          </BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="language" class="form-label">{{$t("languages")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select :disabled="app_for === 'demo'" id="language" class="form-select" v-model="form.language"  @change="selectlanguages">
                        <option disabled value="">{{$t("choose_languages")}}</option>
                        <option v-for="languages in languages" :key="languages.id" :disabled="languages.disabled">
                        {{ languages.label }}
                      </option>
                      </select>
                      <span v-for="(error, index) in errors.language" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("hero_section")}}</h4>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="hero_title" class="form-label">{{$t("hero_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="hero_title" v-model="form.hero_title" />
                      <span v-for="(error, index) in errors.hero_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="hero_user_link_android" class="form-label">{{$t("hero_user_link_android_app")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="url" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_link')" id="hero_user_link_android" v-model="form.hero_user_link_android" />
                      <span v-for="(error, index) in errors.hero_user_link_android" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="hero_user_link_apple" class="form-label">{{$t("hero_user_link_apple_app")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="url" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_link')" id="hero_user_link_apple" v-model="form.hero_user_link_apple" />
                      <span v-for="(error, index) in errors.hero_user_link_apple" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="hero_driver_link_android" class="form-label">{{$t("hero_driver_link_android_app")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="url" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_link')" id="hero_driver_link_android" v-model="form.hero_driver_link_android" />
                      <span v-for="(error, index) in errors.hero_driver_link_android" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="hero_driver_link_apple" class="form-label">{{$t("hero_driver_link_apple_app")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="url" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_link')" id="hero_driver_link_apple" v-model="form.hero_driver_link_apple" />
                      <span v-for="(error, index) in errors.hero_driver_link_apple" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("feature_section")}}</h4>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="feature_heading" class="form-label">{{$t("feature_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="feature_heading" v-model="form.feature_heading" />
                      <span v-for="(error, index) in errors.feature_heading" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="feature_para" class="form-label">{{$t("feature_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.feature_para"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.feature_para" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="feature_sub_heading_1" class="form-label"> {{$t("sub_heading_1")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="feature_sub_heading_1" v-model="form.feature_sub_heading_1" />
                      <span v-for="(error, index) in errors.feature_sub_heading_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="feature_sub_para_1" class="form-label">{{$t("sub_heading_para_1")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.feature_sub_para_1"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.feature_sub_para_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="feature_sub_heading_2" class="form-label"> {{$t("sub_heading_2")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="feature_sub_heading_2" v-model="form.feature_sub_heading_2" />
                      <span v-for="(error, index) in errors.feature_sub_heading_2" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="feature_sub_para_2" class="form-label">{{$t("sub_heading_para_2")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.feature_sub_para_2"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.feature_sub_para_2" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="feature_sub_heading_3" class="form-label"> {{$t("sub_heading_3")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="feature_sub_heading_3" v-model="form.feature_sub_heading_3" />
                      <span v-for="(error, index) in errors.feature_sub_heading_3" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="feature_sub_para_3" class="form-label">{{$t("sub_heading_para_3")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.feature_sub_para_3"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.feature_sub_para_3" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="feature_sub_heading_4" class="form-label"> {{$t("sub_heading_4")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="feature_sub_heading_4" v-model="form.feature_sub_heading_4" />
                      <span v-for="(error, index) in errors.feature_sub_heading_4" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="feature_sub_para_4" class="form-label">{{$t("sub_heading_para_4")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.feature_sub_para_4"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.feature_sub_para_4" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("service_section")}}</h4>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="service_heading_1" class="form-label">{{$t("service_heading")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="service_heading_1" v-model="form.service_heading_1" />
                      <span v-for="(error, index) in errors.service_heading_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="service_heading_2" class="form-label">{{$t("service_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="service_heading_2" v-model="form.service_heading_2" />
                      <span v-for="(error, index) in errors.service_heading_2" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="service_para" class="form-label">{{$t("service_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.service_para"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.service_para" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="services" class="form-label">{{$t("service_lists")}}</label>
                      <span class="text-danger">*</span>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.services"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.services" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="service_img" class="form-label">{{$t("service_image")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.service_img" @image-selected="(file) => handleImageSelected(file, 'service_img')" @image-removed="() => handleImageRemoved('service_img')"></ImageUp>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("about_section")}}</h4>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="about_title_1" class="form-label">{{$t("about_heading")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="about_title_1" v-model="form.about_title_1" />
                      <span v-for="(error, index) in errors.about_title_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="about_title_2" class="form-label">{{$t("about_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="about_title_2" v-model="form.about_title_2" />
                      <span v-for="(error, index) in errors.about_title_2" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="about_para" class="form-label">{{$t("about_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.about_para"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.about_para" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="about_lists" class="form-label">{{$t("service_lists")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.about_lists"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.about_lists" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="about_img" class="form-label">{{$t("about_image")}}</label>
                      <ImageUp :initialImageUrl="form.about_img" @image-selected="(file) => handleImageSelected(file, 'about_img')" @image-removed="() => handleImageRemoved('about_img')"></ImageUp>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("box_section")}}</h4>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="box_para_1" class="form-label">{{$t("box_1_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.box_para_1"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.box_para_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="box_img_1" class="form-label">{{$t("box_1_image")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.box_img_1" @image-selected="(file) => handleImageSelected(file, 'box_img_1')" @image-removed="() => handleImageRemoved('box_img_1')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="box_para_2" class="form-label">{{$t("box_2_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.box_para_2"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.box_para_2" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="box_img_2" class="form-label">{{$t("box_2_image")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.box_img_2" @image-selected="(file) => handleImageSelected(file, 'box_img_2')" @image-removed="() => handleImageRemoved('box_img_2')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="box_para_3" class="form-label">{{$t("box_3_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.box_para_3"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.box_para_3" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="box_img_3" class="form-label">{{$t("box_3_image")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.box_img_3" @image-selected="(file) => handleImageSelected(file, 'box_img_3')" @image-removed="() => handleImageRemoved('box_img_3')"></ImageUp>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("drive_section")}}</h4>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="drive_heading" class="form-label">{{$t("drive_heading")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="drive_heading" v-model="form.drive_heading" />
                      <span v-for="(error, index) in errors.drive_heading" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="drive_title_1" class="form-label">{{$t("drive_title_1")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="drive_title_1" v-model="form.drive_title_1" />
                      <span v-for="(error, index) in errors.drive_title_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="drive_para_1" class="form-label">{{$t("drive_para_1")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.drive_para_1"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.drive_para_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="drive_title_2" class="form-label">{{$t("drive_title_2")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="drive_title_2" v-model="form.drive_title_2" />
                      <span v-for="(error, index) in errors.drive_title_2" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="drive_para_2" class="form-label">{{$t("drive_para_2")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.drive_para_2"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.drive_para_2" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="drive_title_3" class="form-label">{{$t("drive_title_3")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="drive_title_3" v-model="form.drive_title_3" />
                      <span v-for="(error, index) in errors.drive_title_3" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="drive_para_3" class="form-label">{{$t("drive_para_3")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.drive_para_3"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.drive_para_3" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("service_location_section")}}</h4>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="service_area_title" class="form-label">{{$t("service_location_heading")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="service_area_title" v-model="form.service_area_title" />
                      <span v-for="(error, index) in errors.service_area_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="service_area_para" class="form-label">{{$t("service_location_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.service_area_para"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.service_area_para" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="service_area_img" class="form-label">{{$t("service_location_image")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.service_area_img" @image-selected="(file) => handleImageSelected(file, 'service_area_img')" @image-removed="() => handleImageRemoved('service_area_img')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ landingHome ? 'Update' : 'Create' }}</button>
                    </div>
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
