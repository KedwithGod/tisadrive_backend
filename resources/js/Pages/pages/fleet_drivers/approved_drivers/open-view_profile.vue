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
import { BCard, BCardBody } from 'bootstrap-vue-next';
import L from "leaflet";
import 'leaflet-routing-machine';
import "leaflet/dist/leaflet.css";

export default {
 
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
        currency: Array,
        driver_date: String,
        completed_ride_count: Number,
        default_lat:String,
        default_lng:String,
        canceled_ride_count: Number,
        app_for:String,
        acceptance_rate: Number,
        cancellation_rate: Number,
        earnings_data: Object,
        trip_data: Object,
        firebaseSettings:Object,
        earningsChartData:Object,
        disable_options:String,

        tripsChartData: {
            type: Object,
            default: () => ({
                months: [],
                completed: [],
                cancelled: [],
            }),
        },


    },
    setup(props) {
        const map = ref(null);
        const { t } = useI18n();
        const selectedServiceLocations = ref([]);
        const selectedVehicleTypes = ref([]);

        const driverMarker = ref(null);
        const currentLat = ref(parseFloat(props.default_lat));
        const currentLng = ref(parseFloat(props.default_lng));
        // Calculate the maximum value from the earnings data
        const maxValue = Math.max(...(props.earningsChartData.values || []));
        
        const disable_options = ref([props.disable_options]);

        // Set a margin by multiplying with a factor (e.g., 1.2) and round to 2 decimal places
        const maxYValue = (maxValue * 1.2).toFixed(2);
    // Earning Chart Data and Options
    const earning = ref([
      {
        name: t('earnings'),
        data: props.earningsChartData.values || [],
      },
    ]);

    const earningOptions = ref({
      chart: {
        height: 100,
        type: "area",
        toolbar: "false",
      },
      dataLabels: {
        enabled: false,
      },
      stroke: {
        curve: "smooth",
        width: 3,
      },
      xaxis: {
        categories: props.earningsChartData.months || [],
      },
      yaxis: {
        labels: {
          formatter: function (value) {
             return value.toFixed(2);
          },
        },
        tickAmount: 5,
        min: 0,
        max: Number(maxYValue), // Use the computed max value
      },
      colors: getChartColorsArray('[ "--vz-success"]'),
      fill: {
        opacity: 0.5,
        colors: ["#0AB39C", "#F06548"],
        type: "solid",
      },
    });

    
    
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

    
    
    const series = ref([
            {
                name: t('completed'),
                type: "bar",
                data: props.tripsChartData.completed || [],
            },
            {
                name: t('cancelled'),
                type: "bar",
                data: props.tripsChartData.cancelled || [],
            },
        ]);

        const tripOptions = ref({
            chart: {
                height: 374,
                type: "line",
                toolbar: {
                    show: false,
                },
            },
            stroke: {
                curve: "smooth",
                dashArray: [0, 3, 0],
                width: [0, 1, 0],
            },
            fill: {
                opacity: [1, 1, 1],
            },
            markers: {
                size: [0, 4, 0],
                strokeWidth: 2,
                hover: {
                    size: 4,
                },
            },
            xaxis: {
                categories: props.tripsChartData.months || [],
                axisTicks: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
            },
            grid: {
                show: true,
                xaxis: {
                    lines: {
                        show: true,
                    },
                },
                yaxis: {
                    lines: {
                        show: false,
                    },
                },
                padding: {
                    top: 0,
                    right: -2,
                    bottom: 15,
                    left: 10,
                },
            },
            legend: {
                show: true,
                horizontalAlign: "center",
                offsetX: 0,
                offsetY: -5,
                markers: {
                    width: 9,
                    height: 9,
                    radius: 6,
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 0,
                },
            },
            plotOptions: {
                bar: {
                    columnWidth: "30%",
                    barHeight: "70%",
                },
            },
            colors: getChartColorsArray('["--vz-success", "--vz-danger"]'),
        });







 const initializeMap = async () => {
        if (map.value) {
            map.value.remove(); // Removes the map instance and unbinds all event listeners
            map.value = null; // Set map to null for a fresh initialization
        }

        map.value = L.map('map',{
            dragging: false,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            touchZoom: false
        }).setView([currentLat.value, currentLng.value], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map.value);
};


        const searchTerm1 = ref("");
        const searchTerm2 = ref("");
        const filter1 = useForm({ all: "", locked: "" });
        const filter2 = useForm({ all: "", locked: "", limit: 10 });
        const paginatorOption = ref({}); // Spread the results to make them reactive

        const ride_count = props.completed_ride_count;

        const cancel_ride_count = props.canceled_ride_count;


// console.log(props.completed_ride_count);

        const form = ref({
            amount: '',
            operation: 'add', // Default to 'add'
        });
        const validationMessage = ref('');

        const animateDriverMovement = (marker, currentLatLng) => {
            marker.setLatLng(currentLatLng).update();
        };


        const decodeGeohash = (geohash) => {
            const BASE32 = '0123456789bcdefghjkmnpqrstuvwxyz';
            const BITS = [16, 8, 4, 2, 1];
            let isEven = true;
            let latMin = -90, latMax = 90;
            let lonMin = -180, lonMax = 180;
            let lat, lon;

            if (geohash) {
                for (let i = 0; i < geohash.length; i++) {
                    let c = geohash.charAt(i);
                    let cd = BASE32.indexOf(c);
                    for (let j = 0; j < 5; j++) {
                        let mask = BITS[j];
                        if (isEven) {
                            let lonMid = (lonMin + lonMax) / 2;
                            if (cd & mask) {
                                lonMin = lonMid;
                            } else {
                                lonMax = lonMid;
                            }
                        } else {
                            let latMid = (latMin + latMax) / 2;
                            if (cd & mask) {
                                latMin = latMid;
                            } else {
                                latMax = latMid;
                            }
                        }
                        isEven = !isEven;
                    }
                }
                lat = (latMin + latMax) / 2;
                lon = (lonMin + lonMax) / 2;
                return { lat, lon };
            }
            return null;
        };

        onMounted(async() => {
          
            await initializeMap();
            var firebaseConfig = {
                apiKey: props.firebaseSettings['firebase_api_key'],
                authDomain: props.firebaseSettings['firebase_auth_domain'],
                databaseURL: props.firebaseSettings['firebase_database_url'],
                projectId: props.firebaseSettings['firebase_project_id'],
                storageBucket:  props.firebaseSettings['firebase_storage_bucket'],
                messagingSenderId: props.firebaseSettings['firebase_messaging_sender_id'],
                appId: props.firebaseSettings['firebase_app_id'],
                };
                if(firebase.apps.length == 0){
                    firebase.initializeApp(firebaseConfig);
                }
        const driversRef = firebase.database().ref('drivers/driver_' + props.driver.id);

            driversRef.on('value', (snapshot) => {
                    const driver = snapshot.val();
                    const driverLocation = decodeGeohash(driver.g);

                    if (driverLocation) {
                        const driverLatLng = [driverLocation.lat, driverLocation.lon];
                        currentLat.value = driverLocation.lat;
                        currentLng.value = driverLocation.lon;

                        let vehicleTypeIconUrl = driver.vehicle_type_icon ?  `/image/map/${driver.vehicle_type_icon}.png` : "";
                        if (vehicleTypeIconUrl.length > 0) {

                            if (driverMarker.value) {
                                animateDriverMovement(driverMarker.value, driverLatLng);
                                driverMarker.value.setIcon(L.icon({
                                    iconUrl: vehicleTypeIconUrl,
                                    iconSize: [30, 30],
                                }));
                            } else {
                                const driverMarker = L.marker(driverLatLng, {
                                    icon: L.icon({
                                        iconUrl: vehicleTypeIconUrl,
                                        iconSize: [30, 30],
                                    })
                                }).addTo(map.value);
                                map.value.setView([currentLat.value, currentLng.value], 15);


                                driverMarker.value = driverMarker;
                                animateDriverMovement(driverMarker, driverLatLng);
                            }
                        } else {
                            map.value.removeLayer(driverMarker);
                            driverMarker.value = null;
                        }
                    }
            });

        });

        const results2 = ref([]);
        const paginator2 = ref({});
        const requests = ref([]); // Spread the results to make them reactive
        const ratingResults = ref([]);
        const ratingPaginator = ref({});
        const documentResults = ref([]);

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const fetchRequestDatas = async (page = 1) => {
                const params = filter2.data();
                params.search = searchTerm2.value;
                params.page = page;
                const response = await axios.get(`/approved-drivers/request/list/${props.driver.id}`, { params });
                requests.value = response.data.requests;
                paginator2.value = response.data.paginator;
                updatePaginatorOptions(paginator2.value.total);// Update paginator options dynamically

        };


        const fetchRatingDatas = async (page = 1) => {
            try {
                const params = { page: page };
                const response = await axios.get(`/drivers-rating/request-list/${props.driver.id}`, { params });
                ratingResults.value = response.data.results;
                ratingPaginator.value = response.data.paginator;
            } catch (error) {
                console.error(t('error_fetching_drivers_rating'), error);
            }
        };

        const fetchDocumentDatas = async (page = 1) => {
            try {
                const params = { page: page };
                const response = await axios.get(`/fleet-drivers/document/list/${props.driver.id}`, { params });
                documentResults.value = response.data.results;
            } catch (error) {
                console.error(t('error_fetching_drivers_rating'), error);
            }
        };

        const approveDocument = async (documentId) => {
            const status = 1;
            const response = await axios.get(`/fleet-drivers/document-toggle/${documentId}/${props.driver.id}/${status}`);
            successMessage.value = t('document_approved');
            setTimeout(() => {
                successMessage.value='';
            }, 5000);
            fetchDocumentDatas();
        };

        const documentNameFront = ref(null);
        const documentNameBack = ref(null);
        const showModal = ref(false);
        const imageUrl = ref(null);
        const backImageUrl = ref(null);  // To hold the back image URL

        const closeModal = () => {
            showModal.value = false;
            imageUrl.value = null;
            backImageUrl.value = null;
        };
        // Updated method to view document and show modal
        const viewDocument = (document) => {
            // Assuming 'document.image' contains the URL of the image
            imageUrl.value = document.image;
            backImageUrl.value = document.back_image; // Back image URL
            showModal.value = true;
            documentNameFront.value = document.document_name_front ?? t("document"); 
            documentNameBack.value = document.document_name_back ?? t("document");
        };

        const declineDocument = async (documentId) => {
            const status = 5;
            const response = await axios.get(`/approved-drivers/document-toggle/${documentId}/${props.driver.id}/${status}`); 
            alertMessage.value = t('document_declined');
            setTimeout(() => {
                alertMessage.value='';
            }, 5000);
            fetchDocumentDatas();
        };

        const handlePageChanged2 = async (page) => {
            fetchRequestDatas(page);
        };

        const handleRatingPageChanged = async (page) => {
            fetchRatingDatas(page);
        };
        const updatePaginatorOptions = () => {
            paginatorOption.value = [10, 25, 50, 100,200,500]; // Default static options
        };
        // **Handle per-page changes**
        const changeEntriesPerPage = () => {
            fetchRequestDatas(); // Fetch new data
        };

        return {
            form,
            validationMessage,
            searchTerm1,
            searchTerm2,
            mobileFromUser,
            emailFromUser,
            results2,
            paginator2,
            ratingResults,
            ratingPaginator,
            documentResults,
            handlePageChanged2,
            handleRatingPageChanged,
            ride_count,
            cancel_ride_count,
            map,
            selectedServiceLocations,
            selectedVehicleTypes,
            fetchRequestDatas,
            requests,
            earning,
            earningOptions,
            series,
            tripOptions,
            disable_options,
            fetchRatingDatas,
            fetchDocumentDatas,
            successMessage,
            alertMessage,

            approveDocument,
            declineDocument,
            viewDocument,
            closeModal,
            showModal, // Export ref to use in the template
            imageUrl,  // Export ref to use in the template
            backImageUrl, // Export ref for back image
            documentNameFront,
            documentNameBack,
            paginatorOption,
            changeEntriesPerPage,
            filter2
        };
    },
    mounted() {
        this.fetchRequestDatas();
        this.fetchRatingDatas();
        this.fetchDocumentDatas();
    },
};
</script>


