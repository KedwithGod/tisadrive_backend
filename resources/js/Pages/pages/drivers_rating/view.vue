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
        Multiselect,
        flatPickr,
        Link,
        search,
        searchbar,
        Pagination,

    },
    props: {
        successMessage: String,
        alertMessage: String,
        app_for:String,
        driver: {
            type: Object,
            required: true,
        },

    },
    setup(props) {
        const { t } = useI18n();
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

        const driver = props.driver; // Spread the results to make them reactive



        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };



        watch(searchTerm, debounce(function (value) {
            fetchDatas(searchTerm.value);

        }, 300));

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;

                const response = await axios.get(`/drivers-rating/request-list/${props.driver.id}`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_drivers_rating'), error);
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

        const handlePageChanged = (page) => {
            fetchDatas(page);
        };


        return {
            driver,
            results,
            modalShow,
            deleteItemId,
            successMessage,
            alertMessage,
            dismissMessage,
            searchTerm,
            paginator,
            mobileFromUser,
            emailFromUser,
            fetchDatas,
            handlePageChanged,
        };
    },
    mounted() {
        this.fetchDatas();
    },
};
</script>

<template>
    <Layout>

        <Head title="View Rating" />
        <PageHeader :title="$t('view_rating')" :pageTitle="$t('view_rating')" pageLink="/drivers-rating"/>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardBody >
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center mt-2 ">
                                    <div >
                                        <img class="rounded-circle avatar-xl" alt="200x200" :src="driver.profile_picture">
                                    </div>
                                    <div class="ms-4">
                                        <h5>{{driver.name}}</h5> 
                                        <div>{{ mobileFromUser(driver) }}</div>
                                        <div>{{ emailFromUser(driver) }}</div>
                                        <div>
                                            <i v-for="n in 5" :key="n"
                                                :class="{
                                                'bx bxs-star': n <= Math.floor(driver.rating),
                                                'bx bxs-star-half': n === Math.ceil(driver.rating) && driver.rating % 1 !== 0,
                                                'bx bx-star': n > driver.rating
                                                }"
                                                class="align-center text-muted me-2"></i>
                                        </div>
                                    </div>                                   
                                </div>                                
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center mt-2">
                                    <div>
                                        <img class="rounded-circle avatar-xl" alt="200x200" :src="driver.vehicle_type_image">
                                    </div>
                                    <div class="ms-4">
                                        <h5>{{driver.vehicle_type_name}}</h5> 
                                        <div>{{driver.car_make_name}}</div>
                                        <div>{{driver.car_model_name}}</div>
                                    </div>                                   
                                </div>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <BCard no-body id="tasksList">
                    <BCardBody >
                        <div class="row mt-4">
                        <div class="col-lg-12">
                            <div>
                                <div class="timeline-2">
                                    <div class="timeline-year">
                                        <p>{{$t("driver_rating")}}</p>
                                    </div>
                                        <!-- Time Line Loop -->
                                        <div class="timeline-continue" v-for="result in results" :key="result.id">
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
                                                        <Link href="#">{{ result.request_number }}</Link>{{ result.user_name}}
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
                                        <!-- End Time Line Loop -->
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
.timeline-2::after {
    background: #405189;
    height: 91%;
}
.timeline-2 .timeline-year p{
    background:  #405189;
    color:white;
}
.rtl .timeline-2::after {
    background: #405189;
    height: 91%;
}
.rtl .timeline-2 .timeline-year p{
    background:  #405189;
    color:white;
}
</style>
