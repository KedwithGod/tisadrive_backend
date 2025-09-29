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
import { object } from '@amcharts/amcharts5';
import { useI18n } from 'vue-i18n';

export default {
    data() {
        return {
            rightOffcanvas: false,
            amount: '',
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

        user: {
            type: Object,
            required: true,
        },
        currency : Array,
        user_date : object,
        user_wallet : object,
        completed_request : object,
        cancelled_request : object,
        on_going : object,
        app_for:String,



    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");
        const filter = useForm({
            all: "",
            locked: "",
            limit:15
        });
        const form = ref({
        amount: '',
        operation: 'add', // Default to 'add'

        });
        
        const searchTerm2 = ref("");        
        const filter2 = useForm({ all: "", locked: "",limit:15 });
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

    const handleSubmit = async () => {
      validateForm();
      if (!isAmountValid.value) return;

      try {
        let formData = new FormData();
        for (let key in form.value) {
          formData.append(key, form.value[key]);
        }

        let response = await axios.post(`/users/wallet-add-amount/${props.user.id}`, formData);

        if (response.status === 200) {
          props.successMessage = t('amount_adjusted_successfully');
          form.value.amount = '';
          form.value.operation = 'add'; // Reset form operation
          router.push('/users/view-profile');
        } else {
          props.alertMessage = t('failed_to_adjust_amount');
        }
      } catch (error) {
        console.error(t('error_adjusting_amount'), error);
        props.alertMessage =  t('failed_to_adjust_amount');
      }
    };

    watch(() => form.value.amount, validateForm);

        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const requests = ref([]); // Spread the results to make them reactive
        const paginator2 = ref({});
        const paginatorOption = ref({}); // Spread the results to make them reactive


        const user = props.user; // Spread the results to make them reactive
        const currency = props.currency[0]; // Spread the results to make them reactive
        const user_date = props.user_date; // Spread the results to make them reactive
        const user_wallet = props.user_wallet; // Spread the results to make them reactive

        const ratingResults = ref([]);
        const ratingPaginator = ref({});




        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };



        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/users/wallet-history/list/${props.user.id}`,{ params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_list'), error);
            }
        };
        const updatePaginatorOptions = () => {
            paginatorOption.value = [15, 25, 50, 100, 200, 500]; // Default static options
        };
        // **Handle per-page changes**
        const changeEntriesPerPage = () => {
            fetchDatas();
        };
        const changeRequestDataPerPage = () => {
            fetchRequestDatas();
        };

        const fetchRequestDatas = async (page = 1) => {
            try {
                const params = filter2.data();
                params.search = searchTerm2.value;
                params.page = page;
                const response = await axios.get(`/users/request/list/${props.user.id}`,{ params });
                requests.value = response.data.requests;
                paginator2.value = response.data.paginator;
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_list'), error);
            }
        };



        const handlePageChanged = async (page) => {
            fetchDatas(page);
            // fetchRequestDatas(page);
        };
        const handlePageChanged2 = async (page) => {
            fetchRequestDatas(page);
        };

        const fetchRatingDatas = async (page = 1) => {
            try {
                const params = { page: page };
                const response = await axios.get(`/users/rating-list/${props.user.id}`, { params });
                ratingResults.value = response.data.results;
                ratingPaginator.value = response.data.paginator;
            } catch (error) {
                console.error(t('error_fetching_drivers_rating'), error);
            }
        };


        const handleRatingPageChanged = async (page) => {
            fetchRatingDatas(page);
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


        return {
            user,
            user_date,
            user_wallet,
            currency,
            results,
            requests,
            modalShow,
            deleteItemId,
            successMessage,
            alertMessage,
            dismissMessage,
            searchTerm,
            paginator,
            fetchDatas,
            fetchRequestDatas,
            fetchRatingDatas,
            ratingResults,
            ratingPaginator,
            handleRatingPageChanged,
            handlePageChanged,
            form,
            validationMessage,
            isAmountValid,
            handleSubmit,
            validateForm,
            fetchRequestDatas,
            paginator2,
            handlePageChanged2,
            mobileFromUser,
            emailFromUser,
            paginatorOption,
            changeEntriesPerPage,
            filter,
            filter2,
            changeRequestDataPerPage 

        };
    },
    watch: {
    'form.amount': 'validateForm'
  },
    mounted() {
        this.fetchDatas();
        this.fetchRequestDatas();
        this.fetchRatingDatas();

    },
};
</script>

<template>
    <Layout>

        <Head title="User Profile" />
        <PageHeader :title="$t('user_profile')" :pageTitle="$t('user_profile')"  pageLink="/users" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                        <div class="row">
                            <div class="col-sm-6 mt-3 profile-border">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <img class="rounded-circle avatar-xl" alt="200x200" :src="user.profile_picture">
                                        <!-- src="@assets/images/users/avatar-3.jpg"   -->
                                    </div>
                                    <div class="ms-4">
                                        <h5>{{user.name}}</h5> 
                                    </div>
                                 </div>                                
                            </div>
                            <div class="col-sm-6 mt-4">                               
                                <div class=" d-flex align-items-center ">
                                    <i class=" ri-phone-line" style="font-size:20px"></i> &nbsp;&nbsp;
                                    <span>{{mobileFromUser(user)}}</span>
                                </div>                                
                                <div class=" d-flex align-items-center ">
                                    <i class="ri-mail-line" style="font-size:20px"></i> &nbsp;&nbsp;
                                    <span>{{emailFromUser(user)}}</span>
                                </div>  
                                <div class=" d-flex align-items-center ">
                                    <i class="  ri-logout-box-r-line" style="font-size:20px"></i> &nbsp;&nbsp;
                                    <span>{{user_date}}</span>
                                </div>  
                            </div>
                        </div>
                        <div class="border-bottom mt-4"></div>
                        <div>
                            <!-- Nav tabs -->
                                <ul class="nav nav-tabs  mt-4" role="tablist">                                    
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#request-list" role="tab" aria-selected="false">
                                            {{ $t("request_list") }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#payment-history" role="tab" aria-selected="false">
                                           {{$t("user_payment_history")}}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#rating-history" role="tab" aria-selected="false">
                                            {{$t("review_history")}}
                                        </a>
                                    </li>
                                </ul>
                        </div>

                    </BCardHeader>
                </BCard>
                        <!-- Tab panes -->
                        <div class="tab-content  text-muted">  
                            <div class="tab-pane  active" id="request-list" role="tabpanel">                                                                   
                                <BCard>
                                    <BCardBody> 
                                        <div class="row  row-cols-lg-3 row-cols-1">
                                            <div class="col">
                                                <div class="card card-body border card-hover">
                                                    <div class="d-flex mb-4 align-items-center">
                                                        <div>
                                                            <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                            <!-- <i class="bx bxs-flag-checkered" style="font-size: 30px;color:#3160d8"></i> -->
                                                            <div class="avatar-sm flex-shrink-0">
                                                                <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                    <i class=" bx bx-car text-success icon-lg"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <h5 class="card-text text-muted">{{ $t("completed_rides") }}</h5>   
                                                        </div>
                                                        <div class="col">
                                                            <h3 class="text-end">{{ completed_request }}</h3>   
                                                        </div>                                                                    
                                                    </div>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col">
                                                <div class="card card-body border card-hover">
                                                    <div class="d-flex mb-4 align-items-center">
                                                        <div>
                                                            <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                            <!-- <i class="las la-ban" style="font-size: 30px;color:#3160d8"></i> -->
                                                            <div class="avatar-sm flex-shrink-0">
                                                                <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                                                    <i class=" bx bx-car text-danger icon-lg"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <h5 class="card-text text-muted">{{$t("cancelled_rides")}}</h5>   
                                                        </div>
                                                        <div class="col">
                                                            <h3 class="text-end">{{ cancelled_request }}</h3>   
                                                        </div>                                                                    
                                                    </div>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col">
                                                <div class="card card-body border card-hover">
                                                    <div class="d-flex mb-4 align-items-center">
                                                        <div>
                                                            <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                            <!-- <i class=" bx bx-calendar" style="font-size: 30px;color:#3160d8"></i> -->
                                                            <div class="avatar-sm flex-shrink-0">
                                                                <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                                                    <i class=" bx bx-car text-primary icon-lg"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <h5 class="card-text text-muted">{{ $t("upcoming_rides") }}</h5>   
                                                        </div>
                                                        <div class="col">
                                                            <h3 class="text-end">{{ on_going }}</h3>   
                                                        </div>                                                                    
                                                    </div>
                                                </div>
                                            </div><!-- end col -->
                                                                                                        
                                        </div><!-- end row -->
                                        <BCol md="3">
                                            <div class="d-flex align-items-center mt-3">
                                                <label class="me-2 text-muted">{{$t("show")}}</label>
                                                <select v-model="filter2.limit" @change="changeRequestDataPerPage" class="form-select form-select-sm w-auto">
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
                                                        <!-- <th scope="col">{{$t("s_no")}}</th> -->
                                                        <th scope="col">{{ $t("request_id") }}</th>
                                                        <th scope="col">{{ $t("date") }}</th>
                                                        <th scope="col">{{ $t("user_name") }}</th>
                                                        <th scope="col">{{ $t("driver_name") }}</th>
                                                        <th scope="col">{{ $t("trip_Status") }}</th>
                                                        <th scope="col">{{ $t("paid") }}</th>
                                                        <th scope="col">{{$t("payment_option")}}</th>
                                                        <!-- <th scope="col">{{ $t("action") }}</th> -->
                                                    </tr>
                                                </thead>
                                                <tbody v-if="requests.length > 0">
                                                    <tr v-for="(request, index) in requests" :key="index">
                                                        <!-- <td>{{ index+1 }}</td> -->
                                                        <td>{{ request.request_number }}</td>
                                                        <td>{{ request.converted_trip_start_time_date }}</td>
                                                        <td>{{ request.user_name }}</td>
                                                        <td>{{ request.driver_name }}</td>
                                                        <td>{{ request.trip_status }}</td>
                                                        <td>{{ request.trip_payment }}</td>
                                                        <!-- <td>Cash</td>                                       -->
                                                        <td>
                                                            <BBadge :class="{
                                                                'text-uppercase':true,
                                                                'text-bg-success': request.is_paid,
                                                                'text-bg-danger': !request.is_paid,
                                                                }">{{ request.payment_opt == 1 ? 'Cash' : (request.payment_opt == 2 ? 'Wallet' : 'Card') }} </BBadge>
                                                        </td>  
                                                        <!-- <td>
                                                            <div class="dropdown">
                                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <a class="dropdown-item" href="#" @click.prevent="viewData(result)"><i class="bx bxs-edit-alt align-center text-muted me-2">
                                                                    </i>{{ $t("view") }}</a>
                                                                </div>
                                                            </div>
                                                        </td> -->
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
                                            <Pagination :paginator="paginator2" @page-changed="handlePageChanged2" />
                                        </div>                                                          
                                    </BCardBody>
                                </BCard>
                            </div> 
                            <div class="tab-pane " id="payment-history" role="tabpanel">                                
                                <BCard>
                                    <BCardBody>
                                        <div class="row  row-cols-lg-3 row-cols-1">
                                            <div class="col">
                                                <div class="card card-body border card-hover">
                                                    <div class="d-flex mb-4 align-items-center">
                                                        <div>
                                                            <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
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
                                                        <h3 class="mb-1">{{ currency }} {{user_wallet.amount_added ?? 0}}</h3>
                                                    </div>                                                                
                                                    <h5 class="card-text text-muted">{{ $t("total_amount") }}</h5>                                                                
                                                    <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col">
                                                <div class="card card-body border card-hover">
                                                    <div class="d-flex mb-4 align-items-center">
                                                        <div>
                                                            <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                            <!-- <i class="las la-ban" style="font-size: 25px; color:#3160d8"></i> -->
                                                            <div class="avatar-sm flex-shrink-0">
                                                                <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                                                    <i class="bx bx-money text-danger icon-lg"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex">
                                                        <h3 class="mb-1">{{ currency }} {{user_wallet.amount_spent ?? 0}}</h3>
                                                    </div>
                                                    <h5 class="card-text text-muted">{{ $t("spend_amount") }}</h5>                                                                
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
                                                        <h3 class="mb-1">{{ currency }} {{user_wallet.amount_balance ?? 0}}</h3>
                                                    </div>
                                                    <h5 class="card-text text-muted">{{ $t("balance_amount") }}</h5>                                                                
                                                    <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                </div>
                                            </div><!-- end col -->                                                         
                                        </div><!-- end row -->                                    
                                    </BCardBody>
                                </BCard>
                                <BCard class="border p-3">
                                    <BCardHeader>
                                        <h5 class="card-title mb-0">{{$t("credit_or_debit_wallet")}}</h5>
                                    </BCardHeader>
                                    <BCardBody>
                                        <form @submit.prevent="handleSubmit">
                                            <div class="row p-3">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                <label for="amount" class="form-label">{{$t("amount")}}</label>
                                                <input 
                                                    type="number" 
                                                    class="form-control" 
                                                    :placeholder="$t('enter_amount')" 
                                                    id="amount" 
                                                    v-model="form.amount" 
                                                    step="0.01"
                                                    min="1"     
                                                />
                                                <div v-if="validationMessage" class="text-danger">{{ validationMessage }}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                <label for="operation" class="form-label">{{$t("operation")}}</label>
                                                <select v-model="form.operation" class="form-control" id="operation">
                                                    <option value="add">{{ $t("credit") }}</option>
                                                    <option value="subtract">{{ $t("debit") }}</option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                <button type="submit" class="btn btn-primary" :disabled="!isAmountValid">{{ $t("submit") }}</button>
                                                </div>
                                            </div>
                                            </div>
                                        </form>
                                    </BCardBody>
                                </BCard>
                                <BCard>
                                    <BCardHeader>
                                        <h5 class="card-title mb-0">{{$t("payment_history")}}</h5>
                                    </BCardHeader>
                                    <BCardBody>
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
                                        <div class="table-responsive mt-5">
                                            <table class="table align-middle position-relative table-nowrap">
                                                <thead class="table-active">
                                                    <tr>
                                                        <!-- <th scope="col">{{ $t("s_no") }}</th> -->
                                                        <th scope="col">{{$t("date")}}</th>
                                                        <th scope="col">{{$t("user_name")}}</th>
                                                        <th scope="col">{{$t("amount")}}</th>
                                                        <th scope="col">{{$t("payment_option")}}</th>
                                                        <th scope="col">{{$t("status")}}</th>

                                                    </tr>
                                                </thead>
                                                <tbody v-if="results.length > 0">
                                                    <tr v-for="(result, index) in results" :key="index">
                                                        <!-- <td>{{ index+1 }}</td> -->
                                                        <td>{{ result.created_at }}</td>
                                                        <td>{{ user.name }}</td>
                                                        <td>{{ currency }}{{ result.amount }}</td>
                                                        <td>{{ result.remarks }} </td>
                                                        <td>
                                                            <button v-if="result.is_credit == 1" class="btn btn-success">{{$t("credited")}}</button>
                                                            <button v-else class="btn btn-danger">{{$t("debited")}}</button>
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
                                            <Pagination :paginator="paginator" @page-changed="handlePageChanged" />
                                        </div>
                                    </BCardBody>
                                </BCard>                      
                            </div>

                            <div class="tab-pane  p-3" id="rating-history" role="tabpanel">
                                <BCard>
                                    <BCardBody>

                                        <div class="timeline-continue p-2" v-for="result in ratingResults" :key="result.id">
                                        <div class="row timeline-right">
                                            <div class="col-12">
                                            <p class="timeline-date">{{ result.converted_created_at }}</p>
                                            </div>
                                            <div class="col-12">
                                            <div class="timeline-box">
                                                <div class="timeline-text">
                                                <div class="d-flex">
                                                    <img v-if="result.user_profile" class="rounded-circle avatar-sm" alt="User Avatar" :src="result.user_profile">
                                                    <img v-else class="rounded-circle avatar-sm" alt="User Avatar" src="@assets/images/users/avatar-3.jpg">
                                                    <div class="flex-grow-1 ms-3">
                                                    <h5 class="mb-1">
                                                        <Link href="#">{{ result.request_number }}</Link>{{ result.driver_name}}
                                                    </h5>
                                                    <p>{{ result.converted_trip_start_time }}</p>
                                                    <p>{{$t("pickup_address")}}
                                                        <span class="text-muted mb-0">
                                                        {{ result.pick_address }}
                                                        </span>
                                                    </p>
                                                    <div>
                                                        <!-- Display Star Rating -->
                                                        <i v-for="n in 5" :key="n"
                                                            :class="{
                                                            'bx bxs-star': n <= result.user_rating,
                                                            'bx bx-star': n > result.user_rating
                                                            }"
                                                            class="align-center text-muted me-2"></i>
                                                    </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                        <Pagination :paginator="ratingPaginator" @page-changed="handleRatingPageChanged" />
                                    </BCardBody>
                                </BCard>
                            </div>

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
        <!-- <Pagination :paginator="paginator" @page-changed="handlePageChanged" /> -->
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
.ltr .profile-border{
    border-right:1px solid #e9ebec;
}
.rtl .profile-border{
    border-left:1px solid #e9ebec;
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
/* .text-danger {
    padding-top: 5px;
} */
.card-hover:hover{
    box-shadow: 0 5px 15px;
    transition: box-shadow 0.3s ease-in-out;
}
@media only screen and (max-width: 426px) {
    .profile-border{
        border-right:0px;
    }
}

</style>
