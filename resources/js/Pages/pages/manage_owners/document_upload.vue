<template>
  <Layout>
    <Head title="Document Upload" />
    <PageHeader :title="$t('create')" :pageTitle="$t('document_upload')" pageLink="/manage-owners"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="name" class="form-label">{{$t("name")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name" v-model="form.name" readonly />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>

                  <div v-if="document && document.has_identify_number == 1" class="col-6">
                    <div class="mb-3">
                      <label for="identify_number" class="form-label">{{$t("identify_number")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_identify_number')" id="identify_number" v-model="form.identify_number" />
                      <span v-for="(error, index) in errors.identify_number" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>

                  <div v-if="document && document.has_expiry_date == 1" class="col-6">
                    <div class="mb-3">
                      <label for="expiry_date" class="form-label">{{$t("expiry_date")}}
                        <span class="text-danger">*</span>
                      </label>
                      <flat-pickr :placeholder="$t('select_date')" id="expiry_date" class="form-control flatpickr-input" v-model="form.expiry_date" :config="rangeDateconfig" />
                      <span v-for="(error, index) in errors.expiry_date" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <!-- Image preview and uploader -->
                  <!-- <div v-if="imageSrc">
                    <img :src="imageSrc" alt="Driver Document" style="max-width: 100px; max-height: 100px;" />
                  </div> -->

                  <!-- Front image uploader -->
                   <div class="row">
                    <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="document" class="form-label d-flex">
                        <template v-if="document && document.image_type === 'front_and_back'">
                          {{ document.document_name_front }}
                          <span>
                            <h5 class="text-muted mt-1 mb-0 fs-13">(512px x 512px)</h5>
                          </span>
                        </template>
                        <template v-else>
                          {{ $t("document") }}
                          <h5 class="text-muted mt-1 mb-0 fs-13">(512px x 512px)</h5>
                        </template>
                      </label>
                      <!-- <image-upload v-model="form.iconFile"></image-upload> -->
                      <ImageUpload  :imageType="'owner-documents'" :initialImageUrl="form.image"   :flexStyle="'0 0 calc(50% - 20px)'" :aspectRatio="'5 / 5'"   
                      @image-selected="(file) => handleImageSelected(file, 'image')" @image-removed="() => handleImageRemoved('image')">
                      </ImageUpload>
                      <span v-if="errors.image" class="text-danger">{{ errors.image }}</span>
                    </div>
                  </div>

                  <!-- Back image uploader (conditional based on image_type) -->
                  <div v-if="document && document.image_type === 'front_and_back'" class="col-sm-6">
                  <!-- <div v-if="backImageSrc">
                    <img :src="backImageSrc" alt="Driver Document" style="max-width: 100px; max-height: 100px;" />
                  </div> -->
                    <div class="mb-3">
                      <label for="back_document" class="form-label d-flex">{{ document.document_name_back }}
                        <span><h5 class="text-muted mt-1 mb-0 fs-13">(512px x 512px)</h5></span>
                      </label>
                      <!-- <image-upload v-model="form.backImageFile"></image-upload> -->
                      <ImageUpload  :imageType="'owner-documents'" :initialImageUrl="form.back_image"   :flexStyle="'0 0 calc(50% - 20px)'" :aspectRatio="'5 / 5'"   
                      @image-selected="(file) => handleImageSelected(file, 'back_image')" @image-removed="() => handleImageRemoved('back_image')">
                      </ImageUpload>
                      <span v-if="errors.backImageFile" class="text-danger">{{ errors.backImageFile }}</span>
                    </div>
                  </div>
                   </div>
                  

                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ document ? $t('update') : $t('save') }}</button>
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

<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref,computed } from "vue";
import axios from "axios";
import FormValidation from "@/Components/FormValidation.vue";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import imageUpload from "@/Components/widgets/documentUpload.vue";
import { useI18n } from 'vue-i18n';
import ImageUpload from '@/Components/ImageUpload.vue';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    FormValidation,
    flatPickr,
    imageUpload,
    ImageUpload
  },
  props: {
    successMessage: String,
    alertMessage: String,
    ownerId: Array,
    document: Object,
    uploaded: Object,
    validate: Function,
  },


  setup(props) {
    const { t } = useI18n();
    const form = useForm({
        name: props.document ? props.document.name || "" : "",
        identify_number: props.uploaded ? props.uploaded.identify_number || "" : "",
        expiry_date: props.uploaded ? props.uploaded.expiry_date || "" : "",
        image: props.uploaded ? props.uploaded.image || "" : "",
        back_image: props.uploaded ? props.uploaded.back_image || "" : "",
        iconFile: null,
        backImageFile: null, // New back image field
    });


    const validationRules = {
      name: { required: true },
      identify_number: props.document && props.document.has_identify_number ? { required: true } : {},
      expiry_date: props.document && props.document.has_expiry_date ? { required: true } : {},
      image: props.document && props.document.image ? { required: true } : {},
      back_image: props.document && props.document.image_type === 'front_and_back' ? { required: true } : {}, // Conditional validation for back image
    };

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

// console.log(props.uploaded);

    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }

      const formData = new FormData();
      Object.keys(form.data()).forEach(key => {
        formData.append(key, form[key]);
      });

      if (form.image) {
        formData.append('image', form.image);
      }
      if (form.back_image) {
        formData.append('back_image', form.back_image);
      }

      try {
        let response;
        if (props.document && props.document.id) {
          response = await axios.post(`/manage-owners/document-upload/${props.document.id}/${props.ownerId.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        } else {
          response = await axios.post(`/manage-owners/document-upload/${props.ownerId.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        }

        if (response.status === 201) {
          successMessage.value = t('document_successfully_uploaded');
          form.reset();
            router.get(`/manage-owners/document/${props.ownerId.id}`); // Redirect or navigate to another route
        } else {
          alertMessage.value = t('failed_to_upload_document');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_uploading_document'), error);
          alertMessage.value = t('failed_to_upload_document');
        }
      }
    };


    const iconFile = ref('');
        const imageSrc = computed(() => {
            return form.iconFile ? iconFile.value : (props.uploaded && props.uploaded.image ? props.uploaded.image : '/default-profile.jpeg');
        });
    const backImageFile = ref('');
        const backImageSrc = computed(() => {
            return form.iconBackFile ? iconBackFile.value : (props.uploaded && props.uploaded.back_image ? props.uploaded.back_image : '/default-profile.jpeg');
        });

        const handleImageSelected = (file, fieldName) => {
          form[fieldName] = file;
        };

        const handleImageRemoved = (fieldName) => {
          form[fieldName] = null;
        };

    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      validationRef,
      errors,
      imageSrc,
      backImageSrc,
      handleImageSelected,
      handleImageRemoved
    };
  },
};
</script>

<style>
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

