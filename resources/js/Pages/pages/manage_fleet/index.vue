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
        search,
        searchbar,

    },
    props: {
        successMessage: String,
        alertMessage: String,
        types:Object,
        serviceLocations: {
            type: Object,
            required: true,
        }


    },
    setup(props) {
        const { t } = useI18n();
        // const {selectedLocation } = useSharedState();
        const searchTerm = ref("");
        const filter = useForm({
            approve: 1,
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
                await axios.delete(`/manage-fleet/delete/${dataId}`);
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
        const fleetDrivers = ref([]);
        const fleet = ref(null);

        const fleetDriver = async (fleet_data) => {
            try {
                // Make sure to concatenate the URL properly using backticks
                const response = await axios.get(`/manage-fleet/listFleetDriver/${fleet_data.id}`);
                
                // Log to check if data is fetched correctly
                console.log("Response fetched:", response);
                console.log("Drivers:", response.data.drivers);

                // Assign the fetched drivers data to fleetDrivers
                fleetDrivers.value = response.data.drivers;

                // Assign the fleet data
                fleet.value = fleet_data;

                // Show modal
                modalShow.value = true;
            } catch (error) {
                console.error(t('error_fetching_fleet_drivers'), error);
            }
        };

            const assignDriver = async (fleet, driver) => {
                try {
                    // Perform action when driver is assigned
                    const response = await axios.post(`/manage-fleet/assign/${fleet.id}/${driver.id}`);

                    // Check if the response status is 201 (created/success)
                    if (response.status === 201) {
                        console.log(response.data.successMessage);  // Log success message

                        // Show a success message to the user (you can use a notification library or a custom method)
                        alert(response.data.successMessage);

                        // Close the modal after successful assignment
                        closeModal();
                    }
                } catch (error) {
                    console.error(t('error_assigning_driver'), error);

                    // Handle the case where a fleet is already assigned to another driver
                    if (error.response && error.response.status === 405) {
                        console.error(error.response.data.successMessage);

                        // Show a message to the user if the fleet is already assigned
                        alert(error.response.data.successMessage);
                    } else {
                        // Handle other errors
                        alert(t('an_error_occurred_while_assigning_the_driver'));
                    }
                }
            };

   
        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                if(searchTerm.value.length > 0){
                    params.search = searchTerm.value;
                }
                params.page = page;
                const response = await axios.get(`/manage-fleet/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_approved_drivers'), error);
            }
        };

        const handlePageChanged = async (page) => {
            localStorage.setItem("manage-fleets/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/manage-fleet/edit/${result.id}`); 
        };
        const viewProfile = async (result) =>  {
            router.get(`/manage-fleet/view-profile/${result.id}`); 
        };  
        
        
        const documentView = async (driver) => {

                    router.get(`/manage-fleet/document/${driver.id}`);

        };

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };
        const SerialNumber = (index) => {
            return ((paginator.value.current_page-1)*paginator.value.per_page)+index+1;
        };
        const disapprove = async (dataId) => {
            try {
                await axios.post(`/manage-fleet/approve/${dataId}`,{status: 'disapproved'});
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('fleet_driver_disapproved_successfully'), 'success');
                router.get(`/manage-fleet`);
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_disapprove_fleet_driver'), 'error');
            }
        };

        const approveDocument = async (fleet) => {

            const response = await axios.get(`/manage-fleet/documents/${fleet.id}`);
            if(response.data.data == "uploaddocument")
            {                
                setTimeout(()=>{
                    alertMessage.value = t('failed_to_upload_document');
                },15000)
                router.get(`/manage-fleet/document/${fleet.id}`);
            }
            else{                
                router.get(`/manage-fleet`);
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
            SerialNumber,
            fetchSearch,
            dismissMessage,
            serviceLocations,
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            filter,
            types,
            handlePageChanged,
            editData,
            documentView,
            viewProfile,
            fleetDriver,
            fleetDrivers,
            fleet,
            assignDriver,
            rightOffcanvas,
            disapprove,
            approveDocument,
            paginatorOption,
            changeEntriesPerPage
        };
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
    mounted() {
        this.fetchDatas();
        const savedPage = localStorage.getItem("manage-fleet/list");
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

        <Head title="Manage Fleet" />
        <PageHeader :title="$t('manage_fleet')" :pageTitle="$t('manage_fleet')" />
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
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <searchbar @search="fetchSearch"></searchbar>

                                    <!-- <BButton variant="danger" @click="rightOffcanvas = true"><i
                                            class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}</BButton> -->

                                    <Link href="/manage-fleet/create" v-if="permissions.includes('add-fleet')">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> {{$t("add_fleet")}}</BButton>
                                    </Link>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                              <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{$t("vehicle_type")}}</th>
                                        <th scope="col">{{$t("car_brand")}}</th>
                                        <th scope="col">{{$t("car_model")}}</th>
                                        <th scope="col">{{$t("document_view")}}</th>
                                        <th scope="col">{{ $t("license_plate_number") }}</th>
                                        <th scope="col">{{$t("status")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                     <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ result.vehicle_type_name ?? '-' }}</td>  
                                        <td>{{ result.car_make_name }}</td>  
                                        <td>{{ result.car_model_name }}</td>  
                                        <td v-if="permissions.includes('view-fleet-document')">
                                            <!-- <Link :href="`/manage-fleet/document/${result.id}`"> 
                                                <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                            </Link> -->
                                            <a :href="`/manage-fleet/document/${result.id}`" target="_blank">
                                                <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                            </a>
                                        </td>
                                        <td>{{ result.license_number }}</td>
                                        <td>
                                            <template v-if="result.approve">
                                                <BBadge variant="success" class="text-uppercase">{{$t("approved")}}</BBadge>
                                            </template>
                                            <template v-else>
                                                <BBadge variant="danger" class="text-uppercase">{{$t("blocked")}}</BBadge>
                                            </template>
                                        </td>
                                        <td>
                                            <BButton class="dropdown btn btn-soft-info btn-sm m-2" size="sm"
                                                type="button">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-12"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end" v-if="permissions.includes('fleet-approval')">
                                                    <Link class="dropdown-item" @click.prevent="approveDocument(result)" v-if="permissions.includes('fleet-approval') && !result.approve">
                                                        <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> {{$t("approve")}}
                                                    </Link>
                                                    <div class="dropdown-item" @click="disapprove(result.id)" v-if="permissions.includes('fleet-approval') && result.approve">
                                                        <i class="  bx bx-radio-circle-marked align-center text-muted me-2"></i> {{$t("disapprove")}}
                                                    </div>
                                                    <a class="dropdown-item" href="#" @click.prevent="editData(result)" v-if="permissions.includes('edit-fleet')">
                                                        <i class='bx bxs-edit-alt bx-xs text-muted me-2'></i>{{$t("edit")}}
                                                    </a>
                                                    <a class="dropdown-item" v-if="result.approve" @click="fleetDriver(result)" href="#">
                                                        <i class=" bx bx-radio-circle-marked align-center text-muted me-2"></i>{{$t("assign")}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="deleteModal(result.id)" v-if="permissions.includes('delete-fleet')">
                                                        <i class='bx bx-trash me-2 bx-xs text-muted'></i>{{$t("delete")}}
                                                    </a>
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

        <div>
            
<!-- Assign Driver Modal -->
<div v-if="modalShow" class="modal fade show" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{$t("assign_driver")}}</h5>
                <button type="button" class="btn-close" @click="closeModal"></button>
            </div>
            <div class="modal-body">
                <!-- Fleet Details -->
                <BRow>
                        <div class="card-header align-items-center d-flex p-3">
                            <h3 class="card-title mb-0 flex-grow-1">{{$t("fleet_details")}}</h3>
                        </div>
                        <BCardBody class="px-1 py-5">
                            <BCol xl="12">
                                <div class="row row-cols-xxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1">
                                    <div class="col">
                                        <div class="card bg-dark-subtle">
                                            <div class="card-body d-flex">
                                                <div class="flex-grow-1">
                                                    <h5>{{$t("owner")}}</h5>
                                                    <h4 class="text-muted fs-18 mb-0">{{ fleet.owner_name }}</h4>
                                                </div>
                                                <div class="flex-shrink-0 avatar-sm">
                                                    <div class="avatar-title bg-warning-subtle text-warning fs-22 rounded">
                                                        <i class="ri-user-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col">
                                        <div class="card bg-dark-subtle">
                                            <div class="card-body d-flex">
                                                <div class="flex-grow-1">
                                                    <h5>{{$t("vehicle_type")}}</h5>
                                                    <h4 class="text-muted fs-18 mb-0">{{ fleet.vehicle_type_name }}</h4>
                                                </div>
                                                <div class="flex-shrink-0 avatar-sm">
                                                    <div class="avatar-title bg-success-subtle text-success fs-22 rounded">
                                                        <i class="ri-car-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col">
                                        <div class="card bg-dark-subtle">
                                            <div class="card-body d-flex">
                                                <div class="flex-grow-1">
                                                    <h5>{{$t("vehicle_color")}}</h5>
                                                    <h4 class="text-muted fs-18 mb-0">{{ fleet.car_color }}</h4>
                                                </div>
                                                <div class="flex-shrink-0 avatar-sm">
                                                    <div class="avatar-title bg-info-subtle text-info fs-22 rounded">
                                                        <i class="ri-car-fill"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col">
                                        <div class="card bg-dark-subtle">
                                            <div class="card-body d-flex">
                                                <div class="flex-grow-1">
                                                    <h5>{{$t("vehicle_make")}}</h5>
                                                    <h4 class="text-muted fs-18 mb-0">{{ fleet.car_make_name }}</h4>
                                                </div>
                                                <div class="flex-shrink-0 avatar-sm">
                                                    <div class="avatar-title bg-danger-subtle text-danger fs-22 rounded">
                                                        <i class="ri-car-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col">
                                        <div class="card bg-dark-subtle">
                                            <div class="card-body d-flex">
                                                <div class="flex-grow-1">
                                                    <h5>{{$t("vehicle_model")}}:</h5>
                                                    <h4 class="text-muted fs-18 mb-0">{{ fleet.car_model_name }}</h4>
                                                </div>
                                                <div class="flex-shrink-0 avatar-sm">
                                                    <div class="avatar-title bg-primary-subtle text-primary fs-22 rounded">
                                                        <i class="ri-car-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                            </BCol>
                        </BCardBody>
                </BRow>
                
                <!-- Driver Details -->
                <BRow>
                        <div class="card-header align-items-center d-flex p-3">
                            <h3 class="card-title mb-0 flex-grow-1">{{$t("driver_details")}}</h3>
                        </div>
