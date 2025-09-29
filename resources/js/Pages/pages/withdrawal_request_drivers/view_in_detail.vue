<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, watch, onMounted, computed } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";

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
        walletBalance:Number,
        bankDetails:Array,
        bank_infos:Array,
        driver_id:Number,
        formattedBankInfos:Array,
    },
    setup(props) {

    const formattedBankInfos = props.formattedBankInfos;

        const searchTerm = ref("");
        const filter = useForm({
            all: "",
            locked: "",
            limit : 10
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
                await axios.delete(`/withdrawal-request-drivers/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), 'Service Location deleted successfully', 'success');
            } catch (error) {
                Swal.fire(t('error'), 'Failed to delete Service Location', 'error');
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
                        console.error("Error deleting data:", error);
                        Swal.fire("Error", "Failed to delete the data", "error");
                    }
                }
            });
        };
        watch(searchTerm, debounce(function (value) {
            fetchDatas(searchTerm.value);

        }, 300));

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data(); // Assuming this fetches additional parameters
                params.search = searchTerm.value;
                params.page = page;

                const response = await axios.get(`/withdrawal-request-drivers/amounts/${props.driver_id}`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error("Error fetching withdrawal-request-drivers:", error);
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
            fetchDatas(page);
        };

        const updatePaymentStatus = async (result, status) => {
            try {
                const response = await axios.post(`/withdrawal-request-drivers/update-status`, {
                    id: result.id,
                    status: status,
                });
                response.data.successMessage; // Notify success
                fetchDatas(); // Reload data to reflect changes
            } catch (error) {
                console.error("Error updating payment status:", error);
                alert("Failed to update payment status. Please try again.");
            }
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
            updatePaymentStatus,
            formattedBankInfos,
            paginatorOption,
            changeEntriesPerPage,
            filter
        };
    },
    mounted() {
        this.fetchDatas();
    },
};
</script>

<template>
    <Layout>

        <Head title="View Details" />
        <PageHeader :title="$t('view_details')" :pageTitle="$t('view_details')" pageLink="/withdrawal-request-drivers"/>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="card border card-border-primary">
                                    <div class="card-body">
                                        <h5>{{$t("balance_amount")}}</h5>
                                        <div class="row mt-5">
                                            <div class="col-6">
                                                <!-- <i class="las la-ban" style="font-size: 20px;"></i> -->
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                        <i class="bx bx-money text-success icon-lg"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <h5  style="text-align:end;">{{walletBalance}}</h5>                                                
                                            </div>    
                                        </div>                                         
                                    </div>
                                </div>                                        
                            </div>
<div v-for="(method, methodIndex) in formattedBankInfos" :key="methodIndex" class="col-sm-12">
    <div class="card border card-border-primary">
        <div class="card-body">
            <h5 class="mb-3">{{ $t(method.method_name) }}</h5>
            <div v-if="method.fields.length > 0">
                <div class="d-flex" v-for="(field, fieldIndex) in method.fields" :key="fieldIndex">
                    <i 
                        v-if="field.value" 
                        class="ri-checkbox-circle-fill text-success">
                    </i>
                    <h6 class="ms-2">
                        {{ $t(field.field_name) }}: 
                        <span>{{ field.value || $t("No data available") }}</span>
                    </h6>
                </div>
            </div>
            <div v-else>
                <p>{{ $t("No fields available for this method") }}</p>
            </div>
        </div>
    </div>
</div>


                        </div>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <h5>{{$t("withdrawal_request_history")}}</h5>
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
                        <div class="table-responsive mt-4">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{$t("date")}}</th>
                                        <th scope="col">{{$t("name")}}</th>
                                        <th scope="col">{{$t("mobile_number")}}</th>
                                        <th scope="col">{{$t("amount")}}</th>
                                        <th scope="col">{{$t("status")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td> {{ result.created_at }}</td>
                                        <td>{{ result.driver_name }}</td>
                                        <td>{{ result.driver_mobile }}</td>
                                        <td>{{ result.requested_currency }}{{ result.requested_amount }}</td>
                                        <td>
                                            <template v-if="result.payment_status == 'approved'">
                                                <BBadge variant="success" class="text-uppercase">{{$t("approved")}}</BBadge>
                                            </template>
                                            <template v-else-if="result.payment_status == 'requested'">
                                                <BBadge variant="warning" class="text-uppercase">{{$t("requested")}}</BBadge>
                                            </template>
                                            <template v-else>
                                                <BBadge variant="danger" class="text-uppercase">{{$t('declined')}}</BBadge>
                                            </template>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a>
                                                <div v-if="result.payment_status === 'requested'" class="dropdown-menu dropdown-menu-end">
                                                    <a 
                                                        class="dropdown-item" 
                                                        href="#" 
                                                        @click.prevent="updatePaymentStatus(result, 'approved')"
                                                    >
                                                        <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("approve")}}
                                                    </a>
                                                    <a 
                                                        class="dropdown-item" 
                                                        href="#" 
                                                        @click.prevent="updatePaymentStatus(result, 'declined')"
                                                    >
                                                        <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("decline")}}
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
