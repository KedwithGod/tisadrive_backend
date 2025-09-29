<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref,onUnmounted } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import ImageUp from "@/Components/ImageUp.vue";
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
    imageUpload,
    ImageUp
  },
  props: {
    successMessage: String,
    alertMessage: String,
    notification :{
            type: Object,
            required: true,
        },
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
        const searchTerm = ref("");
        const filter = useForm({
            all: "",
            locked: "",
        });
        const { t } = useI18n();
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };



        const filterData = () => {
            modalFilter.value = true;
        };


        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            modalFilter.value = false;
        };

    const toggleActiveStatus = async (id, pushStatus, mailStatus, smsStatus) => {
    Swal.fire({
        title: t('are_you_sure'),
        text: t('you_are_about_to_change_status'),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: t('yes'),
        cancelButtonText: t('cancel')
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                // Send a single request with all statuses
                await axios.post(`/notification-channel/update-status`, {
                    id: id,
                    push_notification: pushStatus,
                    mail: mailStatus,
                    sms: smsStatus
                });

                const index = results.value.findIndex(item => item.id === id);
                if (index !== -1) {
                    results.value[index].push_notification = pushStatus;
                    results.value[index].mail = mailStatus;
                    results.value[index].sms = smsStatus; // Update all statuses locally
                    router.get(`/notification-channel`);
                }

                Swal.fire(t('changed'), t('status_updated_successfully'), "success");
            } catch (error) {
                console.error(t('error_updating_status'), error);
                Swal.fire(t('error'), t('failed_to_update_status'), "error");
            }
        }
    });
}


        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/notification-channel/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_notification'), error);
            }
        };

        const handlePageChanged = async (page) => {
          localStorage.setItem("/notification-channel/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/notification-channel/edit/${result.id}`); 
          if(result.topics === 'Invoice For End of the Ride User'){
            router.get(`/notification-channel/user-invoice-edit/${result.id}`); 
          }
          if(result.topics === 'Invoice For End of the Ride Driver'){
            router.get(`/notification-channel/driver-invoice-edit/${result.id}`); 
          }
         
        };

        const editPushData = async (result) =>  {
            router.get(`/notification-channel/push-template/edit/${result.id}`); 
         
        };

        return {
            results,
            modalShow,
            deleteItemId,
            successMessage,
            alertMessage,
            filterData,
            dismissMessage,
            searchTerm,
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            filter,
            handlePageChanged,
            editData,
            fetchSearch,
            toggleActiveStatus,
            editPushData
        };
    },
    mounted() {
        this.fetchDatas();
        const savedPage = localStorage.getItem("/notification-channel/list");
        if(savedPage){
            this.handlePageChanged(Number(savedPage));
        }
        else{
            this.handlePageChanged(1);
        }
    },
};
</script>

