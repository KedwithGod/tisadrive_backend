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

        serviceLocations: {
            type: Object,
            required: true,
        }


    },
    setup(props) {
        const searchTerm = ref("");
        const filter = useForm({
            all: "",
            locked: "",
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);

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
                await axios.delete(`/service-locations/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire('Success', 'Service Location deleted successfully', 'success');
            } catch (error) {
                Swal.fire('Error', 'Failed to delete Service Location', 'error');
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
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/service-locations/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                modalFilter.value = false;
            } catch (error) {
                console.error("Error fetching Service-locations:", error);
            }
        };

        const handlePageChanged = async (page) => {
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/service-locations/edit/${result.id}`); 
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
            editData
        };
    },
    mounted() {
        this.fetchDatas();
    },
};
</script>

<template>
    <Layout>

        <Head title="Owner Payment History" />
        <PageHeader title="Owner Payment History" pageTitle="Owner Payment History" pageLink="/manage-owners"/>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                      <div class="row mt-5">
                        <div class="col-sm-4">
                          <div class="card border">
                            <div class="card-header border-0">
                              <h5>Total Amount</h5> 
                            </div>
                            <div class="card-body ">
                              <div class="row">
                                <div class="col">
                                  <i class="bx bxs-flag-checkered" style="font-size: 30px;color:#3160d8"></i>
                                </div>
                                <div class="col">
                                  <h4 class="text-end">0</h4>                        
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="card border">
                            <div class="card-header border-0">
                              <h5>Spend Amount</h5> 
                            </div>
                            <div class="card-body ">
                              <div class="row">
                                <div class="col">
                                  <i class="las la-ban" style="font-size: 30px;color:#3160d8"></i>
                                </div>
                                <div class="col">
                                  <h4 class="text-end">0</h4>                        
                                </div>
                              </div>
                            </div>                  
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="card border">
                            <div class="card-header border-0">
                              <h5>Balance Amount</h5> 
                            </div>
                            <div class="card-body ">
                              <div class="row">
                                <div class="col">
                                  <i class="las la-ban" style="font-size: 30px;color:#3160d8"></i>
                                </div>
                                <div class="col">
                                  <h4 class="text-end">0</h4>                        
                                </div>
                              </div>
                            </div>                  
                          </div>
                        </div>
                      </div>
                    </BCardHeader>
                    <BCardBody>
                      <div class="card p-3 border mt-5">
                        <form  @submit.prevent="handleSubmit"  class="form-steps">
                          <!-- <FormValidation :form="form" :rules="validationRules" ref="validationRef">   -->
                            <div class="row">
                              <div class="col-sm-6">
                                <div class="mb-3">
                                  <label for="amount" class="form-label">Amount</label>
                                  <input type="text" class="form-control" placeholder="Enter Amount" id="amount"/>
                                  <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                                </div>
                              </div> 
                                <div class="col-lg-12">
                                  <div class="text-end">
                                    <button type="submit" class="btn btn-primary"> {{ serviceLocation ? 'Update' : 'Save' }}</button>
                                  </div>
                                </div>
                            </div>  
                            <!-- </FormValidation>  -->
                          </form>
                      </div>

                      <div class="card p-3 border mt-5">
                              <h5>Wallet History</h5>
                              <div class="table-responsive mt-3">
                                <table class="table align-middle position-relative table-nowrap">
                                  <thead class="table-active">
                                    <tr>
                                      <th scope="col">S.NO</th>
                                      <th scope="col">Request Id</th>
                                      <th scope="col">Owner Name</th>
                                      <th scope="col">Transaction Id</th>
                                      <th scope="col">Amount</th>
                                      <th scope="col">Remarks</th>
                                      <th scope="col">Date</th>
                                    </tr>
                                  </thead>
                                  <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                      <td>1</td>
                                      <td>12456</td>
                                      <td>test</td>  
                                      <td>45785</td>
                                      <td>1000</td>
                                      <td>-</td>
                                      <td>9/7/2024</td>
                                      
                                    </tr>
                                  </tbody>
                                </table>
                              </div>                
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
