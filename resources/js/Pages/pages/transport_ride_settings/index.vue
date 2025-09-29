<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
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
  },
  props: {
    successMessage: String,
    alertMessage: String,
    app_for: String,
    settings: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    // console.log(props.settings);
    const { t } = useI18n();
    const form = useForm({
      trip_dispatch_type: props.settings.trip_dispatch_type || "",
      driver_search_radius: props.settings.driver_search_radius || "",
      maximum_time_for_accept_reject_bidding_ride: props.settings.maximum_time_for_accept_reject_bidding_ride || "",
      user_can_make_a_ride_after_x_miniutes: props.settings.user_can_make_a_ride_after_x_miniutes || "",
      maximum_time_for_find_drivers_for_bitting_ride: props.settings.maximum_time_for_find_drivers_for_bitting_ride || "",
      minimum_time_for_search_drivers_for_schedule_ride: props.settings.minimum_time_for_search_drivers_for_schedule_ride || "",
      minimum_time_for_starting_trip_drivers_for_schedule_ride: props.settings.minimum_time_for_starting_trip_drivers_for_schedule_ride || "",
      maximum_time_for_find_drivers_for_regular_ride: props.settings.maximum_time_for_find_drivers_for_regular_ride || "",
      trip_accept_reject_duration_for_driver: props.settings.trip_accept_reject_duration_for_driver || "",
      how_many_times_a_driver_can_enable_the_my_route_booking_per_day: props.settings.how_many_times_a_driver_can_enable_the_my_route_booking_per_day || "",
      bidding_low_percentage: props.settings.bidding_low_percentage || "",
      bidding_high_percentage: props.settings.bidding_high_percentage || "",
      bidding_amount_increase_or_decrease: props.settings.bidding_amount_increase_or_decrease || "",
      can_round_the_bill_values: props.settings.can_round_the_bill_values  ===  '1',
      // minimum_trip_distane:props.settings ? props.settings.minimum_trip_distane ||  0 : "",
    });

    const validationRules = {
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

const showModal = ref(false); // Modal state
const modalText = ref(''); // Text for the modal
const currentToggleField = ref(''); // Ensure it's initialized
const newToggleStatus = ref(false); // Ensure it's initialized


// Function to update the status when OK is clicked
const enableStatus = async () => {
  try {
    const status = newToggleStatus.value ? 1 : 0;
    await axios.post('/transport-ride-settings/update-status', { id: currentToggleField.value, status });
    form[currentToggleField.value] = newToggleStatus.value;
  } catch (error) {
    console.error('Error updating status', error);
  } finally {
    showModal.value = false; // Close the modal
  }
};

const cancelModal = () => {
    // Check if currentToggleField is set
    if (currentToggleField.value) {
        // Reset the form value based on the original props.settings value
        form[currentToggleField.value] = props.settings[currentToggleField.value] === '1'; // Convert to boolean
    } else {
        console.warn('No toggle field set to cancel.');
    }
    showModal.value = false; // Close the modal
};





// Function to confirm the toggle and open the modal
// const confirmToggle = (field, value, placeholder, offPlaceholder) => {
//   console.log('Confirming toggle for field:', field); // Log the field being toggled
//   currentToggleField.value = field; // Set the current toggle field
//   newToggleStatus.value = value; // Set the new status
//   // Check the toggle status and set the modal text accordingly
//   modalText.value = value ? `${placeholder}` : `${offPlaceholder}`;
//   showModal.value = true; // Show the modal
// };

const confirmToggle = async (field, value) => {
      if(props.app_for == "demo"){
        form[field] = !value;
        Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
        return;
      }
      const placeholderText = value ? 'Enable' : 'Enable';
      const offPlaceholderText = value ? 'Disable' : 'Disable';

      try {
        const result = await Swal.fire({
          title: `Are you sure you want to ${value ? 'enable' : 'disable'} this setting?`,
          // text: value ? placeholderText : offPlaceholderText,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed',
          cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
          await axios.post('/transport-ride-settings/update-status', { id: field, status: value ? 1 : 0 });
          form[field] = value;
          Swal.fire('Updated!', `Setting has been ${value ? 'enabled' : 'readonly'}.`, 'success');
        } else {
          form[field] = !value;
        }
      } catch (error) {
        console.error('Error updating status', error);
        form[field] = !value; // Reset toggle if error occurs
      }
    };



    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };
    const handleSubmit = async () => {
      if(props.app_for == "demo"){
        Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
        return;
      }
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      try {
        let response;
        if (props.settings) {
          response = await axios.post(`/transport-ride-settings/update`, form.data());
        } else {
          alertMessage.value = t('failed_to_update_transport_ride_settings');
        }
        if (response.status === 200) {
          successMessage.value = t('transport_ride_settings_created_successfully');
          form.reset();
          router.get('/transport-ride-settings');
        } else {
          alertMessage.value = t('failed_to_update_transport_ride_settings');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_updating_transport_ride_settings'), error);
          alertMessage.value = t('failed_to_update_transport_ride_settings');
        }
      }

    };

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
      confirmToggle,
      enableStatus,
      cancelModal,
      modalText,
      showModal,
      errors
    };
  }
};
</script>