<template>
  <Layout>

    <Head title="Notification Channel" />
    <PageHeader :title="$t('notification-channel')" :pageTitle="$t('notification-channel')" />
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
             <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-3" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#customers" role="tab" aria-selected="false">
                    {{$t("customers")}}
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#drivers" role="tab" aria-selected="false">
                    {{$t("driver")}}
                </a>
              </li>
            </ul>
             <!-- Nav tabs Ends -->
            <!-- Tab panes -->
            <div class="tab-content  text-muted mt-4">
              <div class="tab-pane active" id="customers" role="tabpanel">
                <div class="table-responsive">
                  <table class="table align-middle position-relative table-nowrap">
                    <thead class="table-active">
                        <tr>
                            <!-- <th scope="col">{{$t("s_no")}}</th> -->
                            <th scope="col">{{$t("topics")}}</th>
                            <th scope="col">{{$t("push_notifications")}}</th>
                            <th scope="col">{{$t("mail")}}</th>
                            <th scope="col">{{$t("email-template")}}</th>
                            <th scope="col">{{$t("push-template")}}</th>
                        </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(result, index) in results.filter(result => result.role === 'user')" :key="index">
                          <!-- <td>1</td> -->
                          <td>
                            <div>
                              <h6 class="fw-bold">{{ result.topics }}</h6>
                              <p>{{ result.topics_content }}</p>
                            </div>
                          </td>
                          <td>
                            <div :class="{
                                    'form-check': true,
                                    'form-switch': true,
                                    'form-switch-lg': true,
                                    'form-switch-success': result.push_notification,
                                }">
                                 <input class="form-check-input" type="checkbox" role="switch" 
                                  @click.prevent="toggleActiveStatus(result.id, !result.push_notification, result.mail, result.sms)" 
                                  :id="'status_push_' + result.id" :checked="result.push_notification">
                            </div>
                        </td>
                        <td>
                            <div :class="{
                                    'form-check': true,
                                    'form-switch': true,
                                    'form-switch-lg': true,
                                    'form-switch-success': result.mail,
                                }">
                                <input class="form-check-input" type="checkbox" role="switch" 
                                  @click.prevent="toggleActiveStatus(result.id, result.push_notification, !result.mail, result.sms)" 
                                  :id="'status_mail_' + result.id" :checked="result.mail">
                            </div>
                        </td>
                          <td>
                            <BButton  @click.prevent="editData(result)" class="btn btn-soft-warning btn-sm m-2" data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')">
                              <i class='bx bxs-edit-alt bx-xs'></i>
                            </BButton>
                          </td>
                          <td>
                            <BButton  @click.prevent="editPushData(result)" class="btn btn-soft-warning btn-sm m-2" data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')">
                              <i class='bx bxs-edit-alt bx-xs'></i>
                            </BButton>
                          </td>
                      </tr>
                      
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="tab-pane " id="drivers" role="tabpanel">
                <div class="table-responsive">
                  <table class="table align-middle position-relative table-nowrap">
                    <thead class="table-active">
                        <tr>
                            <!-- <th scope="col">{{$t("s_no")}}</th> -->
                            <th scope="col">{{$t("topics")}}</th>
                            <th scope="col">{{$t("push_notifications")}}</th>
                            <th scope="col">{{$t("mail")}}</th>
                            <th scope="col">{{$t("email-template")}}</th>
                            <th scope="col">{{$t("push-template")}}</th>
                        </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(result, index) in results.filter(result => result.role === 'driver')" :key="index">
                          <!-- <td>1</td> -->
                          <td>
                            <div>
                              <h6 class="fw-bold">{{ result.topics }}</h6>
                              <p>{{ result.topics_content }}</p>
                            </div>
                          </td>
                          <td>
                            <div :class="{
                                    'form-check': true,
                                    'form-switch': true,
                                    'form-switch-lg': true,
                                    'form-switch-success': result.push_notification,
                                }">
                                 <input class="form-check-input" type="checkbox" role="switch" 
                                  @click.prevent="toggleActiveStatus(result.id, !result.push_notification, result.mail, result.sms)" 
                                  :id="'status_push_' + result.id" :checked="result.push_notification">
                            </div>
                        </td>
                        <td>
                            <div :class="{
                                    'form-check': true,
                                    'form-switch': true,
                                    'form-switch-lg': true,
                                    'form-switch-success': result.mail,
                                }">
                                <input class="form-check-input" type="checkbox" role="switch" 
                                  @click.prevent="toggleActiveStatus(result.id, result.push_notification, !result.mail, result.sms)" 
                                  :id="'status_mail_' + result.id" :checked="result.mail">
                            </div>
                        </td>
                          <td>
                            <BButton  @click.prevent="editData(result)" class="btn btn-soft-warning btn-sm m-2" data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')">
                              <i class='bx bxs-edit-alt bx-xs'></i>
                            </BButton>
                          </td>
                          <td>
                            <BButton  @click.prevent="editPushData(result)" class="btn btn-soft-warning btn-sm m-2" data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')">
                              <i class='bx bxs-edit-alt bx-xs'></i>
                            </BButton>
                          </td>
                      </tr>
                      
                    </tbody>
                  </table>
                </div>                  
              </div>
               <!-- Pagination -->
        <Pagination :paginator="paginator" @page-changed="handlePageChanged" />
            </div>
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
