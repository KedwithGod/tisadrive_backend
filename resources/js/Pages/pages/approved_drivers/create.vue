<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed, watch } from "vue";
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
    driver: Object,
    serviceLocations: Array,
    vehicleTypes: Array,   
    app_for: String,
    selected_vehicle_type:Array, 
    validate: Function, // Define the prop to receive the method
    countries: Array,
    default_dial_code: String,
    default_flag: String,
    user: Array,
    default_country_id: Number, // Ensure this is provided from the parent component or initialized    
  },
  setup(props) {
    // console.log(props.driver);
    const { t } = useI18n();
    const selectedCountry = ref({
            dial_code: props.default_dial_code || '',
            flag: props.default_flag || ''
        });

    const form = useForm({
      name: props.driver ? props.driver.name || "" : "",
      service_location_id: props.driver ? props.driver.service_location_id || "" : "",
      mobile: props.driver ? props.driver.mobile || "" : "",
      email: props.driver ? props.driver.email || "" : "",
      gender: props.driver ? props.driver.gender || "" : "",
      vehicle_type: props.selected_vehicle_type[0] ? props.selected_vehicle_type[0] || "" : "",
      transport_type: props.driver ? props.driver.transport_type || "" : "",
      vehicle_make: props.driver ? props.driver.custom_make || "" : "",
      vehicle_model: props.driver ? props.driver.custom_model || "" : "",
      car_color: props.driver ? props.driver.car_color || "" : "",
      car_number: props.driver ? props.driver.car_number || "" : "",
      profilePictureFile: null,
      profile_picture: props.driver ? props.driver.profile_picture || "" : "",
      country: props.driver ? props.driver.country.id || "" : null, // Assuming country is an object with an ID
      password: props.driver ? props.driver.password || "" : "",
      confirm_password: props.driver ? props.driver.password || "" : "",
    });
    const validationRules = {
      name: { required: true },
      service_location_id: { required: true },
      mobile: { required: true, regex: /^\d+$/ },
      email: { required: true, email: true },
      gender: { required: true },
      vehicle_type: { required: true },
      vehicle_make: { required: true },
      vehicle_model: { required: true },
      car_color: { required: true },
      car_number: { required: true }, 
      password: { required: !props.driver },
      transport_type: { required: true },
      profile_picture: { required: true },
      confirm_password: { required: !props.driver, sameAs: "password" }           
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');
    const isButtonDisabled = ref(false);

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    const searchQuery = ref('');

    const filteredCountries = computed(() => {
            return props.countries.filter(country =>
                country.name.toLowerCase().includes(searchQuery.value.toLowerCase())
            );
        });
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

        if(props.app_for == "demo"){
            form.mobile = "**********";
            form.email = "**********";
        }
    const handleSubmit = async () => {
      // alert("dfhgusdhfkjsd");
    // Validate form fields locally first
    errors.value = validateForm();

    // Check if validation errors exist locally
    if (Object.keys(errors.value).length === 0) {
        try {

           // Check if mobile number already exists
           if (form.mobile && (!props.driver || form.mobile !== props.driver.mobile)) {
                const mobileCheckResponse = await axios.get(`/approved-drivers/check-mobile/${form.mobile}${props.driver ? `/${props.driver.id}` : ''}`);
                if (mobileCheckResponse.data.exists) {
                    errors.value.mobile = t('mobile_number_already_exists');
                    // errors.value.mobile = 'Mobile number already exists.';
                    return; // Exit early if mobile number exists
                }
            }

            // Check if email already exists
            if (form.email && (!props.driver || form.email !== props.driver.email)) {
                const emailCheckResponse = await axios.get(`/approved-drivers/check-email/${form.email}${props.driver ? `/${props.driver.id}` : ''}`);
                if (emailCheckResponse.data.exists) {
                    errors.value.email = t('email_already_exists');
                    // errors.value.email = 'Email already exists.';
                    return; // Exit early if email exists
                }
            }

            // If no validation errors, proceed with form submission
            const formData = new FormData();
            for (const key in form) {
                if (key === 'iconFile' && form[key]) {
                    formData.append('profile_picture', form[key]);
                } else {
                    formData.append(key, form[key]);
                }
            }

            // Always append country ID to formData
            let response;            
            formData.append('country', form.country);
            isButtonDisabled.value = true;
            if (props.driver) {
                response = await axios.post(`/approved-drivers/update/${props.driver.id}`, formData);
            } else {
                response = await axios.post("store", formData);
            }

            if (response.status === 200 || response.status === 201) {
                successMessage.value = response.data.successMessage;
                form.reset(); // Reset form after successful submission
                router.get('/approved-drivers'); // Redirect or navigate to another route
            } else {
                console.error(t('unexpected_response_status'), response.status);
            }

        } catch (error) {
            console.error(t('error_handling_form_submission'), error);
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            } else {
                console.error(t('an_unexpected_error_occurred'), error);
            }
        }
    }
};




    form.country = props.default_country_id; // Set form.country to default country id

    const selectCountry = (country) => {
        if (country) {
            selectedCountry.value = country;
            form.country = country.id; // Set form.country to the selected country's id
        }
    };
    const onFileChange = (event) => {
            const file = event.target.files[0];
            form.profilePictureFile = file;
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                  driverProfilePictureUrl.value = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };

        const driverProfilePictureUrl = ref('');
        const imageSrc = computed(() => {
            return form.profilePictureFile ? driverProfilePictureUrl.value : (props.driver ?.profile_picture ?props.driver.profile_picture: '/default-profile.jpeg');
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
      selectCountry,
      selectedCountry,
      searchQuery,
      filteredCountries,
      onFileChange,
      imageSrc,
      driverProfilePictureUrl,
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
    <PageHeader :title="driver ? $t('edit') : $t('create')" :pageTitle="$t('approved_drivers')" pageLink="/approved-drivers"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">

            <form @submit.prevent="handleSubmit">
    <FormValidation :form="form" :rules="validationRules" ref="validationRef">
      <div class="row">
        <div class="col-12">
          <div class="mb-3">
            <label for="service_location_id" class="form-label">{{$t("select_area")}}
              <span class="text-danger">*</span>
            </label>
            <select id="service_location_id" class="form-select" v-model="form.service_location_id">
              <option disabled value="">{{$t("select")}}</option>
              <option v-for="location in serviceLocations" :key="location.id" :value="location.id">
                {{ location.name }}
              </option>
            </select>
            <span v-for="(error, index) in errors.service_location_id" :key="index" class="text-danger">{{ error }}</span>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="mb-3">
            <label for="name" class="form-label">{{$t("name")}}<span class="text-danger">*</span></label>
            <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name" v-model="form.name" />
            <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
          </div>
        </div>
        <div class="col-sm-6">
          <div>
            <label class="form-label">{{$t("mobile")}}
              <span class="text-danger">*</span>
            </label>
            <div class="input-group" data-input-flag="">
              <button class="btn btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img :src="selectedCountry.flag" alt="flag" height="20" class="country-flagimg rounded">
                <span class="ms-2 country-codeno">{{ selectedCountry.dial_code }}</span>
              </button>
              <input type="text" class="form-control rounded-end flag-input" v-model="form.mobile" :placeholder="$t('enter_number')">
              <div class="dropdown-menu w-100">
                <div class="p-2 px-3 pt-1 searchlist-input">
                  <input type="text" class="form-control form-control-sm border search-countryList" :placeholder="$t('search_country_name_or_country_code')" v-model="searchQuery">
                </div>
                <ul class="list-unstyled dropdown-menu-list mb-0">
                  <li v-for="country in filteredCountries" :key="country.id">
                    <a href="javascript:void(0);" class="dropdown-item notify-item language py-2" @click="selectCountry(country)">
                      <img :src="country.flag" alt="flag" class="me-2 rounded" height="18">
                      <span class="align-middle">{{ country.name }} {{ country.dial_code }}</span>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            <span v-if="errors.mobile" class="text-danger">{{ errors.mobile }}</span>
          </div>
        </div>
        <div class="col-sm-6">
            <div class="mb-3">
                <label for="select_gender" class="form-label">{{$t("select_gender")}}
                  <span class="text-danger">*</span>
                </label>
                <select id="gender" class="form-select" v-model="form.gender">
                    <option selected disabled value="">{{$t("choose_gender")}}</option>
                    <option value="male">{{$t("male")}}</option>
                    <option value="female">{{$t("female")}}</option>
                    <option value="others">{{$t("others")}}</option>
                </select>
                <span v-if="errors.gender" class="text-danger">{{ errors.gender }}</span>
            </div>
        </div>
        <div class="col-sm-6">
          <div class="mb-3">
            <label for="email" class="form-label">{{$t("email")}}
              <span class="text-danger">*</span>
            </label>
            <input type="email" class="form-control" :placeholder="$t('enter_email')" id="email" v-model="form.email" />
            <span v-if="errors.email" class="text-danger">{{ errors.email }}</span>
          </div>
        </div>
        <div v-if="!driver" class="col-sm-6">
          <div class="mb-3">
            <label for="password" class="form-label">{{$t("password")}}
              <span class="text-danger">*</span>
            </label>
            <input type="password" class="form-control" id="password" v-model="form.password" :placeholder="$t('enter_password')">
            <span v-if="errors.password" class="text-danger">{{ errors.password }}</span>
          </div>
        </div>
        <div v-if="!driver" class="col-sm-6">
          <div class="mb-3">
            <label for="confirm_password" class="form-label">{{$t("confirm_password")}}
              <span class="text-danger">*</span>
            </label>
            <input type="password" class="form-control" id="confirm_password" v-model="form.confirm_password" :placeholder="$t('confirm_password')">
            <span v-if="errors.confirm_password" class="text-danger">{{ errors.confirm_password }}</span>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="mb-3">
            <label for="transport_type" class="form-label">{{$t("transport_type")}}
              <span class="text-danger">*</span>
            </label>
            <select id="transport_type" class="form-select" v-model="form.transport_type">
              <option disabled value="">{{$t("select")}}</option>
              <option value="taxi">{{$t("taxi")}}</option>
              <option value="delivery">{{$t("delivery")}}</option>
              <option value="both">{{$t("both")}}</option>
            </select>
            <span v-if="errors.transport_type" class="text-danger">{{ errors.transport_type }}</span>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="mb-3">
            <label for="vehicle_type" class="form-label">{{$t("vehicle_type")}}
              <span class="text-danger">*</span>
            </label>
            <select id="vehicle_type" class="form-select" v-model="form.vehicle_type">
              <option disabled value="">{{$t("select")}}</option>
              <option v-for="vehicle in vehicleTypes" :key="vehicle.id" :value="vehicle.id">
                {{ vehicle.name }}
              </option>
            </select>
            <span v-if="errors.vehicle_type" class="text-danger">{{ errors.vehicle_type }}</span>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="mb-3">
            <label for="vehicle_make" class="form-label">{{$t("vehicle_make")}}
              <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" :placeholder="$t('enter_vehicle_make')" id="vehicle_make" v-model="form.vehicle_make" />
            <span v-if="errors.vehicle_make" class="text-danger">{{ errors.vehicle_make }}</span>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="mb-3">
            <label for="vehicle_model" class="form-label">{{$t("vehicle_model")}}
              <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" :placeholder="$t('enter_vehicle_model')" id="vehicle_model" v-model="form.vehicle_model" />
            <span v-if="errors.vehicle_make" class="text-danger">{{ errors.vehicle_make }}</span>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="mb-3">
            <label for="car_color" class="form-label">{{$t("vehicle_color")}}
              <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" :placeholder="$t('enter_vehicle_color')" id="car_color" v-model="form.car_color" />
            <span v-if="errors.car_color" class="text-danger">{{ errors.car_color }}</span>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="mb-3">
            <label for="car_number" class="form-label">{{$t("vehicle_number")}}
              <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" :placeholder="$t('enter_vehicle_number')" id="car_number" v-model="form.car_number" />
            <span v-if="errors.car_number" class="text-danger">{{ errors.car_number }}</span>
          </div>
        </div>
        <!-- <div v-if="imageSrc" class="mb-3">
          <img :src="imageSrc" alt="driver Profile" style="max-width: 100px; max-height: 100px;" />
        </div> -->
        <div class="col-sm-6 mb-3">
          <!-- <image-upload id="profilePictureInput" @change="onFileChange" v-model="form.profilePictureFile" :src="profilePictureUrl"></image-upload> -->
          <ImageUpload  :imageType="'driver'" :initialImageUrl="form.profile_picture"   :flexStyle="'0 0 calc(50% - 20px)'" :aspectRatio="'5 / 5'"   
          @image-selected="(file) => handleImageSelected(file, 'profile_picture')" @image-removed="() => handleImageRemoved('profile_picture')"  id="profilePictureInput" @change="onFileChange">
          </ImageUpload>
          <span v-if="errors.profile_picture" class="text-danger">{{ errors.profile_picture }}</span>
        </div>
        <div class="col-lg-12">
          <div class="text-end">
            <button type="submit" class="btn btn-primary" :disabled="isButtonDisabled">{{ driver ? $t('update') : $t('save') }}</button>
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
