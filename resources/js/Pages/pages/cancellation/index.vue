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
            SearchQuery: '',
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
    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");
        const filter = useForm({
            transport_type: null,
            payment_type: null,
            user_type: null,
            arrival_status: null,
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
                await axios.delete(`/cancellation/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('reason_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_reason'), 'error');
            }
        };

        // const toggleActiveStatus = async (id, status) => {
        //     try {
        //         await axios.post(`/cancellation/update-status`, { id: id, status: status });
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
                        await axios.post(`/cancellation/update-status`, { id: id, status: status });
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

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/cancellation/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_reason'), error);
            }
        };

        const handlePageChanged = async (page) => {
            localStorage.setItem("cancellation/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/cancellation/edit/${result.id}`); 
        };

        const SerialNumber = (index) => {
            return ((paginator.value.current_page-1)*paginator.value.per_page)+index+1;
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
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            filter,
            toggleActiveStatus,
            handlePageChanged,
            SerialNumber,
            editData,
            fetchSearch,
            rightOffcanvas,
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
        const savedPage = localStorage.getItem("cancellation/list");
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

        <Head title="Cancellation" />
        <PageHeader :title="$t('cancellation')" :pageTitle="$t('cancellation')" />
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
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <searchbar @search="fetchSearch"></searchbar>
                                    <BButton variant="danger" @click="rightOffcanvas = true"><i
                                        class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}</BButton>

                                    <Link href="/cancellation/create" v-if="permissions.includes('add-cancellation')">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> {{$t("add_cancellation")}}</BButton>
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
                                        <th scope="col">{{$t("reason")}}</th>
                                        <th scope="col">{{$t("user_type")}}</th>
                                        <th scope="col">{{$t("compensable")}}</th>
                                        <th scope="col">{{$t("transport_type")}}</th>
                                        <th scope="col">{{$t("arrival_status")}}</th>
                                        <th scope="col">{{$t("status")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ result.reason }}</td> 
                                        <td> {{ result.user_type }}</td> 
                                        <td> {{ result.payment_type == "free"? $t('free'): $t('compensate') }}</td>
                                        <td> {{ result.transport_type == 'taxi' ? $t('taxi') : $t('delivery') }}</td> 
                                        <td> {{ result.arrival_status == 'before' ? $t('before_arrival') : $t('after_arrival') }}</td> 
                                        <td v-if="permissions.includes('toggle-cancellation')">
                                            <div :class="{
                                                    'form-check': true,
                                                    'form-switch': true,
                                                    'form-switch-lg': true,
                                                    'form-switch-success': result.active,
                                                }">
                                                <input class="form-check-input" type="checkbox" role="switch" @click.prevent="toggleActiveStatus(result.id, !result.active)" :id="'status_'+result.id" :checked="result.active">
                                            </div>
                                        </td>                                 
                                        <td>
                                            <BButton @click.prevent="editData(result)" v-if="permissions.includes('edit-cancellation')"
                                                class="btn btn-soft-warning btn-sm m-2"
                                                data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')">
                                                <i class='bx bxs-edit-alt bx-xs'></i>
                                            </BButton>
                                            <BButton class="btn btn-soft-danger btn-sm m-2" size="sm" v-if="permissions.includes('delete-cancellation')"
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
        <BOffcanvas v-model="rightOffcanvas" placement="end" :title="$t('leads_filters')" header-class="bg-light"
            body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BFrom action="" class="d-flex flex-column justify-content-end h-100">
                <div class="offcanvas-body">
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("transport_type")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"  v-model="filter.transport_type" type="radio" name="transportType"
                                    id="taxi" value='taxi'>
                                <label class="form-check-label" for="taxi">{{$t("taxi")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" v-model="filter.transport_type" type="radio" name="transportType"
                                    id="delivery" value="delivery">
                                <label class="form-check-label" for="delivery">{{$t("delivery")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" v-model="filter.transport_type" type="radio" name="transportType"
                                    id="both" value="both">
                                <label class="form-check-label" for="both">{{$t("all")}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="user-type"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("user_type")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"  v-model="filter.user_type" type="radio" name="userType"
                                    id="user" value='user'>
                                <label class="form-check-label" for="user">{{$t("user")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" v-model="filter.user_type" type="radio" name="userType"
                                    id="driver" value="driver">
                                <label class="form-check-label" for="driver">{{$t("driver")}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="user-type"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("payment_type")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"  v-model="filter.payment_type" type="radio" name="paymentType"
                                    id="free" value='free'>
                                <label class="form-check-label" for="free">{{$t("free")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" v-model="filter.payment_type" type="radio" name="paymentType"
                                    id="compensate" value="compensate">
                                <label class="form-check-label" for="compensate">{{$t("compensate")}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="user-type"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("arrival_status")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"  v-model="filter.arrival_status" type="radio" name="arrival_status"
                                    id="before" value='before'>
                                <label class="form-check-label" for="before">{{$t("before_arrival")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" v-model="filter.arrival_status" type="radio" name="arrival_status"
                                    id="after" value="after">
                                <label class="form-check-label" for="after">{{$t("after_arrival")}}</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- <div class="mb-4">
                        <label for="status-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("status")}}</label>
                        <BRow class="g-2">
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                        value="option1" />
                                    <label class="form-check-label" for="inlineCheckbox1">{{$t("active")}}</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox2"
                                        value="option2" />
                                    <label class="form-check-label" for="inlineCheckbox2">{{$t("inactive")}}</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                        value="option3" />
                                    <label class="form-check-label" for="inlineCheckbox3">{{$t("cash")}}</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox4"
                                        value="option4" />
                                    <label class="form-check-label" for="inlineCheckbox4">{{$t("card")}}</label>
                                </div>
                            </BCol>
                        </BRow>
                    </div> -->
                    <div>
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

</style>
