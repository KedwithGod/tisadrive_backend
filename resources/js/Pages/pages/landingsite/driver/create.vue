<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed,onMounted,watch} from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import ImageUp from "@/Components/ImageUp.vue";
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { useSharedState } from '@/composables/useSharedState';
import { useI18n } from 'vue-i18n';
import Swal from "sweetalert2";

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
    app_for: String,
    landingDriver: Object,
    validate: Function,
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
      hero_title: props.landingDriver ? props.landingDriver.hero_title || "" : "",
      driver_heading_1: props.landingDriver ? props.landingDriver.driver_heading_1 || "" : "",
      driver_para: props.landingDriver ? props.landingDriver.driver_para || "" : "",
      driver_img_1: props.landingDriver ? props.landingDriver.driver_img_1 || "" : "",
      driver_title_1: props.landingDriver ? props.landingDriver.driver_title_1 || "" : "",
      driver_para_1: props.landingDriver ? props.landingDriver.driver_para_1 || "" : "",
      driver_img_2: props.landingDriver ? props.landingDriver.driver_img_2 || "" : "",
      driver_title_2: props.landingDriver ? props.landingDriver.driver_title_2 || "" : "",
      driver_para_2: props.landingDriver ? props.landingDriver.driver_para_2 || "" : "",
      driver_img_3: props.landingDriver ? props.landingDriver.driver_img_3 || "" : "",
      driver_title_3: props.landingDriver ? props.landingDriver.driver_title_3 || "" : "",
      driver_para_3: props.landingDriver ? props.landingDriver.driver_para_3 || "" : "",
      how_it_work_heading: props.landingDriver ? props.landingDriver.how_it_work_heading || "" : "",
      how_it_work_title_1: props.landingDriver ? props.landingDriver.how_it_work_title_1 || "" : "",
      how_it_work_para_1: props.landingDriver ? props.landingDriver.how_it_work_para_1 || "" : "",
      how_it_work_img_1: props.landingDriver ? props.landingDriver.how_it_work_img_1 || "" : "",
      how_it_work_title_2: props.landingDriver ? props.landingDriver.how_it_work_title_2 || "" : "",
      how_it_work_para_2: props.landingDriver ? props.landingDriver.how_it_work_para_2 || "" : "",
      how_it_work_img_2: props.landingDriver ? props.landingDriver.how_it_work_img_2 || "" : "",
      how_it_work_title_3: props.landingDriver ? props.landingDriver.how_it_work_title_3 || "" : "",
      how_it_work_para_3: props.landingDriver ? props.landingDriver.how_it_work_para_3 || "" : "",
      how_it_work_img_3: props.landingDriver ? props.landingDriver.how_it_work_img_3 || "" : "",
      how_it_work_title_4: props.landingDriver ? props.landingDriver.how_it_work_title_4 || "" : "",
      how_it_work_para_4: props.landingDriver ? props.landingDriver.how_it_work_para_4 || "" : "",
      how_it_work_img_4: props.landingDriver ? props.landingDriver.how_it_work_img_4 || "" : "",
      how_it_work_title_5: props.landingDriver ? props.landingDriver.how_it_work_title_5 || "" : "",
      how_it_work_para_5: props.landingDriver ? props.landingDriver.how_it_work_para_5 || "" : "",
      how_it_work_img_5: props.landingDriver ? props.landingDriver.how_it_work_img_5 || "" : "",
      how_it_work_title_6: props.landingDriver ? props.landingDriver.how_it_work_title_6 || "" : "",
      how_it_work_para_6: props.landingDriver ? props.landingDriver.how_it_work_para_6 || "" : "",
      how_it_work_img_6: props.landingDriver ? props.landingDriver.how_it_work_img_6 || "" : "",
      how_it_work_title_7: props.landingDriver ? props.landingDriver.how_it_work_title_7 || "" : "",
      how_it_work_para_7: props.landingDriver ? props.landingDriver.how_it_work_para_7 || "" : "",
      how_it_work_img_7: props.landingDriver ? props.landingDriver.how_it_work_img_7 || "" : "",
      req_heading: props.landingDriver ? props.landingDriver.req_heading || "" : "",
      req_title: props.landingDriver ? props.landingDriver.req_title || "" : "",
      req_lists: props.landingDriver ? props.landingDriver.req_lists || "" : "",
      req_img: props.landingDriver ? props.landingDriver.req_img || "" : "",
      vechile_req_title: props.landingDriver ? props.landingDriver.vechile_req_title || "" : "",
      vechile_req_lists: props.landingDriver ? props.landingDriver.vechile_req_lists || "" : "",
      vechile_req_img: props.landingDriver ? props.landingDriver.vechile_req_img || "" : "",
      doc_req_title: props.landingDriver ? props.landingDriver.doc_req_title || "" : "",
      doc_req_lists: props.landingDriver ? props.landingDriver.doc_req_lists || "" : "",
      doc_req_img: props.landingDriver ? props.landingDriver.doc_req_img || "" : "",
      locale: props.landingDriver ? props.landingDriver.locale || "" : "",
      language: props.landingDriver ? props.landingDriver.language || "" : "",
    });


    const validationRules = {
            hero_title: { required: true }  ,
            driver_heading_1: { required: true } ,
            driver_para : { required: true }  ,
            driver_img_1 : { required: true }  ,
            driver_title_1  : { required: true } ,
            driver_para_1 : { required: true }  ,
            driver_img_2 : { required: true } ,
            driver_title_2 : { required: true }  ,
            driver_para_2 : { required: true } ,
            driver_img_3 : { required: true }  ,
            driver_title_3 : { required: true }  ,
            driver_para_3 : { required: true } ,
            how_it_work_heading : { required: true } ,
            how_it_work_title_1 : { required: true }  ,
            how_it_work_para_1  : { required: true } ,
            how_it_work_img_1 : { required: true } ,
            how_it_work_title_2 : { required: true }  ,
            how_it_work_para_2  : { required: true }, 
            how_it_work_img_2 : { required: true },
            how_it_work_title_3 : { required: true },
            how_it_work_para_3 : { required: true },
            how_it_work_img_3 : { required: true },
            how_it_work_title_4 : { required: true },
            how_it_work_para_4 : { required: true },
            how_it_work_img_4 : { required: true },
            how_it_work_title_5: { required: true },
            how_it_work_para_5 : { required: true },
            how_it_work_img_5 : { required: true },
            how_it_work_title_6 : { required: true },
            how_it_work_para_6 : { required: true },
            how_it_work_img_6 : { required: true },
            how_it_work_title_7 : { required: true },
            how_it_work_para_7 : { required: true },
            how_it_work_img_7 : { required: true },
            req_heading : { required: true },
            req_title : { required: true },
            req_list : { required: true },
            req_img : { required: true },
            vechile_req_title : { required: true },
            vechile_req_lists : { required: true },
            vechile_req_img : { required: true },
            doc_req_title : { required: true },
            doc_req_lists : { required: true },
            doc_req_img : { required: true },
            locale : { required: true },
            language : { required: true },
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
        if (props.landingDriver && props.landingDriver.id) {
          response = await axios.post(`/landing-driver/update/${props.landingDriver.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });

        } else {
          response = await axios.post('/landing-driver/store', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        }

        if (response.status === 201 ) {
          successMessage.value = t('driver_created_successfully');
          form.reset();
          router.get('/landing-driver');
        } else {
          alertMessage.value = t('failed_to_create_driver');
        }
      } catch (error) {
        console.error(t('error_creating_driver'), error);
        alertMessage.value = t('failed_to_create_driver_catch');
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
        const response = await axios.get('/landing-driver/list');
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
        (lang) => lang.label === form.language
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
      landingDriver: props.landingDriver,
      iconUrl,
      handleImageSelected,
      handleImageRemoved,
      languages,
      selectlanguages,
    };
  }
};
</script>

<template>
  <Layout>
    <Head title="Manage LandingSite-Driver" />
    <PageHeader :title="landingDriver ? $t('edit') : $t('create')" :pageTitle="$t('landing_driver')" pageLink="/landing-driver"/>
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
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="language" class="form-label">{{$t("languages")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select :disabled="app_for === 'demo'" id="language" class="form-select" v-model="form.language" @change="selectlanguages">
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
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="hero_title" class="form-label">{{$t("hero_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="hero_title" v-model="form.hero_title" />
                      <span v-for="(error, index) in errors.hero_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_heading_1" class="form-label">{{$t("driver_heading")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="driver_heading_1" v-model="form.driver_heading_1" />
                      <span v-for="(error, index) in errors.driver_heading_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_para" class="form-label">{{$t("driver_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.driver_para"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.driver_para" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("box_section")}}</h4>
                  </div>
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="driver_title_1" class="form-label">{{$t("driver_title_1")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="driver_title_1" v-model="form.driver_title_1" />
                      <span v-for="(error, index) in errors.driver_title_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_para_1" class="form-label">{{$t("driver_para_1")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.driver_para_1"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.driver_para_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_img_1" class="form-label">{{$t("driver_image_1")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.driver_img_1" @image-selected="(file) => handleImageSelected(file, 'driver_img_1')" @image-removed="() => handleImageRemoved('driver_img_1')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="driver_title_2" class="form-label">{{$t("driver_title_2")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" placeholder="Enter Hero Title" id="driver_title_2" v-model="form.driver_title_2" />
                      <span v-for="(error, index) in errors.driver_title_2" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_para_2" class="form-label">{{$t("driver_para_2")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.driver_para_2"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.driver_para_2" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_img_2" class="form-label">{{$t("driver_image_2")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.driver_img_2" @image-selected="(file) => handleImageSelected(file, 'driver_img_2')" @image-removed="() => handleImageRemoved('driver_img_2')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="driver_title_3" class="form-label">{{$t("driver_title_3")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" placeholder="Enter Hero Title" id="driver_title_3" v-model="form.driver_title_3" />
                      <span v-for="(error, index) in errors.driver_title_3" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_para_3" class="form-label">{{$t("driver_para_3")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.driver_para_3"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.driver_para_3" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="driver_img_3" class="form-label">{{$t("driver_image_3")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.driver_img_3" @image-selected="(file) => handleImageSelected(file, 'driver_img_3')" @image-removed="() => handleImageRemoved('driver_img_3')"></ImageUp>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("how_it_works_section")}}</h4>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_heading" class="form-label">{{$t("how_it_works_heading")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="how_it_work_heading" v-model="form.how_it_work_heading" />
                      <span v-for="(error, index) in errors.how_it_work_heading" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>   
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_title_1" class="form-label">{{$t("how_it_work_title_1")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="how_it_work_title_1" v-model="form.how_it_work_title_1" />
                      <span v-for="(error, index) in errors.how_it_work_title_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div> 
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_para_1" class="form-label">{{$t("how_it_work_para_1")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.how_it_work_para_1"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.how_it_work_para_1" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_img_1" class="form-label">{{$t("how_it_work_image_1")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.how_it_work_img_1" @image-selected="(file) => handleImageSelected(file, 'how_it_work_img_1')" @image-removed="() => handleImageRemoved('how_it_work_img_1')"></ImageUp>
                    </div>
                  </div>     
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_title_2" class="form-label">{{$t("how_it_work_title_2")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="how_it_work_title_2" v-model="form.how_it_work_title_2" />
                      <span v-for="(error, index) in errors.how_it_work_title_2" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div> 
                  
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_para_2" class="form-label">{{$t("how_it_work_para_2")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.how_it_work_para_2"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.how_it_work_para_2" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_img_2" class="form-label">{{$t("how_it_work_image_2")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.how_it_work_img_2" @image-selected="(file) => handleImageSelected(file, 'how_it_work_img_2')" @image-removed="() => handleImageRemoved('how_it_work_img_2')"></ImageUp>
                    </div>
                  </div>  
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_title_3" class="form-label">{{$t("how_it_work_title_3")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="how_it_work_title_3" v-model="form.how_it_work_title_3" />
                      <span v-for="(error, index) in errors.how_it_work_title_3" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>  
                  
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_para_3" class="form-label">{{$t("how_it_work_para_3")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.how_it_work_para_3"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.how_it_work_para_3" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_img_3" class="form-label">{{$t("how_it_work_image_3")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.how_it_work_img_3" @image-selected="(file) => handleImageSelected(file, 'how_it_work_img_3')" @image-removed="() => handleImageRemoved('how_it_work_img_3')"></ImageUp>
                    </div>
                  </div>  
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_title_4" class="form-label">{{$t("how_it_work_title_4")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" placeholder="Enter Hero Title" id="how_it_work_title_4" v-model="form.how_it_work_title_4" />
                      <span v-for="(error, index) in errors.how_it_work_title_4" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div> 
                  
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_para_4" class="form-label">{{$t("how_it_work_para_4")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.how_it_work_para_4"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.how_it_work_para_4" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_img_4" class="form-label">{{$t("how_it_work_image_4")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.how_it_work_img_4" @image-selected="(file) => handleImageSelected(file, 'how_it_work_img_4')" @image-removed="() => handleImageRemoved('how_it_work_img_4')"></ImageUp>
                    </div>
                  </div>  
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_title_5" class="form-label">{{$t("how_it_work_title_5")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="how_it_work_title_5" v-model="form.how_it_work_title_5" />
                      <span v-for="(error, index) in errors.how_it_work_title_5" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div> 
                  
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_para_5" class="form-label">{{$t("how_it_work_para_5")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.how_it_work_para_5"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.how_it_work_para_5" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_img_5" class="form-label">{{$t("how_it_work_image_5")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.how_it_work_img_5" @image-selected="(file) => handleImageSelected(file, 'how_it_work_img_5')" @image-removed="() => handleImageRemoved('how_it_work_img_5')"></ImageUp>
                    </div>
                  </div>    
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_title_6" class="form-label">{{$t("how_it_work_title_6")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="how_it_work_title_6" v-model="form.how_it_work_title_6" />
                      <span v-for="(error, index) in errors.how_it_work_title_6" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>  
                  
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_para_6" class="form-label">{{$t("how_it_work_para_6")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.how_it_work_para_6"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.how_it_work_para_6" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_img_6" class="form-label">{{$t("how_it_work_image_6")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.how_it_work_img_6" @image-selected="(file) => handleImageSelected(file, 'how_it_work_img_6')" @image-removed="() => handleImageRemoved('how_it_work_img_6')"></ImageUp>
                    </div>
                  </div>   
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_title_7" class="form-label">{{$t("how_it_work_title_7")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="how_it_work_title_7" v-model="form.how_it_work_title_7" />
                      <span v-for="(error, index) in errors.how_it_work_title_7" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div> 
                  
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_para_7" class="form-label">{{$t("how_it_work_para_7")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.how_it_work_para_7"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.how_it_work_para_7" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="how_it_work_img_7" class="form-label">{{$t("how_it_work_image_7")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.how_it_work_img_7" @image-selected="(file) => handleImageSelected(file, 'how_it_work_img_7')" @image-removed="() => handleImageRemoved('how_it_work_img_7')"></ImageUp>
                    </div>
                  </div>                                
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("requirements_section")}}</h4>
                  </div>                  
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="req_heading" class="form-label">{{$t("requirement_heading")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="req_heading" v-model="form.req_heading" />
                      <span v-for="(error, index) in errors.req_heading" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="req_title" class="form-label">{{$t("requirement_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="req_title" v-model="form.req_title" />
                      <span v-for="(error, index) in errors.req_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="req_lists" class="form-label">{{$t("requirement_lists")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.req_lists"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.req_lists" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="req_img" class="form-label">{{$t("requirement_image")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.req_img" @image-selected="(file) => handleImageSelected(file, 'req_img')" @image-removed="() => handleImageRemoved('req_img')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="vechile_req_title" class="form-label">{{$t("vechile_requirement_title")}}</label>
                      <span class="text-danger">*</span>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="vechile_req_title" v-model="form.vechile_req_title" />
                      <span v-for="(error, index) in errors.vechile_req_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="vechile_req_lists" class="form-label">{{$t("vechile_requirement_lists")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.vechile_req_lists"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.vechile_req_lists" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="vechile_req_img" class="form-label">{{$t("vechile_requirement_image")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.vechile_req_img" @image-selected="(file) => handleImageSelected(file, 'vechile_req_img')" @image-removed="() => handleImageRemoved('vechile_req_img')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-sm-12 mt-3">
                    <div class="mb-3">
                      <label for="doc_req_title" class="form-label">{{$t("document_requirement_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="doc_req_title" v-model="form.doc_req_title" />
                      <span v-for="(error, index) in errors.doc_req_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="doc_req_lists" class="form-label">{{$t("document_requirement_lists")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.doc_req_lists"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.doc_req_lists" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="doc_req_img" class="form-label">{{$t("document_requirement_image")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.doc_req_img" @image-selected="(file) => handleImageSelected(file, 'doc_req_img')" @image-removed="() => handleImageRemoved('doc_req_img')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ landingDriver ? $t('update') : $t('create') }}</button>
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
