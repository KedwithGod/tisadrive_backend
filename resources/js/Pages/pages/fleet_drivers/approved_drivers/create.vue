<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";

import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';
import ImageUpload from '@/Components/ImageUpload.vue';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation,
    imageUpload,
    ImageUpload
  },
  props: {
    successMessage: String,
    alertMessage: String,
    countries: Array,
    serviceLocation: Array, // Ensure this is an array of locations
    timeZones: Array,
    owner: Object,
    driver: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    const { t } = useI18n();
    const serviceLocation = props.serviceLocation;

    const owners = ref([]);  // This will hold the list of owners

    const form = useForm({
      owner_id: props.driver ? props.driver.owner_id || "" : "",
      service_location_id: props.driver ? props.driver.service_location_id || "" : "",
      country: props.driver ? props.driver.country || "" : "null",
      name: props.driver ? props.driver.name || "" : "",
      mobile: props.driver ? props.driver.mobile || "" : "",
      email: props.driver ? props.driver.email || "" : "",
      transport_type: props.driver ? props.driver.transport_type || "" : "",
      password: "",
      confirm_password: "",
      gender: props.driver ? props.driver.gender || "" : "",
      profile_picture: props.driver ? props.driver.profile_picture || "" : "",
    });
    const validationRules = {
      owner_id: { required: true },
      name: { required: true },
      service_location_id: { required: true },
      //transport_type: { required: true },
      country: { required: true },
      mobile: { required: true, regex: /^\d+$/ },
      email: { required: true },
      gender: { required: true },
      password: { required: !props.driver },
      confirm_password: { required: !props.driver},
      profile_picture: { required: true },
    };
    const validationRef = ref(null);
    const errors = ref({});
    const isButtonDisabled = ref(false);
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');
    const location_name = ref(props.owner.area?.name);
    const owner_name = ref(props.owner?.name);
    const countries = ref(props.countries);
    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };


    const onFileChange = (event) => {
        const file = event.target.files[0];
        form.profile_picture = file;
        if (file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
        }
    };
    const imageSrc = ref(props.driver ? props.driver.profile_picture : '/default-profile.jpeg');

const fetchOwners = async () => {
  try {
    // Access form.service_location_id directly
    const response = await axios.get('/fleet-drivers/list-owners', {
      params: { service_location_id: form.service_location_id },
    });
    owners.value = response.data.results || []; // Populate the owners array
  } catch (error) {
    console.error("Error fetching owners:", error);
    owners.value = []; // Reset owners if there's an error
  }
};

onMounted(() => {
  if (form.service_location_id) {
    fetchOwners(); // Prepopulate owners based on driver's service location
  }
});





    const handleSubmit = async () => {
      console.log(form.password,form.confirm_password);
      if (form.password !== form.confirm_password) {
          errors.value = {'confirm_password': [t('password_mismatch')]};
          return;
      } else {
          delete errors.value?.confirm_password;
      }
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      try {

        // Check if mobile number already exists
        if (form.mobile && (!props.driver || form.mobile !== props.driver.mobile)) {
          const mobileCheckResponse = await axios.get(`/fleet-drivers/check-mobile/${form.mobile}${props.driver ? `/${props.driver.id}` : ''}`);
          if (mobileCheckResponse.data.exists) {
              errors.value.mobile = t('mobile_number_already_exists');
              // errors.value.mobile = 'Mobile number already exists.';
              return; // Exit early if mobile number exists
          }
        }

      // Check if email already exists
      if (form.email && (!props.driver || form.email !== props.driver.email)) {
        const emailCheckResponse = await axios.get(`/fleet-drivers/check-email/${form.email}${props.driver ? `/${props.driver.id}` : ''}`);
        if (emailCheckResponse.data.exists) {
            errors.value.email = t('email_already_exists');
            // errors.value.email = 'Email already exists.';
            return; // Exit early if email exists
        }
      }

        const formData = new FormData();
          for (const key in form) {
              if (key === 'iconFile' && form[key]) {
                  formData.append('profile_picture', form[key]);
              } else {
                  formData.append(key, form[key]);
              }
          }
        let response;        
        isButtonDisabled.value = true;
        if (props.driver && props.driver.id) {
          response = await axios.post(`/fleet-drivers/update/${props.driver.id}`, formData);
        } else {
          response = await axios.post('/fleet-drivers/store', formData);
        }
        if (response.status === 201) {
          successMessage.value = t('fleet_drivers_created_successfully');
          form.reset();
          router.get('/fleet-drivers');
        } else {
          alertMessage.value = t('failed_to_create_fleet_drivers');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_creating_fleet_drivers'), error);
          alertMessage.value = t('failed_to_create_fleet_drivers_catch');
        }
      }

    };

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
      location_name,
      owner_name,
      onFileChange,
      validationRules,
      validationRef,
      imageSrc,
      countries,
      driver,
      fetchOwners,
      owners,
      errors,
      handleImageSelected,
      handleImageRemoved,
      isButtonDisabled
    };
  }
};
</script>

