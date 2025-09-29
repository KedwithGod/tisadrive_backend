<script>
import { Link,Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import { ref, watch,reactive,computed,onMounted } from "vue";
import axios from "axios";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import { Scrollbar } from 'swiper/modules';
import FormValidation from "@/Components/FormValidation.vue";
import debounce from 'lodash/debounce';
import { mapGetters } from 'vuex';
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
Link,
Scrollbar,
FormValidation,
flatPickr,
},
computed: {
    ...mapGetters(['permissions']),
    
  },
props: {
successMessage: String,
alertMessage: String,
firebaseSettings:Array,
app_for:String,
map_key:String,
driver_radius: Number,
result: Object,
},

methods: {
    timer() {
      let timerInterval;
      Swal.fire({
        title: "Booking alert!",
        html: "Your Ride has been Booked <b></b> Successfully.",
        timer: 2000,
        timerProgressBar: true,
        onBeforeOpen: () => {
          Swal.showLoading();
          timerInterval = setInterval(() => {
            Swal.getContent().querySelector("b").textContent =
              Swal.getTimerLeft();
          }, 100);
        },
        onClose: () => {
          clearInterval(timerInterval);
        },
      }).then((result) => {
        if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.timer
        ) {
          console.log("I was closed by the timer"); // eslint-disable-line
        }
      });
    },
  },

