<script>
import { Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from "vue";
import { useI18n } from 'vue-i18n';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import Layout from "@/Layouts/main.vue";
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
        baseUrl: String,
        default_location:Object,
    },
    setup(props) {
        const { t } = useI18n();
        // Reactive data for filters
        const selectedServiceLocations = ref(null);
        const selectedVehicleTypes = ref(null);
        const selectedDriverModes = ref([]);

        // Options for filters
        const serviceLocations = ref(props.service_location.map(loc => ({ value: loc.id, label: loc.name })));
        const vehicleTypes = ref(props.vehicle_type.map(type => ({ value: type.id, label: type.name })));

        // Map and related variables
        const driverMarkers = ref({});

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
                        if(driver.hasOwnProperty('is_active') && driver.hasOwnProperty('is_available')){
                            if (driver.is_active ==1 && driver.is_available==true && active) {
                                flag = true;
                            } else if (driver.is_active == 1 && driver.is_available==false && onride) {
                                flag = true;
                            } else {
                                if(offline){
                                    flag = true;
                                }
                            }
                        }
                        if (matchesServiceLocation && matchesVehicleType && flag) {


                            driverMarkers.value[driver.id] = {
                                lat: decodedDriver.lat,
                                lng: decodedDriver.lon,
                                type_icon: driver.vehicle_type_icon,
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
            clearFilters,
            applyFilters,
            selectedVehicleTypes,
            driverMarkers,
        };
    },
};
</script>



<template>
    <Layout>

        <Head :title="$t('gods-eye')" />
        <PageHeader :title="$t('gods-eye')" :pageTitle="$t('map')" />
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
                  @change="applyFilters"
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
