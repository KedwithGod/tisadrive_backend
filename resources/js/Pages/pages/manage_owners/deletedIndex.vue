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
        app_for:String,
        serviceLocations: {
            type: Object,
            required: true,
        }


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
                const response = await axios.get(`/manage-owners/deletedList`, { params });
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
            fetchDatas(page);
        };

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };
        const viewOwnerDocument = async (owner) =>  {
                    router.get(`/manage-owners/document/${owner.id}`);
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

        const restoreUser = async (userId) => {
                if (!userId) {
                    console.error("Invalid user data:", userId);
                    return;
                }

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to restore this user?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#34c38f",
                    cancelButtonColor: "#f46a6a",
                    confirmButtonText: "Yes, restore it!",
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await axios.patch(`/users/restore/${userId}`);
                            results.value = results.value.filter(user => user.id !== userId); // Remove from list
                            Swal.fire("Restored!", response.data.message, "success");
                        } catch (error) {
                            console.error("Error restoring user:", error);
                            Swal.fire("Error!", "Failed to restore user.", "error");
                        }
                    }
                });
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
            restoreUser,
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
    },
};
</script>

<template>
    <Layout>

        <Head title="Manage Deleted Owners" />
        <PageHeader :title="$t('manage_deleted_owners')" :pageTitle="$t('manage_owners')" />
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
                                <!-- <div class="search-box">
                                    <input type="text" id="name" class="form-control search"
                                        placeholder="Search Service..." v-model="searchTerm" @keyup.enter="fetchDatas" />
                                    <i class="ri-search-line search-icon"></i>
                                </div> -->
                            </BCol>
                            <!-- <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <searchbar @search="fetchSearch"></searchbar>
                                    <BButton variant="danger" @click="rightOffcanvas = true">
                                        <i class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}
                                    </BButton>

                                    <BButton variant="primary" @click="addOwner" class="float-end" v-if="permissions.includes('add-owner')">
                                        <i class="ri-add-line align-bottom me-1"></i> {{$t("add_manage_owners")}}
                                    </BButton>
                                </div>
                            </BCol> -->
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-owner">
                                    <tr>
                                        <th scope="col">{{$t("name")}}</th>
                                        <!-- <th scope="col">{{$t("owner_name")}}</th> -->
                                        <th scope="col">{{$t("email")}}</th>
                                        <th scope="col">{{$t("mobile_number")}}</th>
                                        <!-- <th scope="col">{{$t("document_view")}}</th>
                                        <th scope="col">{{$t("approval_status")}}</th> -->
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ result.name }}</td>
                                        <!-- <td> {{ result.owner_name }}</td>   -->
                                        <td>{{ emailFromUser(result) }}</td>
                                        <td>{{ mobileFromUser(result) }}</td>
                                        <!-- <td v-if="permissions.includes('view-owner-document')">
                                            <Link @click="viewOwnerDocument(result)" href="#"> 
                                                <i class="bx bxs-file text-primary" style="font-size: 35px"></i>
                                            </Link>
                                        </td>
                                        <td v-if="permissions.includes('toggle-owner')">
                                            <div :class="{
                                                    'form-check': true,
                                                    'form-switch': true,
                                                    'form-switch-lg': true,
                                                    'form-switch-success': result.approve,
                                                }">
                                                <input class="form-check-input" type="checkbox" role="switch" @click.prevent="viewOwnerDocument(result)" :id="'status_'+result.id" :checked="result.approve">
                                            </div>
                                        </td>                                       -->
                                        <td>
                                            <!-- <BButton @click.prevent="editData(result)" v-if="permissions.includes('edit-owner')"
                                                class="btn btn-soft-warning btn-sm m-2"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title='$t("edit")'>
                                                <i class='bx bxs-edit-alt bx-xs'></i>
                                            </BButton>
                                            <BButton @click.prevent="editPassData(result)" v-if="permissions.includes('edit-owner')"
                                                class="btn btn-soft-secondary btn-sm m-2"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('update_password')">
                                                <i class=' ri-lock-password-line bx-xs'></i>
                                            </BButton> -->
                                            <BButton class="btn btn-soft-danger btn-sm m-2" size="sm" v-if="permissions.includes('delete-owner') && app_for !== 'demo'"
                                                type="button" @click.prevent="deleteModal(result.id)"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title='$t("delete")'>
                                                <i class='bx bx-trash bx-xs'></i>
                                            </BButton>
                                            <BButton class="btn btn-soft-success btn-sm m-2" size="sm" v-if="permissions.includes('delete-owner') && app_for !== 'demo'"
                                                type="button" @click.prevent="restoreUser(result.id)"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title='$t("restore")'>
                                                <i class='bx bx-undo bx-xs'></i>
                                            </BButton>
                                            <!-- <BButton class="btn btn-soft-success btn-sm m-2" size="sm" v-if="permissions.includes('view-owner-profile') && app_for !== 'demo'"
                                                type="button" @click.prevent="viewProfile(result)"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover title="View Profile">
                                                <i class='  ri-account-circle-line bx-xs'></i>
                                            </BButton> -->
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
