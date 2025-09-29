<script>
import { Link,Head, useForm } from '@inertiajs/vue3';
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
import useVuelidate from "@vuelidate/core";
import { UsersIcon } from '@zhuowenli/vue-feather-icons';
import getChartColorsArray from "@/common/getChartColorsArray";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/autoplay";
import { useI18n } from 'vue-i18n';

export default {
  props: {
    firebaseSettings: Array,
    driverIds: Array,
    earningsData: Array,
    currencySymbol:Object,
    todayEarnings:Object,
    overallEarnings:Object,
    todayTrips:Object,
    fleetsEarnings: Array,
    fleetDriverEarnings: Array,
    total_drivers:Object,
    total_fleets:Object,

    fire_base_driver_ids: Array,

    map_key: String,
    default_lat:Number,
    default_lng:Number,

  },
  data() {
    return {};
  },
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    flatPickr,
    Link,
    UsersIcon,
    Swiper,
    SwiperSlide,
  },
  setup(props) {
    const driverIds = props.driverIds;



        // Map and related variables
        let map;
        let driverMarkers = {};
        let infoWindow;


    const total_drivers = props.total_drivers;
    const total_fleets = props.total_fleets;

    const { t } = useI18n();
        // Reactive references for counting
    const onLine = ref(0);
    const offLine = ref(0);
    const blockedFleetDrivers = ref(0);

        // Initialize Map
        const initializeMap = () => {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: parseFloat(props.default_lat), lng: parseFloat(props.default_lng) },
                zoom: 15,
            });

            infoWindow = new google.maps.InfoWindow();

            // Fetch drivers initially
            fetchNearbyDrivers();
        };

