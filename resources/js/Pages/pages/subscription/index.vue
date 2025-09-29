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
        enabled_module:String,

        app_for: String,
        users: Array,
        promoCodes: {
            type: Object,
            required: true,
        }


    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");
        const filter = useForm({
            status: null,
            transport_type: null,
            limit:10
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const users = ref(props.users);
        const enabled_module = ref(props.enabled_module);
        const paginatorOption = ref({}); // Spread the results to make them reactive
        

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        // const toggleActiveStatus = async (id, status) => {
        //     try {
        //         await axios.post(`/subscription/update-status`, { id: id, status: status });
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
                        await axios.post(`/subscription/update-status/${id}`, { id: id, status: status });
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
        const capitalizeFirstLetter = (value) => {
            return value.charAt(0).toUpperCase() + value.slice(1);
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


        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/subscription/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('subscription_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_subscription'), 'error');
            }
        };
        const enable_subscription = ref(enabled_module.value != "commission");


        const handleSubscriptionUpdate = async (key) => {
            if (enabled_module.value !== key) {
                try {
                    await axios.post(`/drivers-levelup/settingsUpdate`, { id: 'driver_register_module', status: key });
                    enabled_module.value = key;
                    enable_subscription.value = key !== "commission";
                } catch (error) {
                    console.error(t('error_updating_module'), error);
                    Swal.fire(t('error'), t('failed_to_update_module'), "error");
                }
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

        // watch(searchTerm, debounce(function (value) {
        //     fetchDatas(searchTerm.value);

        // }, 300));

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.page = page;
                if(searchTerm.value.length > 0){
                    params.search = searchTerm.value;
                }
                const response = await axios.get(`/subscription/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_subscriptions'), error);
            }
        };

        const handlePageChanged = async (page) => {
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/subscription/edit/${result.id}`); 
        };

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
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
            users,
            modalShow,
            deleteItemId,
            successMessage,
            enable_subscription,
            handleSubscriptionUpdate,
            alertMessage,
            filterData,
            deleteModal,
            enabled_module,
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
            editData,
            capitalizeFirstLetter,
            toggleActiveStatus,
            rightOffcanvas,
            fetchSearch,
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

        <Head title="Subscription Code" />
        <PageHeader :title="$t('subscription')" :pageTitle="$t('subscription')" />
        <BRow>
            <BCol lg="12">
                <BCard v-if="app_for === 'demo'" no-body id="tasksList">
                <BCardHeader class="border-0">
                    <div class="alert bg-warning border-warning fs-18" role="alert">
                    <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
                    </div>
                </BCardHeader>
                </BCard>
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                        <BRow>
                            <div class="col-sm-4" v-if="permissions.includes('add-subscription')">
                                <div class="mb-3">
                                <div class="border rounded">
                                    <div class="row">
                                    <div class="col">
                                        <label class="form-check-label p-2 mt-2" for="enable_commission">{{$t("enable_commission")}}</label>
                                    </div>
                                    <div class="col">
                                        <div class="form-check form-switch form-switch-md float-end p-2">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="enable_commission"
                                            :disabled="app_for == 'demo'"
                                            :checked="enabled_module === 'commission'"
                                            @click.prevent="handleSubscriptionUpdate('commission')"
                                        />
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="col-sm-4" v-if="permissions.includes('add-subscription')">
                                <div class="mb-3">
                                <div class="border rounded">
                                    <div class="row">
                                    <div class="col">
                                        <label class="form-check-label p-2 mt-2" for="enable_subscribtion">{{$t("enable_subscribtion")}}</label>
                                    </div>
                                    <div class="col">
                                        <div class="form-check form-switch form-switch-md float-end p-2">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            :disabled="app_for == 'demo'"
                                            :checked="enabled_module === 'subscription'"
                                            id="enable_subscribtion"
                                            @click.prevent="handleSubscriptionUpdate('subscription')"
                                        />
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="col-sm-4" v-if="permissions.includes('add-subscription')">
                                <div class="mb-3">
                                <div class="border rounded">
                                    <div class="row">
                                    <div class="col-8">
                                        <label class="form-check-label p-2 mt-2" for="enable_subscribtion_and_commission">{{$t("enable_subscribtion_and_commission")}}</label>
                                    </div>
                                    <div class="col">
                                        <div class="form-check form-switch form-switch-md float-end p-2">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            :disabled="app_for == 'demo'"
                                            :checked="enabled_module === 'both'"
                                            id="enable_subscribtion_and_commission"
                                            @click.prevent="handleSubscriptionUpdate('both')"
                                        />
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </BRow>
                        <BRow class="g-2" v-if="enable_subscription">
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
                                    <BButton variant="danger" @click="rightOffcanvas = true"><i
                                            class="ri-filter-2-line me-1 align-bottom"></i> {{ $t("filters") }}</BButton>

                                    <Link href="/subscription/create" v-if="permissions.includes('add-subscription')">
                                    <BButton v-if="app_for!== 'demo'" variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> {{ $t("add_subscription") }}</BButton>
                                    </Link>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0"  v-if="enable_subscription">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{ $t("name") }}</th>
                                        <th scope="col">{{ $t("vehicle_type") }}</th>
                                        <th scope="col">{{ $t("status") }}</th>
                                        <th scope="col">{{ $t("action") }}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>
                                            <div class="d-flex">
                                                <div class="flex-grow-1">{{ result.name }}</div>
                                            </div>
                                        </td>
                                        <td>{{ capitalizeFirstLetter(result.vehicle_type_name) }}</td>
                                        <td v-if="permissions.includes('toggle-subscription')">
                                            <div :class="{
                                                    'form-check': true,
                                                    'form-switch': true,
                                                    'form-switch-lg': true,
                                                    'form-switch-success': result.active,
                                                }">
                                                <input class="form-check-input" type="checkbox" :disabled="app_for == 'demo'" role="switch" @click.prevent="toggleActiveStatus(result.id, !result.active)" :id="'status_'+result.id" :checked="result.active">
                                            </div>
                                        </td>
                                        <td>
                                            <BButton @click.prevent="editData(result)" v-if="permissions.includes('edit-subscription')"
                                                class="btn btn-soft-warning btn-sm m-2"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')">
                                                <i class='bx bxs-edit-alt bx-xs'></i>
                                            </BButton>
                                            <BButton class="btn btn-soft-danger btn-sm m-2" size="sm" v-if="permissions.includes('delete-subscription') && app_for!== 'demo'"
                                                type="button" @click.prevent="deleteModal(result.id)"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('delete')">
                                                <i class='bx bx-trash bx-xs'></i>
                                            </BButton>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                            <h5>{{ $t("no_data_found") }}</h5>
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
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{ $t("status") }}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="WithoutinlineRadio2" value=1 v-model="filter.status">
                                <label class="form-check-label" for="WithoutinlineRadio2">{{ $t("active") }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="WithoutinlineRadio3" value=0 v-model="filter.status">
                                <label class="form-check-label" for="WithoutinlineRadio3">{{ $t("inactive") }}</label>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{ $t("transport_type") }}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="filter.transport_type">
                            <option value="taxi">{{ $t("taxi") }}</option>
                            <option value="delivery">{{ $t("delivery") }}</option>
                            <option value="both">{{ $t("both") }}</option>
                        </select>
                    </div> -->
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton variant="light" @click="clearFilter()" class="w-100">{{ $t("clear_filter") }}</BButton>
                    <BButton type="submit" @click="filterData" variant="success" class="w-100">
                        {{ $t("apply") }}
                    </BButton>
                </div>
                <!--end offcanvas-footer-->
            </BFrom>
        </BOffcanvas>
        <!--end offcanvas-->
        <!-- filter end -->

        <!-- Pagination -->
        <Pagination v-if="enable_subscription":paginator="paginator" @page-changed="handlePageChanged" />
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
