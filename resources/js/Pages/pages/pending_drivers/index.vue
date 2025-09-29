<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";
import { useI18n } from 'vue-i18n';


export default {
    data() {
        return {
            rightOffcanvas: false,
            isListView: true, // Default to list view
            copiedIndex: null,
        };
    },
    created() {
        // Load view mode from localStorage
        const savedView = localStorage.getItem("pendingViewMode");
        if (savedView) {
        this.isListView = savedView === "list";
        }
    },
    methods: {
        toggleView(view) {
        this.isListView = view === "list";
        localStorage.setItem("pendingViewMode", view); // Save to localStorage
        },
        copyText(text, index) {
            navigator.clipboard.writeText(text).then(() => {
                this.copiedIndex = index;
                setTimeout(() => {
                this.copiedIndex = null;
                }, 2000); // Reset the icon after 2 seconds
            });
        },
    },
    components: {
        Layout,
        PageHeader,
        Head,
        Pagination,
        Multiselect,
        flatPickr,
        Link,
        search,
        searchbar,

    },
    props: {
        successMessage: String,
        alertMessage: String,
        types:Object,
        serviceLocations: Object,
        app_for:String,


    },
    setup(props) {
        const { t } = useI18n();
        // const {selectedLocation } = useSharedState();
        const searchTerm = ref("");
        const filter = useForm({
            approve: 0,
            transport_type: 'all',
            service_location_id: "all",
            vehicle_type: [],
            limit:10
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const types = ref(props.types);
        const serviceLocations = ref(props.serviceLocations);
        const paginatorOption = ref({}); // Spread the results to make them reactive

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };
        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };


        const mobileFromUser = (user) => {
            if(props.app_for && props.app_for == "demo"){
                return "***********";
            }
            return user.mobile_number;
        }

        const emailFromUser = (user) => {
            if(props.app_for && props.app_for == "demo"){
                return "***********";
            }
            return user.email;
        }

        const rightOffcanvas = ref(false);
        const filterData = () => {
            fetchDatas();
            modalFilter.value = true;
            rightOffcanvas.value = false;
        };


        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            // modalFilter.value = false;
            rightOffcanvas.value = false;
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

        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/approved-drivers/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('approved_drivers_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_approved_drivers'), 'error');
            }
        };

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                if(searchTerm.value.length > 0){
                    params.search = searchTerm.value;
                }
                params.page = page;
                const response = await axios.get(`/approved-drivers/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;

                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_approved_drivers'), error);
            }
        };

        // const location = () => {
        //     return serviceLocations.value.find(location => location.id === selectedLocation.value);
        // };
        // watch(location, (newValue) => {
        //     filter.service_location_id = newValue;
        //     fetchDatas();
        // });
        const handlePageChanged = async (page) => {
            localStorage.setItem("approved-drivers/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/approved-drivers/edit/${result.id}`); 
        };
        const viewProfile = async (result) =>  {
            router.get(`/approved-drivers/view-profile/${result.id}`); 
        };  
        
        
        const SerialNumber = (index) => {
            return ((paginator.value.current_page-1)*paginator.value.per_page)+index+1;
        };
        const approveDocument = async (driver) => {

            const response = await axios.get(`/approved-drivers/documents/${driver.id}`);
            if(response.data.data == "uploaddocument")
            {                
                setTimeout(()=>{
                    alertMessage.value = t('failed_to_upload_document');
                },15000)
                router.get(`/approved-drivers/document/${driver.id}`);
            }
            else{                
                router.get(`/approved-drivers`);
            }                    
        };

        const documentView = async (driver) => {
            router.get(`/approved-drivers/document/${driver.id}`);

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
            fetchSearch,
            deleteData,
            dismissMessage,
            serviceLocations,
            paginator,
            modalFilter,
            clearFilter,
            mobileFromUser,
            fetchDatas,
            filter,
            types,
            handlePageChanged,
            SerialNumber,
            editData,
            documentView,
            viewProfile,
            rightOffcanvas,
            approveDocument,
            changeEntriesPerPage,
            paginatorOption,
            emailFromUser
        };
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
    mounted() {
        this.fetchDatas();
        const savedPage = localStorage.getItem("approved-drivers/list");
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

        <Head title="Pending Drivers" />
        <PageHeader :title="$t('pending_drivers')" :pageTitle="$t('pending_drivers')" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                        <BRow class="g-2">
                            <BCol md="4">
                                <div class="d-flex align-items-center ">
                                <div class="toggle-buttons mt-3 me-4">
                                <a data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('list_view')" class="list-btn" @click="toggleView('list')" :class="{ active: isListView }">
                                    <i class="ri-list-check"></i> 
                                </a>
                                <a data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('grid_view')" class="list-btn" @click="toggleView('grid')" :class="{ active: !isListView }">
                                    <i class="ri-grid-fill"></i> 
                                </a>
                                </div>
                                <div class="d-flex align-items-center mt-3">
                                    <label class="me-2 text-muted">{{$t("show")}}</label>
                                    <select v-model="filter.limit" @change="changeEntriesPerPage" class="form-select form-select-sm w-auto">
                                    <option v-for="option in paginatorOption" :key="option" :value="option">
                                        {{ option }}
                                    </option>
                                    </select>
                                    <label class="ms-2 text-muted">{{$t("entries")}}</label>
                                </div>
                            </div>
                            </BCol>
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <searchbar @search="fetchSearch"></searchbar>

                                    <BButton variant="danger" @click="rightOffcanvas = true"><i
                                            class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}</BButton>

                                    <Link href="/approved-drivers/create" v-if="permissions.includes('add-driver')">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> {{$t("add_drivers")}}</BButton>
                                    </Link>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
