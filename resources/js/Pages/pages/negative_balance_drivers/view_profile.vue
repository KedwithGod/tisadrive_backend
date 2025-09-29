<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import getChartColorsArray from "@/common/getChartColorsArray";
import { useI18n } from 'vue-i18n';

export default {
    data() {    
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
        getChartColorsArray,
    },
    props: {
        successMessage: String,
        alertMessage: String,
        driver: Object,
        currency: String,
        app_for:String,
        driver_date: Object,
        driver_wallet: Object,
    },
    setup(props) {
        const { t } = useI18n();
        const selectedServiceLocations = ref([]);
        const selectedVehicleTypes = ref([]);

        const searchTerm1 = ref("");
        const searchTerm2 = ref("");
        const filter1 = useForm({ all: "", locked: "", limit: 10 });
        const successMessage = ref("");
        const alertMessage = ref("");
        const errors = ref({});

// console.log(props.completed_ride_count);

        const form = ref({
            amount: '',
            operation: 'add', // Default to 'add'
        });
        const validationMessage = ref('');
        const isAmountValid = ref(false);

        const validateForm = () => {
            if (!form.value.amount) {
                isAmountValid.value = false;
            } else {
                validationMessage.value = '';
                isAmountValid.value = true;
            }
        };

        // const handleSubmit = async () => {
        //     validateForm();
        //     if (!isAmountValid.value) return;

        //     try {
        //         let formData = new FormData();
        //         for (let key in form.value) {
        //             formData.append(key, form.value[key]);
        //         }

        //         let response = await axios.post(`/approved-drivers/wallet-add-amount/${props.driver.id}`, formData);

        //         if (response.status === 200) {
        //             props.successMessage = t('amount_adjusted_successfully');
        //             form.value.amount = '';
        //             form.value.operation = 'add'; // Reset form operation
        //             router.push('/negative-balance-drivers/view-profile');
        //         } 
        //         else {
        //             props.alertMessage = t('failed_to_adjust_amount');
        //         }
        //     } catch (error) {
        //         console.error(t('error_adjusting_amount'), error);
        //         props.alertMessage = t('failed_to_adjust_amount');
        //     }
        // };

        const handleSubmit = async () => {
            validateForm();
            if (!isAmountValid.value) return;

            try {
                let formData = new FormData();
                for (let key in form.value) {
                    formData.append(key, form.value[key]);
                }

                let response = await axios.post(`/approved-drivers/wallet-add-amount/${props.driver.id}`, formData);

                if (response.status === 200) {
                    successMessage.value = t('amount_adjusted_successfully');
                    form.value.amount = '';
                    form.value.operation = 'add'; // Reset form operation
                    router.get(`/negative-balance-drivers/view-profile/${props.driver.id}`);
                } else {
                    alertMessage.value = t('failed_to_adjust_amount');
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    errors.value = error.response.data.errors;
                } else {
                    console.error(t('error_adjusting_amount'), error);
                    alertMessage.value = t('failed_to_adjust_amount');
                }
            }
        };

        onMounted(() => {
        });
        watch(() => form.value.amount, validateForm);

        const results1 = ref([]);
        const paginator1 = ref({});
        const results2 = ref([]);
        const paginator2 = ref({});
        const paginatorOption = ref({}); // Spread the results to make them reactive

        const fetchDatas1 = async (page = 1) => {
            try {
                const params = filter1.data();
                params.search = searchTerm1.value;
                params.page = page;
                const response = await axios.get(`/approved-drivers/wallet-history/list/${props.driver.id}`,{ params });
                results1.value = response.data.results;
                paginator1.value = response.data.paginator;
                updatePaginatorOptions(paginator1.value.total);// Update paginator options dynamically
            } catch (error) {
                console.error(t('error_fetching_first_list_of_data'), error);
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
        const handlePageChanged1 = async (page) => {
            fetchDatas1(page);
        };
        const updatePaginatorOptions = () => {
            paginatorOption.value = [10, 25, 50, 100,200,500]; // Default static options
        };
        // **Handle per-page changes**
        const changeEntriesPerPage = () => {
            fetchDatas1(); // Fetch new data
        };
        return {
            form,
            validationMessage,
            isAmountValid,
            handleSubmit,
            searchTerm1,
            results1,
            mobileFromUser,
            emailFromUser,
            paginator1,
            fetchDatas1,
            handlePageChanged1,
            successMessage,
            alertMessage,
            errors,
            paginatorOption,
            changeEntriesPerPage,
            filter1

        };
    },
    mounted() {
        this.fetchDatas1();
    },
};
</script>


<template>
    <Layout>

        <Head title="Driver Profile" />
        <PageHeader :title="$t('driver_profile')" :pageTitle="$t('driver_profile')" pageLink="/negative-balance-drivers"/>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                        <div class="row">
                            <div class="col-sm-4 mt-3 profile-border">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <img class="rounded-circle avatar-xl" alt="200x200" :src="driver.profile_picture">
                                    </div>
                                    <div class="ms-2">
                                        <h5>{{driver.name}}</h5> 
                                    </div>
                                 </div>                                
                            </div>
                            <div class="col-sm-3 mt-4 profile-border">                               
                                <div class=" d-flex align-items-center ">
                                    <i class=" ri-phone-line" style="font-size:20px"></i> &nbsp;&nbsp;
                                    <span>{{mobileFromUser(driver)}}</span>
                                </div>                                
                                <div class=" d-flex align-items-center ">
                                    <i class="ri-mail-line" style="font-size:20px"></i> &nbsp;&nbsp;
                                    <span>{{emailFromUser(driver)}}</span>
                                </div>  
                                <div class=" d-flex align-items-center ">
                                    <i class="  ri-logout-box-r-line" style="font-size:20px"></i> &nbsp;&nbsp;
                                    <span>{{driver_date}}</span>
                                </div>  
                            </div>
                            <div class="col-sm-5 mt-3 ">
                                <div class="d-flex align-items-center ">
                                    <div>
                                        <img class="rounded-circle avatar-xl" alt="200x200" :src="driver.vehicle_type_image">

                                    </div>
                                    <div class="ms-2">
                                        <h5>{{driver.vehicle_type_name}}</h5> 
                                        <p>{{driver.car_make_name}}</p>
                                        <p>{{driver.car_model_name}}</p>
                                        <p>{{driver.car_number}}</p>
                                    </div>
                                 </div>                                
                            </div>
                        </div>
                        <div class="border-bottom mt-4"></div>
                        <div>
                            <!-- Nav tabs -->
                                <ul class="nav nav-tabs  mt-4" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#payment-history" role="tab" aria-selected="false">
                                            {{$t("payment_history")}}
                                        </a>
                                    </li>
                                </ul>
                        </div>

                    </BCardHeader>
                </BCard>
                        <!-- Tab panes -->                        
                <div class="tab-pane p-3" id="payment-history" role="tabpanel">                                        
                    <BCard>
                        <BCardBody>
                            <div class="row  row-cols-lg-3 row-cols-1">
                                <div class="col">
                                    <div class="card card-body border card-hover">
                                        <div class="d-flex mb-4 align-items-center">
                                            <div>
                                                <!-- <img src="@assets/images/drivers/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                <!-- <i class="bx bxs-flag-checkered" style="font-size: 25px;color:#3160d8"></i> -->
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                        <i class="bx bx-money text-success icon-lg"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                            <h3 class="mb-1">{{ currency[0] }} {{driver_wallet.amount_added ?? 0}}</h3>
                                        </div>                                                                
                                        <h5 class="card-text text-muted">{{$t("total_amount")}}</h5>                                                                
                                        <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                    </div>
                                </div><!-- end col -->
                                <div class="col">
                                    <div class="card card-body border card-hover">
                                        <div class="d-flex mb-4 align-items-center">
                                            <div>
                                                <!-- <img src="@assets/images/drivers/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                <!-- <i class="las la-ban" style="font-size: 25px; color:#3160d8"></i> -->
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                                        <i class="bx bx-money text-danger icon-lg"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <h3 class="mb-1">{{ currency[0] }} {{driver_wallet.amount_spent ?? 0}}</h3>
                                        </div>
                                        <h5 class="card-text text-muted">{{$t("spend_amount")}}</h5>                                                                
                                        <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                    </div>
                                </div><!-- end col -->
                                <div class="col">
                                    <div class="card card-body border card-hover">
                                        <div class="d-flex mb-4 align-items-center">
                                            <div>
                                                <!-- <i class="las la-ban" style="font-size: 25px;color:#3160d8"></i> -->
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                                        <i class="bx bx-money text-primary icon-lg"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <h3 class="mb-1">{{ currency[0] }} {{driver_wallet.amount_balance ?? 0}}</h3>
                                        </div>
                                        <h5 class="card-text text-muted">{{$t("balance_amount")}}</h5>                                                                
                                        <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                    </div>
                                </div><!-- end col -->                                                         
                            </div><!-- end row -->
                        </BCardBody>
                    </BCard>

                    <BCard>
                        <BCardBody>
                            <form @submit.prevent="handleSubmit">
                                <div class="row p-3">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">{{$t("amount")}}</label>
                                            <input 
                                            type="text" 
                                            class="form-control" 
                                            :placeholder="$t('enter_amount')" 
                                            id="amount" 
                                            v-model="form.amount"
                                            />
                                            <div v-if="validationMessage" class="text-danger">{{ validationMessage }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="operation" class="form-label">{{$t("operation")}}</label>
                                            <select v-model="form.operation" class="form-control" id="operation">
                                                <option value="add">{{$t("add")}}</option>
                                                <option value="subtract">{{$t("subtract")}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary" :disabled="!isAmountValid">{{$t("submit")}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>                                                         
                            <!-- </div> -->
                            <BCol md="3">
                                <div class="d-flex align-items-center mt-3">
                                    <label class="me-2 text-muted">{{$t("show")}}</label>
                                    <select v-model="filter1.limit" @change="changeEntriesPerPage" class="form-select form-select-sm w-auto">
                                    <option v-for="option in paginatorOption" :key="option" :value="option">
                                        {{ option }}
                                    </option>
                                    </select>
                                    <label class="ms-2 text-muted">{{$t("entries")}}</label>
                                </div>
                            </BCol>
                            <div class="table-responsive mt-5">
                                <table class="table align-middle position-relative table-nowrap">
                                    <thead class="table-active">
                                        <tr>
                                            <th scope="col">{{$t("date")}}</th>
                                            <th scope="col">{{$t("amount")}}</th>
                                            <th scope="col">{{$t("driver_name")}}</th>
                                            <th scope="col">{{$t("remarks")}}</th>
                                            <th scope="col">{{$t("status")}}</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="results1.length > 0">
                                        <tr v-for="(result, index) in results1" :key="index">
                                            <td>{{ result.created_at }}</td>
                                            <td>{{ currency[0] }}{{ result.amount ?? '-' }}</td>
                                            <td>{{ driver.name }}</td>
                                            <td>{{ result.remarks }} </td>
                                            <td>
                                                <template v-if="result.is_credit == 1">
                                                    <BBadge variant="success" class="text-uppercase">{{$t("credited")}}</BBadge>
                                                </template>
                                                <template v-else>
                                                    <BBadge variant="danger" class="text-uppercase">{{$t("debited")}}</BBadge>
                                                </template>
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
                                <Pagination :paginator="paginator1" @page-changed="handlePageChanged1" />
                            </div>
                        </BCardBody>
                    </BCard>                                                    
                </div>
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
        <!-- <Pagination :paginator="paginator1" @page-changed="handlePageChanged" /> -->
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
.card-hover:hover{
    box-shadow: 0 5px 15px;
    transition: box-shadow 0.3s ease-in-out;
}
.ltr .profile-border{
    border-right:1px solid #e9ebec;
}
.rtl .profile-border{
    border-left:1px solid #e9ebec;
}

@media only screen and (max-width: 426px) {
    .profile-border{
        border-right:0px;
    }
}

</style>