<template>
    <Layout>

        <Head title="Driver Profile" />
        <PageHeader :title="$t('driver_profile')" :pageTitle="$t('driver_profile')" pageLink="/fleet-drivers"/>
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
                                    <div class="ms-4">
                                        <h5>{{driver.name}}</h5> 
                                        <p>{{driver.service_location_name}}</p> 
                                    </div>
                                 </div>                                
                            </div>
                            <div class="col-sm-4 mt-4 profile-border">                               
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
                            <div class="col-sm-4 mt-3 ">
                                <div class="d-flex align-items-center ">
                                    <div>
                                        <img class="rounded-circle avatar-xl" alt="200x200" :src="driver.vehicle_type_image">

                                    </div>
                                    <div class="ms-4">
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
                                        <a class="nav-link active" data-bs-toggle="tab" href="#driver-profile" role="tab" aria-selected="false">
                                            {{$t("driver_profile")}}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-bs-toggle="tab" href="#request-list" role="tab" aria-selected="false">
                                            {{$t("request_list")}}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#rating-history" role="tab" aria-selected="false">
                                            {{$t("review_history")}}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#documents" role="tab" aria-selected="false">
                                            {{$t("documents")}}
                                        </a>
                                    </li>
                                </ul>
                        </div>

                    </BCardHeader>
                </BCard>
                        <!-- Tab panes -->
                        <div class="tab-content  text-muted">
                            <div class="tab-pane active  p-3" id="driver-profile" role="tabpanel">                                            
                                <BCard>
                                    <BCardBody>
                                        <h5 class="mb-4 mt-4">{{$t("general_report")}}</h5>

                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="row  row-cols-lg-4 row-cols-1">
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="ri-group-line" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                                <i class=" bx bx-car text-success icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h1 class="mb-1"> {{ trip_data.completed_today }} </h1>
                                                                <h5 class="card-text text-muted">{{$t("today_trips")}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="ri-group-line" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-success icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h1 class="mb-1"> {{ earnings_data.today_total }}</h1>
                                                                <h5 class="card-text text-muted">{{$t("today_earnings")}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="ri-group-line" style="font-size: 30px;color:#fbc500"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                                                                <i class=" bx bx-car text-primary icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h1 class="mb-1"> {{ trip_data.completed }}</h1>
                                                                <h5 class="card-text text-muted">{{$t('total_trips')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="bx bx-user-check" style="font-size: 30px;color:#fbc500"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-primary icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h1 class="mb-1"> {{ earnings_data.overall_total }}</h1>
                                                                <h5 class="card-text text-muted">{{$t('total_earnings')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="ri-group-line" style="font-size: 30px;color:#91c714"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                                                                <i class=" bx bx-car text-danger icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h1 class="mb-1"> {{ trip_data.cancelled_today }}</h1>
                                                                <h5 class="card-text text-muted">{{$t('today_cancelled')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        
                                                    </div><!-- end row -->
                                                </div><!-- end col -->
                                            </div><!-- end row -->

                                            <h5 class="mb-4 mt-4">{{$t('driver_location')}}</h5>
                                             <!-- map  -->
                                             <div class="col-12 col-lg-12">
                                            <div class="mb-3 text-center m-auto">
                                            <div id="map" style="height: 500px;"> {{$t('map_loading')}}</div>
                                            </div>
                                            </div>  

                                            <h5 class="mb-4 mt-4">{{$t('earnings')}}</h5>
                                            <div class="row">
                                                <div class="col-sm-6 col-md-12 col-lg-12 col-xl-6">
                                                <apexchart
                                                    class="apex-charts"
                                                    height="350"
                                                    dir="ltr"
                                                    :series="earning"
                                                    :options="earningOptions"
                                                ></apexchart>           
                                             </div>
                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-6">
                                                    <div class="row  row-cols-lg-3 row-cols-1">
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="bx bx-rupee" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-success icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ currency[0] }} {{ earnings_data.today_total }}</h3>
                                                                </div>                                                                
                                                                <h5 class="card-text text-muted">{{$t('today_earnings')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class=" bx bx-rupee" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-primary icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ currency[0] }} {{ earnings_data.admin_commission }}</h3>
                                                                </div>
                                                                <h5 class="card-text text-muted">{{$t('admin_commission')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class=" bx bx-rupee" style="font-size: 30px;color:#fbc500"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-warning-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-warning icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ currency[0] }} {{ earnings_data.driver_commission }}</h3>
                                                                </div>
                                                                <h5 class="card-text text-muted">{{$t('drivers_earnings')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class=" bx bx-rupee" style="font-size: 30px;color:#fbc500"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-success icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ earnings_data.overall_cash }}</h3>
                                                                </div>
                                                                <h5 class="card-text text-muted">{{$t('by_cash')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class=" bx bx-rupee" style="font-size: 30px;color:#fbc500"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-money text-primary icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ earnings_data.overall_wallet }}</h3>
                                                                </div>
                                                                <h5 class="card-text text-muted">{{$t('by_wallet')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class=" bx bx-credit-card" style="font-size: 30px;color:#91c714"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-warning-subtle rounded-circle fs-2">
                                                                                <i class="bx bx-credit-card text-warning icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="border p-1 rounded ms-3">{{ earnings_data.overall_card }}</div>
                                                                </div>
                                                                <!-- <div class="d-flex">
                                                                    <i class="bx bx-rupee" style="font-size: 25px;"></i>
                                                                    <h3 class="mb-1">0</h3>
                                                                </div> -->
                                                                <h5 class="card-text text-muted">{{$t('by_card')}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->                                                        
                                                    </div><!-- end row -->
                                                    
                                                </div>
                                            </div>

                                            <h5 class="mb-4 mt-4">{{$t("trips")}}</h5>
                                            <div class="row">
                                                <div class="col-sm-6 col-md-12 col-xl-6 col-lg-6">
                                                <apexchart
                                                    class="apex-charts"
                                                    height="350"
                                                    dir="ltr"
                                                    :series="series"
                                                    :options="tripOptions"
                                                ></apexchart>
                                                </div>
                                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                                    <div class="row  row-cols-lg-2 row-cols-1">
                                                        <div class="col">
                                                            <div class="card card-body border  card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="las la-check-circle" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                        <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                            <i class=" bx bx-car text-success icon-lg"></i>
                                                                        </span>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ ride_count }}</h3>
                                                                </div>                                                                
                                                                <h5 class="card-text text-muted">{{$t("completed_trips")}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->
                                                        <div class="col">
                                                            <div class="card card-body border card-hover">
                                                                <div class="d-flex mb-4 align-items-center">
                                                                    <div>
                                                                        <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                                        <!-- <i class="bx bx-box" style="font-size: 30px;color:#3160d8"></i> -->
                                                                        <div class="avatar-sm flex-shrink-0">
                                                                            <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                                                                <i class=" bx bx-car text-danger icon-lg"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                                    <h3 class="mb-1">{{ cancel_ride_count}}</h3>
                                                                </div>
                                                                <h5 class="card-text text-muted">{{$t("cancelled_trips")}}</h5>                                                                
                                                                <!-- <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i> -->
                                                            </div>
                                                        </div><!-- end col -->                                                     
                                                    </div><!-- end row -->
                                                    
                                                </div>
                                            </div>

                                    </BCardBody>
                                </BCard>
                            </div>
                                        
                            <div class="tab-pane  p-3" id="request-list" role="tabpanel">
                                <BCard>
                                    <BCardBody>
                                        <div class="row  row-cols-lg-2 row-cols-1">
                                            <div class="col-lg-3 col-md-6 col-sm-12">
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
                                                    <div class="d-flex">
                                                        <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                        <h3 class="mb-1">{{ ride_count }}</h3>
                                                    </div>                                                                
                                                    <h5 class="card-text text-muted">{{$t("completed_rides")}}</h5>                                                                
                                                    <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                <div class="card card-body border card-hover">
                                                    <div class="d-flex mb-4 align-items-center">
                                                        <div>
                                                            <div class="avatar-sm flex-shrink-0">
                                                                <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                                    <i class="bx bx-check-circle text-success icon-lg"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex">
                                                        <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                        <h3 class="mb-1">{{ acceptance_rate }} %</h3>
                                                    </div>
                                                    <h5 class="card-text text-muted">{{$t("acceptance_rate")}}</h5>                                                                
                                                    <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                <div class="card card-body border card-hover">
                                                    <div class="d-flex mb-4 align-items-center">
                                                        <div>
                                                            <!-- <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-sm rounded-circle" /> -->
                                                            <!-- <i class="las la-ban" style="font-size: 30px;color:#3160d8"></i> -->
                                                            <div class="avatar-sm flex-shrink-0">
                                                                <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                                                    <i class="bx bx-x-circle text-danger icon-lg"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex">
                                                        <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                        <h3 class="mb-1">{{ cancellation_rate}}  %</h3>
                                                    </div>
                                                    <h5 class="card-text text-muted">{{$t("cancellation_rate")}}</h5>                                                                
                                                    <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i>
                                                </div>
                                            </div><!-- end col -->
                                            <div class="col-lg-3 col-md-6 col-sm-12">
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
                                                    <div class="d-flex">
                                                        <!-- <i class="bx bx-rupee" style="font-size: 25px;"></i> -->
                                                        <h3 class="mb-1">{{ cancel_ride_count}}</h3>
                                                    </div>
                                                    <h5 class="card-text text-muted">{{$t("cancelled_rides")}}</h5>                                                                
                                                    <i class="ri-arrow-right-s-line" style="font-size: 20px;"></i>
                                                </div>
                                            </div><!-- end col -->
                                                                                                    
                                        </div><!-- end row -->
                                        <BCol md="3" class="mb-3">
                                            <div class="d-flex align-items-center mt-3">
                                                <label class="me-2 text-muted">{{$t("show")}}</label>
                                                <select v-model="filter2.limit" @change="changeEntriesPerPage" class="form-select form-select-sm w-auto">
                                                <option v-for="option in paginatorOption" :key="option" :value="option">
                                                    {{ option }}
                                                </option>
                                                </select>
                                                <label class="ms-2 text-muted">{{$t("entries")}}</label>
                                            </div>
                                        </BCol>
                                        <div class="table-responsive">
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
                                                        <td>{{ request.converted_created_at }}</td>
                                                        <td>{{ request.user_name }}</td>
                                                        <td>{{ request.driver_name }}</td>
                                                        <td>{{ request.trip_status }}</td>
                                                        <td>{{ request.trip_payment }}</td>
                                                        <!-- <td>{{ request.payment_opt }}</td>  -->
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
                                        <Pagination :paginator="ratingPaginator" @page-changed="handleRatingPageChanged" />
                                    </BCardBody>
                                </BCard>
                            </div>

                            <div class="tab-pane  p-3" id="documents" role="tabpanel">
                                <BCard>
                                    <BCardBody>
                                        <div class="table-responsive">
                                            <table class="table align-middle position-relative table-nowrap">
                                                <thead class="table-active">
                                                    <tr>
                                                        <th scope="col">{{$t("document_name")}}</th>
                                                        <th scope="col">{{$t("identify_number")}}</th>
                                                        <th scope="col">{{$t("expiry_date")}}</th>
                                                        <th scope="col">{{$t("status")}}</th>
                                                        <th scope="col">{{$t("comment")}}</th>
                                                        <th scope="col">{{$t("document")}}</th>
                                                        <th scope="col">{{$t("action")}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(document, index) in documentResults" :key="document.id">
                                                        <td>{{ document.name }}</td>
                                                        <td>{{ document.identify_number || 'N/A' }}</td>
                                                        <td>{{ document.expiry_date || 'N/A' }}</td>
                                                        <td>
                                                            <span 
                                                                :class="{
                                                                    badge: true,
                                                                    'bg-success': document.document_status === 1, // Approved
                                                                    'bg-secondary': document.document_status === 2 || document.document_status == null, // Not Uploaded or Missing
                                                                    'bg-warning': document.document_status === 3 || document.document_status === 4, // Waiting for Approval
                                                                    'bg-danger': document.document_status === 5, // Declined
                                                                    'bg-dark': document.document_status === 6 // Expired
                                                                }"
                                                            >
                                                                {{ 
                                                                    document.document_status === 1 ? 'Approved' :
                                                                    document.document_status === 2 || document.document_status == null ? 'Not Uploaded' :
                                                                    document.document_status === 3 || document.document_status === 4 ? 'Waiting for Approval' :
                                                                    document.document_status === 5 ? 'Declined' :
                                                                    document.document_status === 6 ? 'Expired' :
                                                                    'Unknown'
                                                                }}
                                                            </span>
                                                        </td>
                                                        <td>{{ document.comment || 'N/A' }}</td>
                                                        <td>
                                                            <BButton class="btn btn-soft-info btn-sm m-2" size="sm"data-bs-toggle="tooltip" v-b-tooltip.hover
                                                            title="view" @click="viewDocument(document)">
                                                                <i class="bx bx-show-alt align-center"></i>
                                                            </BButton>
                                                            <BButton class="btn btn-soft-success btn-sm m-2" size="sm" data-bs-toggle="tooltip" v-b-tooltip.hover
                                                            title="upload" :href="`/approved-drivers/document-upload/${document.id}/${driverId}`">
                                                                <i class="bx bx-upload align-center"></i>
                                                            </BButton>
                                                        </td>                                        
                                                        <td>
                                                            <!-- Show only Decline button if document_status is 1 (Approved) -->
                                                            <button 
                                                                v-if="document.document_status === 1" 
                                                                type="button" 
                                                                class="btn btn-outline-danger waves-effect waves-light btn-sm" 
                                                                @click="declineDocument(document.id)">
                                                                {{$t("decline")}}
                                                            </button>

                                                            <!-- Show only Approve button if document_status is 5 (Declined) -->
                                                            <button 
                                                                v-if="document.document_status === 5" 
                                                                type="button" 
                                                                class="btn btn-outline-success waves-effect waves-light btn-sm me-1" 
                                                                @click="approveDocument(document.id)">
                                                                {{$t("approve")}}
                                                            </button>

                                                            <!-- Do not show any buttons if document_status is 2 or null -->
                                                            <template v-if="!(document.document_status === 2 || document.document_status == null)">
                                                                <!-- Show both Approve and Decline buttons if document_status is 3, 4, or 6 -->
                                                                <button 
                                                                v-if="document.document_status === 3 || document.document_status === 4 || document.document_status === 6" 
                                                                type="button" 
                                                                class="btn btn-outline-success waves-effect waves-light btn-sm me-1" 
                                                                @click="approveDocument(document.id)">
                                                                {{$t("approve")}}
                                                                </button>
                                                                <button 
                                                                v-if="document.document_status === 3 || document.document_status === 4 || document.document_status === 6" 
                                                                type="button" 
                                                                class="btn btn-outline-danger waves-effect waves-light btn-sm" 
                                                                @click="declineDocument(document.id)">
                                                                {{$t("decline")}}
                                                                </button>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div v-if="showModal" class="modal fade show" tabindex="-1" role="dialog" style="display: block;">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header d-flex align-items-center justify-content-between">
                                                        <h5 class="modal-title">{{$t("view_document")}}</h5>
                                                        <a href="#" class="close fs-18" @click="closeModal" aria-label="Close">
                                                            <span class="fs-22" aria-hidden="true">&times;</span>
                                                        </a>
                                                    </div>
                                                    <div class="modal-body">
                                                                    <!-- With Crossfade Animation -->
                                                        <div id="carouselExampleFade" class="carousel slide carousel-fade py-5" data-ride="carousel">
                                                            <div class="carousel-indicators">
                                                                <button type="button" data-bs-target="#carouselExampleFade" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                                <button type="button" data-bs-target="#carouselExampleFade" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                            </div>
                                                            <div class="carousel-inner">
                                                                <div class="carousel-item active">
                                                                    <div class="car-img" v-if="imageUrl">
                                                                    <img  :src="imageUrl" :alt="documentNameFront" class="img-fluid mb-2" />
                                                                    <h5 class="text-center">{{ documentNameFront  }}</h5>
                                                                    </div>
                                                                    <div v-else class="d-grid"><i class="ri-image-fill m-auto fs-22"></i><h6 class="text-center m-auto">{{$t("no_front_image_available")}}</h6></div>
                                                                    
                                                                </div>
                                                                <div class="carousel-item">
                                                                    <div class="car-img" v-if="backImageUrl">
                                                                    <img  :src="backImageUrl" :alt="documentNameBack" class="img-fluid mb-2" />
                                                                    <h5 class="text-center">{{ documentNameBack }}</h5>
                                                                    </div>
                                                                    <div v-else class="d-grid"><i class="ri-image-fill m-auto fs-22"></i><h6 class="text-center m-auto">{{$t("no_back_image_available")}}</h6></div>
                                                                    
                                                                </div>
                                                            </div>
                                                            <a class="carousel-control-prev bg-dark" style="height:30px"  href="#carouselExampleFade" role="button" data-bs-slide="prev">
                                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                <span class="sr-only">Previous</span>
                                                            </a>
                                                            <a class="carousel-control-next bg-dark" style="height:30px" href="#carouselExampleFade" role="button" data-bs-slide="next">
                                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                <span class="sr-only">Next</span>
                                                            </a>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" @click="closeModal">{{$t("close")}}</button>
                                                        <!-- Add download buttons for both images if available -->
                                                        <a v-if="imageUrl" :href="imageUrl" download class="btn btn-primary">
                                                            {{$t("download_front_image")}}
                                                        </a>
                                                        <a v-if="backImageUrl" :href="backImageUrl" download class="btn btn-primary">
                                                            {{$t("download_back_image")}}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
    float: right;
    position: fixed;
    top: 100px;
    right: 80px;
}
/* .text-danger {
    padding-top: 5px;
} */
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