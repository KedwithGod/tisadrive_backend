<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';
import ImageUpload from '@/Components/ImageUpload.vue';
import { errorMessages } from 'vue/compiler-sfc';


export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation,
    ImageUpload,
  },
  props: {
    successMessage: String,
    alertMessage: String,
    bannerimage :{
      type: Object,
      required: true,
    },
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    const { t } = useI18n();
    const form = useForm({
      image: props.bannerimage ? props.bannerimage.image || "" : "",
    });
    const validationRules = {
      image: { required: true },
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
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      try {

        const formData = new FormData();
        for (const key in form) {
          if (key === 'iconFile' && form[key]) {
            formData.append('image', form[key]);
          } else {
            formData.append(key, form[key]);
          }
        }
        let response;
        if (props.bannerimage && props.bannerimage.id) {
          response = await axios.post(`/banner-image/update/${props.bannerimage.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        } else {
          response = await axios.post('store', formData);
        }
        if (response.status === 201) {
          successMessage.value = t('banner_image_created_successfully');
          form.reset();
          router.get('/banner-image');
        } else {
          alertMessage.value = t('failed_to_create_banner_image');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else if (error.response && error.response.status === 403) {
          alertMessage.value = error.response.data.alertMessage;
          setTimeout(()=>{
            router.get('/banner-image');
          },5000)
        } else {
          console.error(t('error_creating_banner_image'), error);
          alertMessage.value = t('failed_to_create_banner_image_catch');
        }
      }

    };

    const handleImageSelected = (file, fieldName) => {
      form[fieldName] = file;
    };

    const handleImageRemoved = (fieldName) => {
      form[fieldName] = null;
    };
  

  
    const iconUrl = props.bannerimage ? props.bannerimage.image :null;

    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      selectedCountry: ref(null),
      selectedTimezone: ref(null),
      validationRules,
      validationRef,
      errors,
      handleImageSelected,
      handleImageRemoved,
      iconUrl,
    };
  }
};
</script>

<template>
  <Layout>

    <Head title="Banner Image" />
    <PageHeader :title="bannerimage ? $t('edit') : $t('create')" :pageTitle="$t('banner-image')" pageLink="/banner-image"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">

            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="onboarding_image" class="form-label d-flex">{{$t("banner-image")}}
                        <span><h5 class="text-muted mt-1 mb-0 fs-13">(500px x 100px)</h5></span>
                        <span class="text-danger">*</span>
                      </label>
                      
                      <ImageUpload  :imageType="'banner'" :flexStyle="'0 0 calc(50% - 20px)'" :aspectRatio="'5 / 1'"   :initialImageUrl="form.image"  
                        @image-selected="(file) => handleImageSelected(file, 'image')" @image-removed="() => handleImageRemoved('image')">
                      </ImageUpload>
                      <span v-for="(error, index) in errors.image" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>                           
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ bannerimage ? $t('update') : $t('save') }}</button>
                    </div>
                  </div>
                </div>                 
                
              </FormValidation>
            </form>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
    <div>
      <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert"
        id="alertMsg">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Success</strong> - {{ successMessage }}
          <button type="button" class="btn-close btn-close-success" @click="dismissMessage"
            aria-label="Close Success Message"></button>
        </div>
      </div>

      <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert"
        id="alertMsg">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Alert</strong> - {{ alertMessage }}
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
