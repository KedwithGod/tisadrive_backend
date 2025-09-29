<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed,onMounted,watch,reactive} from "vue";
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
import { debounce } from 'lodash';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    app_for: String,
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
    landingAbouts: Object,
    validate: Function,
    languages: {
      type: Array,
      required: true
    },
  },
  setup(props) {
    const fields = ref(props.landingAbouts && props.landingAbouts.team_members
            ? JSON.parse(props.landingAbouts.team_members) // Ensure it's parsed to an array
            : [{ team_member_name: "", team_member_posision: "", team_member_image: "" }]
        );

    const fields_testinomial = ref(props.landingAbouts && props.landingAbouts.testimonial_content
        ? JSON.parse(props.landingAbouts.testimonial_content) // Ensure it's parsed to an array
        : [{ testimonial_para: "", testimonial_title_1: "", testimonial_title_2: "" }]
    );
    const { t } = useI18n();
    const { languages } = useSharedState();
    const storedLanguages = ref([]); 
    const form = useForm({
      hero_title: props.landingAbouts ? props.landingAbouts.hero_title || "" : "",
      about_heading: props.landingAbouts ? props.landingAbouts.about_heading || "" : "",
      about_title: props.landingAbouts ? props.landingAbouts.about_title || "" : "",
      about_para: props.landingAbouts ? props.landingAbouts.about_para || "" : "",
      about_lists: props.landingAbouts ? props.landingAbouts.about_lists || "" : "",
      about_img: props.landingAbouts ? props.landingAbouts.about_img || "" : "",
      ceo_name: props.landingAbouts ? props.landingAbouts.ceo_name || "" : "",
      ceo_title: props.landingAbouts ? props.landingAbouts.ceo_title || "" : "",
      ceo_para: props.landingAbouts ? props.landingAbouts.ceo_para || "" : "",
      ceo_img: props.landingAbouts ? props.landingAbouts.ceo_img || "" : "",
      signature: props.landingAbouts ? props.landingAbouts.signature || "" : "",
      vision_mision_heading: props.landingAbouts ? props.landingAbouts.vision_mision_heading || "" : "",
      vision_title: props.landingAbouts ? props.landingAbouts.vision_title || "" : "",
      vision_para: props.landingAbouts ? props.landingAbouts.vision_para || "" : "",
      mission_title: props.landingAbouts ? props.landingAbouts.mission_title || "" : "",
      mission_para: props.landingAbouts ? props.landingAbouts.mission_para || "" : "",
      team_title: props.landingAbouts ? props.landingAbouts.team_title || "" : "",
      team_para: props.landingAbouts ? props.landingAbouts.team_para || "" : "",
      testimonial_heading: props.landingAbouts ? props.landingAbouts.testimonial_heading || "": "",
      locale: props.landingAbouts ? props.landingAbouts.locale || "" : "",
      language: props.landingAbouts ? props.landingAbouts.language || "" : "",
    });


    const validationRules = {
            hero_title: { required: true }  ,
            about_heading: { required: true } ,
            about_title : { required: true }  ,
            about_para : { required: true }  ,
            about_lists  : { required: true } ,
            about_img : { required: true }  ,
            ceo_name : { required: true } ,
            ceo_title : { required: true }  ,
            ceo_para : { required: true } ,
            ceo_img : { required: true }  ,
            signature : { required: true }  ,
            vision_mision_heading : { required: true }  ,
            driver_para_3 : { required: true } ,
            vision_title : { required: true } ,
            vision_para : { required: true }  ,
            mission_title  : { required: true } ,
            mission_para : { required: true } ,
            team_title : { required: true }  ,
            team_para  : { required: true }, 
            team_members : { required: true },
            testimonial_heading : { required: true },
            testimonial_content : { required: true },
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

        fields.value.forEach((member, index) => {
        formData.append(`team_members[${index}][team_member_name]`, member.team_member_name);
        formData.append(`team_members[${index}][team_member_posision]`, member.team_member_posision);

       if (member.team_member_image) {
          formData.append(`team_members[${index}][team_member_image]`, member.team_member_image);
        } else {
          console.log(`No image for team member at index ${index}`);
        }
      });

      fields_testinomial.value.forEach((member, index) => {
        formData.append(`testimonial_content[${index}][testimonial_para]`, member.testimonial_para);
        formData.append(`testimonial_content[${index}][testimonial_title_1]`, member.testimonial_title_1);
        formData.append(`testimonial_content[${index}][testimonial_title_2]`, member.testimonial_title_2);
      });
        if (props.landingAbouts && props.landingAbouts.id) {
          response = await axios.post(`/landing-aboutus/update/${props.landingAbouts.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });

        } else {
          response = await axios.post('/landing-aboutus/store', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        }

        if (response.status === 201 ) {
          successMessage.value = t('aboutus_created_successfully');
          form.reset();
          router.get('/landing-aboutus');
        } else {
          alertMessage.value = t('failed_to_create_aboutus');
        }
      } catch (error) {
        console.error(t('error_creating_driver'), error);
        alertMessage.value = t('failed_to_create_aboutus_catch');
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
        const response = await axios.get('/landing-aboutus/list');
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

      const handleImageSelected = (file, fieldName,index) => {
      if(props.app_for == "demo"){
          Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
          return;
      }
        // form[fieldName] = file;
        if (fieldName === 'team_member_image') {
            fields.value[index].team_member_image = file;
        } else if (fieldName === 'about_img') {
            form.about_img = file; // Assuming form.about_img is set directly
        } else if (fieldName === 'ceo_img') {
            form.ceo_img = file; // Assuming form.ceo_img is set directly
        }
        else if (fieldName === 'signature') {
            form.signature = file; // Assuming form.ceo_img is set directly
        }
      };

    const handleImageRemoved = (fieldName,index) => {
      if(props.app_for == "demo"){
          Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
          return;
      }
      // form[fieldName] = null;
      if (fieldName === 'team_member_image') {
            fields.value[index].team_member_image = null;
        } else if (fieldName === 'about_img') {
            form.about_img = null; // Assuming form.about_img is set directly
        } else if (fieldName === 'ceo_img') {
            form.ceo_img = null; // Assuming form.ceo_img is set directly
        }
        else if (fieldName === 'signature') {
            form.signature = null; // Assuming form.ceo_img is set directly
        }
    };

    const selectlanguages = async () =>{
      const selectedLanguage = languages.value.find(
        (lang) => lang.label === form.language
      );
        form.language = selectedLanguage.label;
        form.locale = selectedLanguage.code;
        form.direction = selectedLanguage.direction;
    }


    const addTeamMember =  debounce(() =>{
      if(props.app_for == "demo"){
          Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
          return;
      }
      fields.value.push({
        team_member_name: '',
        team_member_posision: '',
        team_member_image: '',
      });
    }, 300);
   const  removeTeamMember = (index) =>{
      if(props.app_for == "demo"){
          Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
          return;
      }
    fields.value.splice(index, 1);
    }

    const addTestimonial =  debounce(() =>{
      if(props.app_for == "demo"){
          Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
          return;
      }
      fields_testinomial.value.push({
        testimonial_para: '',
        testimonial_title_1: '',
        testimonial_title_2: '',
      });
    }, 300);
   const  removeTestimonial = (index) =>{
      if(props.app_for == "demo"){
          Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
          return;
      }
    fields_testinomial.value.splice(index, 1);
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
      landingAbouts: props.landingAbouts,
      iconUrl,
      handleImageSelected,
      handleImageRemoved,
      languages,
      selectlanguages,
      removeTeamMember,
      addTeamMember,
      fields,
      fields_testinomial,
      addTestimonial,
      removeTestimonial
    };
  },

};
</script>

<template>
  <Layout>
    <Head title="Manage LandingSite-Driver" />
    <PageHeader :title="landingAbouts ? $t('edit') : $t('create')" :pageTitle="$t('landing_aboutus')" pageLink="/landing-aboutus"/>
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
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="hero_title" v-model="form.hero_title" />
                      <span v-for="(error, index) in errors.hero_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>


                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("about_section")}}</h4>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="about_heading" class="form-label">{{$t("about_heading")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="about_heading" v-model="form.about_heading" />
                      <span v-for="(error, index) in errors.about_heading" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="about_title" class="form-label">{{$t("about_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="about_title" v-model="form.about_title" />
                      <span v-for="(error, index) in errors.about_title" :key="index" class="text-danger">
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
                      <label for="about_lists" class="form-label">{{$t("about_lists")}}
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
                      <label for="about_img" class="form-label">{{$t("about_image")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.about_img" @image-selected="(file) => handleImageSelected(file, 'about_img')" @image-removed="() => handleImageRemoved('about_img')"></ImageUp>
                    </div>
                  </div>

                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("ceo_section")}}</h4>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="ceo_name" class="form-label">{{$t("ceo_name")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="ceo_name" v-model="form.ceo_name" />
                      <span v-for="(error, index) in errors.ceo_name" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="ceo_title" class="form-label">{{$t("ceo_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="ceo_title" v-model="form.ceo_title" />
                      <span v-for="(error, index) in errors.ceo_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="ceo_para" class="form-label">{{$t("ceo_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.ceo_para"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.ceo_para" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>                  
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="ceo_img" class="form-label">{{$t("ceo_img")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :initialImageUrl="form.ceo_img" @image-selected="(file) => handleImageSelected(file, 'ceo_img')" @image-removed="() => handleImageRemoved('ceo_img')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label  for="signature" class="form-label d-flex">{{$t("signature")}}
                        <span><h5 class="text-muted mt-1 mb-0 fs-13">(100px x 40px)</h5></span> 
                        <span class="text-danger">*</span></label>
                      
                      <ImageUp :initialImageUrl="form.signature" :flexStyle="'0 0 calc(50% - 20px)'" :aspectRatio="'5 / 2'"  @image-selected="(file) => handleImageSelected(file, 'signature')" @image-removed="() => handleImageRemoved('signature')"></ImageUp>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("vision_mission_section")}}</h4>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="vision_mision_heading" class="form-label">{{$t("vision_mision_heading")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="vision_mision_heading" v-model="form.vision_mision_heading" />
                      <span v-for="(error, index) in errors.vision_mision_heading" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="vision_title" class="form-label">{{$t("vision_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="vision_title" v-model="form.vision_title" />
                      <span v-for="(error, index) in errors.vision_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="vision_para" class="form-label">{{$t("vision_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.vision_para"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.vision_para" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="mission_title" class="form-label">{{$t("mission_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="mission_title" v-model="form.mission_title" />
                      <span v-for="(error, index) in errors.mission_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="mission_para" class="form-label">{{$t("mission_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.mission_para"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.mission_para" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("our_team_section")}}</h4>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="team_title" class="form-label">{{$t("team_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="team_title" v-model="form.team_title" />
                      <span v-for="(error, index) in errors.team_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="team_para" class="form-label">{{$t("team_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.team_para"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.team_para" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("box_section")}}</h4>
                  </div>
                  <div  class="d-flex align-items-center gap-2 ">                  
                    <button type="button" class="btn btn-primary  mb-3" @click="addTeamMember()">
                      <i class="ri-add-line align-bottom me-1"></i>{{$t("add_team_member")}}
                    </button>                    
                  </div>                   
                  <div v-for="(teamMember, index) in fields" :key="index" class="row mt-3">
                    <div class="col-sm-6">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="mb-3">
                            <label for="team_member_name" class="form-label">{{$t("team_member_name")}}
                              <span class="text-danger">*</span>
                            </label>
                            <input type="text" :readonly="app_for === 'demo'" class="form-control" id="team_member_name" :placeholder="$t('enter_hero_title')" v-model="teamMember.team_member_name" />
                            <span v-for="(error, index) in errors.team_member_name" :key="index" class="text-danger">{{ error }}</span>
                          </div>                          
                        </div>
                        <div class="col-sm-12">
                        <div class="mb-3 mt-5">
                            <label for="team_member_posision" class="form-label">{{$t("team_member_position")}}
                              <span class="text-danger">*</span>
                            </label>
                            <input type="text" :readonly="app_for === 'demo'" class="form-control" id="team_member_posision" :placeholder="$t('enter_hero_title')" v-model="teamMember.team_member_posision" />
                            <span v-for="(error, index) in errors.team_member_posision" :key="index" class="text-danger">{{ error }}</span>
                        </div>
                    </div>
                      </div>
                    </div>                        
                    <div class="col-sm-1"></div>                
                    <div class="col-sm-4">
                        <div class="mb-3">
                            <label for="team_member_image" class="form-label">{{$t("team_member_image")}}
                              <span class="text-danger">*</span>
                            </label>
                            <ImageUp :initialImageUrl="teamMember.team_member_image"  @image-selected="(file) => handleImageSelected(file,'team_member_image',index)" 
                              @image-removed="() => handleImageRemoved('team_member_image',index)"/>
                              <span v-for="(error, index) in errors.team_member_image" :key="index" class="text-danger">{{ error }}</span>
                        </div>
                    </div>  
                    <div class="col-sm-1">
                      <div class="mb-3">
                        <div class="form-check form-check-inline">
                          <i class="bx bx-trash text-danger fs-22 btn" @click="removeTeamMember(index)"></i>
                        </div>
                      </div>
                    </div>                               
                  </div>                              

                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("testnimonial")}}</h4>
                  </div>
                  <div class="col-sm-12 mt-3">
                      <div class="mb-3">
                        <label for="testimonial_heading" class="form-label">{{$t("testimonial_heading")}}
                          <span class="text-danger">*</span>
                        </label>
                        <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="testimonial_heading" v-model="form.testimonial_heading" />
                        <span v-for="(error, index) in errors.testimonial_heading" :key="index" class="text-danger">
                          {{ error }}
                        </span>
                      </div>
                    </div>
                  <div  class="d-flex align-items-center gap-2 ">                  
                    <button type="button" class="btn btn-primary  mb-3" @click="addTestimonial()">
                      <i class="ri-add-line align-bottom me-1"></i>{{$t("add_testimonial_content")}}
                    </button>                    
                  </div>  
                  <div v-for="(teamMember, index) in fields_testinomial" :key="index" class="row mt-3">
                    <div class="col-sm-5 mt-3">
                      <div class="mb-3">
                        <label for="testimonial_para" class="form-label">{{$t("testimonial_para")}}
                          <span class="text-danger">*</span>
                        </label>
                        <ckeditor :disabled="app_for === 'demo'" v-model="teamMember.testimonial_para"  :editor="editor"></ckeditor>
                        <span v-for="(error, index) in errors.testimonial_para" :key="index" class="text-danger">
                          {{ error }}
                        </span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="row">
                        <div class="col-sm-12 mt-3">
                      <div class="mb-3">
                        <label for="testimonial_title_1" class="form-label">{{$t("testimonial_title_1")}}
                          <span class="text-danger">*</span>
                        </label>
                        <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="testimonial_title_1" v-model="teamMember.testimonial_title_1" />
                        <span v-for="(error, index) in errors.testimonial_title_1" :key="index" class="text-danger">
                          {{ error }}
                        </span>
                      </div>
                    </div>
                    <div class="col-sm-12 mt-3">
                      <div class="mb-3 mt-5">
                        <label for="testimonial_title_2" class="form-label">{{$t("testimonial_title_2")}}
                          <span class="text-danger">*</span>
                        </label>
                        <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_hero_title')" id="testimonial_title_2" v-model="teamMember.testimonial_title_2" />
                        <span v-for="(error, index) in errors.testimonial_title_2" :key="index" class="text-danger">
                          {{ error }}
                        </span>
                      </div>
                    </div>
                      </div>
                    </div>                    
                    <div class="col-sm-1">
                        <div class="mb-3 mt-5">
                          <div class="form-check form-check-inline">
                            <i class="bx bx-trash text-danger fs-22 btn" @click="removeTestimonial(index)"></i>
                          </div>
                        </div>
                      </div>  
                    </div>                 
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ landingAbouts ? $t('update') : $t('create') }}</button>
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
