<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, watch, computed,onMounted } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import { integer } from '@vuelidate/validators';
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";
import { useI18n } from 'vue-i18n';

export default {
    data() {
        return {
            rightOffcanvas: false,
            isListView: true, // Default to list view
        };
    },
    created() {
        // Load view mode from localStorage
        const savedView = localStorage.getItem("pendingFleetViewMode");
        if (savedView) {
        this.isListView = savedView === "list";
        }
    },
    methods: {
        toggleView(view) {
        this.isListView = view === "list";
        localStorage.setItem("pendingFleetViewMode", view); // Save to localStorage
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
        owners: Object,
        types: Object,
        results: Object,
        owner: Object,
        serviceLocations: Object,



    },
    setup(props) {
        const { t } = useI18n();
        // const {selectedLocation } = useSharedState();
        const searchTerm = ref("");
        const  owners = ref(props.owners);
        const  owner = ref(props.owner);
        const filter = useForm({
            approve: 0,
            service_location_id:  "all",
            owner_id: null,
            transport_type: 'all',
            fleet_vehicle_type: [],
            limit:10
        });
        const ownerSearch =  ref("");
        const results = ref({}); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const types = ref(props.types || []);
        const serviceLocations = ref(props.serviceLocations || []);
        const paginatorOption = ref({}); // Spread the results to make them reactive

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

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


        const selectedOwner = ref(null);
        // const location = () => {
        //     return serviceLocations.value.find(location => location.id === selectedLocation.value);
        // };
        // watch(location, async (newValue) => {
        //     ownerFilter.service_location_id = newValue.id;
        //     filter.service_location_id = newValue.id;
        //     await fetchOwners();
        // });
        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            // modalFilter.value = false;
            rightOffcanvas.value = false;
        };

        const inputsearch = (value) => {
            ownerSearch.value = value;
        }
        watch(() => ownerSearch.value, debounce((newValue) => {
            fetchOwners();
        }, 300));


        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/fleet-drivers/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('fleet_driver_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_fleet_driver'), 'error');
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
        const fetchOwners = async () => {
            try {
                var url;
                if(ownerSearch.value.length > 0)
                {
                    url = `/fleet-drivers/ownerList?search=${ownerSearch.value}`;
                }else if(filter.service_location_id == 'all')
                {
                    url = `/fleet-drivers/ownerList`;
                }else{
                    url = `/fleet-drivers/ownerList?service_location_id=${filter.service_location_id}`;
                }
                const response = await axios.get(url);
                owners.value = response.data.results;
                if(owners.value.length > 0){
                    owner.value = owners.value[0];
                }else{
                    filter.owner_id = null;
                }
            } catch (error) {
                console.error(('error_fetching_fleet_drivers'), error);
            }
            };


        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.page = page;
                if(searchTerm.value.length > 0){
                    params.search = searchTerm.value;
                }
                selectedOwner.value = owners.value?.find(owner => owner.id === filter.owner_id);
                const response = await axios.get(`/fleet-drivers/list`, { params });
                results.value = response.data.results.data;
                paginator.value = response.data.results;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_fleet_drivers'), error);
            }
        };
        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };

        const handlePageChanged = async (page) => {
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/fleet-drivers/edit/${result.id}`); 
        };

        const approveDocument = async (driver) => {

        const response = await axios.get(`/fleet-drivers/documents/${driver.id}`);
        if(response.data.data == "uploaddocument")
        {                
            setTimeout(()=>{
                alertMessage.value = t('failed_to_upload_document');
            },15000)
            router.get(`/fleet-drivers/document/${driver.id}`);
        }
        else{                
            router.get(`/fleet-drivers`);
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
            fetchSearch,
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            filter,
            serviceLocations,
            fetchOwners,
            selectedOwner,
            inputsearch,
            owners,
            types,
            handlePageChanged,
            editData,
            rightOffcanvas,
            approveDocument,
            paginatorOption,
            changeEntriesPerPage,
            mobileFromUser,
            emailFromUser
        };
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
    async mounted() {
        await this.fetchOwners();
        await this.fetchDatas();
    },
};
</script>

<template>
    <Layout>

        <Head title="Pending Drivers" />
        <PageHeader :title="`Pending Drivers ${selectedOwner ? '('+selectedOwner.name+')' : ''}`" :pageTitle="$t('pending_drivers')" />
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

                                    <Link :href="`/fleet-drivers/create`" v-if="permissions.includes('add-fleet-drivers')">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> {{$t("add_fleet_drivers")}}</BButton>
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
                                        <th scope="col">{{$t("service_locations")}}</th>
                                        <th scope="col">{{$t("email")}}</th>
                                        <th scope="col">{{$t("mobile_number")}}</th>
                                        <th scope="col">{{$t("transport_type")}}</th>
                                        <th scope="col">{{$t("document_view")}}</th>
                                        <th scope="col">{{$t("approved_status")}}</th>
                                        <th scope="col">{{$t("declined_reason")}}</th>
                                        <th scope="col">{{$t("rating")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>
                                            <Link class="dropdown-item" :href="`/fleet-drivers/view-profile/${result.id}`">{{ result.name }}</Link>
                                        </td> 
                                        <td>{{ result.service_location_name }}</td> 
                                        <td>{{ emailFromUser(result) }}</td> 
                                        <td>{{ mobileFromUser(result) }}</td> 
                                        <td>
                                            <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }}</span>
                                            <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }}</span>
                                            <span v-else>{{ $t('all') }}</span>
                                        </td>
                                        <td>
                                            <!-- <Link :href="`/fleet-drivers/document/${result.id}`"> 
                                                <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                            </Link> -->
                                            <a :href="`/fleet-drivers/document/${result.id}`" target="_blank">
                                                <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                            </a>
                                        </td>    
                                        <td>
                                            <template v-if="result.approve">
                                                <BBadge variant="success" class="text-uppercase">{{$t("approved")}}</BBadge>
                                            </template>
                                            <template v-else>
                                                <BBadge variant="danger" class="text-uppercase">{{$t("disapproved")}}</BBadge>
                                            </template>
                                        </td>   
                                        <td>{{ result.reason }}</td> 
                                        <td>
                                            <i v-for="n in 5" :key="n"
                                                :class="{
                                                'bx bxs-star': n <= Math.floor(result.rating),
                                                'bx bxs-star-half': n === Math.ceil(result.rating) && result.rating % 1 !== 0,
                                                'bx bx-star': n > result.rating
                                                }"
                                                class="align-center text-muted me-2"></i>
                                        </td>                                          <td>
                                            <BButton class="btn btn-soft-info btn-sm m-2" size="sm" type="button">
                                                <div class="dropdown">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-12"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <Link class="dropdown-item" @click.prevent="approveDocument(result)" v-if="permissions.includes('approve-fleet-drivers')">
                                                        <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> {{$t("approve")}}
                                                    </Link>
                                                    <a class="dropdown-item" href="#" @click.prevent="editData(result)" v-if="permissions.includes('edit-fleet-drivers')">
                                                        <i class='bx bxs-edit-alt bx-xs text-muted me-2'></i>{{$t("edit")}}
                                                    </a>
                                                    <Link class="dropdown-item" :href="`/fleet-drivers/view-profile/${result.id}`" v-if="permissions.includes('view-fleet-driver-profile')">
                                                        <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> {{$t("view_profile")}}
                                                    </Link>
                                                    <a class="dropdown-item" href="#" @click.prevent="deleteModal(result.id)" v-if="permissions.includes('delete-fleet-drivers') && app_for !== 'demo'">
                                                        <i class='bx bx-trash me-2 bx-xs text-muted'></i>{{$t("delete")}}
                                                    </a>
                                                </ul>
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
                                <!-- <div data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('signed_at')" class="flex-grow-1">
                                    <p class="text-muted mb-4">{{ result.converted_created_at }}</p>
                                </div> -->
                                <div class="flex-grow-1">
                                    <!-- <p class="text-muted mb-4">Updated 3hrs ago</p> -->
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="d-flex gap-1 align-items-center">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-1 mt-n2 py-0 text-decoration-none fs-15" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal icon-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end">
                                                <Link class="dropdown-item" @click.prevent="approveDocument(result)" v-if="permissions.includes('approve-fleet-drivers')">
                                                        <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> {{$t("approve")}}
                                                    </Link>
                                                    <a class="dropdown-item" href="#" @click.prevent="editData(result)" v-if="permissions.includes('edit-fleet-drivers')">
                                                        <i class='bx bxs-edit-alt bx-xs text-muted me-2'></i>{{$t("edit")}}
                                                    </a>
                                                    <Link class="dropdown-item" :href="`/fleet-drivers/view-profile/${result.id}`" v-if="permissions.includes('view-fleet-driver-profile')">
                                                        <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> {{$t("view_profile")}}
                                                    </Link>
                                                    <a class="dropdown-item" href="#" @click.prevent="deleteModal(result.id)" v-if="permissions.includes('delete-fleet-drivers') && app_for !== 'demo'">
                                                        <i class='bx bx-trash me-2 bx-xs text-muted'></i>{{$t("delete")}}
                                                    </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                    <h5 class="mb-1 fs-15"><Link class="dropdown-item" :href="`/fleet-drivers/view-profile/${result.id}`">{{ result.name ?? "----" }}</Link></h5>
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
                                <td>
                                    <!-- <Link :href="`/fleet-drivers/document/${result.id}`"> 
                                        <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                    </Link> -->
                                    <a :href="`/fleet-drivers/document/${result.id}`" target="_blank">
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
                        <label for="service-location"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("service_location")}}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="service-location" @change="fetchOwners()"  v-model="filter.service_location_id">
                            <option value="all">{{$t("all")}}</option>
                            <option v-for="(location, index) in serviceLocations" :key="location.id" :value="location.id">{{ location.name }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="owner-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("owner")}}</label>
                            <Multiselect
                                v-model="filter.owner_id"
                                :options="owners?.map(owner=>({ value: owner.id, label: owner.name }))"
                                label="label"
                                searchable
                                @search-change="inputsearch"
                                track-by="value"
                                close-on-select
                                id="owner-select"
                                :placeholder="$t('select_owner')"
                            />
                    </div>
                    <div class="mb-4">
                        <label for="choices-select-status"
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
                        <label for="type-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("vehicle_type")}}</label>

                        <Multiselect class="form-control" v-model="filter.fleet_vehicle_type" 
                        :close-on-select="false" :searchable="true"
                        multiple :placeholder="$t('select_vehicle_type')"
                        mode="tags" id="type-select"
                            :create-option="false" :options="types.map(type => ({value:type.id,label:type.name}))" />
                    </div>
                    <div>
                    </div>
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton @click="clearFilter" variant="light" class="w-100">{{$t("clear_filter")}}</BButton>
                    <BButton @click="filterData"  type="submit" variant="success" class="w-100">
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

