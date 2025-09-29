<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import searchbar from "@/Components/widgets/searchbar.vue";
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";
import { useI18n } from 'vue-i18n';

export default {
    data() {
        return {
            rightOffcanvas: false,
            counts: {
                open: 0,
                acknowledge: 0,
                closed: 0,
                total: 0
            }
        };
    },
    components: {
        Layout,
        PageHeader,
        Head,
        Pagination,
        Multiselect,
        flatPickr,
        Link,
        searchbar,

    },
    props: {
        successMessage: String,
        alertMessage: String,
        app_for:String,
        employees:Array,
        ticket: Object,
        support_ticket:Array,
        admin:Array
    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref('');
        const filter = useForm({
            support_type: null,
            status: null,
            limit:10
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const showEmployess = ref(false);
        const employees_name = ref([]);
        const support_ticket = ref(props.support_ticket);
        const ticket_id = ref();
        const validationRef = ref(null);
        const paginatorOption = ref({}); // Spread the results to make them reactive
        const employee_id = ref(props.admin);
        

        const form = useForm({
            assign_to: props.ticket ? props.ticket.assign_to || "" : "",
        });

        const validationRules = {
            assign_to: { required: true },
    };
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const rightOffcanvas = ref(false);
        const filterData = () => {
            fetchDatas();
            modalFilter.value = true;
            rightOffcanvas.value = false;
        };


        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            rightOffcanvas.value = false;
        };


        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/title/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('title_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_title'), 'error');
            }
        };

        const deleteModal = async (itemId) => {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Yes, delete it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await deleteData(itemId);
                    } catch (error) {
                        console.error(t('error_deleting_data'), error);
                        Swal.fire(t('error'), t('failed_to_delete_the_data'), "error");
                    }
                }
            });
        };

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/support-tickets/list`, { params });
                results.value = response.data.results;
                console.log("result",results.value);
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_title'), error);
            }
        };

        const toggleActiveStatus = async (id, status, employee_id) => {
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
                        await axios.post(`/support-tickets/update-status`, { id: id, status: status, employee_id: employee_id });
                        const index = results.value.findIndex(item => item.id === id);
                        if (index !== -1) {
                            results.value[index].active = status; // Update the active status locally
                        }
                        Swal.fire(t('changed'), t('status_updated_successfully'), "success");
                        router.get('/support-tickets');
                    } catch (error) {
                        console.error(t('error_updating_status'), error);
                        Swal.fire(t('error'), t('failed_to_update_status'), "error");
                    }
                }
            });
        };

        const handlePageChanged = async (page) => {
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/title/edit/${result.id}`); 
        };

        const assignEmployees = async (id) => {
            showEmployess.value = true; 
            ticket_id.value = id;
            const category_type = ticket_id.value.ticket_title.category_type
            if (category_type) {
                await fetchEmployees(category_type);
            } else {
                console.error("Category type is undefined");
            }       
        };

        const fetchEmployees = async (categoryType) => {
            try {
                const response = await axios.get(`/admins/list?category_type=${categoryType}`);
                const categoryArray = categoryType.split(',');
                console.log(response.data.results);

                employees_name.value = response.data.results.filter(emp => 
                   emp.category_type && categoryArray.some(type => emp.category_type.includes(type))
                );
            } catch (error) {
                console.error("Error fetching employees:", error);
            }
        };

        const handleSubmit = async () => {
            console.log("data",form.data());
            try {
                const response = await axios.post(`support-tickets/update/${ticket_id.value.id}`, {
                    'assign_to':form.assign_to
                });                 

                if (response.status === 200 || response.status === 201) { 
                    form.reset();
                    router.get(`/support-tickets`);
                } else {
                    console.error('Unexpected response status:', response.status);
                }
            } catch (error) {
                console.error('Error submitting form:', error);
            }
        };
        const getStatusLabel = (status) => {
            switch (status) {
                case 1:
                    return "Pending";
                case 2:
                    return "Acknowledge";
                case 3:
                    return "Closed";
                default:
                    return "Unknown";
            }
        };

        const getStatusVariant = (status) => {
            switch (status) {
                case 1:
                    return "primary"; // Red for open
                case 2:
                    return "warning"; // Yellow for acknowledge
                case 3:
                    return "danger"; // Green for closed
                default:
                    return "secondary"; // Grey for unknown
            }
        };
        const updatePaginatorOptions = () => {
            paginatorOption.value = [10, 25, 50, 100,200,500]; // Default static options
        };
        // **Handle per-page changes**
        const changeEntriesPerPage = () => {
            fetchDatas(); // Fetch new data
        };
        return {
            results,
            modalShow,
            deleteItemId,
            successMessage,
            alertMessage,
            filterData,
            deleteModal,
            closeModal,
            deleteData,
            dismissMessage,
            searchTerm,
            fetchSearch,
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            filter,
            handlePageChanged,
            editData,
            rightOffcanvas,
            toggleActiveStatus,
            assignEmployees,
            showEmployess,
            employees_name,
            handleSubmit,
            validationRules,
            validationRef,
            form,
            fetchEmployees,
            getStatusLabel,
            getStatusVariant,
            paginatorOption,
            changeEntriesPerPage,
            employee_id
        };
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
    // mounted() {
    //     this.fetchDatas();
    // },
    methods: {
        async fetchTicketCounts() {
            try {
                const response = await axios.get(`/support-tickets/ticket-counts`);
                this.counts = response.data;
            } catch (error) {
                console.error("Error fetching ticket counts:", error);
            }
        }
    },
    mounted() {
        this.fetchDatas();
        this.fetchTicketCounts(); // Fetch data when component loads
        setInterval(this.fetchTicketCounts, 10000); // Refresh every 10 seconds
    }
};
</script>

