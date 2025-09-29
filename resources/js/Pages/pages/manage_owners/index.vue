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
import { useSharedState } from '@/composables/useSharedState';
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
        const savedView = localStorage.getItem("ownerViewMode");
        if (savedView) {
        this.isListView = savedView === "list";
        }
    },
    methods: {
        toggleView(view) {
        this.isListView = view === "list";
        localStorage.setItem("ownerViewMode", view); // Save to localStorage
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
        app_for:String,
        serviceLocations: {
            type: Object,
            required: true,
        },


    },
    setup(props) {
        const searchTerm = ref("");
        // const { selectedLocation } = useSharedState();
        const serviceLocations = ref(props.serviceLocations || null);
        const filter = useForm({
            service_location_id: 'all',
            status: null,
            approveStatus: null,
            limit:10
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const { t } = useI18n();
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
        // const location = () => {
        //     return serviceLocations.value.find(location => location.id === selectedLocation.value);
        // };

        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            // modalFilter.value = false;
            rightOffcanvas.value = false;
        };


        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/manage-owners/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('service_location_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_service_location'), 'error');
            }
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
            return user.email
        }

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


        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.page = page;
                if(searchTerm.value.length>0){
                    params.search = searchTerm.value;
                }
                const response = await axios.get(`/manage-owners/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_manage_owners'), error);
            }
        };
        // watch(location, (newValue) => {
        //     filter.service_location_id = newValue.id;
        //     fetchDatas();
        // });
        const handlePageChanged = async (page) => {
            localStorage.setItem("manage-owners/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };
        const viewOwnerDocument = async (owner) =>  {
                    router.get(`/manage-owners/document/${owner.id}`);
        };
        const toggleActiveStatus = async (id, status, owner) => {
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
                    const response = await axios.post(`/manage-owners/update-status`, { id: id, status: status });
                    

                    if(response.data.data == "uploaddocument") {
                        router.get(`/manage-owners/document/${id}`);
                    }
                    else{
                        const index = results.value.findIndex(item => item.id === id);
                        if (index !== -1) {
                            results.value[index].approve = status;
                        }

                        Swal.fire(t('changed'), t('status_updated_successfully'), "success");

                    }

                    

                } catch (error) {
                    console.error(t('error_updating_status'), error);
                    Swal.fire(t('error'), t('failed_to_update_status'), "error");
                }
            }
        });
    };

        const editData = async (result) =>  {
            router.get(`/manage-owners/edit/${result.id}`); 
        };
        const addOwner = async () =>  {
            // router.get(`/manage-owners/create/${filter.service_location_id}`); 
            router.get(`/manage-owners/create`); 
        };
        const editPassData = async (result) =>  {
            router.get(`/manage-owners/password/edit/${result.id}`); 
        };
        const viewProfile = async (result) =>  {
            router.get(`/manage-owners/view-profile/${result.id}`); 
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
            searchTerm,
            paginator,
            modalFilter,
            clearFilter,
            mobileFromUser,
            emailFromUser,
            fetchDatas,
            filter,
            addOwner,
            handlePageChanged,
            editData,
            viewOwnerDocument,
            rightOffcanvas,
            editPassData,
            viewProfile,
            toggleActiveStatus,
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
        const savedPage = localStorage.getItem("manage-owners/list");
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

        <Head title="Manage Owners" />
        <PageHeader :title="$t('manage_owners')" :pageTitle="$t('manage_owners')" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                        <BRow class="g-2">
                            <BCol md="4">
                   <!-- Toggle Buttons -->
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
                            <BCol md="2">
                                <!-- <div class="search-box">
                                    <input type="text" id="name" class="form-control search"
                                        placeholder="Search Service..." v-model="searchTerm" @keyup.enter="fetchDatas" />
                                    <i class="ri-search-line search-icon"></i>
                                </div> -->
                            </BCol>
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <searchbar @search="fetchSearch"></searchbar>
                                    <BButton variant="danger" @click="rightOffcanvas = true">
                                        <i class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}
                                    </BButton>

                                    <BButton variant="primary" @click="addOwner" class="float-end" v-if="permissions.includes('add-owner')">
                                        <i class="ri-add-line align-bottom me-1"></i> {{$t("add_manage_owners")}}
                                    </BButton>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
<!-- list view  -->
                    <BCardBody class="border border-dashed border-end-0 border-start-0" v-if="isListView">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-owner">
                                    <tr>
                                        <th scope="col">{{$t("company_name")}}</th>
                                        <!-- <th scope="col">{{$t("owner_name")}}</th> -->
                                        <th scope="col">{{$t("email")}}</th>
                                        <th scope="col">{{$t("mobile_number")}}</th>
                                        <th scope="col">{{$t("document_view")}}</th>
                                        <th scope="col">{{$t("approval_status")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ result.company_name }}</td>
                                        <!-- <td> {{ result.owner_name }}</td>   -->
                                        <td>{{ emailFromUser(result) }}</td>
                                        <td>{{ mobileFromUser(result) }}</td>
                                        <td v-if="permissions.includes('view-owner-document')">
                                            <!-- <Link @click="viewOwnerDocument(result)" href="#"> 
                                                <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                            </Link> -->
                                            <a :href="`/manage-owners/document/${result.id}`" target="_blank">
                                                <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                            </a>
                                        </td>
                                        <td v-if="permissions.includes('toggle-owner')">
                                            <div :class="{
                                                    'form-check': true,
                                                    'form-switch': true,
                                                    'form-switch-lg': true,
                                                    'form-switch-success': result.approve,
                                                }">
                                                <input class="form-check-input" type="checkbox" role="switch" @click.prevent="toggleActiveStatus(result.id,!result.approve)" :id="'status_'+result.id" :checked="result.approve">
                                            </div>
                                        </td>                                      
                                        <td>
                                            <BButton @click.prevent="editData(result)" v-if="permissions.includes('edit-owner')"
                                                class="btn btn-soft-warning btn-sm m-2"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover title="Edit">
                                                <i class='bx bxs-edit-alt bx-xs'></i>
                                            </BButton>
                                            <BButton @click.prevent="editPassData(result)" v-if="permissions.includes('edit-owner')"
                                                class="btn btn-soft-secondary btn-sm m-2"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('update_password')">
                                                <i class=' ri-lock-password-line bx-xs'></i>
                                            </BButton>
                                            <BButton class="btn btn-soft-danger btn-sm m-2" size="sm" v-if="permissions.includes('delete-owner') && app_for !== 'demo'"
                                                type="button" @click.prevent="deleteModal(result.id)"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover title="Delete">
                                                <i class='bx bx-trash bx-xs'></i>
                                            </BButton>
                                            <BButton class="btn btn-soft-success btn-sm m-2" size="sm" v-if="permissions.includes('view-owner-profile')"
                                                type="button" @click.prevent="viewProfile(result)"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover title="View Profile">
                                                <i class='  ri-account-circle-line bx-xs'></i>
                                            </BButton>
                                        </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="7" class="text-center">
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

                <!-- list view end  -->
<!-- grid view -->
<div v-if = "!isListView" class="row">
            <div class="col-xxl-3 col-sm-6 project-card" v-if="results.length>0" v-for="(result, index) in results" :key="index">
                <div class="card card-height-100">
                    <div class="card-body">
                        <div class="d-flex flex-column h-100">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="d-flex gap-1 align-items-center">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-1 mt-n2 py-0 text-decoration-none fs-15" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal icon-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#" v-if="permissions.includes('view-owner-profile')"
                                                 @click.prevent="viewProfile(result)">
                                                    <i class="bx bx-radio-circle-marked  align-center text-muted me-2"></i>{{$t("view_profile")}}
                                                </a>
                                                <a class="dropdown-item" href="#" @click.prevent="editData(result)" v-if="permissions.includes('edit-owner')">
                                                    <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("edit")}}
                                                </a>
                                                <a class="dropdown-item" href="#" @click.prevent="editPassData(result)" v-if="permissions.includes('edit-owner')">
                                                    <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("update_password")}}
                                                </a>
                                                <a class="dropdown-item" href="#" v-if="permissions.includes('delete-owner') && app_for !== 'demo'"
                                                @click.prevent="deleteModal(result.id)">
                                                    <i class="bx bxs-trash-alt align-center text-muted me-2"></i>{{$t("delete")}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mb-2 card-footer bg-transparent border-top-dashed">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm" v-if = "result.profile_picture">
                                        <span class="avatar-title bg-warning-subtle rounded p-2">
                                            <img class="avatar-xs rounded-circle p-1" alt="profile picture" :src="result.profile_picture">
                                        </span>
                                    </div>
                                    <div class="avatar-sm" v-else>
                                        <span class="avatar-title bg-warning-subtle rounded p-2">
                                            <img class="avatar-xs rounded-circle p-1" alt="profile picture" src="/assets/images/Male_default_image.png">
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fs-15"><a class="text-body">{{ result.name ?? "----" }}</a></h5>
                                    <tr>
                                        <td class="flex-grow-1"  scope="row">{{$t("company_name")}} :</td>
                                        <td class="fw-medium p-2"> {{result.company_name ??  "----"}} </td>
                                    </tr>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-2 ms-2">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <!-- <tr>
                                        <td class="fw-medium" scope="row">
                                            <i class="las la-user-alt fs-24"></i>
                                        </td>
                                        <td>{{ result.drivers }}</td>
                                    </tr> -->
                                    <tr>
                                        <td class="fw-medium" scope="row">
                                            <i class="las la-phone fs-24"></i></td>
                                        <td>{{ mobileFromUser(result) ?? "----" }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium" scope="row"><i class="las la-envelope-open-text fs-24"></i></td>
                                        <td>{{ emailFromUser(result) ?? "----" }}</td>
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
                                <td v-if="permissions.includes('view-owner-document')">
                                    <!-- <Link @click="viewOwnerDocument(result)" href="#"> 
                                        <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                    </Link> -->
                                    <a :href="`/manage-owners/document/${result.id}`" target="_blank">
                                        <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                    </a>
                                </td>
                            </div>
                            <div class="flex-end">
                                <td v-if="permissions.includes('toggle-owner')">
                                    <div :class="{
                                            'form-check': true,
                                            'form-switch': true,
                                            'form-switch-lg': true,
                                            'form-switch-success': result.approve,
                                        }">
                                        <input class="form-check-input" type="checkbox" role="switch" @click.prevent="toggleActiveStatus(result.id,!result.approve)" :id="'status_'+result.id" :checked="result.approve">
                                    </div>
                                </td>
                            </div>

                        </div>

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
                            id="service-location"  v-model="filter.service_location_id">
                            <option value="all">{{$t("all")}}</option>
                            <option v-for="(location, index) in serviceLocations" :key="location.id" :value="location.id">{{ location.name }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("approval_status")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="approval_status"
                                    id="inlineRadio1" value="1" v-model="filter.approveStatus">
                                <label class="form-check-label" for="inlineRadio1">{{$t("approved")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="approval_status"
                                    id="WithoutinlineRadio2" value="0" v-model="filter.approveStatus">
                                <label class="form-check-label" for="WithoutinlineRadio2">{{$t("disapproved")}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("status")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status"
                                    id="active" value=1 v-model="filter.status">
                                <label class="form-check-label" for="active">{{$t("active")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status"
                                    id="inactive" value=0 v-model="filter.status">
                                <label class="form-check-label" for="inactive">{{$t("inactive")}}</label>
                            </div>
                        </div>
                    </div>
                     <div>
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