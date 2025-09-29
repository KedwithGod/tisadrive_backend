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
        const savedView = localStorage.getItem("adminViewMode");
        if (savedView) {
        this.isListView = savedView === "list";
        }
    },
    methods: {
        toggleView(view) {
        this.isListView = view === "list";
        localStorage.setItem("adminViewMode", view); // Save to localStorage
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
        app_for: String,

        serviceLocations: {
            type: Object,
            required: true,
        }


    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");
        const filter = useForm({
            all: "",
            locked: "",
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



        const filterData = () => {
            modalFilter.value = true;
        };


        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            modalFilter.value = false;
        };


        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/admins/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('admin_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_admin'), 'error');
            }
        };
        const toggleActiveStatus = async (id, currentStatus) => {
            try {
                const newStatus = currentStatus ? 0 : 1; // Toggle the status

                await axios.post(`/admins/update-status`, { id: id, status: newStatus });

                const index = results.value.findIndex(item => item.id === id);
                if (index !== -1) {
                    results.value[index].active = newStatus; // Update the status in the local results array
                }
                Swal.fire(t('success'), t('admin_status_updated_successfully'), 'success');
            } catch (error) {
                console.error(t('error_updating_status'), error);
                Swal.fire(t('error'), t('failed_to_update_admin_status'), 'error');
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
                        Swal.fire(t('error'), t('failed_to_delete_the_data'));
                    }
                }
            });
        };

        // watch(searchTerm, debounce(function (value) {
        //     fetchDatas(searchTerm.value);

        // }, 300));

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/admins/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_admin'), error);
            }
        };

        const handlePageChanged = async (page) => {
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/admins/edit/${result.id}`); 
        };
        const editPassData = async (result) =>  {
            router.get(`/admins/password/edit/${result.id}`); 
        };
        const updatePaginatorOptions = () => {
            paginatorOption.value = [10, 25, 50, 100,200,500]; // Default static options
        };
        // **Handle per-page changes**
        const changeEntriesPerPage = () => {
            fetchDatas(); // Fetch new data
        };
        const mobileFromUser = (user) => {
            if(props.app_for && props.app_for == "demo"){
                return "***********";
            }
            return user.mobile;
        }

        const emailFromUser = (user) => {
            if(props.app_for && props.app_for == "demo"){
                return "***********";
            }
            return user.email
        }
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
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            filter,
            handlePageChanged,
            toggleActiveStatus,
            editData,
            fetchSearch,
            editPassData,
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
    mounted() {
        this.fetchDatas();
    },
};
</script>

<template>
    <Layout>

        <Head title="Admins" />
        <PageHeader :title="$t('admins')" :pageTitle="$t('admins')" pageLink="/admins"/>
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
                                    <!-- <BButton variant="danger" @click="rightOffcanvas = true"><i
                                            class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}</BButton> -->

                                    <Link href="/admins/create" v-if="permissions.includes('add-admin')">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> {{$t("add_admin")}}</BButton>
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
                                        <th scope="col">{{$t("email")}}</th>
                                        <th scope="col">{{$t("service_location")}}</th>
                                        <th scope="col">{{$t("role")}}</th>
                                        <th scope="col">{{$t("status")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ result.first_name }}</td>
                                        <td>{{ emailFromUser(result) }}</td>
                                        <td>{{ result.service_location_name }}</td>
                                        <td>{{ result.role_name }}</td>
                                        <td>
                                            <div
                                                :class="{
                                                    'form-check': true,
                                                    'form-switch': true,
                                                    'form-switch-lg': true,
                                                    'form-switch-success': result.active
                                                }"
                                            >
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    role="switch"
                                                    :disabled="!permissions.includes('toggle-admin') || app_for == 'demo'"
                                                    :id="'status_' + result.id"
                                                    :checked="result.active"
                                                    @click.prevent="toggleActiveStatus(result.id, result.active)"
                                                />
                                            </div>
                                        </td>

                                       <td>
                                            <BButton @click.prevent="editData(result)" v-if="permissions.includes('edit-admin')"
                                                class="btn btn-soft-warning btn-sm m-2"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')">
                                                <i class='bx bxs-edit-alt bx-xs'></i>
                                            </BButton>
                                            <BButton @click.prevent="editPassData(result)" v-if="permissions.includes('edit-admin')"
                                                class="btn btn-soft-secondary btn-sm m-2"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('update_password')">
                                                <i class=' ri-lock-password-line'></i>
                                            </BButton>
                                            <BButton class="btn btn-soft-danger btn-sm m-2" size="sm" v-if="permissions.includes('delete-admin') && app_for !== 'demo'"
                                                type="button" @click.prevent="deleteModal(result.id)"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('delete')">
                                                <i class='bx bx-trash bx-xs'></i>
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
                                    
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="d-flex gap-1 align-items-center">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-1 mt-n2 py-0 text-decoration-none fs-15" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal icon-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#" @click.prevent="editData(result)" v-if="permissions.includes('edit-admin')">
                                                    <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("edit")}}
                                                </a>
                                                <a class="dropdown-item" href="#" @click.prevent="editPassData(result)" v-if="permissions.includes('edit-admin')">
                                                    <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("update_password")}}
                                                </a>
                                                <a class="dropdown-item" href="#" v-if="permissions.includes('delete-admin') && app_for !== 'demo'" @click.prevent="deleteModal(result.id)">
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
                                    <h5 class="mb-1 fs-15"><a class="text-body">{{ result.first_name ?? "----" }}</a></h5>
                                    <tr>
                                        <td  scope="row">{{$t("role")}} :</td>
                                        <td class="fw-medium p-2"> {{result.role_name ??  "----"}} </td>
                                    </tr>
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
                                <td v-if="permissions.includes('toggle-admin')">
                                    <div :class="{'form-check': true,'form-switch': true,'form-switch-lg': true,'form-switch-success': result.active
                                                }">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            :disabled="!permissions.includes('toggle-admin') || app_for == 'demo'"
                                            :id="'status_' + result.id"
                                            :checked="result.active"
                                            @click.prevent="toggleActiveStatus(result.id, result.active)"
                                        />
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
        <BOffcanvas v-model="rightOffcanvas" placement="end" :title="$t('leads_filters')" header-class="bg-light"
            body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BFrom action="" class="d-flex flex-column justify-content-end h-100">
                <div class="offcanvas-body">
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{ $t("process") }}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="status">
                            <option value="All Tasks">{{ $t("all") }}</option>
                            <option value="Completed">{{ $t("completed") }}</option>
                            <option value="Inprogress">{{ $t("inprogress") }}</option>
                            <option value="Pending">{{ $t("pending") }}</option>
                            <option value="Pending">{{ $t("cancelled") }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{ $t("payment") }}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="WithoutinlineRadio1" value="option1">
                                <label class="form-check-label" for="inlineRadio1">{{ $t("online") }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="WithoutinlineRadio2" value="option2">
                                <label class="form-check-label" for="inlineRadio1">{{ $t("card") }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="WithoutinlineRadio3" value="option3">
                                <label class="form-check-label" for="inlineRadio1">{{ $t("cash") }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{ $t("date") }}</label>
                        <flat-pickr :placeholder="$t('select_date')" v-model="date" :config="rangeDateconfig"
                            class="form-control flatpickr-input" id="demo-datepicker"></flat-pickr>
                    </div>

                    <div class="mb-4">
                        <label for="country-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{ $t("country") }}</label>

                        <Multiselect class="form-control" v-model="value" :close-on-select="true" :searchable="true"
                            :create-option="true" :options="[
                                { value: '', label: 'Select country' },
                                { value: 'Argentina', label: 'Argentina' },
                                { value: 'Belgium', label: 'Belgium' },
                                { value: 'Brazil', label: 'Brazil' },
                                { value: 'Colombia', label: 'Colombia' },
                                { value: 'Denmark', label: 'Denmark' },
                                { value: 'France', label: 'France' },
                                { value: 'Germany', label: 'Germany' },
                                { value: 'Mexico', label: 'Mexico' },
                                { value: 'Russia', label: 'Russia' },
                                { value: 'Spain', label: 'Spain' },
                                { value: 'Syria', label: 'Syria' },
                                { value: 'United Kingdom', label: 'United Kingdom' },
                            ]" />
                    </div>
                    <div class="mb-4">
                        <label for="status-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{ $t("status") }}</label>
                        <BRow class="g-2">
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                        value="option1" />
                                    <label class="form-check-label" for="inlineCheckbox1">{{ $t("active") }}</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox2"
                                        value="option2" />
                                    <label class="form-check-label" for="inlineCheckbox2">{{ $t("inactive") }}</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                        value="option3" />
                                    <label class="form-check-label" for="inlineCheckbox3">{{ $t("cash") }}</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox4"
                                        value="option4" />
                                    <label class="form-check-label" for="inlineCheckbox4">{{ $t("card") }}</label>
                                </div>
                            </BCol>
                        </BRow>
                    </div>
                    <div>
                    </div>
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton variant="light" class="w-100">{{ $t("clear_filter") }}</BButton>
                    <BButton type="submit" variant="success" class="w-100">
                        {{ $t("apply") }}
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