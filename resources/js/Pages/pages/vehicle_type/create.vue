<template>
  <Layout>
    <Head title="Vehicle Type" />
    <PageHeader :title="vehicleType ? $t('edit') : $t('create')" :pageTitle="$t('vehicle_type')" pageLink="/vehicle_type" />
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="transport_type" class="form-label">{{$t("transport_type")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="transport_type" class="form-select" v-model="form.transport_type">
                        <option disabled value="">{{$t("choose_transport_type")}}</option>
                        <option value="taxi">{{$t("taxi")}}</option>
                        <option value="delivery">{{$t("delivery")}}</option>
                        <option value="both">{{$t("all")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.transport_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_icon_types_for" class="form-label">{{$t("icon_type")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="select_icon_types_for" class="form-select" v-model="form.icon_types_for">
                        <option disabled value="">{{$t('choose_icon_type')}}</option>
                        <option value="car">{{$t("car")}}</option>
                        <option value="motor_bike">{{$t("bike")}}</option>
                        <option value="auto">{{$t("auto")}}</option>
                        <option value="truck">{{$t("truck")}}</option>
                        <option value="ehcv">{{$t("ehcv")}}</option>
                        <option value="hatchback">{{$t("hatchback")}}</option>
                        <option value="hcv">{{$t("hcv")}}</option>
                        <option value="lcv">{{$t("lcv")}}</option>
                        <option value="mcv">{{$t("mcv")}}</option>
                        <option value="luxury">{{$t("luxury")}}</option>
                        <option value="premium">{{$t("premium")}}</option>
                        <option value="suv">{{$t("suv")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.icon_types_for" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-12">
                  <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="icon" class="form-label d-flex">{{$t("vehicle_image")}} 
                        <span><h5 class="text-muted mt-1 mb-0 fs-13">(512px x 512px)</h5></span>
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUpload  :imageType="'types'" :initialImageUrl="form.icon"   :flexStyle="'0 0 calc(50% - 20px)'" :aspectRatio="'5 / 5'"   
                        @image-selected="(file) => handleImageSelected(file, 'icon')" @image-removed="() => handleImageRemoved('icon')"   @change="onFileChange">
                      </ImageUpload>
                      <span v-for="(error, index) in errors.icon" :key="index" class="text-danger">
                        {{ error }}
                      </span>    
                      </div>
                  </div>

                  <div class="col-sm-6 position-relative">
                      <Bcard>
                        <!-- First map view image (relative position) -->
                        <img src="/assets/images/icon-types/map-view.jpg" class="img-fluid map-view-image" alt="Map View Image" />

                        <!-- Second image (absolute position) -->
                        <div v-if="imagePath" class="overlay-icon-container">
                          <img :src="imagePath" alt="Selected Icon" class="img-fluid icon-image" />
                        </div>
                      </Bcard>
                    </div>
                </div>
            </div>
              <!-- Name Field -->
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="name" class="form-label">{{$t("name")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" placeholder="Enter Name" id="name" v-model="form.name" required />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6" v-if="showMaxWeightField">
                    <div class="mb-3">
                      <label :for="maxWeightLabel" class="form-label">{{ maxWeightLabel }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" class="form-control" :placeholder="'Enter ' + maxWeightLabel + ' ' +  'in kg,lb..etc'" id="capacity"
                        v-model="form.capacity" />
                      <span v-for="(error, index) in errors.capacity" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6" v-if="showSizeField">
                    <div class="mb-3">
                      <label for="size" class="form-label">{{$t("size")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_size')+ ' ' + '7ft (L) x 4ft (W) x 4ft (H)'" id="size" v-model="form.size" />
                      <span v-for="(error, index) in errors.size" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="short_description" class="form-label">{{$t("short_description")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_short_description')" id="short_description"
                        v-model="form.short_description" />
                      <span v-for="(error, index) in errors.short_description" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="description" class="form-label">{{$t("description")}}
                        <span class="text-danger">*</span>
                      </label>
                      <textarea class="form-control" id="description" rows="3" :placeholder="$t('enter_description')" v-model="form.description"></textarea>
                      <span v-for="(error, index) in errors.description" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>               
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="trip_dispatch_type" class="form-label">{{$t("trip_dispatch_type")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="trip_dispatch_type" class="form-select" v-model="form.trip_dispatch_type">
                        <option disabled value="">{{$t('choose_trip_dispatch_type')}}</option>
                        <option value="normal">{{$t("normal")}}</option>
                        <option value="bidding">{{$t("bidding")}}</option>
                        <option value="both">{{$t("all")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.trip_dispatch_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6" v-if="enabled_sub_vehicle_modules == 1">
                  <div class="mb-3">
                    <label for="select_vehicle_type" class="form-label">{{$t("supported_other_vehilce_types")}}
                    </label>
                    <Multiselect
                    mode="tags" 
                      id="select_vehicle_type"
                      v-model="form.supported_vehicles"
                      :options="filteredVehicleTypes"
                      multiple
                      :close-on-select="false"
                      :searchable="true"
                      :create-option="false"
                      :placeholder="$t('select_vehicle_type')"
                    />
                    <span v-for="(error, index) in errors.supported_vehicles" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                </div>

                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ vehicleType ?  $t('update') : $t('save') }}</button>
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

<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, onMounted, computed, watch } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import '@vueform/multiselect/themes/default.css';
import FormValidation from "@/Components/FormValidation.vue";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import { useI18n } from 'vue-i18n';
import ImageUpload from '@/Components/ImageUpload.vue';

export default {
  data() {
    return {
      form: {
        icon_types_for: ''
      },
    };
  },
  computed: {
    imagePath() {
      // Map the selected value to the corresponding image path
      switch (this.form.icon_types_for) {
        case 'car':
          return '/assets/images/icon-types/car.png';
        case 'motor_bike':
          return '/assets/images/icon-types/bike.png';
        case 'auto':
          return '/assets/images/icon-types/auto.png';
        case 'truck':
          return '/assets/images/icon-types/truck.png';
        case 'ehcv':
          return '/assets/images/icon-types/ehcv.png';
        case 'hatchback':
          return '/assets/images/icon-types/hatchback.png';
        case 'hcv':
          return '/assets/images/icon-types/hcv.png';
        case 'lcv':
          return '/assets/images/icon-types/lcv.png';
        case 'mcv':
          return '/assets/images/icon-types/mcv.png';
        case 'luxury':
          return '/assets/images/icon-types/luxury.png';
        case 'premium':
          return '/assets/images/icon-types/premium.png';
        case 'suv':
          return '/assets/images/icon-types/SUV.png';
        default:
          return ''; // Return an empty string if no valid option is selected
      }
    },
    filteredVehicleTypes() {
      if (this.form.name == "") {
        // Return all vehicle types formatted for Multiselect
        const filtered = this.vehicleTypes.map((type) => ({
          value: type.id,
          label: type.name
        }));
        return filtered;
      } 
      else {
        // Filter out vehicle type with the same name as form.name
        return this.vehicleTypes
          .filter((type) => type.name !== this.form.name)
          .map((type) => ({
            value: type.id,
            label: type.name
          }));
      }
    }
  },
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation,
    ImageUpload
  },
  props: {
    successMessage: String,
    alertMessage: String,
    vehicleType: Object,
    validate: Function,
    languages: Array,
    vehicleTypes: Array,
    enabled_sub_vehicle_modules: String,

  },
  setup(props) {
    const { t } = useI18n();
    const { languages, fetchData } = useSharedState(); // Destructure the shared state
    const activeTab = ref('English');
    const setActiveTab = (tab) => {
      activeTab.value = tab;
    }

    const form = useForm({
     // languageFields:  props.vehicleType ? props.vehicleType.languageFields || {'en': props.vehicleType.name} : {}, // To hold data from the Tab component
      name: props.vehicleType ? props.vehicleType.name || "" : "",
      capacity: props.vehicleType ? props.vehicleType.capacity || "" : "",
      icon_types_for: props.vehicleType ? props.vehicleType.icon_types_for || "" : "",
      transport_type: props.vehicleType ? props.vehicleType.is_taxi || "" : "",
      size: props.vehicleType ? props.vehicleType.size || "" : "",
      short_description: props.vehicleType ? props.vehicleType.short_description || "" : "",
      description: props.vehicleType ? props.vehicleType.description || "" : "",
      trip_dispatch_type: props.vehicleType ? props.vehicleType.trip_dispatch_type || "" : "",
      maximum_weight_can_carry: props.vehicleType ? props.vehicleType.maximum_weight_can_carry || "" : "",
      icon: props.vehicleType ? props.vehicleType.icon || "" : "",
      supported_vehicles: props.vehicleType ? props.vehicleType.sub_vehicle || [] : [],

    });
    
// console.log(form.languageFields);
    const validationRules = {
      name: { required: true },
      capacity: { required: true },
      transport_type: { required: true },
      description: { required: true },
      short_description: { required: true },
      trip_dispatch_type: { required: true },
      icon: { required: props.vehicleType ? false : true }
    };

const categories = ref([]); // Holds fetched category data

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');
    const results = ref([]); 

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    const fetchDatas = async () => {
      try {
          const response = await axios.get(`/vehicle_type/list`);
          results.value = response.data.results;
      } catch (error) {
          console.error(t('error_fetching_vehicle_type'), error);
      }
  };

    const showSizeField = computed(() => form.transport_type === 'delivery' || form.transport_type === 'both');
    const showMaxWeightField = computed(() => form.transport_type === 'delivery' || form.transport_type === 'both' || form.transport_type === 'taxi');
    const maxWeightLabel = computed(() => (form.transport_type === 'taxi' ? 'Capacity' : 'Maximum Weight Can Carry'));

    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();
        if (Object.keys(errors.value).length > 0) {
        return;
      }
        try {
        var formData = form.data();
        let response;
        if (props.vehicleType && props.vehicleType.id) {
          response = await axios.post(`/vehicle_type/update/${props.vehicleType.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });

        } else {
          response = await axios.post('/vehicle_type/store', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        }

        if (response.status === 201) {
          successMessage.value = t('vehicle_type_created_successfully');
          form.reset();
          router.get('/vehicle_type');
        } else {
          alertMessage.value = t('failed_to_create_vehicle_type');
        }
      } catch (error) {
        if (error.response && error.response.status === 403) {
          alertMessage.value = error.response.data.alertMessage;
          setTimeout(()=>{
            router.get('/vehicle_type');
          },5000);
        } else {
          console.error(t('error_creating_vehicle_type'), error);
        }
      }
    };



    onMounted(async () => {
      if (Object.keys(languages).length == 0) {
        await fetchData();
      }
    });

      // Construct the full URL for the vehicle type icon
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
      showSizeField,
      showMaxWeightField,
      maxWeightLabel,
      vehicleType: props.vehicleType,
      setActiveTab,
      activeTab,
      categories,
      languages,
      handleImageSelected,
      handleImageRemoved,
      fetchDatas,
      vehicleTypes: results,
    };
  },
  mounted() {
        this.fetchDatas();
    },
};
</script>

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

.rtl .form-check {
    position: relative;
    text-align: right;
}
/* Container for the card with relative positioning */
.position-relative{
  position: relative;
}

/* First image styling */
.map-view-image {
  width: 45%;
  /* width: 500px; */
  height: auto;
  display: block;
  /* Makes the first image responsive */
}

/* Overlay container for the second image */
.overlay-icon-container {
  position: absolute;
  top: 50%;
  left: 25%;
  transform: translate(-50%, -50%);
  width: 60px; /* Adjust size of overlay image */
  height: 60px; /* Adjust size of overlay image */
  display: flex;
  justify-content: center;
  align-items: center;
  pointer-events: none; /* Ensures the image won't interfere with clicks */
}

/* Second image styling (icon) */
.icon-image {
  max-width: 80%;
  height: auto;
  object-fit: cover; /* Ensures the image retains its aspect ratio */
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .overlay-icon-container {
    width: 75px; /* Adjust size for smaller screens */
    height: 75px;
  }
}

@media (max-width: 576px) {
  .overlay-icon-container {
    width: 50px; /* Further adjust size for mobile */
    height: 50px;
  }
}
</style>