<template>
  <Layout>

    <Head title="Transport Ride Settings" />
    <PageHeader :title="$t('transport-ride-settings')" :pageTitle="$t('transport-ride-settings')" />
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
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row"> 
                   <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="trip_dispatch_type" class="form-label">{{$t("trip_dispatch_type")}}</label>
                      <select id="trip_dispatch_type" :disabled="app_for === 'demo'" class="form-select" v-model="form.trip_dispatch_type">
                        <!-- <option readonly value="">Select</option> -->
                        <option value="1">{{$t("one_by_one")}}</option>
                        <option value="0">{{$t("to_all_drivers")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.trip_dispatch_type" :key="index" class="text-danger">{{ error.trip_dispatch_type }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="driver_search_radius" class="form-label">{{$t("driver_search_radius_in_kilometer")}}</label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_driver_search_radius_in_kilometer')" 
                      id="driver_search_radius"  v-model="form.driver_search_radius"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.driver_search_radius }}</span>
                    </div>
                  </div>    
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="maximum_time_for_accept_reject_bidding_ride" class="form-label">{{$t("maximum_time_for_accept_reject_bidding_ride_in_seconds")}}</label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_maximum_time_for_accept_reject_bidding_ride_in_seconds')"
                       id="maximum_time_for_accept_reject_bidding_ride" 
                       v-model="form.maximum_time_for_accept_reject_bidding_ride"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.maximum_time_for_accept_reject_bidding_ride }}</span>
                    </div>
                  </div>  
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="user_can_make_a_ride_after_x_miniutes" class="form-label">{{$t("user_can_schedule_a_ride_after_x_minutes")}}</label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_user_can_schedule_a_ride_after_x_minutes') " 
                      id="user_can_make_a_ride_after_x_miniutes"  v-model="form.user_can_make_a_ride_after_x_miniutes"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>  
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="maximum_time_for_find_drivers_for_bitting_ride" class="form-label">{{$t("maximum_time_for_find_drivers_for_bitting_ride")}}</label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_maximum_time_for_find_drivers_for_bitting_ride')" 
                      id="maximum_time_for_find_drivers_for_bitting_ride" v-model="form.maximum_time_for_find_drivers_for_bitting_ride"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> 
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="minimum_time_for_search_drivers_for_schedule_ride" class="form-label">
                        {{$t("minimum_time_for_find_drivers_for_schedule_ride_in_minutes")}}
                        <a href="" class="text-success" data-bs-toggle="modal" data-bs-target="#high">{{$t("how_it_works")}}</a>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_minimum_time_for_find_drivers_for_schedule_ride_in_minutes')" 
                      id="minimum_time_for_search_drivers_for_schedule_ride" v-model="form.minimum_time_for_search_drivers_for_schedule_ride"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> 
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="maximum_time_for_find_drivers_for_regular_ride" class="form-label">{{$t("minimum_time_for_find_drivers_for_regular_rides")}}</label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_minimum_time_for_find_drivers_for_regular_rides')" 
                      id="maximum_time_for_find_drivers_for_regular_ride" v-model="form.maximum_time_for_find_drivers_for_regular_ride"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> 
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="minimum_time_for_starting_trip_drivers_for_schedule_ride" class="form-label">
                        {{$t("minimum_time_for_starting_trip_drivers_for_schedule_ride")}}
                        <a href="" class="text-success" data-bs-toggle="modal" data-bs-target="#low">{{$t("how_it_works")}}</a>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_minimum_time_for_starting_trip_drivers_for_schedule_ride')" 
                      id="minimum_time_for_starting_trip_drivers_for_schedule_ride" v-model="form.minimum_time_for_starting_trip_drivers_for_schedule_ride"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>   
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="trip_accept_reject_duration_for_driver" class="form-label">{{$t("trip_accept_Reject_duration_for_driver")}}</label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_trip_accept_Reject_duration_for_driver')" id="trip_accept_reject_duration_for_driver"
                      v-model="form.trip_accept_reject_duration_for_driver" 
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>  
                  <!-- <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="how_many_times_a_driver_can_enable_the_my_route_booking_per_day" class="form-label">{{$t("how_many_times_a_driver_can_enable_the_my_route_booking_per_day")}}</label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_how_many_times_a_driver_can_enable_the_my_route_booking_per_day')" id="how_many_times_a_driver_can_enable_the_my_route_booking_per_day" 
                      v-model="form.how_many_times_a_driver_can_enable_the_my_route_booking_per_day"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> 
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="bidding_low_percentage" class="form-label">{{$t("bidding_low_percentage")}}</label>
                      <input type="text" class="form-control" :placeholder="$t('enter_bidding_low_percentage')" id="bidding_low_percentage" 
                     v-model="form.bidding_low_percentage"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> 
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="bidding_high_percentage" class="form-label">{{$t("bidding_high_percentage")}}</label>
                      <input type="text" class="form-control" :placeholder="$t('enter_bidding_high_percentage')" id="bidding_high_percentage" 
                      v-model="form.bidding_high_percentage"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> 
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="bidding_amount_increase_or_decrease" class="form-label">{{$t("increase_decrease_percentage_range")}}</label>
                      <input type="text" class="form-control" :placeholder="$t('enter_increase_decrease_percentage_range')" id="bidding_amount_increase_or_decrease" 
                      v-model="form.bidding_amount_increase_or_decrease"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>   -->
                  <div class="col-sm-6 mt-4">
                    <div class="mb-0">
                      <div class="border rounded">
                        <div class="row">
                          <div class="col">
                            <label class="form-check-label p-2 mt-2" for="can_round_the_bill_values">{{$t("can_round_the_bill_values")}}</label>
                          </div>
                          <div class="col">
                            <div class="form-check form-switch form-switch-md float-end p-2">
                              <input
                                type="checkbox"
                                :placeholder="'If Enable This Feature, Ride Bills Will Round Figured'"
                                :offPlaceholder="'Ride Bills Will be In 2 digit Decimal Values '"
                                class="form-check-input"
                                id="can_round_the_bill_values"
                                v-model="form.can_round_the_bill_values"
                                @change.prevent="confirmToggle('can_round_the_bill_values', form.can_round_the_bill_values, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                              />                   
                            </div>                            
                          </div>
                        </div>
                      </div>
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>                   
                  <!-- <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="minimum_trip_distane" class="form-label">{{$t("minimum_trip_distane")}} 
                        <strong class="ms-1 text-danger"> {{$t('note')}} : <em> {{$t('above_this_distance_is_consider_as_outstation_ride')}}</em> </strong>

                      </label>
                      <input type="number" class="form-control" :placeholder="$t('minimum_trip_distane')" id="minimum_trip_distane" v-model.number="form.minimum_trip_distane">
                      <span v-for="(error, index) in errors.minimum_trip_distane" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>                                                                                                         -->
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ settings ? $t('update') : $t('save') }}</button>
                    </div>
                  </div>
                </div>  
              </FormValidation>
            </form>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
<!-- Modal for Confirmation -->
<div v-if="showModal" id="removeNotificationModal" class="modal fade show d-block" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
        <button type="button" class="btn-close" @click="showModal = false"></button>
      </div>
      <div class="modal-body">
        <!-- Display the dynamic modal text -->
        <p>{{ modalText }}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" @click="cancelModal">Cancel</button>
        <button type="button" class="btn btn-primary" @click="enableStatus">OK</button>
      </div>
    </div>
  </div>
</div>



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
<!-- Low % Modals -->
<div id="low" class="modal fade" tabindex="-1" aria-labelledby="lowLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <h5 class="fs-15">
                  {{$t("minimum_time_for_starting_trip_drivers_for_schedule_ride")}}
                </h5>
                <p class="text-muted"> {{$t("minimum_time_for_starting_trip_drivers_for_schedule_ride_explanation")}} </p>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- High % Modals -->
<div id="high" class="modal fade" tabindex="-1" aria-labelledby="highLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <h5 class="fs-15">
                  {{$t("minimum_time_for_find_drivers_for_schedule_ride_in_minutes")}}
                </h5>
                <p class="text-muted"> {{$t("minimum_time_for_find_drivers_for_schedule_ride_in_minutes_explanation")}} </p>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
