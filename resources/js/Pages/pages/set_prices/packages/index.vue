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
        zoneTypePrice: Object,

    },

    setup(props) {
    const { t } = useI18n();
    const searchTerm = ref("");
    const filter = useForm({
        all: "",
        locked: "",
        status:null,
        limit: 10
    });

console.log(props.zoneTypePrice.id);

    const results = ref([]);
    const paginator = ref({});
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


        const toggleActiveStatus = async (id, status) => {
            try {
              const response =   await axios.post(`/set-prices/packages/update-status`, { id: id, status: status });
                const index = results.value.findIndex(item => item.id === id);
                if (index !== -1) {
                    results.value[index].active = status; // Update the active status locally
                }
                // Optionally, you may want to re-fetch all data to ensure consistency
                 successMessage.value = response.data.successMessage; // Set the success message from the response
                // fetchDatas(); 
            } catch (error) {
                console.error(t('error_updating_status'), error);
            }
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


        // Function to clear filters
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
                await axios.delete(`/set-prices/packages/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('package_price_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_package_price'), 'error');
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

        // Function to fetch data based on filters
        const fetchDatas = async (page = 1) => {
      try {
        const params = { ...filter.data(), search: searchTerm.value, page };

        // Only include status parameter if it's not empty (to show all)
        if (filter.status !== "") {
          params.status = filter.status;
        }

        const response = await axios.get(`/set-prices/packages/list/${props.zoneTypePrice.id}`, { params });
        results.value = response.data.results;
        paginator.value = response.data.paginator;
        updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
        modalFilter.value = false;
      } catch (error) {
        console.error(t('error_fetching_set_prices'), error);
      }
    };
    const updatePaginatorOptions = () => {
        paginatorOption.value = [10, 25, 50, 100,200,500]; // Default static options
    };
    // **Handle per-page changes**
    const changeEntriesPerPage = () => {
        fetchDatas(); // Fetch new data
    };




        const handlePageChanged = async (page) => {
            localStorage.setItem("set-prices/packages/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/set-prices/packages/edit/${result.id}`); 
        };
        const packagesData = async (result) =>  {
            router.get(`set-prices/packages/${result.id}`); 
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
            packagesData,
            toggleActiveStatus,
            capitalizeFirstLetter,
            rightOffcanvas,
            paginatorOption,
            changeEntriesPerPage,
        };
    },
    mounted() {
        this.fetchDatas();
        const savedPage = localStorage.getItem("set-prices/packages/list");
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

        <Head title="Set Package Prices" />
        <PageHeader :title="$t('set_package_prices')" :pageTitle="$t('set_package_prices')" pageLink="/set-prices" />
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
                            <BCol md="3"></BCol>
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <!-- <searchbar></searchbar> -->
                                    <BButton variant="danger" @click="rightOffcanvas = true">
                                        <i class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}
                                    </BButton>
                                    <Link :href="`/set-prices/packages/create/${zoneTypePrice.id}`">
                                        <BButton variant="primary" class="float-end">
                                            <i class="ri-add-line align-bottom me-1"></i> {{$t("add_package_price")}}
                                        </BButton>
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
                                        <th scope="col"> {{$t("s_no")}}</th>
                                        <th scope="col"> {{$t("packages")}}</th>
                                        <th scope="col"> {{$t("status")}}</th>
                                        <th scope="col"> {{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ result.package_name }}</td>
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
                                                    <a v-if="result.active === 1" class="dropdown-item" href="#" @click="toggleActiveStatus(result.id, 0)">
                                                        <i class="bx bx-show-alt align-center text-muted me-2"></i>  {{$t("inactive")}} 
                                                    </a>
                                                    <a v-else class="dropdown-item" href="#" @click="toggleActiveStatus(result.id, 1)">
                                                        <i class="bx bxs-low-vision align-center text-muted me-2"></i>  {{$t("active")}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="editData(result)">
                                                        <i class="bx bxs-edit-alt align-center text-muted me-2"></i> {{$t("edit")}}
                                                    </a>                                                  
                                                    <a class="dropdown-item" href="#" @click.prevent="deleteModal(result.id)">
                                                        <i class="bx bxs-trash align-center text-muted me-2"></i> {{$t("delete")}}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                   </tr>
                                </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="7" class="text-center">
                                <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                <h5> {{$t("no_data_found")}}</h5>
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
        <!-- Filter Modal -->
        <BOffcanvas v-model="rightOffcanvas" placement="end" :title="$t('filters')" header-class="bg-light"
        body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BForm>
                <div class="offcanvas-body">                   
                    <div class="mb-4">
                        <label for="status-select" class="form-label text-muted text-uppercase fw-semibold mb-3"> {{$t("status")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusActive" v-model="filter.status" value="1">
                                <label class="form-check-label" for="statusActive"> {{$t("active")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusInactive" v-model="filter.status" value="0">
                                <label class="form-check-label" for="statusInactive"> {{$t("inactive")}}</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton variant="light" class="w-100" @click="clearFilter"> {{$t("clear_filter")}}</BButton>
                    <BButton type="submit" variant="success" class="w-100"  @click="filterData"> {{$t("apply")}}</BButton>
                </div>
            </BForm>
        </BOffcanvas>
        <!-- Filter End -->
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