<BCardBody class="overflow-y-auto" style="height:400px">
    <BCol xl="12">
        <div class="table-responsive">
            <table class="table align-middle position-relative table-nowrap">
                <thead class="table-active">
                    <tr>
                        <th>{{$t("profile")}}</th>
                        <th>{{$t("name")}}</th>
                        <th>{{$t("mobile")}}</th>
                        <th>{{$t("email")}}</th>
                        <th>{{$t("action")}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="driver in fleetDrivers" :key="driver.id">
                        <!-- Driver Avatar -->
                        <td>
                            <img :src="driver.profile_picture || '/assets/images/users/user-dummy-img.jpg'" alt="driver" class="img-thumbnail rounded-circle shadow-none" width="50" height="50">
                        </td>
                        
                        <!-- Driver Name -->
                        <td>{{ driver.name }}</td>

                        <!-- Driver Mobile -->
                        <td>{{ driver.mobile }}</td>

                        <!-- Driver Email -->
                        <td>{{ driver.email }}</td>

                        <!-- Assign Button -->
                        <td>
                            <button @click="assignDriver(fleet, driver)" class="btn btn-primary">
                                {{$t("assign")}}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </BCol>
</BCardBody>
                </BRow>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" @click="closeModal">Close</button>
            </div>
        </div>
    </div>
</div>




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
