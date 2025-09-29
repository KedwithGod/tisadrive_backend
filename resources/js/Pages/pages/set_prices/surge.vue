<script>
import { Link, Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import { ref, reactive } from "vue";
import axios from "axios";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
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
    flatPickr,
    Link,
  },
    props: {
        successMessage: String,
        alertMessage: String,
        zoneType: {
            type: Object,
            required: true,
        },
        surge: {
            type: Object,
            required: true,
        }

    },
    setup(props) {

        const { t } = useI18n();

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');
        
        const surge = ref(props.surge);
        const zoneType = ref(props.zoneType);
        const errors = ref({});
        
        const activeDay = ref('Sunday');

        const getCurrentTime = (now) => {
          const hours = now.getHours().toString().padStart(2, '0');
          const minutes = now.getMinutes().toString().padStart(2, '0');
          return `${hours}:${minutes}`;
        };

        const timeToMinutes = (time) => {
          const [hours, minutes] = time.split(':').map(Number);
          return hours * 60 + minutes;
        };

        const preloadingTime = {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            defaultDate: getCurrentTime(new Date()),
          };

        const fields = reactive({
          Sunday: surge ? surge.value['Sunday'] : [],
          Monday: surge ? surge.value['Monday'] : [],
          Tuesday: surge ? surge.value['Tuesday'] : [],
          Wednesday: surge ? surge.value['Wednesday'] : [],
          Thursday: surge ? surge.value['Thursday'] : [],
          Friday: surge ? surge.value['Friday'] : [],
          Saturday: surge ? surge.value['Saturday'] : [],
        });

        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        const addField = (day) => {
          if(validate()){
            const from = new Date();
            const to =new Date(from).addMinutes(60);
            const newField = {
              index: fields[day] ? fields[day].length + 1 : 1,
              start_time: getCurrentTime(from),
              end_time: getCurrentTime(to),
              value: 0
            };
            fields[day].push(newField);
          };
          sortBy(day);
        };

        const sortBy = (day) => {
            fields[day].sort((a, b) => {
              const timeA = timeToMinutes(a.start_time);
              const timeB = timeToMinutes(b.start_time);
              return timeA - timeB;
            });
            
        }

        const validate = () => {
          let isValid = true;
          
          days.forEach((day) => {
            const currentDayFields = fields[day];
              if(currentDayFields.length > 1){
              currentDayFields.forEach((field, index) => {
                if (index > 0) {
                  const prevField = currentDayFields[index - 1];
                  const isOverlap = timeToMinutes(field.start_time) < timeToMinutes(prevField.end_time);
                  const isReverseTime = timeToMinutes(field.end_time) < timeToMinutes(field.start_time);
                  
                  if (isOverlap) {
                    if (!errors.value[day]) {
                      errors.value[day] = {};
                    }
                    if (!errors.value[day][index]) {
                      errors.value[day][index] = [];
                    }
                    
                    errors.value[day][index] = t('surge_must_be_distinct');
                    isValid = false;
                  } else if (isReverseTime) {
                    if (!errors.value[day]) {
                      errors.value[day] = {};
                    }
                    if (!errors.value[day][index]) {
                      errors.value[day][index] = [];
                    }
                    
                    errors.value[day][index] = t('surge_start_must_be_before_end');
                    isValid = false;
                  } else {
                    if (errors.value[day] && errors.value[day][index]) {
                      delete errors.value[day][index];
                      if (Object.keys(errors.value[day]).length === 0) {
                        delete errors.value[day];
                      }
                    }
                  }
                }
              });
            }
          });
          
          return isValid;
        };

        const surgeUpdate = async () => {
          try {
            if(validate()){
              const response = await axios.post(`/set-prices/surge/update/${zoneType?.value?.id}`,{ surge: fields});
              surge.value = response.data.surge;
              Swal.fire(t('success'), t('surge_updated_successfully'), 'success');
            }
          } catch (error) {
            console.log(error);
            Swal.fire(t('error'), t('surge_update_failed'), 'error');
          }
        }
    
        Date.prototype.addMinutes = function (minutes) {
          this.setMinutes(this.getMinutes() + minutes);
          return this;
        };
        const removeField = (day, index) => {
          fields[day].splice(index, 1);
        };
        return {
          successMessage,
          alertMessage,
          fields,
          days,
          surgeUpdate,
          addField,
          sortBy,
          errors,
          removeField,
          activeDay,
          preloadingTime,
        };
    }
};
</script>