const fetchNearbyDrivers = () => {
    const driversRef = firebase.database().ref('drivers');
// console.log(driversRef);


    driversRef.once('value', (snapshot) => {
        const drivers = snapshot.val();

        if (!drivers) return;

        // Remove existing markers
        for (let id in driverMarkers) {
            driverMarkers[id].setMap(null);
        }
        driverMarkers = {};

        Object.keys(drivers).forEach(driverId => {
              const fire_base_driver_ids = props.fire_base_driver_ids;

          //  console.log("fire_base_driver_ids id");
          // console.log(drivers[driverId]);

            // Check if the driverId is in the selected driverIds array
      if (!fire_base_driver_ids.includes(driverId)) {
          // console.log(`Driver ID ${driverId} is not included in fire_base_driver_ids`);
          return;
      }
          console.log("selected drivers id");
          console.log(driverId);

            const driver = drivers[driverId];
            const driverGeoHash = driver.g;
            const decodedDriver = decodeGeohash(driverGeoHash);

            if (decodedDriver && driver.service_location_id) {
                const driverLatLng = new google.maps.LatLng(decodedDriver.lat, decodedDriver.lon);

                let active = true;
                let onride = true;
                let offline = true;

                let vehicleTypeIconUrl = "";
                if (driver.hasOwnProperty('is_active') && driver.hasOwnProperty('is_available')) {
                    if (driver.is_active === "1" && driver.is_available === true && active) {
                        vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;
                    } else if (driver.is_active === "1" && driver.is_available === false && onride) {
                        vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;
                    } else {
                        if (offline) {
                            vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;
                        }
                    }
                }

                if (vehicleTypeIconUrl.length > 0) {
                    // Create new marker
                    const newDriverMarker = new google.maps.Marker({
                        position: driverLatLng,
                        map: map,
                        icon: {
                            url: vehicleTypeIconUrl,
                            scaledSize: new google.maps.Size(30, 30),
                        },
                    });

                    newDriverMarker.addListener('mouseover', () => {
                        infoWindow.setContent(`<div><strong>${driver.name}</strong><br>Mobile: ${driver.mobile}</div>`);
                        infoWindow.open(map, newDriverMarker);
                    });

                    newDriverMarker.addListener('mouseout', () => {
                        infoWindow.close();
                    });

                    driverMarkers[driver.id] = newDriverMarker;
                }
            }
        });
    });
};
        // Decode geohash to latitude and longitude
        const decodeGeohash = (geohash) => {
            const BASE32 = '0123456789bcdefghjkmnpqrstuvwxyz';
            const BITS = [16, 8, 4, 2, 1];
            let isEven = true;
            let latMin = -90,
                latMax = 90;
            let lonMin = -180,
                lonMax = 180;
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
                return { lat: lat, lon: lon };
            }
        };


    // Firebase initialization
 onMounted(() => {

            const map_key = props.map_key;

            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${map_key}&libraries=places`;
            script.async = true;
            script.defer = true;
            script.onload = initializeMap;
            document.head.appendChild(script);

      // Firebase initialization
      const firebaseConfig = {
        apiKey: props.firebaseSettings['firebase_api_key'],
        authDomain: props.firebaseSettings['firebase_auth_domain'],
        databaseURL: props.firebaseSettings['firebase_database_url'],
        projectId: props.firebaseSettings['firebase_project_id'],
        storageBucket: props.firebaseSettings['firebase_storage_bucket'],
        messagingSenderId: props.firebaseSettings['firebase_messaging_sender_id'],
        appId: props.firebaseSettings['firebase_app_id'],
      };

      if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
      }

      const driversRef = firebase.database().ref('drivers');
      const driverIds = props.driverIds;



      let totalFleetDrivers = driverIds.length;

      const promises = driverIds.map(driverId => {
        return driversRef.orderByChild('id').equalTo(driverId).once('value').then(snapshot => {
          snapshot.forEach(childSnapshot => {
            const driverData = childSnapshot.val();
            if (driverData.is_active === 1) {
              onLine.value++;
            } else if (driverData.is_active === 0) {
              offLine.value++;
            }
          });
        });
      });

      Promise.all(promises).then(() => {
        blockedFleetDrivers.value = totalFleetDrivers - (onLine.value + offLine.value);
        // console.log(`Online Count: ${onLine.value}`);
        // console.log(`Offline Count: ${offLine.value}`);
        // console.log(`Total Fleet Drivers: ${totalFleetDrivers}`);
        // console.log(`Blocked Drivers: ${blockedFleetDrivers.value}`);
      }).catch(error => {
        console.error(t('error_fetching_data'), error);
      });
    });

    return {
      onLine,
      offLine,
      blockedFleetDrivers,
      total_drivers,
      total_fleets,      
      // Today earnings chart options
      series: [
        Number(props.todayTrips.today_completed),
        Number(props.todayTrips.today_cancelled),
        Number(props.todayTrips.today_scheduled),    
      ],
      chartOptions: {
        labels: [t('completed'), t('cancelled'), t('scheduled')],
        chart: {
          type: "donut",
          height: 219,
        },
        plotOptions: {
          pie: {
            size: 100,
            donut: {
              size: "76%",
            },
          },
        },
        dataLabels: {
          enabled: false,
        },
        legend: {
          show: false,
          position: "bottom",
          horizontalAlign: "center",
          offsetX: 0,
          offsetY: 0,
          markers: {
            width: 20,
            height: 6,
            radius: 2,
          },
          itemMargin: {
            horizontal: 12,
            vertical: 0,
          },
        },
        stroke: {
          width: 0,
        },
        yaxis: {
          labels: {
            formatter: function (value) {
              return value;
            },
          },
          tickAmount: 4,
          min: 0,
        },
        colors: getChartColorsArray('["--vz-primary", "--vz-warning", "--vz-info"]'),
      },

      // Overall earnings chart options
      overall: [
        {
          name: t('overall_earnings'),
          data: props.earningsData.earnings.values,
        },
      ],
      overallChartOptions: {
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
        categories: props.earningsData.earnings.months, //x Axis months
        },
        yaxis: {
          labels: {
            formatter: function (value) {
              return Number(value.toFixed(2));
            },
          },
          tickAmount: 5,
          min: 0,
          max: Math.max(...props.earningsData.earnings.values) * 1.1, // Adjust max value dynamically
        },
        colors: getChartColorsArray('["--vz-success"]'),
        fill: {
          opacity: 0,
          colors: ["#0AB39C", "#F06548"],
          type: "solid",
        },
      },

    };
  },

  methods: {},
};
</script>

<template>
  <Layout>
    <PageHeader :title="$t('owner-dashboard')" :pageTitle="$t('owner-dashboard')" pageLink="/owner-dashboard"/>
    <div class="row">
 <!-- vehicles cards -->
      <div class="col-xl-4">
              <div class="card card-animate">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div class="avatar-sm flex-shrink-0">
                              <span class="avatar-title bg-success-subtle text-success rounded-2 fs-2">
                                <i class="ri-taxi-line"></i>
                              </span>
                          </div>
                          <div class="flex-grow-1 overflow-hidden ms-3">
                              <h4 class="text-uppercase fw-medium text-muted text-truncate mb-3"><span class="text-success">Registered</span>  Fleets</h4>
                              <div class="d-flex align-items-center mb-3">
                                  <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="825">{{ total_fleets.total }}</span></h4>
                                  <Link href="/manage-fleet" class="text-decoration-underline">{{ $t("view_all") }}</Link>
                              </div>
                          </div>
                      </div>
                  </div><!-- end card body -->
              </div>
          </div><!-- end col -->
      <div class="col-xl-4">
              <div class="card card-animate">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div class="avatar-sm flex-shrink-0">
                              <span class="avatar-title bg-success-subtle text-success rounded-2 fs-2">
                                <i class="ri-taxi-line"></i>
                              </span>
                          </div>
                          <div class="flex-grow-1 overflow-hidden ms-3">
                              <h4 class="text-uppercase fw-medium text-muted text-truncate mb-3"><span class="text-success">Approved</span>  Fleets</h4>
                              <div class="d-flex align-items-center mb-3">
                                  <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="825">{{ total_fleets.approved }}</span></h4>
                                  <Link href="/manage-fleet" class="text-decoration-underline">{{ $t("view_all") }}</Link>
                              </div>
                          </div>
                      </div>
                  </div><!-- end card body -->
              </div>
          </div><!-- end col -->
          <div class="col-xl-4">
              <div class="card card-animate">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div class="avatar-sm flex-shrink-0">
                              <span class="avatar-title bg-danger-subtle text-danger rounded-2 fs-2">
                                <i class="ri-taxi-line"></i>
                              </span>
                          </div>
                          <div class="flex-grow-1 ms-3">
                              <h4 class="text-uppercase fw-medium text-muted mb-3"><span class="text-danger">Fleets</span> Awaiting Review</h4>
                              <div class="d-flex align-items-center mb-3">
                                  <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="7522">{{ total_fleets.declined }}</span></h4>
                                  <Link href="/manage-fleet" class="text-decoration-underline">{{ $t("view_all") }}</Link>
                              </div>
                          </div>
                      </div>
                  </div><!-- end card body -->
              </div>
          </div><!-- end col -->

<!-- driver cards -->
      <div class="col-xl-4">
              <div class="card card-animate">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div class="avatar-sm flex-shrink-0">
                              <span class="avatar-title bg-success-subtle text-success rounded-2 fs-2">
                                <i class=" ri-steering-2-fill"></i>
                              </span>
                          </div>
                          <div class="flex-grow-1 overflow-hidden ms-3">
                              <h4 class="text-uppercase fw-medium text-muted text-truncate mb-3"><span class="text-success">Registered</span>  Drivers</h4>
                              <div class="d-flex align-items-center mb-3">
                                  <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="825">{{ total_drivers.total }}</span></h4>
                                  <Link href="/fleet-drivers" class="text-decoration-underline">{{ $t("view_all") }}</Link>
                              </div>
                          </div>
                      </div>
                  </div><!-- end card body -->
              </div>
          </div><!-- end col -->
      <div class="col-xl-4">
              <div class="card card-animate">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div class="avatar-sm flex-shrink-0">
                              <span class="avatar-title bg-success-subtle text-success rounded-2 fs-2">
                                <i class=" ri-steering-2-fill"></i>
                              </span>
                          </div>
                          <div class="flex-grow-1 overflow-hidden ms-3">
                              <h4 class="text-uppercase fw-medium text-muted text-truncate mb-3"><span class="text-success">Approved</span>  Drivers</h4>
                              <div class="d-flex align-items-center mb-3">
                                  <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="825">{{ total_drivers.approved }}</span></h4>
                                  <Link href="/fleet-drivers" class="text-decoration-underline">{{ $t("view_all") }}</Link>
                              </div>
                          </div>
                      </div>
                  </div><!-- end card body -->
              </div>
          </div><!-- end col -->
          <div class="col-xl-4">
              <div class="card card-animate">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div class="avatar-sm flex-shrink-0">
                              <span class="avatar-title bg-danger-subtle text-danger rounded-2 fs-2">
                                <i class=" ri-steering-2-fill"></i>
                              </span>
                          </div>
                          <div class="flex-grow-1 ms-3">
                              <h4 class="text-uppercase fw-medium text-muted mb-3"><span class="text-danger">Drivers</span> Awaiting Review</h4>
                              <div class="d-flex align-items-center mb-3">
                                  <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="7522">{{ total_drivers.declined }}</span></h4>
                              </div>
                          </div>
                      </div>
                  </div><!-- end card body -->
              </div>
          </div><!-- end col -->
      </div>

  <div>
    <div id="map" style="height: 400px;">{{$t("map_loading")}}</div>
  </div>
<!-- revenue  -->
<!-- rides -->
  <BRow>
    <BCol xl="6" md="6">
          <BCard no-body class="card-height-100">
    <BCardHeader class="align-items-center d-flex py-0">
      <BCardTitle class="mb-0 flex-grow-1 p-3">{{ $t("today_trips") }}</BCardTitle>
    </BCardHeader>
    <BCardBody>
      <apexchart class="apex-charts" dir="ltr" height="219" :series="series" :options="chartOptions"></apexchart>

      <div class="table-responsive mt-3">
        <table class="table table-borderless table-sm table-centered align-middle table-nowrap mb-0">
          <tbody class="border-0">
            <tr>
              <td>
                <h4 class="text-truncate fs-14 fs-medium mb-0">
                  <i class="ri-stop-fill align-middle fs-18 text-primary me-2"></i>{{ $t("completed_rides") }}
                </h4>
              </td>
            </tr>
            <tr>
              <td>
                <h4 class="text-truncate fs-14 fs-medium mb-0">
                  <i class="ri-stop-fill align-middle fs-18 text-warning me-2"></i>{{ $t("cancelled_rides") }}
                </h4>
              </td>
            </tr>
            <tr>
              <td>
                <h4 class="text-truncate fs-14 fs-medium mb-0">
                  <i class="ri-stop-fill align-middle fs-18 text-info me-2"></i>{{ $t("scheduled_rides") }}
                </h4>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </BCardBody>
  </BCard>
</BCol>

<BCol xl="6" md="6">
<div class="row">
      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("today_earnings") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                          <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ todayEarnings.total }} </h2>
                            </div>
                      <div>
                      <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-info icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->

      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("by_cash") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                          <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ todayEarnings.cash }} </h2>
                      </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-success icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->
  </div>
  <div class="row">
      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("by_wallet") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                              <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ todayEarnings.wallet }} </h2>
                      </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-warning icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->

      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("by_card") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                              <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ todayEarnings.card }} </h2>                     
                           </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-danger icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->
  </div>
  <div class="row">
      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("admin_commission") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                              <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ todayEarnings.admin_commision }} </h2>                     
                      </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-primary icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->

      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("drivers_earnings") }}</p>
                         <h2 class="mt-4 ff-secondary fw-semibold">
                              <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ todayEarnings.driver_commision }} </h2>                                           
                             </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-dark-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-dark icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->
  </div>
</Bcol>
</BRow>


<!-- total earnings -->
<BRow>
<BCol xl="6" md="6">
<div class="row">
      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("overall_earnings") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                              <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ overallEarnings.total }} </h2>  
                            </div>
                      <div>
                      <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-danger icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->

      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("by_cash") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                           <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ overallEarnings.cash }} </h2>                     
                           </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-warning icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->
  </div>
  <div class="row">
      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("by_wallet") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                          <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ overallEarnings.wallet }} </h2>                    
                      </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-success icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->

      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("by_card") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                          <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ overallEarnings.card }} </h2>                     
                          </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-info icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->
  </div>
  <div class="row">
      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("admin_commission") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                          <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ overallEarnings.admin_commision.toFixed(2) }} </h2>                     
                          </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-dark-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-dark icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->

      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("owner_earnings") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                          <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             {{ overallEarnings.driver_commision }} </h2>                     
                          </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                          <i class="bx bx-money text-primary icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->
  </div>
</Bcol>
<BCol xl="6" md="6">
    <BCard no-body>
    <BCardBody class="p-0">
      <BRow class="g-0">
        <BCol xxl="12">
          <div class="">
            <BCardHeader class="align-items-center d-flex">
              <BCardTitle class="mb-0 flex-grow-1">{{ $t("overall_earnings") }}</BCardTitle>
            </BCardHeader>
            <apexchart class="apex-charts" height="350" dir="ltr" :series="overall" :options="overallChartOptions"></apexchart>
          </div>
        </BCol>       
      </BRow>
    </BCardBody>
  </BCard>
  </BCol>
</BRow>
<!-- cab perfomance -->
 <BRow>
    <BCard no-body>
      <div class="card-header align-items-center d-flex">
        <h3 class="card-title mb-0 flex-grow-1">{{ $t("cab_performance") }}</h3>
        <BLink href="/manage-fleet" class="text-decoration-underline fs-16">{{ $t("view_all") }}</BLink>
      </div>
      <BCardBody>
        <BCol xl="12">
          <div class="tasks-board mb-3 mt-3">
            <div v-for="(fleet, index) in fleetsEarnings" :key="index" class="tasks-list border p-3 mb-3">
              <div class="product">
                <div class="card-body">
                  <div class="row gy-3 text-center">
                    <div class="col-sm">
                      <h3 class="fs-22 text-truncate">{{ fleet.license_number }}</h3>
                      <h4 class="fs-22 text-success">{{ currencySymbol }}{{ fleet.total_earnings }}</h4>
                    </div>
                  </div>
                </div>
                <div class="">
                  <div class="row align-items-center gy-3">
                    <div class="col-sm">
                      <div class="d-flex flex-wrap my-n1">
                        <div>
                          <a href="#" class="d-block text-body p-1 px-2">
                            <i class="ri-star-fill text-warning align-bottom me-1"></i>
                            {{ fleet.average_user_rating }}
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-auto">
                      <div class="d-flex align-items-center gap-2 text-muted">
                        <div>{{ $t("rides") }} :</div>
                        <h5 class="fs-14 mb-0">
                          <span class="product-line-price">{{ fleet.total_trips }}</span>
                        </h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </BCol>
      </BCardBody>
    </BCard>
  </BRow>

<!-- Driver performance -->
          <BRow>
            <BCard no-body>
                <div class="card-header align-items-center d-flex">
                    <h3 class="card-title mb-0 flex-grow-1">{{ $t("driver_performance") }}</h3>
                    <BLink href="/fleet-drivers" class="text-decoration-underline fs-16">{{ $t("view_all") }}</BLink>
                </div>
                <BCardBody>
            <BCol xl="12">
                <div class="tasks-board mb-3 mt-3">
                  <!-- card -->
 <div v-for="driver in fleetDriverEarnings" :key="driver.driver_id" class="tasks-list border p-3 mb-3">
      <div class="product">
        <div class="card-body">
          <div class="avatar-md mb-3 mx-auto">
            <img src="@assets/images/users/user-dummy-img.jpg" alt="driver" class="img-thumbnail rounded-circle shadow-none">
          </div>
          <div class="row gy-3 text-center">
            <div class="col-sm">
              <h3 class="fs-22 text-truncate">{{ driver.name }}</h3>
              <h4 class="fs-22 text-success">${{ driver.total_driver_earnings }}</h4>
            </div>
          </div>
        </div>
        <div class="">
          <div class="row align-items-center gy-3">
            <div class="col-sm">
              <div class="d-flex flex-wrap my-n1">
                <div>
                  <a href="#" class="d-block text-body p-1 px-2">
                    <i class="ri-star-fill text-warning align-bottom me-1"></i> {{ driver.average_user_rating }}
                  </a>
                </div>
              </div>
            </div>
            <div class="col-sm-auto">
              <div class="d-flex align-items-center gap-2 text-muted">
                <div>{{ $t("rides") }} :</div>
                <h5 class="fs-14 mb-0"><span class="product-line-price">{{ driver.total_trips }}</span></h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
                        <!--end card-->
                    </div>
            </BCol>
        </BCardBody>
        </BCard>
          </BRow>

  </Layout>
</template>
<style>
#map {
  height: 400px;
  width: 100%;
  border: 1px dashed #ddd; /* Optional styling for visibility */
}
</style>