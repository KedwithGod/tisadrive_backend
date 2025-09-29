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
import { useI18n } from 'vue-i18n';
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";

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
        app_for: String,
        activeMessage: String,
        onboarding_screen: {
            type: Object,
            required: true,
        }


    },
    setup(props) {
        const searchTerm = ref("");
        const { t } = useI18n();
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
        const activeMessage = ref(props.activeMessage || '');

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
                await axios.delete(`/onboarding-screen/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('onboarding_screen_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'),t('failed_to_delete_onboarding_screen'), 'error');
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

        watch(searchTerm, debounce(function (value) {
            fetchDatas(searchTerm.value);

        }, 300));

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/onboarding-screen/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_onboarding_screen'), error);
            }
        };

        const handlePageChanged = async (page) => {
          localStorage.setItem("/onboarding-screen/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/onboarding-screen/edit/${result.id}`); 
        };
        const toggleActiveStatus = async (id, status) => {
            if(props.app_for == "demo"){
                Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
                return;
            }
            try {
                await axios.post(`/onboarding-screen/update-status`, { id: id, status: status });
                const index = results.value.findIndex(item => item.id === id);
                if (index !== -1) {
                    if(results.value[index].active === 1){                       
                        successMessage.value = t('onboarding_inactive_successfully');
                        results.value[index].active = status; // Update the active status locally
                    }
                    else{
                        successMessage.value = t('onboarding_active_successfully');
                        results.value[index].active = status; // Update the active status locally
                    }
                    setTimeout(() => {
                        successMessage.value = ""; // Clear the message
                    }, 5000);
                    
                }
                // Optionally, you may want to re-fetch all data to ensure consistency
                // fetchDatas(); 
            } catch (error) {
                console.error(t('error_updating_status'), error);
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
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            filter,
            handlePageChanged,
            editData,
            toggleActiveStatus,
            activeMessage,
            paginatorOption,
            changeEntriesPerPage
        };
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
    methods: {
        stripHtmlTags(content) {
            const parser = new DOMParser();
            const parsedContent = parser.parseFromString(content, 'text/html');
            return parsedContent.body.textContent || "";
            },
    },
    mounted() {
        this.fetchDatas();
        const savedPage = localStorage.getItem("/onboarding-screen/list");
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

        <Head title="Onboarding Screen" />
        <PageHeader :title="$t('onboarding_screen')" :pageTitle="$t('onboarding_screen')" />
        <BRow>
            <BCard v-if="app_for === 'demo'" no-body id="tasksList">
                <BCardHeader class="border-0">
                    <div class="alert bg-warning border-warning fs-18" role="alert">
                        <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
                    </div>
                </BCardHeader>
            </BCard>
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
                                    <searchbar></searchbar>
                                    <BButton variant="danger" @click="rightOffcanvas = true"><i
                                            class="ri-filter-2-line me-1 align-bottom"></i> Filters</BButton>

                                    <Link href="/owner-needed-documents/create">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> Add Owner Needed Document</BButton>
                                    </Link>
                                </div>
                            </BCol> -->
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{$t("screen")}}</th>
                                        <th scope="col">{{$t("screen_order")}}</th>
                                        <th scope="col">{{$t("onboarding_title")}}</th>
                                        <th scope="col">{{$t("description")}}</th>
                                        <th scope="col">{{$t("status")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ result.screen }}</td>
                                        <td>{{ result.order }}</td>
                                        <td>{{ result.title }}</td>
                                        <td>{{stripHtmlTags(result.description )}}</td>
                                        <td>
                                            <template v-if="result.active == 1">
                                                <BBadge variant="success" class="text-uppercase">{{$t("active")}}</BBadge>
                                            </template>
                                            <template v-else>
                                                <BBadge variant="danger" class="text-uppercase">{{$t("inactive")}}</BBadge>
                                            </template>
                                        </td>                                      
                                        <td>
                                            <div class="dropdown">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#" @click.prevent="editData(result)"  v-if="permissions.includes('edit_onboarding')"><i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("edit")}}</a>
                                                    <div  v-if="permissions.includes('toggle_onboarding')">
                                                        <a class="dropdown-item" href="#" v-if="result.active === 1"  @click="toggleActiveStatus(result.id, 0)"><i class="bx bxs-low-vision align-center text-muted me-2"></i> {{ $t("inactive") }}</a>
                                                        <a class="dropdown-item" href="#" v-else  @click="toggleActiveStatus(result.id, 1)"><i class="bx bx-show-alt align-center text-muted me-2"></i> {{$t("active")}}</a>
                                                    </div>                                                    
                                                    <!-- <a class="dropdown-item" href="#" @click.prevent="deleteModal(result.id)"><i class="bx bxs-trash align-center text-muted me-2"></i>Delete</a> -->
                                                </div>
                                            </div>
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
        <BOffcanvas v-model="rightOffcanvas" placement="end" title="Leads Filters" header-class="bg-light"
            body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BFrom action="" class="d-flex flex-column justify-content-end h-100">
                <div class="offcanvas-body">
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">Process</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="status">
                            <option value="All Tasks">All</option>
                            <option value="Completed">Completed</option>
                            <option value="Inprogress">Inprogress</option>
                            <option value="Pending">Pending</option>
                            <option value="Pending">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">Payment</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="WithoutinlineRadio1" value="option1">
                                <label class="form-check-label" for="inlineRadio1">Online</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="WithoutinlineRadio2" value="option2">
                                <label class="form-check-label" for="inlineRadio1">Card</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="WithoutinlineRadio3" value="option3">
                                <label class="form-check-label" for="inlineRadio1">Cash</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">Date</label>
                        <flat-pickr placeholder="Select date" v-model="date" :config="rangeDateconfig"
                            class="form-control flatpickr-input" id="demo-datepicker"></flat-pickr>
                    </div>

                    <div class="mb-4">
                        <label for="country-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">Country</label>

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
                            class="form-label text-muted text-uppercase fw-semibold mb-3">Status</label>
                        <BRow class="g-2">
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                        value="option1" />
                                    <label class="form-check-label" for="inlineCheckbox1">Active</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox2"
                                        value="option2" />
                                    <label class="form-check-label" for="inlineCheckbox2">Inactive</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                        value="option3" />
                                    <label class="form-check-label" for="inlineCheckbox3">Cash</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox4"
                                        value="option4" />
                                    <label class="form-check-label" for="inlineCheckbox4">Card</label>
                                </div>
                            </BCol>
                        </BRow>
                    </div>
                    <div>
                    </div>
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton variant="light" class="w-100">Clear Filter</BButton>
                    <BButton type="submit" variant="success" class="w-100">
                        Apply
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