setup(props) {
  const { t } = useI18n();


const modalShow = ref(false);
const driver_radius = ref(props.driver_radius ?? 10);
const successMessage = ref(props.successMessage || '');
const alertMessage = ref(props.alertMessage || '');

const dismissMessage = () => {
successMessage.value = "";
alertMessage.value = "";
};

const fleetDrivers = ref([]);
const modalDriver = ref(null);
const result = ref(props.result);

const assignModal = (driver) => {
  modalDriver.value = driver;
  modalShow.value = true;
}

const assignDriver = async (driver) => {
  try {
    const response = await axios.post(`/ongoing-rides/assign-driver/${result.value.id}`,{ 'driver_id' : driver.id});
    if (response.data.status) {
      modalShow.value = false;
      Swal.fire(t('success'), t('users_assigned_successfully'), 'success');
    } else {
      Swal.fire(t('Warning'),response.data.message , 'warning');
      console.log( response );
    }
  } catch (error) {
    Swal.fire(t('Warning'), response.data.message, 'warning');
    console.error( error );
  }
}
// Initialize Map
const initializeMap = () => {
const map = new google.maps.Map(document.getElementById('map'), {
center: { lat: result.value.pick_lat, lng: result.value.pick_lng },
zoom: 15,
});

const pickupIconUrl = '/image/map/pickup.png';
const pickupMarker = new google.maps.Marker({
position:{ lat: result.value.pick_lat, lng: result.value.pick_lng },
map,
icon: {
url: pickupIconUrl,
scaledSize: new google.maps.Size(30, 30),
},
});


let driverMarkers = {};





map.setZoom(15);

const fetchNearbyDrivers = () => {

const driversRef = firebase.database().ref('drivers');
const requestRef = firebase.database().ref('requests').child(result.value.id);

requestRef.on('value', (snapshot) => {
  const val = snapshot.val();
  if(val.is_accept || val.hasOwnProperty('driver_id')) {
      router.get(`/rides-request/view/${result.value.id}`);
  }
});
driversRef.on('value', (snapshot) => {
fleetDrivers.value = [];
snapshot.forEach((childSnapshot) => {
const driver = childSnapshot.val();

const now = Date.now();
const sevenMinutesAgo = now - (15 * 60 * 1000);
if(driver.updated_at < sevenMinutesAgo) {
    return;
}

const driverGeoHash = driver.g;
const driverLocation = decodeGeohash(driverGeoHash);

if (driverLocation) {
const distance = calculateDistance(result.value.pick_lat, result.value.pick_lng, driverLocation.lat, driverLocation.lon);

if (distance <= driver_radius.value && driver.is_available && driver.is_active && driver.vehicle_types.includes(result.value.vehicle_type_id)) {
const driverLatLng = new google.maps.LatLng(driverLocation.lat, driverLocation.lon);
const vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;
fleetDrivers.value.push(driver);

if (driverMarkers[driver.id]) {
animateDriverMovement(driverMarkers[driver.id], driverMarkers[driver.id].getPosition(), driverLatLng, driver.bearing);
driverMarkers[driver.id].setIcon({
url: vehicleTypeIconUrl,
scaledSize: new google.maps.Size(30, 30),
});
} else {
const driverMarker = new google.maps.Marker({
position: driverLatLng,
map: map,
icon: {
url: vehicleTypeIconUrl,
scaledSize: new google.maps.Size(30, 30),
},
});
driverMarker.addListener('click', () => {
  assignModal(driver);
});
driverMarkers[driver.id] = driverMarker;
animateDriverMovement(driverMarker, driverMarker.getPosition(), driverLatLng, driver.bearing);
}

const time = calculateTime(distance);


} else if (driverMarkers[driver.id]) {

driverMarkers[driver.id].setMap(null);
delete driverMarkers[driver.id];

}
}

});
});

driversRef.on('child_changed', (childSnapshot) => {
const driver = childSnapshot.val();
if (!driver.is_active) {

removeDriverMarker(driver.id);
} else {
updateDriverMarker(driver);
}
});

driversRef.on('child_removed', (childSnapshot) => {
const driver = childSnapshot.val();

removeDriverMarker(driver.id);
});
};

const calculateTime = (distance) => {

if(distance<2){
return '3 Mins';
}else if(distance > 2 && distance <5){
return '5 Mins';
}
else if(distance > 5 && distance <7){
return '10 Mins';
}else{
return '15 mins';
}
};

const animateDriverMovement = (marker, prevLatLng, currentLatLng, currentBearing) => {
const numSteps = 60;
const timeInterval = 2000;
const delay = timeInterval / numSteps;

let startLat = prevLatLng.lat();
let startLng = prevLatLng.lng();

let endLat = currentLatLng.lat();
let endLng = currentLatLng.lng();

const easeInOutQuad = (t) => t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;

const moveMarker = (step) => {
let t = easeInOutQuad(step / numSteps);
let newPosition = new google.maps.LatLng(startLat + (endLat - startLat) * t, startLng + (endLng - startLng) * t);
marker.setPosition(newPosition);

if (currentBearing !== undefined) {
const prevBearing = marker.prevBearing || currentBearing;
const bearingDiff = currentBearing - prevBearing;
const interpolatedBearing = prevBearing + (bearingDiff * t);
marker.setIcon({
...marker.getIcon(),
rotation: interpolatedBearing,
});
marker.prevBearing = currentBearing;
}

if (step < numSteps) {
setTimeout(() => moveMarker(step + 1), delay);
}
};

moveMarker(0);
};

const updateDriverMarker = (driver) => {
const driverGeoHash = driver.g;
const driverLocation = decodeGeohash(driverGeoHash);

if (driverLocation) {
const driverLatLng = new google.maps.LatLng(driverLocation.lat, driverLocation.lon);
const vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;

if (driverMarkers[driver.id]) {
// animateDriverMovement(driverMarkers[driver.id], driverMarkers[driver.id].getPosition(), driverLatLng, driver.bearing);
driverMarkers[driver.id].setIcon({
url: vehicleTypeIconUrl,
scaledSize: new google.maps.Size(30, 30),
});
} else {
const driverMarker = new google.maps.Marker({
position: driverLatLng,
map: map,
icon: {
url: vehicleTypeIconUrl,
scaledSize: new google.maps.Size(30, 30),
},
});
driverMarker.addListener('click', () => {
  assignModal(driver);
});
driverMarkers[driver.id] = driverMarker;
}
} else {
console.error(`Failed to decode geohash for driver ID: ${driver.id}`);
}
};

const removeDriverMarker = (driverId) => {
if (driverMarkers[driverId]) {
driverMarkers[driverId].setMap(null);
delete driverMarkers[driverId];
}
};

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

const calculateDistance = (lat1, lon1, lat2, lon2) => {
const R = 6371;
const dLat = (lat2 - lat1) * Math.PI / 180;
const dLon = (lon2 - lon1) * Math.PI / 180;
const a =
Math.sin(dLat / 2) * Math.sin(dLat / 2) +
Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
Math.sin(dLon / 2) * Math.sin(dLon / 2);
const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
const distance = R * c;
return distance;
};

fetchNearbyDrivers();

};
onMounted(async () => {
    try{
        const map_key = props.map_key;
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${map_key}&libraries=places`;
        script.async = true;
        script.defer = true;
        script.onload = initializeMap;
        document.head.appendChild(script);

        var firebaseConfig = {
            apiKey: props.firebaseSettings['firebase_api_key'],
            authDomain: props.firebaseSettings['firebase_auth_domain'],
            databaseURL: props.firebaseSettings['firebase_database_url'],
            projectId: props.firebaseSettings['firebase_project_id'],
            storageBucket:  props.firebaseSettings['firebase_storage_bucket'],
            messagingSenderId: props.firebaseSettings['firebase_messaging_sender_id'],
            appId: props.firebaseSettings['firebase_app_id'],
        };

        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }
    }catch (error) {
      console.error(t('error_initializing_firebase_or_fetching_settings'), error);
    }
});


return {
modalShow,
successMessage,
alertMessage,
modalDriver,
assignDriver,
assignModal,
fleetDrivers,
dismissMessage,
errors:{},
};
},


};
</script>

<template>
<Layout>
<Head title="Taxi Ride" />
<PageHeader :title="$t('book')" :pageTitle="$t('taxi_ride')" pageLink="/ongoing-rides"/>
<BRow>
  <BCol>
      <BCard>
      <BCardBody>
      <BRow>
        <div class="col-lg-3">
          <div class="card-header">
              <h6 class="card-title mb-0">{{ $t('user_details') }}</h6>
          </div>
          <div class="card-body p-4 text-center">
              <div class="mx-auto avatar-md mb-3">
                  <img :src="result.userDetail.data.profile_picture" alt="2" class="avatar-md rounded-circle">
              </div>
              <h5 class="card-title mb-1"> {{ result.userDetail.data.name }}</h5>
              <p class="text-muted mb-0">{{ app_for == 'demo' ? '***********' : result.userDetail.data.mobile }}</p>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="card-header">
              <h6 class="card-title mb-0">{{ $t('vehicle_details') }}</h6>
          </div>
          <div class="card-body p-4 text-center">
              <div class="mx-auto avatar-md mb-3">
                  <img :src="result.vehicle_type_image" alt="2" class="avatar-md rounded-circle">
              </div>
              <h5 class="card-title mb-1"> {{ result.vehicle_type_name }}</h5>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card-header">
              <h6 class="card-title mb-0">{{ $t('trip_details') }}</h6>
          </div>
          <div class="card-body p-4">
            <h5 class=" mb-2"><b> {{ $t('pickup_location') }}</b></h5>
            <h6 class=" mb-2">{{ result.pick_address }}</h6>
            <div v-if="result.requestStops.data.length > 0">
            <h5 class=" mb-2"><b> {{ $t('stop_location') }}</b></h5>
            
              <h5 class=" mb-2"><b> {{ $t('stop_location') }}</b></h5>
              <div class="btn-group mb-2">
                  <button class="btn btn-light dropdown-toggle" type="button" id="stop_locations_list" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    {{ $t('view_stop_location') }}
                  </button>
                  <ul class="dropdown-menu overflow-auto" style="max-width:500px" aria-labelledby="stop_locations_list">
                      <li v-for="(stop,index) in result.requestStops.data">
                        <a class="dropdown-item overflow-auto" > {{ stop.address }}</a>
                      </li>
                  </ul>
              </div>
            </div>
            <div v-if= "!result.is_rental">
            <h5 class=" mb-2"><b> {{ $t('drop_location') }}</b></h5>
            <h6 class=" mb-2">{{ result.drop_address }}</h6>
          </div>
          </div>
        </div>
      </BRow>
      </BCardBody>
      </BCard>
</BCol>
</BRow>
<BRow>
<BCol lg="12">
<BCard no-body id="tasksList">
<BCardHeader class="border-0">
</BCardHeader>
<BCardBody class="border border-dashed border-end-0 border-start-0">

<div class="row">
<div class="col-12 col-lg-4" style="overflow-x: auto;">
<div class="row">
          <BRow>
            <BCard no-body>
                <BCardBody class="overflow-y-auto" style="max-height: 500px; width: 100%">
            <BCol xl="12">
                <div class="row" >
                  <!-- card -->
                        <div v-for="driver in fleetDrivers" :key="driver.driver_id" @click="assignModal(driver)" class="row">
                            <div class="col-sm-3 border-end d-flex align-items-center justify-content-center">
                                <div>
                                    <img class="rounded-circle avatar-md" alt="200x200" :src="driver.profile_picture">
                                </div>
                            </div>
                            <div class="col-sm-5 mt-3 border-end">
                              <div class=" d-flex align-items-center ">
                                  <i class="ri-user-line" style="font-size:20px"></i> &nbsp;&nbsp;
                                  <span>{{driver.name}}</span>
                              </div>
                              <div class=" d-flex align-items-center ">
                                  <i class=" ri-phone-line" style="font-size:20px"></i> &nbsp;&nbsp;
                                  <span>{{app_for == 'demo' ? '***********' : driver.mobile}}</span>
                              </div>
                              <div class=" d-flex align-items-center ">
                                    <i class="ri-star-fill" style="font-size:20px"></i> &nbsp;&nbsp;
                                    <span>{{ driver.rating }} </span>
                                </div>
                            </div>
                            <div class="col-sm-4 d-flex align-items-center justify-content-center">
                              <div>
                                <button type="button" class="btn btn-info btn-label waves-effect waves-light"><i class="ri-car-line label-icon fs-20 align-middle me-2"></i> {{ $t('assign') }}</button>
                              </div>
                            </div>
                        </div>
                        <!--end card-->
                    </div>
            </BCol>
        </BCardBody>
        </BCard>
          </BRow>
</div>
</div>

<!-- map  -->
<div class="col-12 col-lg-8">
<div class="mb-3 text-center m-auto">
<div id="map" style="height: 500px;"> {{$t("map_loading")}}</div>
</div>
</div>  
</div>

</BCardBody>
</BCard>
</BCol>
</BRow>
<div>

    <!-- modal -->
    <BModal v-model="modalShow" hide-footer :title="$t('driver_details')" class="v-modal-custom" size="sm">
      <BCard>
      <BCardBody>
      <BRow>
        <div class="col-lg-12">
          <div class="card-header">
              <h6 class="card-title mb-0">{{ $t('confirm_assign_driver') }}</h6>
          </div>
          <div class="card-body p-4 text-center">
              <div class="mx-auto avatar-md mb-3">
                  <img :src="modalDriver?.profile_picture" alt="2" class="avatar-md rounded-circle">
              </div>
              <h5 class="card-title mb-1"> {{ modalDriver?.name }}</h5>
              <p class="text-muted mb-0">{{ app_for == 'demo' ? '***********' : modalDriver?.mobile }}</p>
          </div>
          <p class="text-muted mb-0">{{ $t('confirm_assign_driver_info') }}</p>
        </div>
      </BRow>
      </BCardBody>
      </BCard>
          <div class="modal-footer v-modal-footer">
            <BLink href="javascript:void(0);" class="btn btn-link link-warning fw-medium"
                @click="modalShow = false">
                <i class="ri-close-line me-1 align-middle"></i> {{$t('close')}}
            </BLink>
            <button type="button" class="btn btn-soft-info waves-effect waves-light" @click="assignDriver(modalDriver)">
              <i class="ri-car-line me-1 align-middle"></i> {{$t('assign_driver')}}
            </button>
        </div>
    </BModal>
<!-- modal end -->
</div>
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

:root {
--primary: #222222;
--primary-hover: {{ $side -> value }};
}
/* payments */
.select-form{
position: relative;
width: 100%;
margin-bottom: 18px;
}

.select-checkbox-group {
display: flex;
align-items: center;
position: relative;
}
.select-checkbox-btn {
margin-right: 15px;
margin-bottom: 15px;
}
.select-checkbox-btn-wrapper {
display: flex;
align-items: center;
justify-content: center;
width: fit-content;
position: relative;
}
.select-checkbox-btn-input {
clip: rect(0 0 0 0);
-webkit-clip-path: inset(100%);
clip-path: inset(100%);
height: 1px;
overflow: hidden;
position: absolute;
white-space: nowrap;
width: 1px;
}
.select-checkbox-btn-input:checked + .select-checkbox-btn-content {
border-color: var(--primary);
color: var(--primary);
}
.select-checkbox-btn-input:checked + .select-checkbox-btn-content:before {
transform: scale(1);
opacity: 1;
background-color: var(--primary);
border-color: var(--primary);
}
.select-checkbox-btn-input:checked
+ .select-checkbox-btn-content
.select-checkbox-btn-icon,
.select-checkbox-btn-input:checked
+ .select-checkbox-btn-content
.select-checkbox-btn-label {
color: var(--primary);
}
.select-checkbox-btn-input:focus + .select-checkbox-btn-content {
border-color: var(--primary);
}
.select-checkbox-btn-input:focus + .select-checkbox-btn-content:before {
transform: scale(1);
opacity: 1;
}

.select-checkbox-btn-content {
display: flex;
flex-direction: column;
align-items: center;
justify-content: center;
width: 140px;
min-height: 140px;
border-radius: 10px;
border: 0.1rem solid #dfe2e6;
background-color: #fff;
transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s,
-webkit-box-shadow ease-in-out 0.15s;
cursor: pointer;
position: relative;
user-select: none;
appearance: none;
}
.select-checkbox-btn-content:before {
content: "";
position: absolute;
width: 22px;
height: 22px;
border: 0.1rem solid #bbc1e1;
background-color: #fff;
border-radius: 9999px;
top: 5px;
left: 5px;
opacity: 0;
transform: scale(0);
transition: 0.25s ease;
background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='192' height='192' fill='%23FFFFFF' viewBox='0 0 256 256'%3E%3Crect width='256' height='256' fill='none'%3E%3C/rect%3E%3Cpolyline points='216 72.005 104 184 48 128.005' fill='none' stroke='%23FFFFFF' stroke-linecap='round' stroke-linejoin='round' stroke-width='32'%3E%3C/polyline%3E%3C/svg%3E");
background-size: 12px;
background-repeat: no-repeat;
background-position: 50% 50%;
display: flex;
align-items: center;
justify-content: center;
}
.select-checkbox-btn-content:hover {
border-color: var(--primary);
}
.select-checkbox-btn-content:hover:before {
transform: scale(1);
opacity: 1;
}

.select-checkbox-btn-icon {
transition: 0.375s ease;
color: #3c3c3cc7;
}
.select-checkbox-btn-icon svg {
width: 50px;
height: 50px;
}

.select-checkbox-btn-label {
color: #3c3c3cc7;
transition: 0.375s ease;
text-align: center;
}
.marker {
transition: transform 0.5s ease-out;
}

.amount {
font-family: Arial, sans-serif; /* Ensure the font supports the rupee symbol */
font-size: 16px; /* Adjust the size as needed */
white-space: nowrap; /* Prevent text from wrapping */
letter-spacing: 0.1em; /* Adjust spacing if needed */
}
/* end */
</style>
