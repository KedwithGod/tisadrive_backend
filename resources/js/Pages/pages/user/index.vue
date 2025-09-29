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
import Warning from "@/Components/warning.vue";

export default {
    data() {
        return {
            rightOffcanvas: false,
            // SearchQuery: '',
            isListView: true, // Default to list view
            copiedIndex: null,
        };
    },
    created() {
        // Load view mode from localStorage
        const savedView = localStorage.getItem("userViewMode");
        if (savedView) {
        this.isListView = savedView === "list";
        }
    },
    methods: {
        toggleView(view) {
        this.isListView = view === "list";
        localStorage.setItem("userViewMode", view); // Save to localStorage
        },
        getStarIcons(rating) {
      let stars = [];
      let fullStars = Math.floor(rating); // Full stars
      let hasHalfStar = rating % 1 >= 0.5; // Half star if decimal is >= 0.5
      let emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0); // Remaining empty stars

      // Add full stars
      for (let i = 0; i < fullStars; i++) {
        stars.push("ri-star-s-fill text-warning fs-16");
      }

      // Add half star if applicable
      if (hasHalfStar) {
        stars.push("ri-star-half-s-fill text-warning fs-16");
      }

      // Add empty stars
      for (let i = 0; i < emptyStars; i++) {
        stars.push("ri-star-line text-muted");
      }

      return stars;
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
        searchbar,
        Warning,

    },
    props: {
        successMessage: String,
        alertMessage: String,
        app_for:String,

        vehicleTypes: {
            type: Object,
            required: true,
        },
        results: {
      type: Array,
      required: true
    },


    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref('');
        const filter = useForm({
            transport_type: null,
            dispatch_type: null,
            status: null,
            limit:10
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const paginatorOption = ref({}); // Spread the results to make them reactive

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        // const toggleActiveStatus = async (id, status) => {
        //     try {
        //         await axios.post(`/users/update-status`, { id: id, status: status });
        //         const index = results.value.findIndex(item => item.id === id);
        //         if (index !== -1) {
        //             results.value[index].active = status; // Update the active status locally
        //         }
        //         // Optionally, you may want to re-fetch all data to ensure consistency
        //         // fetchDatas(); 
        //     } catch (error) {
        //         console.error(t('error_updating_status'), error);
        //     }
        // };

        const toggleActiveStatus = async (id, status) => {
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
                        await axios.post(`/users/update-status`, { id: id, status: status });
                        const index = results.value.findIndex(item => item.id === id);
                        if (index !== -1) {
                            results.value[index].active = status; // Update the active status locally
                        }
                        Swal.fire(t('changed'), t('status_updated_successfully'), "success");
                    } catch (error) {
                        console.error(t('error_updating_status'), error);
                        Swal.fire(t('error'), t('failed_to_update_status'), "error");
                    }
                }
            });
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
                await axios.delete(`/users/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('users_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(('error'), t('failed_to_delete_users'), 'error');
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
                const response = await axios.get(`/users/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_users'), error);
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

        // const handlePageChanged = async (page) => {
        //     fetchDatas(page);
        // };

   const handlePageChanged = async (page) => {
          localStorage.setItem("users/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/users/edit/${result.id}`); 
        };
        const editPassData = async (result) =>  {
            router.get(`/users/password/edit/${result.id}`); 
        };
        const viewProfile = async (result) =>  {
            router.get(`/users/view-profile/${result.id}`); 
        };
        const updatePaginatorOptions = () => {
            paginatorOption.value = [10, 25, 50, 100, 200, 500]; // Default static options
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
            mobileFromUser,
            emailFromUser,
            handlePageChanged,
            toggleActiveStatus,
            editData,
            viewProfile,
            rightOffcanvas,
            editPassData,
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
        const savedPage = localStorage.getItem("users/list");
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
<Warning />
        <Head title="Users" />
        <PageHeader :title="$t('users')" :pageTitle="$t('users')" />
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
                            </BCol>
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <searchbar @search="fetchSearch"></searchbar>
                                    <BButton variant="danger" @click="rightOffcanvas = true"><i
                                            class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}</BButton>

                                    <Link href="/users/create" v-if="permissions.includes('add-user')">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i>{{$t("add_user")}}</BButton>
                                    </Link>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
<!-- list view  -->
                    <BCardBody class="border border-dashed border-end-0 border-start-0" v-if="isListView">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{$t("name")}}</th>
                                        <th scope="col">{{$t("gender")}}</th>
                                        <th scope="col">{{$t("mobile")}}</th>
                                        <th scope="col">{{$t("email")}}</th>
                                        <th scope="col">{{$t("status")}}</th>
                                        <th scope="col">{{$t("action")}}</th> 
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ result.name }}</td>
                                        <td>{{ result.gender }}</td>
                                        <td>{{ mobileFromUser(result) }}</td>
                                        <td>{{ emailFromUser(result) }}</td>

                                       <td v-if="permissions.includes('toggle-user')">
                                            <div :class="{
                                                    'form-check': true,
                                                    'form-switch': true,
                                                    'form-switch-lg': true,
                                                    'form-switch-success': result.active,
                                                }">
                                                <input class="form-check-input" type="checkbox" role="switch" @click.prevent="toggleActiveStatus(result.id,!result.active)" :id="'status_'+result.id" :checked="result.active">
                                            </div>
                                        </td>
                                       <td>
                                            <BButton class="btn btn-soft-info btn-sm m-2" size="sm" type="button">
                                            <div class="dropdown">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#" @click.prevent="viewProfile(result)" v-if="permissions.includes('view-user-profile')">
                                                        <i class="bx bx-radio-circle-marked align-center text-muted me-2"></i>{{$t("view_profile")}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="editData(result)" v-if="permissions.includes('edit-user')">
                                                        <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("edit")}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="editPassData(result)" v-if="permissions.includes('edit-user')">
                                                        <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("update_password")}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="deleteModal(result.id)" v-if="permissions.includes('delete-user') && app_for !== 'demo'">
                                                        <i class="bx bxs-trash-alt align-center text-muted me-2"></i>{{$t("delete")}}
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
<!-- list view end  -->
<!-- grid view -->
        <div v-if = "!isListView" class="row">
            <div class="col-xxl-3 col-sm-6 project-card" v-if="results.length>0" v-for="(result, index) in results" :key="index">
                <div class="card card-height-100">
                    <div class="card-body">
                        <div class="d-flex flex-column h-100">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h6>{{$t("referral_code")}} : <span class="text-muted mb-4">{{ result.refferal_code ?? "----" }}</span>
                                        <button @click="copyText(result.refferal_code, index)" class="btn btn-light btn-sm ms-2">
                                            <i :class="copiedIndex === index ? 'bx bxs-check-circle text-success' : 'bx bx-copy'"></i>
                                        </button>
                                    </h6>
                                    <h6>{{$t("referred_by")}} : <span class="text-muted mb-4">{{ result.referred_by_name ?? '----' }}</span>
                                    </h6>
                                    
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="d-flex gap-1 align-items-center">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-1 mt-n2 py-0 text-decoration-none fs-15" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal icon-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#" @click.prevent="viewProfile(result)" v-if="permissions.includes('view-user-profile')">
                                            <i class="bx bx-radio-circle-marked align-center text-muted me-2"></i>{{$t("view_profile")}}
                                        </a>
                                        <a class="dropdown-item" href="#" @click.prevent="editData(result)" v-if="permissions.includes('edit-user')">
                                            <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("edit")}}
                                        </a>
                                        <a class="dropdown-item" href="#" @click.prevent="editPassData(result)" v-if="permissions.includes('edit-user')">
                                            <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("update_password")}}
                                        </a>
                                        <a class="dropdown-item" href="#" @click.prevent="deleteModal(result.id)" v-if="permissions.includes('delete-user') && app_for !== 'demo'">
                                            <i class="bx bxs-trash-alt align-center text-muted me-2"></i>{{$t("delete")}}
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
                                            <!-- <img src="assets/images/brands/slack.png" alt="" class="img-fluid p-1"> -->
                                            <img class="avatar-xs rounded-circle p-1" alt="profile picture" :src="result.profile_picture">
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fs-15"><a class="text-body">{{ result.name ?? "----" }}</a></h5>
                                    <!-- <p>{{ result.rating }} <i class="ri-star-s-fill text-warning"></i></p> -->
                                    <!-- <p class="mb-0">
                                        <span v-for="(star, index) in getStarIcons(result.rating)" :key="index">
                                        <i :class="star"></i>
                                        </span>
                                    </p> -->
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
                                            <i class="las la-transgender fs-24"></i>
                                        </td>
                                        <td>{{ result.gender ?? "----" }}</td>
                                    </tr>
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
                                <th scope="col">{{$t("status")}}</th>
                            </div>
                            <div class="flex-end">
                                <td v-if="permissions.includes('toggle-user')">
                                    <div :class="{
                                            'form-check': true,
                                            'form-switch': true,
                                            'form-switch-lg': true,
                                            'form-switch-success': result.active,
                                        }">
                                        <input class="form-check-input" type="checkbox" role="switch" @click.prevent="toggleActiveStatus(result.id,!result.active)" :id="'status_'+result.id" :checked="result.active">
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
        <BOffcanvas v-model="rightOffcanvas" placement="end" :title="$t('users_filters')" header-class="bg-light"
            body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BFrom action="" class="d-flex flex-column justify-content-end h-100">
                <div class="offcanvas-body">
                    <!-- <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("transport_type")}}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="filter.transport_type">
                            <option value="all">{{$t("all")}}</option>
                            <option value="taxi">{{$t("taxi")}}</option>
                            <option value="delivery">{{$t("delivery")}}</option>
                        </select>
                    </div> -->

                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("status")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"  v-model="filter.status" type="radio" name="inlineRadioOptions"
                                    id="active" value='1'>
                                <label class="form-check-label" for="active">{{$t("active")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" v-model="filter.status" type="radio" name="inlineRadioOptions"
                                    id="inactive" value="0">
                                <label class="form-check-label" for="inactive">{{$t("inactive")}}</label>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="mb-4">
                        <label for="status-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("dispatch_type")}}</label>
                        <BRow class="g-2">
                            <BCol lg="6">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" v-model="filter.dispatch_type" type="radio" id="inlineCheckbox1"
                                        value="bidding" />
                                    <label class="form-check-label" for="inlineCheckbox1">{{$t("bidding")}}</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"  v-model="filter.dispatch_type" type="radio" id="inlineCheckbox2"
                                        value="normal" />
                                    <label class="form-check-label" for="inlineCheckbox2">{{$t("normal")}}</label>
                                </div>
                            </BCol>
                        </BRow>
                    </div> -->
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