<template>
  <Layout>

    <Head title="Approved Drivers" />
    <PageHeader :title="$t('create')" :pageTitle="$t('approved_drivers')" pageLink="/fleet-drivers"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">

            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="service_location_id" class="form-label">{{$t("select_area")}}
                        <span class="text-danger">*</span>
                      </label>
                  <select class="form-control" id="service_location_id" v-model="form.service_location_id" @change="fetchOwners">
                        <option value="" disabled>{{$t("select_area")}}</option>
                        <option v-for="location in serviceLocation" :key="location.id" :value="location.id">
                          {{ location.name }}
                        </option>

                      </select>
                      <span v-for="(error, index) in errors.service_location_id" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>

                <!-- Dropdown for selecting owners -->
                <div class="col-sm-6">
                  <div class="mb-3">
                    <label for="owner" class="form-label">{{$t("owner")}}
                      <span class="text-danger">*</span>
                    </label>
                    <select class="form-control" id="owner" v-model="form.owner_id">
                      <option value="" disabled>{{$t("select_owner")}}</option>
                      <option v-for="owner in owners" :key="owner.id" :value="owner.id">
                        {{ owner.company_name }}
                      </option>
                    </select>
                    <span v-for="(error, index) in errors.owner_id" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="country" class="form-label">{{$t("country")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="select_country" class="form-select" v-model="form.country">
                        <option disabled value="">{{$t("choose_country")}}</option>
                        <option v-for="country in countries" :key="country.id" :value="country.id">
                          {{ country.name }}
                        </option>
                      </select>
                      <span v-for="(error, index) in errors.country" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="name" class="form-label">{{$t("name")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name" v-model="form.name"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="mobile_number" class="form-label">{{$t("enter_mobile_number")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_mobile_number')" id="mobile_number" 
                      v-model="form.mobile"/>
                      <span v-for="(error, index) in errors.mobile" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="email" class="form-label">{{$t("email")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="email" class="form-control" :placeholder="$t('enter_email')" id="email" 
                      v-model="form.email"/>
                      <span v-for="(error, index) in errors.email" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                 <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="password" class="form-label">{{$t("password")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="password" class="form-control" :placeholder="$t('enter_password')" id="password" 
                      v-model="form.password"/>
                      <span v-for="(error, index) in errors.password" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
               <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="confirm_password" class="form-label">{{$t("confirm_password")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="password" class="form-control" :placeholder="$t('confirm_password')" id="confirm_password" 
                      v-model="form.confirm_password"/>
                      <span v-for="(error, index) in errors.confirm_password" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_gender" class="form-label">{{$t("gender")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="select_gender" class="form-select"  v-model="form.gender">
                        <option disabled value="">{{$t("select")}}</option>
                        <option  value="male">{{$t("male")}}</option>
                        <option  value="female">{{$t("female")}}</option>
                        <option  value="others">{{$t("others")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.gender" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="mb-3 row">
                      <label for="icon" class="form-label d-flex">{{$t("profile")}} 
                      </label>
                      <div class="col-sm-6">
                        <!-- <image-upload id="profilePictureInput" @change="onFileChange" v-model="form.profile_picture" /> -->
                        <ImageUpload  :imageType="'driver'" :initialImageUrl="form.profile_picture"   :flexStyle="'0 0 calc(50% - 20px)'" :aspectRatio="'5 / 5'"   
                        @image-selected="(file) => handleImageSelected(file, 'profile_picture')" @image-removed="() => handleImageRemoved('profile_picture')">
                        </ImageUpload>
                        <span v-for="(error, index) in errors.profile_picture" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                      <!-- <div class="col-sm-6" v-if="imageSrc">
                        <img :src="imageSrc" alt="driver Profile" style="max-width: 300px; max-height: 300px" />
                      </div> -->
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary" :disabled="isButtonDisabled"> {{ driver ? $t('update') : $t('save') }}</button>
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
