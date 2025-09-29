<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, computed, onMounted} from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import ImageUp from "@/Components/ImageUp.vue";
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { useSharedState } from '@/composables/useSharedState';
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
    landingHeader: Object,
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
      header_logo: props.landingHeader ? props.landingHeader.header_logo || "" : "",
      home: props.landingHeader ? props.landingHeader.home || "" : "",
      aboutus: props.landingHeader ? props.landingHeader.aboutus || "" : "",
      driver: props.landingHeader ? props.landingHeader.driver || "" : "",
      user: props.landingHeader ? props.landingHeader.user || "" : "",
      contact: props.landingHeader ? props.landingHeader.contact || "" : "",
      book_now_btn: props.landingHeader ? props.landingHeader.book_now_btn || "" : "",
      footer_logo: props.landingHeader ? props.landingHeader.footer_logo || "" : "",
      footer_para: props.landingHeader ? props.landingHeader.footer_para || "" : "",
      quick_links: props.landingHeader ? props.landingHeader.quick_links || "" : "",
      compliance: props.landingHeader ? props.landingHeader.compliance || "" : "",
      privacy: props.landingHeader ? props.landingHeader.privacy || "" : "",
      terms: props.landingHeader ? props.landingHeader.terms || "" : "",
      dmv: props.landingHeader ? props.landingHeader.dmv || "" : "",
      user_app: props.landingHeader ? props.landingHeader.user_app || "" : "",
      user_play: props.landingHeader ? props.landingHeader.user_play || "" : "",
      user_play_link: props.landingHeader ? props.landingHeader.user_play_link || "" : "",
      user_apple: props.landingHeader ? props.landingHeader.user_apple || "" : "",
      user_apple_link: props.landingHeader ? props.landingHeader.user_apple_link || "" : "",
      driver_app: props.landingHeader ? props.landingHeader.driver_app || "" : "",
      driver_play: props.landingHeader ? props.landingHeader.driver_play || "" : "",
      driver_play_link: props.landingHeader ? props.landingHeader.driver_play_link || "" : "",
      driver_apple: props.landingHeader ? props.landingHeader.driver_apple || "" : "",
      driver_apple_link: props.landingHeader ? props.landingHeader.driver_apple_link || "" : "",
      copy_rights: props.landingHeader ? props.landingHeader.copy_rights || "" : "",
      fb_link: props.landingHeader ? props.landingHeader.fb_link || "" : "",
      linkdin_link: props.landingHeader ? props.landingHeader.linkdin_link || "" : "",
      x_link: props.landingHeader ? props.landingHeader.x_link || "" : "",
      insta_link: props.landingHeader ? props.landingHeader.insta_link || "" : "",
      locale: props.landingHeader ? props.landingHeader.locale || "" : "",
      language: props.landingHeader ? props.landingHeader.language || "" : "",
    });


    const validationRules = {
      header_logo: { required: true },
      home: { required: true }, 
      aboutus: { required: true }, 
      driver: { required: true },
      user: { required: true }, 
      contact: { required: true }, 
      book_now_btn: { required: true },
      footer_logo: { required: true }, 
      footer_para: { required: true }, 
      quick_links: { required: true }, 
      compliance: { required: true }, 
      privacy: { required: true }, 
      terms: { required: true }, 
      dmv: { required: true },
      user_app: { required: true }, 
      user_play: { required: true }, 
      user_play_link: { required: true }, 
      user_apple: { required: true }, 
      user_apple_link: { required: true }, 
      driver_app: { required: true }, 
      driver_play: { required: true }, 
      driver_play_link: { required: true }, 
      driver_apple: { required: true }, 
      driver_apple_link: { required: true }, 
      copy_rights: { required: true }, 
      fb_link: { required: true }, 
      linkdin_link: { required: true }, 
      x_link: { required: true }, 
      insta_link: { required: true }, 
      locale: { required: true }, 
      language: { required: true }, 
      
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
        if (props.landingHeader && props.landingHeader.id) {
          response = await axios.post(`/landing-header/update/${props.landingHeader.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });

        } else {
          response = await axios.post('/landing-header/store', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        }

        if (response.status === 201) {
          successMessage.value = t('header_footer_created_successfully');
          form.reset();
          router.get('/landing-header');
        } else {
          alertMessage.value = t('failed_to_create_header_footer');
        }
      } catch (error) {
        console.error(t('error_creating_header'), error);
        alertMessage.value = t('failed_to_create_header');
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
        const response = await axios.get('/landing-header/list');
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
      landingHeader: props.landingHeader,
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
    <Head title="Manage LandingSite-Header-Footer" />
    <PageHeader :title="landingHeader ? $t('edit') : $t('create')" :pageTitle="$t('landingSite_header_footer')" pageLink="/landing-header"/>
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
                      <h4 class="card-title mb-3">{{$t("header_section")}}</h4>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="home" class="form-label">{{$t("home")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="home" v-model="form.home" />
                      <span v-for="(error, index) in errors.home" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="home" class="form-label">{{$t("aboutus")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="home" v-model="form.aboutus" />
                      <span v-for="(error, index) in errors.home" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="driver" class="form-label">{{$t("driver")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="driver" v-model="form.driver" />
                      <span v-for="(error, index) in errors.driver" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="user" class="form-label">{{$t("user")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="user" v-model="form.user" />
                      <span v-for="(error, index) in errors.user" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="contact" class="form-label">{{$t("contact")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="contact" v-model="form.contact" />
                      <span v-for="(error, index) in errors.contact" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="book_now_btn" class="form-label">{{$t("book_now_btn")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" placeholder="Enter Link" id="book_now_btn" v-model="form.book_now_btn" />
                      <span v-for="(error, index) in errors.book_now_btn" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="header_logo" class="form-label">{{$t("header_logo")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :flexStyle="'0 0 calc(60% - 10px)'"
                      :aspectRatio="'16 / 7'" :initialImageUrl="form.header_logo" @image-selected="(file) => handleImageSelected(file, 'header_logo')" @image-removed="() => handleImageRemoved('header_logo')"></ImageUp>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("footer_section")}}</h4>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="footer_logo" class="form-label">{{$t("footer_logo")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :flexStyle="'0 0 calc(60% - 10px)'"
                      :aspectRatio="'16 / 7'" :initialImageUrl="form.footer_logo" @image-selected="(file) => handleImageSelected(file, 'footer_logo')" @image-removed="() => handleImageRemoved('footer_logo')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="footer_para" class="form-label">{{$t("footer_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.footer_para"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.footer_para" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="quick_links" class="form-label"> {{$t("quicklinks")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="quick_links" v-model="form.quick_links" />
                      <span v-for="(error, index) in errors.quick_links" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="compliance" class="form-label"> {{$t("compliance")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="compliance" v-model="form.compliance" />
                      <span v-for="(error, index) in errors.compliance" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="privacy" class="form-label"> {{$t("privacy_policy")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="privacy" v-model="form.privacy" />
                      <span v-for="(error, index) in errors.privacy" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="terms" class="form-label"> {{$t("terms_and_conditions")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="terms" v-model="form.terms" />
                      <span v-for="(error, index) in errors.terms" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="dmv" class="form-label"> {{$t("dmv_check")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="dmv" v-model="form.dmv" />
                      <span v-for="(error, index) in errors.dmv" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="user_app" class="form-label">{{$t("user_apps")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="user_app" v-model="form.user_app" />
                      <span v-for="(error, index) in errors.user_app" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="user_play" class="form-label"> {{$t("user_app_playstore")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="user_play" v-model="form.user_play" />
                      <span v-for="(error, index) in errors.user_play" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="user_play_link" class="form-label"> {{$t("user_app_playstore_link")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="url" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="user_play_link" v-model="form.user_play_link" />
                      <span v-for="(error, index) in errors.user_play_link" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="user_apple" class="form-label"> {{$t("user_app_applestore")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="user_apple" v-model="form.user_apple" />
                      <span v-for="(error, index) in errors.user_apple" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="user_apple_link" class="form-label"> {{$t("user_app_applestore_link")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="url" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="user_apple_link" v-model="form.user_apple_link" />
                      <span v-for="(error, index) in errors.user_apple_link" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="driver_app" class="form-label">{{$t("driver_apps")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="driver_app" v-model="form.driver_app" />
                      <span v-for="(error, index) in errors.driver_app" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_play" class="form-label"> {{$t("driver_app_playstore")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="driver_play" v-model="form.driver_play" />
                      <span v-for="(error, index) in errors.driver_play" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_play_link" class="form-label"> {{$t("driver_app_playstore_link")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="url" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="driver_play_link" v-model="form.driver_play_link" />
                      <span v-for="(error, index) in errors.driver_play_link" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_apple" class="form-label"> {{$t("driver_app_applestore")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="driver_apple" v-model="form.driver_apple" />
                      <span v-for="(error, index) in errors.driver_apple" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_apple_link" class="form-label"> {{$t("driver_app_applestore_link")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="url" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="driver_apple_link" v-model="form.driver_apple_link" />
                      <span v-for="(error, index) in errors.driver_apple_link" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="copy_rights" class="form-label">{{$t("copy_rights")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="copy_rights" v-model="form.copy_rights" />
                      <span v-for="(error, index) in errors.copy_rights" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="fb_link" class="form-label"> {{$t("facebook_link")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="fb_link" v-model="form.fb_link" />
                      <span v-for="(error, index) in errors.fb_link" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="linkdin_link" class="form-label"> {{$t("linkedin_link")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="linkdin_link" v-model="form.linkdin_link" />
                      <span v-for="(error, index) in errors.linkdin_link" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="x_link" class="form-label"> {{$t("x_twitter_link")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="x_link" v-model="form.x_link" />
                      <span v-for="(error, index) in errors.x_link" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="insta_link" class="form-label"> {{$t("instagram_link")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="insta_link" v-model="form.insta_link" />
                      <span v-for="(error, index) in errors.insta_link" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ landingHeader ? $t('update') : $t('create') }}</button>
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
