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

        <Head title="View Details" />
        <PageHeader :title="$t('view_details')" :pageTitle="$t('view_details')" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0"></BCardHeader>
                    <BCardBody>
                        <h5 class="border-bottom p-2 fw-bold"> ORDER ID: REQ_1719468991</h5>
                        <div class="row">  
                            <div class="mt-3"></div>                         
                            <div class="col-sm-8">
                                <h5>{{$t("map_view")}}</h5>
                                <img class="rounded" alt="google_map" src="@assets/images/google-map.jpg"   width="100%" height="450px">
                            </div>

                            <div class="col-sm-4 mt-4">
                                <div class="card border">
                                    <div class="card-header bg-body">
                                        <h5>{{$t("pickup_details")}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <h5>{{$t("location")}}:</h5>
                                            <p>Gala Swins Land Rd Swastik Clairmont Road Bopal Ahmedabad Gujarat India 380058</p>
                                        </div>
                                        <div>
                                            <h5>{{$t("time")}}:</h5>
                                            <p>05:31 PM</p>
                                        </div>                                        
                                    </div>                                                                        
                                </div>
                                <div class="card border">
                                    <div class="card-header bg-body">
                                        <h5>{{$t("drop_details")}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <h5>{{$t("location")}}:</h5>
                                            <p>AAROHI RESIDENCY Bopal Ahmedabad Gujarat India 380058</p>
                                        </div>
                                        <div>
                                            <h5>{{$t("time")}}:</h5>
                                        </div>
                                    </div>                                    
                                </div>                                  
                            </div>
                        </div>

                        <div class="row">
                            <div class="mt-5"></div>
                            <div class="col-sm-4">
                                <div class="card border">
                                    <div class="card-header bg-body">
                                        <h5>{{$t("trip_details")}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <h6>{{$t("vehicle_type")}}:
                                                <b>Bike</b>
                                            </h6>
                                        </div>
                                    </div>                                    
                                </div>                                 
                            </div>                               
                                
                            <div class="col-sm-4">
                                <div class="card border">
                                    <div class="card-header bg-body">
                                        <h5>{{$t("payment_details")}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <h6>{{$t("payment_type")}}:
                                                <b>Cash</b>
                                            </h6>
                                            <h6>{{$t("amount")}}:
                                                <b>-</b>
                                            </h6>
                                        </div>
                                    </div>                                    
                                </div>                                 
                            </div>
                            
                            <div class="col-sm-4">
                                <div class="card border">
                                    <div class="card-header bg-body">
                                        <h5>{{$t("activity_timeline")}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="profile-timeline">
                                            <div class="accordion accordion-flush" id="accordionFlushExample">                                                
                                                <div class="accordion-item border-0">
                                                    <div class="accordion-header" id="headingFour">
                                                        <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseOne" aria-expanded="false">
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 avatar-xs">
                                                                    <div class="avatar-title bg-light text-success rounded-circle">
                                                                        <i class="ri-e-bike-2-fill"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 ms-3">
                                                                    <h6 class="mb-0 fw-semibold">{{$t("request_made_at")}}:</h6>
                                                                    <p class="text-muted mb-0">12th July 5:30 PM</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="accordion-item border-0">
                                                    <div class="accordion-header" id="headingFive">
                                                        <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 avatar-xs">
                                                                    <div class="avatar-title bg-light text-success rounded-circle">
                                                                        <i class="ri-e-bike-2-fill"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 ms-3">
                                                                    <h6 class="mb-0 fw-semibold">{{$t("accepted_at")}}:</h6>
                                                                    <p class="text-muted mb-0">12th July 5:40 PM</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>                                 
                                </div>
                            </div>                                
                        </div>        

                        <div class="row">
                            <div class="mt-3"></div>                             
                            <div class="col-sm-4">
                                <div class="card border">
                                    <div class="card-header bg-body">
                                        <h5>{{$t("customer_details")}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <h6>{{$t("name")}}:
                                                <b>test</b>
                                            </h6>
                                            <h6>{{$t("mobile")}}:
                                                <b>9786451235</b>
                                            </h6>
                                        </div>
                                    </div>                                    
                                </div>                                 
                            </div>
                            <div class="col-sm-4">
                                <div class="card border">
                                    <div class="card-header bg-body">
                                        <h5>{{$t("vehicle_details")}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col ">
                                                <img src="@assets/images/bike.png"  width="130px" height="130px"/>
                                            </div>
                                            <div class="col">
                                                <h6>{{$t("plate_no")}}:
                                                    <b>-</b>
                                                </h6>
                                                <h6>{{$t("color")}}:
                                                    <b>-</b>                                                    
                                                </h6>
                                                <h6>{{$t("type")}}:
                                                    <b>Bike</b>
                                                </h6>
                                                <h6>{{$t("make")}}:
                                                    <b>-</b>
                                                </h6>
                                                <h6>{{$t("model")}}:
                                                    <b>-</b>
                                                </h6>                                                
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>                                 
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

.text-danger {
    padding-top: 5px;
}

</style>