<template>
    <Layout>

        <Head title="Tickets" />
        <PageHeader :title="$t('tickets')" :pageTitle="$t('support_management')" />
        <div class="row">
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="fw-medium text-muted mb-0">{{$t("total_tickets")}}</h5>
                                <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="547">{{ counts.total ?? 0 }}</span></h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-4">
                                        <i class="ri-ticket-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div> <!-- end card-->
            </div>
            <!--end col-->
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="fw-medium text-muted mb-0">{{$t("pending")}} {{$t("tickets")}}</h5>
                                <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="547">{{ counts.open ?? 0 }}</span></h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded-circle fs-4">
                                        <i class="ri-ticket-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div> <!-- end card-->
            </div>
            <!--end col-->
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="fw-medium text-muted mb-0">{{$t("assigned_tickets")}}</h5>
                                <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="124">{{ counts.acknowledge ?? 0 }}</span></h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-4">
                                        <i class="ri-ticket-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div>
            <!--end col-->
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="fw-medium text-muted mb-0">{{$t("closed_tickets")}}</h5>
                                <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="107">{{ counts.closed ?? 0 }}</span></h2>
                            </div>
                            <div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-4">
                                        <i class="ri-ticket-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div>
            </div>
            <!--end col-->
        </div>
<!-- Ticket list -->
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                        <BRow class="g-2">
                            <BCol md="3">
                                <div class="d-flex align-items-center mt-3">
                                    <label class="me-2 text-muted">{{$t("show")}}</label>
                                    <select v-model="filter.limit" @change="changeEntriesPerPage" class="form-select form-select-sm w-auto">
                                    <option v-for="option in paginatorOption" :key="option" :value="option">
                                        {{ option }}
                                    </option>
                                    </select>
                                    <label class="ms-2 text-muted">{{$t("entries")}}</label>
                                </div>
                            </BCol>
                            <BCol md="3">
                            </BCol>
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <searchbar placeholder="search_by_ticket_id" @search="fetchSearch"></searchbar>
                                    <BButton variant="danger" @click="rightOffcanvas = true"><i
                                            class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}</BButton>

                                    <!-- <Link href="/title/create" v-if="permissions.includes('add-ticket-title')">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i>{{$t("add_title")}}</BButton>
                                    </Link> -->
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{$t("ticket_id")}}</th>
                                        <th scope="col">{{$t("title")}}</th>
                                        <th scope="col">{{$t("support_type")}}</th>
                                        <th scope="col">{{$t("service_location")}}</th>
                                        <th scope="col">{{$t("user_name")}}</th> 
                                        <th scope="col">{{$t("user_type")}}</th> 
                                        <th scope="col">{{$t("assign_to")}}</th>
                                        <th scope="col">{{$t("status")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ result.ticket_id }}</td> 
                                        <td>{{ result.title }}</td>                                       
                                        <td>{{ result.support_type}} <span v-if="result.request_id">({{result.request_id}})</span></td>
                                        <td>{{ result.service_location_name}}</td>
                                        <td>{{ result.user_name }}</td>
                                        <td>{{ result.user_type }}</td>
                                        <td  style="cursor: pointer;">
                                            <span v-if="result?.admin_details">
                                                <BBadge  variant="success">
                                                      {{ result.admin_details.first_name }}
                                                </BBadge>
                                            </span>  
                                            <span v-else>
                                                <BBadge  variant="warning">
                                                      {{ $t('not_taken') }}
                                                </BBadge>                                           
                                            </span> 
                                        </td>
                                        <td>
                                            <BBadge 
                                                :variant="getStatusVariant(result.status)"
                                            >
                                                {{ getStatusLabel(result.status) }}
                                            </BBadge>
                                        </td>
                                        <div class="dropdown">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#"  v-if="result.status === 1" >
                                                        <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> 
                                                        <span @click="toggleActiveStatus(result.id, 2, employee_id.user_id)">{{ $t('acknowledge') }}</span>
                                                    </a>
                                                    <a class="dropdown-item" href="#"  v-if="result.status === 2" @click="toggleActiveStatus(result.id, 3,  employee_id.user_id)">
                                                        <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> 
                                                        <span >{{ $t('closed') }}</span>
                                                    </a>
                                                    <!-- <a class="dropdown-item" href="#">
                                                        <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> 
                                                        <span  @click.prevent="viewTicket(result)">{{ $t('view') }}</span>
                                                    </a> -->
                                                    <Link class="dropdown-item" :href="`/support-tickets/view-details/${result.id}`">
                                                        <i class="bx bx-radio-circle-marked align-center text-muted me-2"></i> 
                                                        {{ $t('view') }}
                                                    </Link>
                                                </div>
                                            </div>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="12" class="text-center">
                                            <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                            <h5>{{$t("no_data_found")}}</h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </BCardBody>
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




        <BModal v-model="showEmployess" hide-footer :title="$t('assign_to')" class="v-modal-custom" size="md">
            <!-- <BCard> -->
            <!-- <BCardBody> -->
                <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
                    <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label for="assing_to" class="form-label">{{$t("assign_to")}}<span class="text-danger">*</span></label>
                                    <select id="assing_to" class="form-control" v-model = "form.assign_to">
                                        <option value="" disabled>{{$t("select_title")}}</option>
                                        <option v-for="employees in employees_name" :key="employees.first_name" :value="employees.user_id">{{ employees.first_name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer v-modal-footer">
                                <BLink href="javascript:void(0);" class="btn btn-link link-warning fw-medium"
                                    @click="showEmployess = false">
                                    <i class="ri-close-line me-1 align-middle"></i> {{$t('close')}}
                                </BLink>
                                <button type="button" class="btn btn-soft-info waves-effect waves-light" @click.prevent="handleSubmit"> {{$t('submit')}}
                                </button>
                            </div>
                    </FormValidation>
                </form>
            <!-- </BCardBody> -->
            <!-- </BCard> -->
            </BModal>
        <!-- filter -->
        <BOffcanvas v-model="rightOffcanvas" placement="end" :title="$t('Ticket_filters')" header-class="bg-light"
            body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BFrom action="" class="d-flex flex-column justify-content-end h-100">
                <div class="offcanvas-body">
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("status")}}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="filter.status">
                            <option selected disabled>{{$t("select")}}</option>
                            <option value="1">{{$t("open")}}</option>
                            <option value="2">{{$t("acknowledge")}}</option>
                            <option value="3">{{$t("closed")}}</option>
                        </select>
                    </div>


                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("support_type")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"  v-model="filter.support_type" type="radio" name="inlineRadioOptions"
                                    id="general" value='general'>
                                <label class="form-check-label" for="general">{{$t("general")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" v-model="filter.support_type" type="radio" name="inlineRadioOptions"
                                    id="request" value="request">
                                <label class="form-check-label" for="request">{{$t("request")}}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton variant="light" @click="clearFilter" class="w-100">{{$t("clear_filter")}}</BButton>
                    <BButton type="submit" @click="filterData" variant="success" class="w-100">
                        {{$t("apply")}}
                    </BButton>
                </div>
                <!--end offcanvas-footer-->
            </BFrom>
        </BOffcanvas>
        <!--end offcanvas-->
        <!-- filter end -->

        <!-- Pagination -->
        <Pagination :paginator="paginator" @page-changed="handlePageChanged" />
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
a{
    cursor: pointer;
}
/* .text-danger {
    padding-top: 5px;
} */

</style>
