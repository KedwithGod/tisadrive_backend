<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import FormValidation from "@/Components/FormValidation.vue";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import ImageUp from "@/Components/ImageUp.vue";
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { useI18n } from 'vue-i18n';
import Swal from "sweetalert2";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable

export default {
    methods: {
        stripHtmlTags(content) {
            const parser = new DOMParser();
            const parsedContent = parser.parseFromString(content, 'text/html');
            return parsedContent.body.textContent || "";
            },
    },
  components: {
    Layout,
    PageHeader,
    Head,
    FormValidation,
    imageUpload,
    ImageUp,
    ckeditor: CKEditor.component,
  },
  props: {
    successMessage: String,
    alertMessage: String,
    app_for: String,
    onboarding: Object,
  },
  setup(props) {
    const { t } = useI18n();
    const { languages, fetchData } = useSharedState(); // Destructure the shared state
    const activeTab = ref('English');
    const form = useForm({
      // id: props.onboarding ? props.onboarding.id || "" : "",
      // title: props.onboarding ? props.onboarding.title || "" : "",
      // description: props.onboarding ? props.onboarding.description || "" : "",
      onboarding_image: props.onboarding ? props.onboarding.onboarding_image || "" : "",
      
      title: Object.keys(props.onboarding.languageFields).reduce((acc, langCode) => {
        acc[langCode] = props.onboarding.languageFields[langCode]?.title || '';
        return acc;
      }, {}),
      description: Object.keys(props.onboarding.languageFields).reduce((acc, langCode) => {
        acc[langCode] = props.onboarding.languageFields[langCode]?.description || '';
        return acc;
      }, {}),
    });

    const validationRules = {
      // title: { required: true },
      // description: { required: true },
      onboarding_image: { required: true },
    };

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };
    const setActiveTab = (tab) => {
      activeTab.value = tab;
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

    const handleSubmit = async () => {
      if(props.app_for == "demo"){
          Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
          return;
      }
      try {
        
      const formData = new FormData();
        for (const key in form) {
          if (key === 'iconFile' && form[key]) {
            formData.append('onboarding_image', form[key]);
          } else {
            formData.append(key, form[key]);
          }
        }
        let response;
        Object.keys(form.title).forEach(langCode => {
          formData.append(`title[${langCode}]`, form.title[langCode]);
        });
        Object.keys(form.description).forEach(langCode => {
          formData.append(`description[${langCode}]`, form.description[langCode]);
        });
        // if (props.onboarding && props.onboarding.id) {
        //   response = await axios.post(`/onboarding-screen/update/${props.onboarding.id}`, formData);
        // } 
        if (props.onboarding && props.onboarding.id) {
          response = await axios.post(`/onboarding-screen/update/${props.onboarding.id}`, formData, {
            headers: {
             'Content-Type': 'multipart/form-data',
            },
          });
          console.log("response",response)

        } 

        if (response.status === 201) {
          successMessage.value = t('onboarding_saved_successfully');
          form.reset();
          router.get('/onboarding-screen'); // Use router.push instead of router.get
        } else {
          alertMessage.value = t('failed_to_save_onboarding_screen');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_saving_onboarding'), error);
          alertMessage.value = t('failed_to_save_onboarding_screen');
        }
      }
    };

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

    const iconUrl = props.onboarding ? props.onboarding.icon :null;

    onMounted(async () => {
      if (Object.keys(languages).length == 0) {
        await fetchData();
      }
    });

    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      validationRef,
      errors,      
      handleImageSelected,
      handleImageRemoved,      
      iconUrl,
      validateIconSize,
      editor: ClassicEditor,
      editorData: "",
      languages,
      setActiveTab,
      activeTab,
    };
  }
};
</script>

<template>
  <Layout>
    <Head title="Onboarding" />
    <PageHeader :title="$t('edit')" :pageTitle="$t('onboarding_screen')" pageLink="/onboarding-screen"/>
    <BRow>
      <BCard v-if="app_for === 'demo'" no-body id="tasksList">
          <BCardHeader class="border-0">
              <div class="alert bg-warning border-warning fs-18" role="alert">
                  <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
              </div>
          </BCardHeader>
      </BCard>
      <BCol lg="6">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"><h5>{{$t("mobile_view")}}</h5></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <div class="col-sm-12">
              <div class="mb-3" style="display: grid;place-items:center;">                      
                <div class="onboardingScreen">                 
                  <div class="overlap">
                    <div>
                      <img  :src="`/storage/uploads/onboarding/${form.onboarding_image}`" class="onboardingImage"/>
                    </div>
                    <div class="card cards" style="background-color: white;">
                      <h6 class="text-center text-black" v-if=" onboarding && onboarding.title">{{ onboarding.title }}</h6> 
                      <p class="fs-10 text-center text-black" v-if=" onboarding && onboarding.description"><div v-html="onboarding.description"></div></p>
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
      <BCol lg="6">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
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
                <div class="row mt-3">  
                  <div class="tab-content text-muted" v-for="language in languages" :key="language.code">
                        <div v-if="activeTab === language.label" class="tab-pane active show" :id="`${language.label}`"
                          role="tabpanel">                        
                  <div class="col-sm-12">
                    <div class="mb-3">
                        <label :for="`title- ${language.code}`" class="form-label">{{$t("onboarding_title")}}({{ language.label }})
                          <span class="text-danger">*</span>
                        </label>
                        <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_onboarding_title',{language: language.label})" 
                        :id="`title-${language.code}`" v-model="form.title[language.code]" :required="language.code === 'en' ">
                        <span v-if="form.errors.name" class="text-danger">{{ form.errors.title }}</span>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label :for="`description-${language.code}`" class="form-label">{{$t("onboarding_description")}}({{ language.label }})
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.description[language.code]"  :editor="editor"
                      :id="`description-${language.code}`" ></ckeditor>
                      <span v-for="(error, index) in errors.description" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  </div>
                </div>
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="onboarding_image" class="form-label">{{$t("onboarding_image")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp  :imageType="'onboarding'" :flexStyle="'0 0 calc(40% - 10px)'"
                      :aspectRatio="'4 / 7'"   :initialImageUrl="form.onboarding_image" @image-selected="(file) => handleImageSelected(file, 'onboarding_image')" @image-removed="() => handleImageRemoved('onboarding_image')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-lg-12">
                      <div class="text-end">
                        <button type="submit" class="btn btn-primary">{{ onboarding ? $t('update') : $t('save') }}</button>
                      </div>
                  </div><!--end col-->
                </div><!--end row-->
              </FormValidation>
            </form>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
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

.onboardingScreen{
  width: 300px;
  height: 550px;
  background-image: url(/images/onboarding.jpeg);
  background-position: center;
  background-repeat: no-repeat;
  background-size: contain;
}

.cards{
  position: relative;
  top: 20px;
  left: 26px;
  z-index: 2;
  width: 248px;
  height: 100px;
  padding:10px;
  box-shadow: none;
}
.onboardingImage{
  clip-path: polygon(0 0, 100% 0, 100% 91%, 0 100%);
  width: 206px;
  height: 220px;
  margin-left: 48px;
  margin-top: 78px; 
  border-radius: 5px;
}

.rtl .cards{
  position: relative;
  top: 20px;
  right: 26px;
  z-index: 2;
  width: 248px;
  height: 100px;
  padding:10px;
  box-shadow: none;
}
.rtl .onboardingImage{
  clip-path: polygon(0 0, 100% 0, 100% 91%, 0 100%);
  width: 206px;
  height: 220px;
  margin-right: 46px;
  margin-top: 78px; 
  border-radius: 5px;
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
