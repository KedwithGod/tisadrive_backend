<script>
import { Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from "vue";
import { useI18n } from 'vue-i18n';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import Layout from "@/Layouts/dispatchmain.vue";
import PageHeader from "@/Components/page-header.vue";
import googleMap from '@/Components/googleMap.vue';

export default {
    components: {
        Head,
        Layout,
        PageHeader,
        Multiselect,
        googleMap,
    },
    props: {
        firebaseSettings: Object,
        map_key: String,
        service_location: Array,
        vehicle_type: Array,
        app_for:String,
        baseUrl: String,
        default_location:Object,
    },
    setup(props) {
        const { t } = useI18n();
        const map = ref(null);
        // Reactive data for filters
        const selectedServiceLocations = ref(null);
        const selectedVehicleTypes = ref(null);
        const selectedDriverModes = ref([]);
        const drivers_list = ref([]);

        // Options for filters
        const serviceLocations = ref(props.service_location.map(loc => ({ value: loc.id, label: loc.name })));
        const vehicleTypes = ref(props.vehicle_type.map(type => ({ value: type.id, label: type.name })));

        // Map and related variables
        const driverMarkers = ref({});
        let infoWindow;

        const clearFilters = () => {
            selectedServiceLocations.value = null;
            selectedVehicleTypes.value = null;
            selectedDriverModes.value = [];
            fetchNearbyDrivers();
        }
        const applyFilters = () => {
            fetchNearbyDrivers();
        }
        // Fetch drivers and update markers
        const fetchNearbyDrivers = async() => {
            const driversRef = firebase.database().ref('drivers');

            driversRef.once('value', (snapshot) => {
                const drivers = snapshot.val();

                if (!drivers) return;

                driverMarkers.value = {};
                drivers_list.value = [];

                Object.keys(drivers).forEach(driverId => {
                    const driver = drivers[driverId];
                    const driverGeoHash = driver.g;
                    const decodedDriver = decodeGeohash(driverGeoHash);

                    if (decodedDriver && driver.service_location_id) {
                        // Filter based on selected service location and vehicle types
                        let matchesServiceLocation = driver.service_location_id ? true : false;
                        if(selectedServiceLocations.value){
                            matchesServiceLocation = selectedServiceLocations.value == driver.service_location_id ?? false;
                        }

                        let matchesVehicleType = Array.isArray(driver.vehicle_types);
                        if(selectedVehicleTypes.value && Array.isArray(driver.vehicle_types) && driver.vehicle_types.length>0){
                            matchesVehicleType = (Array.isArray(driver.vehicle_types) && driver.vehicle_types.some(type => selectedVehicleTypes.value === type)) ?? false;
                        }

                        let active = selectedDriverModes.value.length>0 ? selectedDriverModes.value.includes("online") : true;
                        let onride = selectedDriverModes.value.length>0 ? selectedDriverModes.value.includes("onride") : true;
                        let offline = selectedDriverModes.value.length>0 ? selectedDriverModes.value.includes("offline") : true;

                        let flag=false;
                        let status="offline";

                        let last_seen = '';
                        if(driver.hasOwnProperty('is_active') && driver.hasOwnProperty('is_available')){
                            if (driver.is_active == 1 && driver.is_available==true && active) {
                                flag = true;
                                status="online";
                            } else if (driver.is_active == 1 && driver.is_available==false && onride) {
                                status="onride";
                                flag = true;
                            } else {
                                if(offline){
                                    flag = true;

                                    let last_seen_time = new Date() - new Date(driver.updated_at);
                                    let seenInMinutes = parseInt(last_seen_time / 60000),
                                        seenInHours = 0,
                                        seenInDays = 0,
                                        seenInWeeks = 0;
                                    if(seenInMinutes > 59){
                                        seenInHours = parseInt(seenInMinutes / 60);
                                        if(seenInHours > 23){
                                        seenInDays = parseInt(seenInHours / 24);
                                        if(seenInDays > 6){
                                            seenInWeeks = parseInt(seenInDays / 7);
                                        }
                                        }
                                    }
                                    if(seenInMinutes <= 1){
                                        last_seen = 'just now';
                                    }
                                    if(seenInMinutes > 1 && seenInMinutes < 59){
                                        last_seen = seenInMinutes + ' minutes ago';
                                    }
                                    if(seenInHours == 1){last_seen = 'An hour ago'}
                                    if(seenInHours > 1){
                                        last_seen = seenInHours + ' hours ago';
                                    }
                                    if(seenInDays == 1){last_seen = 'A day ago'}
                                    if(seenInDays > 1){
                                        last_seen = seenInDays +' days ago';
                                    }
                                    if(seenInWeeks == 1){last_seen = 'A week ago'}
                                    if(seenInWeeks > 1){
                                        last_seen = seenInWeeks + ' weeks ago';
                                    }
                                }
                            }
                        }
                        if (matchesServiceLocation && matchesVehicleType && flag) {


                            driver.status = status;
                            driver.last_seen = last_seen;
                            drivers_list.value.push(driver);

                            const driverContent = `<strong>${driver.name}</strong><br>Mobile: ${props.app_for == 'demo' ? '**********' :driver.mobile}`;

                            const contentWrapper = document.createElement('div');
                            contentWrapper.innerHTML = driverContent;

                            driverMarkers.value[driver.id] = {
                                lat: decodedDriver.lat,
                                lng: decodedDriver.lon,
                                type_icon: driver.vehicle_type_icon,
                                info: contentWrapper,
                            };
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

        // Watch filters and update map when they change
        watch([selectedServiceLocations, selectedVehicleTypes], () => {
            fetchNearbyDrivers();
        }),
        onMounted(async() => {

            var firebaseConfig = {
                apiKey: props.firebaseSettings['firebase_api_key'],
                authDomain: props.firebaseSettings['firebase_auth_domain'],
                databaseURL: props.firebaseSettings['firebase_database_url'],
                projectId: props.firebaseSettings['firebase_project_id'],
                storageBucket: props.firebaseSettings['firebase_storage_bucket'],
                messagingSenderId: props.firebaseSettings['firebase_messaging_sender_id'],
                appId: props.firebaseSettings['firebase_app_id'],
            };
            if(!firebase.apps.length){
                firebase.initializeApp(firebaseConfig);
            }

            fetchNearbyDrivers();

        });

        return {
            serviceLocations,
            vehicleTypes,
            selectedServiceLocations,
            selectedDriverModes,
            driverMarkers,
            drivers_list,
            clearFilters,
            applyFilters,
            selectedVehicleTypes,
        };
    },
};
</script>



<template>
    <Layout>

        <Head :title="$t('gods-eye')" />
        <PageHeader :title="$t('gods-eye')" :pageTitle="$t('dispatch')" />
        <BRow>
    <BCol lg="12">
      <BCard no-body id="tasksList">
        <BCardHeader class="border-0">
          <h4>{{$t("filters")}}</h4>
          <div class="row">
            <div class="col-12 col-lg-3">
              <div class="mb-3">
                <label for="select_location" class="form-label">{{$t("service_location")}}</label>
                <Multiselect
                    v-model="selectedServiceLocations"
                    :options="serviceLocations"
                    label="label"
                    track-by="value"
                    multiple
                    close-on-select
                    :placeholder="$t('select_service_locations')"
                />
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <div class="mb-3">
                <label for="select_driver_mode" class="form-label">{{$t("drivers")}}</label>
                <Multiselect 
                  v-model="selectedDriverModes"
                  id="select_driver_mode" 
                  mode="tags" 
                  :close-on-select="false"
                  :searchable="true" 
                  :create-option="false"
                  :options="[
                    { value: 'offline', label: $t('offline') },
                    { value: 'online', label: $t('online') },
                    { value: 'onride', label: $t('onride') },
                  ]"
                  :placeholder="$t('select_mode')"
                />
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <div class="mb-3">
                <label for="vehicle_type" class="form-label">{{$t("vehicle_types")}}</label>
                <Multiselect
                    v-model="selectedVehicleTypes"
                    :options="vehicleTypes"
                    label="label"
                    track-by="value"
                    multiple
                    close-on-select
                    :placeholder="$t('select_vehicle_types')"
                />
              </div>
            </div>
            <div class="col-12 col-lg-6 d-flex gap-1">
              <BButton type="button" variant="success" class="btn btn-md" @click="applyFilters">{{$t("apply")}}</BButton>
              <BButton type="button" variant="danger" class="btn btn-md" @click="clearFilters">{{$t("clear")}}</BButton>
            </div>
          </div>
        </BCardHeader>
        <BCardBody class="border border-dashed border-end-0 border-start-0">
            <BRow>
                <BCol lg="4">
                    <div v-if="drivers_list.length>0" class="overflow-auto" style="height: 400px;">
                        <BCard class="col-lg-12" v-for="(driver) in drivers_list">
                            <div class="accordion-item border-0">
                                <div class="accordion-header" id="headingThree">
                                    <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#" aria-expanded="false" aria-controls="collapseThree">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 border border-4 rounded-circle" style="border: var(--landing_header_act_text);">
                                                <img :src="driver.profile_picture" alt="" class="avatar-md rounded-circle">
                                            </div>
                                            <div class="d-flex justify-content-around">
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="fs-15 mb-1 fw-semibold" style="color: var(--landing_header_act_text);">{{ driver.name }}</h4>
                                                <h6 class="fs-15 mb-1 fw-normal" style="color: var(--landing_header_act_text);">{{ app_for == 'demo' ? "*********" :  driver.mobile }}</h6>
                                                <h6 class="fs-15 mb-1 fw-normal">{{ driver.rating }} <i class="ri-star-fill text-warning align-bottom me-1" /></h6>
                                            </div>
                                            <div class="text-end ms-5 d-block">
                                                <h4 v-if="driver.status == 'offline'" class="fs-15 mb-1 fw-semibold text-danger">{{ driver.status }}</h4>
                                                <h4 v-if="driver.status == 'online'" class="fs-15 mb-1 fw-semibold text-success">{{ driver.status }}</h4>
                                                <h4 v-if="driver.status == 'onride'" class="fs-15 mb-1 fw-semibold text-primary">{{ driver.status }}</h4>
                                                <p class="flex-grow-1 fs-15 mb-1 text-muted mt-5">{{ driver.last_seen }}</p>
                                            </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </BCard>
                    </div>
                    <div class="d-flex" v-else style="height: 400px;">
                        <img src="/image/map/no-drivers.png" style="width: 60%;margin:auto;" alt="No Drivers Found">
                    </div>
                </BCol>
                <BCol lg="8">
                    <div id="map" style="height: 400px;">
                        <googleMap
                            :baseUrl="baseUrl"
                            :default_location="default_location"
                            :nearbyDrivers="driverMarkers"
                            :map_key="map_key"
                            :libraries="['marker']"
                        >
                        {{$t("map_loading")}}
                        </googleMap>
                    </div>
                </BCol>
            </BRow>
        </BCardBody>
      </BCard>
    </BCol>
  </BRow>
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