<!-- list view -->
                    <BCardBody class="border border-dashed border-end-0 border-start-0" v-if="isListView">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{$t("name")}}</th>
                                        <th scope="col">{{$t("service_location")}}</th>
                                        <th scope="col">{{$t("mobile_number")}}</th>
                                        <th scope="col">{{$t("transport_type")}}</th>
                                        <th scope="col">{{$t("document_view")}}</th>
                                        <th scope="col">{{$t("approved_status")}}</th>
                                        <th scope="col">{{$t("declined_reason")}}</th>
                                        <th scope="col">{{$t("rating")}}</th>
                                        <th scope="col">{{$t("signed_at")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>
                                        <a @click.prevent="viewProfile(result)">{{ result.name }}</a>
                                    </td> 
                                        <td> {{ result.service_location_name }}</td>
                                        <td>{{ mobileFromUser(result) }}</td>
                                        <td class="text-wrap">
                                            <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }} - {{ result.vehicle_type_name }}</span>
                                            <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }} - {{ result.vehicle_type_name }}</span>
                                            <span v-else>{{ $t('all') }} - {{ result.vehicle_type_name }}</span>
                                        </td>
                                        <td v-if="permissions.includes('driver-upload-documents')">
                                            <!-- <Link href="#" @click.prevent="documentView(result)">
                                                <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                            </Link> -->
                                            <a :href="`/approved-drivers/document/${result.id}`" target="_blank">
                                                <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                            </a>
                                        </td>
  
                                        <td>
                                            <template v-if="result.approve == 1">
                                                <BBadge variant="success" class="text-uppercase">{{$t("approved")}}</BBadge>
                                            </template>
                                            <template v-else>
                                                <BBadge variant="danger" class="text-uppercase">{{$t("disapproved")}}</BBadge>
                                            </template>
                                        </td>   
                                        <td>{{result.reason}}</td> 
                                        <td>
                                            <i v-for="n in 5" :key="n"
                                                :class="{
                                                'bx bxs-star': n <= Math.floor(result.rating),
                                                'bx bxs-star-half': n === Math.ceil(result.rating) && result.rating % 1 !== 0,
                                                'bx bx-star': n > result.rating
                                                }"
                                                class="align-center text-muted me-2"></i>
                                        </td> 
                                        <td> {{ result.converted_created_at }}</td> 
                                        <td>
                                            <BButton class="btn btn-soft-info btn-sm m-2" size="sm" type="button">
                                            <div class="dropdown">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a> 
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#" @click.prevent="approveDocument(result)" v-if="permissions.includes('driver-approval')">
                                                        <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> 
                                                        {{ result.approve ?'Disapprove' : "Approve"}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="editData(result)" v-if="permissions.includes('edit-driver')">
                                                        <i class='bx bxs-edit-alt bx-xs text-muted me-2'></i>{{$t("edit")}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="viewProfile(result)" v-if="permissions.includes('view-driver-profile')">
                                                        <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("view_profile")}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="deleteModal(result.id)" v-if="permissions.includes('delete-driver') && app_for !== 'demo'">
                                                        <i class='bx bx-trash me-2 bx-xs text-muted'></i>{{$t("delete")}}
                                                    </a>
                                                </div>
                                            </div>
                                            </BButton>
                                        </td>
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
        <!-- list view end -->
<!-- grid view -->
<div v-if = "!isListView" class="row">
            <div class="col-xxl-3 col-sm-6 project-card" v-if="results.length>0" v-for="(result, index) in results" :key="index">
                <div class="card card-height-100">
                    <div class="card-body">
                        <div class="d-flex flex-column h-100">
                            <div class="d-flex">
                                <div data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('signed_at')" class="flex-grow-1">
                                    <p class="text-muted mb-4">{{ result.converted_created_at }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="d-flex gap-1 align-items-center">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-1 mt-n2 py-0 text-decoration-none fs-15" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal icon-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#"  v-if="permissions.includes('driver-approval')">
                                                    <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> 
                                                    <span v-if="result.approve === 1" @click.prevent="declinedModel(result.id)">{{ $t('disapprove') }}</span>
                                                    <span v-else  @click.prevent="documentView(result)" >{{ $t('approve') }}</span>
                                                </a>
                                                <a class="dropdown-item" href="#" @click.prevent="editData(result)" v-if="permissions.includes('edit-driver')">
                                                    <i class='bx bxs-edit-alt bx-xs text-muted me-2'></i>{{$t("edit")}}
                                                </a>
                                                <a class="dropdown-item" href="#" @click.prevent="editPassData(result)" v-if="permissions.includes('edit-driver')">
                                                    <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("update_password")}}
                                                </a>
                                                <a class="dropdown-item" href="#" @click.prevent="viewProfile(result)" v-if="permissions.includes('view-driver-profile')">
                                                    <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("view_profile")}}
                                                </a>
                                                <a class="dropdown-item" href="#" @click.prevent="deleteModal(result.id)" v-if="permissions.includes('delete-driver') && app_for !== 'demo'">
                                                    <i class='bx bx-trash me-2 bx-xs text-muted'></i>{{$t("delete")}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6>{{$t("referral_code")}} : <span class="text-muted mb-4">{{ result.user.refferal_code ?? "----" }}</span>
                                    <button @click="copyText(result.user.refferal_code, index)" class="btn btn-light btn-sm ms-2">
                                        <i :class="copiedIndex === index ? 'bx bxs-check-circle text-success' : 'bx bx-copy'"></i>
                                    </button>
                                </h6>
                                <h6>{{$t("referred_by")}} : <span class="text-muted mb-4">{{ result.user.referred_by_name ?? '----' }}</span>
                                </h6>                                
                            </div>
                            <div class="d-flex mb-2 card-footer bg-transparent border-top-dashed">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-warning-subtle rounded p-2">
                                            <img class="avatar-xs rounded-circle p-1" alt="profile picture" :src="result.profile_picture">
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fs-15"><a @click.prevent="viewProfile(result)" class="text-body">{{ result.name ?? "----" }}</a></h5>
                                    <p class="mb-0">
                                        <i v-for="n in 5" :key="n"
                                        :class="{
                                        'bx bxs-star text-warning': n <= Math.floor(result.rating),
                                        'bx bxs-star-half text-warning': n === Math.ceil(result.rating) && result.rating % 1 !== 0,
                                        'bx bx-star text-muted': n > result.rating
                                        }"
                                        class="align-center me-2"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-2 ms-2">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-medium" scope="row">
                                            <i class="las la-globe fs-24"></i>
                                        </td>
                                        <td>{{ result.service_location_name ?? "----" }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium" scope="row"><i class="las la-envelope-open-text fs-24"></i></td>
                                        <td>{{ emailFromUser(result) ?? "----" }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium" scope="row"><i class="las la-phone fs-24"></i></td>
                                        <td>{{ mobileFromUser(result) ?? "----" }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium" scope="row"><i class="las la-car fs-24"></i></td>
                                        <td>
                                            <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }} - {{ result.vehicle_type_name }}</span>
                                            <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }} - {{ result.vehicle_type_name }}</span>
                                            <span v-else>{{ $t('all') }} - {{ result.vehicle_type_name }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium" scope="row"><i class="las la-taxi fs-24"></i></td>
                                        <td class="d-flex" style="width:fit-content;overflow-x: auto;"><span data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('vehicle_make')" class="p-1 border border-dark border-dashed rounded">{{ result.car_make_name ?? "----" }}</span> <span data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('vehicle_model')" class="p-1 ms-2 border border-dark border-dashed rounded">{{ result.car_model_name ?? "----" }}</span>  <span data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('vehicle_number')" class="p-1 ms-2 border border-dark border-dashed rounded">{{ result.car_number ?? "----" }}</span> </td>
                                    </tr>
                                    <!-- <tr>
                                        <td class="fw-medium" scope="row">Last Contacted</td>
                                        <td>15 Dec, 2021 <small class="text-muted">08:58AM</small></td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                        </div>

                    </div>
                    <!-- end card body -->
                    <div class="card-footer bg-transparent border-top-dashed py-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <template v-if="result.approve == 1">
                                    <BBadge variant="success" class="text-uppercase">{{$t("approved")}}</BBadge>
                                </template>
                                <template v-else>
                                    <BBadge variant="danger" class="text-uppercase">{{$t("disapproved")}}</BBadge>
                                </template>
                            </div>
                            
                            <div class="flex-end">
                                <td v-if="permissions.includes('driver-upload-documents')">
                                    <!-- <Link href="#" @click.prevent="documentView(result)" data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('document_view')">
                                        <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                    </Link> -->
                                    <a :href="`/approved-drivers/document/${result.id}`" target="_blank">
                                        <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                    </a>
                                </td>
                            </div>

                        </div>
                        <tr>
                            <td  scope="row">{{$t("declined_reason")}} :</td>
                            <td class="fw-medium p-2"> {{result.reason ??  "----"}} </td>
                        </tr>

                    </div>
                    <!-- end card footer -->
                </div>
                <!-- end card -->
            </div>
            <!-- end col --> 
            <BCard v-else>
                <div class="table-responsive table-card mt-2 ms-2">
                    <table class="table table-borderless mb-0">
                <tbody >
                    <tr>
                        <td colspan="12" class="text-center m-auto">
                            <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                            <h5>{{$t("no_data_found")}}</h5>
                        </td>
                    </tr>
                </tbody>
                </table>
                </div>
            </BCard>          
        </div>
      
<!-- grid view end -->

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

        

        <!-- filter -->
        <BOffcanvas v-model="rightOffcanvas" placement="end" :title="$t('filters')" header-class="bg-light"
            body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BFrom action="" class="d-flex flex-column justify-content-end h-100">
                <div class="offcanvas-body">
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("transport_type")}}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="filter.transport_type">
                            <option value="all">{{$t("all")}}</option>
                            <option value="both">{{$t("both")}}</option>
                            <option value="taxi">{{$t("taxi")}}</option>
                            <option value="delivery">{{$t("delivery")}}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="service-location"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("service_locations")}}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="service-location" v-model="filter.service_location_id">
                            <option value="all">{{$t("all")}}</option>
                            <option v-for="(location, index) in serviceLocations" :key="location.id" :value="location.id">{{ location.name }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="country-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("vehicle_type")}}</label>

                        <Multiselect class="form-control" v-model="filter.vehicle_type" 
                        :close-on-select="false" :searchable="true"
                        multiple placeholder="Select Vehicle Type"
                        mode="tags"
                            :create-option="false" :options="types.map(type => ({value:type.id,label:type.name}))" />
                    </div>
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton @click="clearFilter" variant="light" class="w-100">{{$t("clear_filter")}}</BButton>
                    <BButton @click="filterData" type="submit" variant="success" class="w-100">
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

</style>
<style scoped>
/* Toggle Button Styles */
.toggle-buttons {
  display: flex;
  gap: 5px;
  /* margin-bottom: 15px; */
}
.list-btn {
  padding: 8px 15px;
  border: none;
  cursor: pointer;
  background: #ddd;
  border-radius: 5px;
}
.list-btn.active {
  background: #0ab39c;
  color: white;
}

/* .card {
  padding: 15px;
  background: #f9f9f9;
  border-radius: 8px;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
} */

</style>
