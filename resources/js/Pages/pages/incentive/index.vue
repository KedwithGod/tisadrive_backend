<template>
  <Layout>
    <Head title="Incentive" />
    <PageHeader :title="$t('incentive')" :pageTitle="$t('incentive')" pageLink="/set-prices" />
    <BRow>
      <BCol lg="12">
        <BCard v-if="app_for === 'demo'" no-body id="tasksList">
          <BCardHeader class="border-0">
            <div class="alert bg-warning border-warning fs-18" role="alert">
              <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
            </div>
          </BCardHeader>
        </BCard>
        <BCard no-body id="">
          <BCardHeader class="border-0">
              <BRow>
                  <BCol lg="12">
                      <div class="row">                                   
                          <div class="col-sm-3">
                              <div class="card border border-dashed border-primary">
                                  <div class="card-body text-center">
                                      <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("zone")}}</h4>
                                      <h5>{{ zone_type.zone_name }}</h5>
                                  </div>
                              </div>
                          </div>  
                          <div class="col-sm-3">
                              <div class="card border border-dashed border-primary">
                                  <div class="card-body text-center">
                                      <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("vehicle_type")}}</h4>
                                      <h5>{{ zone_type.vehicle_type_name }}</h5>
                                  </div>
                              </div>
                          </div>                                 
                      </div>
                  </BCol>
              </BRow>
          </BCardHeader>
        </BCard>
        <BCard no-body id="tasksList">
          <BTabs nav-class="nav-tabs-custom nav-success mb-3 pt-3" justified v-model="activemode">
            <BTab
              v-for="(mode, index) in modes"
              :key="index"
              :title="mode"
              :active="activemode === mode"
            >
              <div class="text-muted">
                <div class="d-flex">
                  <div class="flex-grow-1 ms-2">
                    <BCardHeader class="border-0">
                      <BRow class="g-2">
                        <BCol md="auto" class="ms-auto">
                          <div class="d-flex align-items-center gap-2">
                            <BButton v-if="app_for!== 'demo'" variant="primary" @click="addField(mode)">
                              <i class="ri-add-line align-bottom me-1"></i> {{ $t('add') }}
                            </BButton>
                          </div>
                        </BCol>
                      </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                      <!-- Show an empty state if no fields are available for the selected mode -->
                      <div v-if="fields[mode].length === 0" class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                        <i class="ri-file-add-line text-muted display-4"></i>
                        <p class="text-muted mt-2">{{ $t('add_mode') }}</p>
                      </div>
                      <!-- Iterate over the fields array specific to the active mode -->
                      <div
                        v-else
                        v-for="(field, index) in fields[mode]"
                        :key="field.index"
                        class="row d-flex align-items-center"
                      >
                        <div class="col-sm-5">
                          <div class="mb-3">
                            <label for="ride-count" class="form-label">{{ $t('ride_count') }}</label>
                            <input
                              type="number"
                              class="form-control"
                              v-model="field.ride_count"
                              @input="validateField(mode, index, field.ride_count)"
                              :placeholder="$t('ride_count')"
                            />
                          </div>
                        </div>
                        <div class="col-sm-5">
                          <div class="mb-3">
                            <label for="amount" class="form-label">{{ $t('incentive_amount') }}</label>
                            <input
                              type="number"
                              class="form-control"
                              v-model="field.amount"
                              :placeholder="$t('amount')"
                            />
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="mt-3">
                            <div v-if="app_for!== 'demo'" class="form-check form-check-inline">
                              <i
                                class="bx bx-trash text-danger fs-22 btn"
                                @click="removeField(mode, index)"
                              ></i>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <span v-if="errors?.[mode]?.[index]" class="text-danger">{{ errors?.[mode]?.[index] }}</span>
                        </div>
                      </div>
                      <BRow class="g-2">
                        <BCol md="auto" class="ms-auto">
                          <div class="d-flex align-items-center gap-2">
                            <BButton v-if="app_for!== 'demo'" variant="primary" @click="submitForm">{{ $t('submit') }}</BButton>
                          </div>
                        </BCol>
                      </BRow>
                    </BCardBody>
                  </div>
                </div>
              </div>
            </BTab>
          </BTabs>
        </BCard>
      </BCol>
    </BRow>

    <div>
      <!-- Success Message -->
      <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" data="alert"
           id="alertMsg">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Success</strong> - {{ successMessage }}
          <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
        </div>
      </div>

      <!-- Alert Message -->
      <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" data="alert"
           id="alertMsg">
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
import { Link, Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import { ref } from "vue";
import axios from "axios";
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";
import { useI18n } from 'vue-i18n';

export default { 
  data() {
    return {
      rightOffcanvas: false,
    };
  },
  computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
  components: {
    Layout,
    PageHeader,
    Head,
    Link,
  },
  props: {
    successMessage: String,
    alertMessage: String,
    app_for: String,
    dailyIncentives: Array, // Pass stored daily incentives
    weeklyIncentives: Array, // Pass stored weekly incentives
    zone_type: Object,
  },
  setup(props) {
    const { t } = useI18n();
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');
    const errors = ref({});
    const activemode = ref('Daily');

    // Initialize fields with passed data if available, else set them to empty arrays
    const fields = ref({
      Daily: props.dailyIncentives?.length ? props.dailyIncentives : [],
      Weekly: props.weeklyIncentives?.length ? props.weeklyIncentives : [],
    });

    const modes = ['Daily', 'Weekly'];

    // Validate that ride_count is unique within the same mode
    const validateUniqueRideCount = (mode, rideCount, excludeIndex = -1) => {
      const isDuplicate = fields.value[mode].some((field, index) => {
        return field.ride_count === rideCount && index !== excludeIndex;
      });
      return !isDuplicate;
    };
    const validateField = (mode, index, value) => {
      if (!validateUniqueRideCount(mode, value, index)) {
        if (!errors.value[mode]) errors.value[mode] = {};
        errors.value[mode][index] = t('ride_count_must_be_unique');
      } else if (errors.value[mode]?.[index]) {
        delete errors.value[mode][index];
      }
    };

    const addField = (mode) => {
      const newField = {
        index: fields.value[mode].length + 1,
        ride_count: 0,
        amount: 0,
        zone_type_id: props.zone_type ? props.zone_type.id : null,
      };
      fields.value[mode].push(newField);
      // console.log(fields.value[mode]);
    };

    const removeField = (mode, index) => {
      fields.value[mode].splice(index, 1);
    };

    const submitForm = async () => {
      // Validate uniqueness of ride_count across all fields in each mode
      let hasErrors = false;
      errors.value = {}; // Clear existing errors

      modes.forEach((mode) => {
        const rideCounts = fields.value[mode].map((field) => field.ride_count);
        const duplicateIndices = rideCounts.reduce((acc, count, index, arr) => {
          if (arr.indexOf(count) !== index) acc.push(index);
          return acc;
        }, []);

        if (duplicateIndices.length > 0) {
          hasErrors = true;
          duplicateIndices.forEach((index) => {
            if (!errors.value[mode]) errors.value[mode] = {};
            errors.value[mode][index] = t('ride_count_must_be_unique');
          });
        }
      });

      if (hasErrors) {
        alertMessage.value = t('ride_count_must_be_unique');
        return;
      }

      // Submit form data
      const formData = {
        Daily: fields.value.Daily,
        Weekly: fields.value.Weekly,
        zone_type_id: props.zone_type ? props.zone_type.id : null,
      };

      try {
        const response = await axios.post('/incentives/update', formData);
        successMessage.value = t('form_submitted');
        alertMessage.value = '';
      } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
          errors.value = error.response.data.errors;
        }
        alertMessage.value = t('form_submission_failed');
      }
    };

    return {
      successMessage,
      alertMessage,
      fields,
      modes,
      addField,
      removeField,
      submitForm,
      validateField,
      activemode,
      errors,
    };
  }
};
</script>