<template>
  <Layout>
    <Head title="Surge" />
    <PageHeader :title="$t('surge')" :pageTitle="$t('surge')" pageLink="/set-prices" />
    <BCard no-body id="">
      <BCardHeader class="border-0">
        <BRow>
          <BCol lg="12">
            <div class="row">                                   
              <div class="col-sm-3">
                <div class="card border border-dashed border-primary">
                  <div class="card-body text-center">
                    <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("zone")}}</h4>
                    <h5>{{ zoneType.zone_name }}</h5>
                  </div>
                </div>
              </div>  
              <div class="col-sm-3">
                <div class="card border border-dashed border-primary">
                  <div class="card-body text-center">
                    <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("vehicle_type")}}</h4>
                    <h5>{{ zoneType.vehicle_type_name }}</h5>
                  </div>
                </div>
              </div>                                 
            </div>
          </BCol>
        </BRow>
      </BCardHeader>
      </BCard>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BTabs nav-class="nav-tabs-custom nav-success mb-3 pt-3" justified v-model="activeDay">
            <BTab
              v-for="(day, index) in days"
              :key="index"
              :active="activeDay === day"
            >
            <template #title>
              {{ day }}
              <span v-if="errors?.[day]" class="text-danger">*</span>
            </template>

              <div class="text-muted">
                <div class="d-flex">
                  <div class="flex-grow-1 ms-2">
                    <BCardHeader v-if="permissions.includes('update-zone-surge')" class="border-0">
                      <BRow class="g-2">
                        <BCol md="auto" class="ms-auto">
                          <div class="d-flex align-items-center gap-2">
                            <BButton variant="primary" @click="addField(day)">
                              <i class="ri-add-line align-bottom me-1"></i> {{ $t('add_new_surge') }}
                            </BButton>
                          </div>
                        </BCol>
                      </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                       <!-- Show an empty state if no fields are available for the selected day -->
                      <div v-if="fields[day].length === 0"  class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                        <i class="ri-file-add-line text-muted display-4"></i>
                        <p class="text-muted mt-2">{{$t('add_surge')}}</p>
                      </div>
                      <!-- Iterate over the fields array specific to the active day -->
                      <div
                        v-else
                        v-for="(field, index) in fields[day]"
                        :key="field.index"
                        class="row d-flex align-items-center"
                      >
                        <div class="col-sm-3">
                          <div class="mb-3">
                            <label for="from" class="form-label">{{ $t('from') }}</label>
                            <flat-pickr v-model="field.start_time" @on-change="sortBy(day)"
                                        :placeholder="$t('from')" :config="preloadingTime"  class="form-control flatpickr-input">
                            </flat-pickr>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="mb-3">
                            <label for="to" class="form-label">{{ $t('To') }}</label>
                            <flat-pickr v-model="field.end_time"
                                      :placeholder="$t('To')" :config="preloadingTime"  class="form-control flatpickr-input">
                            </flat-pickr>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="mb-3">
                            <label for="surge" class="form-label">{{ $t('surge_value') }}</label>
                            <input
                              type="text"
                              class="form-control"
                              v-model="field.value"
                              :placeholder="$t('surge')"
                            />
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="mt-3">
                            <div class="form-check form-check-inline">
                              <i
                                class="bx bx-trash text-danger fs-22 btn"
                                @click="removeField(day, index)"
                              ></i>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <span v-if="errors?.[day]?.[index]" class="text-danger">{{ errors?.[day]?.[index] }}</span>

                        </div>
                      </div>
                      <BRow v-if="permissions.includes('update-zone-surge')" class="g-2">
                        <BCol md="auto" class="ms-auto">
                          <div class="d-flex align-items-center gap-2">
                            <BButton variant="primary" @click="surgeUpdate(day)">
                              {{$t('update')}}
                            </BButton>
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
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Success</strong> - {{
                        successMessage }}
                    <button type="button" class="btn-close btn-close-success" @click="dismissMessage"
                        aria-label="Close Success Message"></button>
                </div>
            </div>

            <!-- Alert Message -->
            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" data="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage
                    }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage"
                        aria-label="Close Alert Message"></button>
                </div>
            </div>
        </div>

  </Layout>
</template>