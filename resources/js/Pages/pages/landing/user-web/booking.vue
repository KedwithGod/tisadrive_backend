<script>
import { Link,Head, useForm } from '@inertiajs/vue3';
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
import  { initI18n } from "@/i18n";
import UserWebMenu from "@/Components/UserWebMenu.vue";
import googleMap from '@/Components/googleMap.vue';

export default {
data() {
return {
rightOffcanvas: false,
isPocSame: false, // checkbox state
form: {
is_later: '',
assign_method: 1,
name: '',
mobile: '',
drop_poc_name: '',
drop_poc_mobile: '',
},
dateTimeConfig: {
enableTime: true,
dateFormat: "Y-m-d H:i:ss",
},
};
},

watch: {
    isPocSame(newVal) {
      if (newVal) {
        this.form.drop_poc_name = this.form.name;
        this.form.drop_poc_mobile = this.form.mobile;
      } else {
        this.form.drop_poc_name = '';
        this.form.drop_poc_mobile = '';
      }
    },
    // Watch for changes in personal info to update POC if the checkbox is checked
    'form.name'(newVal) {
      if (this.isPocSame) {
        this.form.drop_poc_name = newVal;
      }
    },
    'form.mobile'(newVal) {
      if (this.isPocSame) {
        this.form.drop_poc_mobile = newVal;
      }
    },
  },

components: {
Layout,
PageHeader,
Head,
Link,
Scrollbar,
FormValidation,
googleMap,
flatPickr,
UserWebMenu
},
computed: {
    ...mapGetters(['permissions']),
        
    transport_options() {
      let options = [];

      // Handling 'regular' ride type
      if (this.form.ride_type === 'regular') {
        options = this.transport_type_regular;
        this.form.transport_type = options[0];
      }

      // Automatically select the only available option if there's just one
      if (options.length === 1) {
        this.form.transport_type = options[0]; // Automatically select the single option
      }

      return options;
    },
  },
props: {
countries:Array,
default_flag:String,
default_dial_code:String,
successMessage: String,
alertMessage: String,
validate: Function,
transport_type_regular:Array,
ride_type_for_ride:Array,
is_rental:Boolean,
goodsTypes:Array,
user: Object,
firebaseSettings:Object,
map_key:String,
baseUrl: String,
default_location:Object,
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
const selectedCountry = ref({
dial_code: props.default_dial_code || '',
flag: props.default_flag || ''
});

  const form = useForm({
  mobile:props.user.mobile,
  name:props.user.name,
  user_id:props.user.id,
  pick_address: "",
  drop_address: "",
  terminal:null,
  enterance:null,
  pick_lat:"",
  pick_lng:"",
  drop_lat:null,
  drop_lng:null,
  vehicle_type:null,
  payment_opt:1,
  goods_type_id:null,
  transport_type:'',
  pickup_poc_name:null,
  drop_poc_name:null,
  goods_type_quantity:null,
  is_later:0,
  assign_method:1,
  is_rental:0,
  rental_package_id:null,
  ride_type:'regular',
  stops:null,
  trip_start_time:null,
  country:props.default_dial_code,
  drop_poc_mobile:null,
  distance:0,
  duration:0


  });
const unit = ref('-');
// For Airport
const showAiportTerminal = ref(false);
const showTrainStationEnterance = ref(false);

const pickupLocation = ref(null);
const dropLocation = ref(null);
const map = ref(null);

const pickSuggestions = ref([]);
const dropSuggestions = ref([]);
const stopSuggestions = ref([]);

const validationRules = {
pick_address: { required: true },
mobile: { required: true },
vehicle_type: { required: true }
};
const errors = ref({});
const validationRef = ref(null);
const vehicleTypes = ref([]);
const stopMarkers = ref([]);

const etaParam = useForm({
pick_lat:"",
pick_lng:"",
drop_lat:"",
drop_lng:"",
distance:0,
distance_in_unit: 0,
maximum_distance: 0,
duration:0
});

// Reactive ref to store formatted time
const formattedDuration = ref("0m");

// Watch for changes in etaParam.duration and update formattedDuration
watch(() => etaParam.duration, (newDuration) => {
    const hours = Math.floor(newDuration / 60);
    const minutes = newDuration % 60;
    formattedDuration.value = hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`;
});


const modalShow = ref(false);

const successMessage = ref(props.successMessage || '');
const alertMessage = ref(props.alertMessage || '');

const serviceVerify = async () => {
    const address = [];
    const pick_location = { latitude: etaParam.pick_lat, longitude: etaParam.pick_lng,};

    address.push(pick_location);

    if (Array.isArray(stopovers)) {
        stopovers.forEach((stop) => {
            const stop_location = { latitude: stop.latitude, longitude: stop.longitude, };
            address.push(stop_location);
        });
    }

    if (etaParam.drop_lat) {
        const drop_location = { latitude: etaParam.drop_lat, longitude: etaParam.drop_lng, };
        address.push(drop_location);
    }

    const payload = { address: address, ride_type: form.ride_type, };

    try {
        const response = await axios.post("/dispatch/serviceVerify", payload );
        etaParam.maximum_distance = response.data.data.maximum_distance;
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else if (error.response && error.response.status === 500) {
            if ( error.response.data.message == "service not available with this location" ) {
                errors.value.pick_address = [t("service_unavailable")];
            } else if ( error.response.data.message == "Outstation service not available within this location" ) {
                delete errors.value.pick_address;
                errors.value.drop_address = [t("outstation_service_unavailable")];
            } else if ( error.response.data.message == "Pick up and Drop should not be in the same zone") {
                delete errors.value.pick_address;
                errors.value.drop_address = [t("same_zone_outstation_unavailable")];
            } else {
                const data = error.response.data;
                delete errors.value.pick_address;
                delete errors.value.drop_address;
                address.forEach((location, key) => {
                    if (!errors.value.stop) {
                        errors.value.stop = {};
                    }
                    if (key !== 0) {
                        if (key !== address.length) {
                            if (data[key]["available"]) {
                                errors.value.stop[key] = [
                                    t("ride_service_unavailable"),
                                ];
                            } else {
                                delete errors.value.stop[key];
                            }
                        } else {
                            if (data[key]["available"]) {
                                errors.value.drop_address = [
                                    t("ride_service_unavailable"),
                                ];
                            } else {
                                delete errors.value.drop_address;
                            }
                        }
                    }
                });
            }
            if(errors.value?.stop && Object.keys(errors.value.stop).length == 0){
                delete errors.value.stop;
            }
            
        } else {
            console.error(t("error_creating_vehicle_price"), error);
            alertMessage.value = t(
                "failed_to_make_booking_contact_admin"
            );
        }
    }
};

const handleInput = (input) =>{
  if(input == 'pickup') {
    if (form.pick_address.length < 3) {
      pickSuggestions.value = [];
    }else{
      setTimeout(() => {
        fetchAutocompleteResults(form.pick_address,input);
      }, 300);
    }
  }else if(input == 'drop') {
    if (form.drop_address.length < 3) {
      dropSuggestions.value = [];
    }else{
      setTimeout(() => {
        fetchAutocompleteResults(form.drop_address,input);
      }, 300);
    }
  }
}
const fetchAutocompleteResults = (search,type,index=null) => {

  const apiUrl = `https://places.googleapis.com/v1/places:autocomplete`;
      const headers = {
        "Content-Type": "application/json",
        "X-Goog-Api-Key": props.map_key,
        "X-Goog-FieldMask": "suggestions.placePrediction.placeId,suggestions.placePrediction.place,suggestions.placePrediction.text",
      };
      const requestData = {
        input: search,
      };

      fetch(apiUrl, {
        method: "POST",
        headers: headers,
        body: JSON.stringify(requestData),
      })
        .then((response) => response.json())
        .then((data) => {
          if(data.suggestions.length>0) {
            if(type == 'pickup') {
              pickSuggestions.value = data.suggestions.filter(suggestion => suggestion.placePrediction).map(suggestion => ({
                placeId: suggestion.placePrediction.placeId,
                formattedAddress: suggestion.placePrediction.text.text,
              }));
            }else if(type == 'drop') {
              dropSuggestions.value = data.suggestions.filter(suggestion => suggestion.placePrediction).map(suggestion => ({
                placeId: suggestion.placePrediction.placeId,
                formattedAddress: suggestion.placePrediction.text.text,
              }));
            }else if(type == 'stop') {
              stopSuggestions.value[index] = data.suggestions.filter(suggestion => suggestion.placePrediction).map(suggestion => ({
                placeId: suggestion.placePrediction.placeId,
                formattedAddress: suggestion.placePrediction.text.text,
              }));
            }
          }
        })
        .catch((error) => {
          console.error("Error fetching autocomplete results:", error);
        })
}
const selectSuggestion = async(suggestion,input,index=null) => {

  const headers = {
        "X-Goog-Api-Key": props.map_key,
        "X-Goog-FieldMask": "types,location",
      };
  fetch(`https://places.googleapis.com/v1/places/${suggestion.placeId}?fields=types,location`, {
    headers: headers,
  })
    .then((response) => response.json())
    .then((data) => {
      if(data.types.length > 0) {
        if(data.types.includes('airport')){
          showAiportTerminal.value=true
        }
        if(data.types.includes('train_station')){
          showTrainStationEnterance.value=true
        }
      }
      if(input == 'pickup'){
        etaParam.pick_lat = data.location.latitude;
        etaParam.pick_lng = data.location.longitude;
        form.pick_address = suggestion.formattedAddress;

        const position = {lat: data.location.latitude, lng: data.location.longitude };
        pickupLocation.value = position;
        

        pickSuggestions.value  = [];

      }else if(input == 'drop') {
        etaParam.drop_lat = data.location.latitude;
        etaParam.drop_lng = data.location.longitude;
        form.drop_address = suggestion.formattedAddress;

        const position = {lat: data.location.latitude, lng: data.location.longitude };
        dropLocation.value = position;
        dropSuggestions.value = [];

      }else if(input == 'stop') {
        form.drop_address = suggestion.formattedAddress;

        stopSuggestions.value[index] = [];
        stopovers[index].address = suggestion.formatted_address;
        stopovers[index].latitude = data.location.latitude;
        stopovers[index].longitude = data.location.longitude;

        const position = {lat: data.location.latitude, lng: data.location.longitude };
        stopMarkers[index] = position;
        drawRoute();
      }
    })
    .catch((error) => {
      console.error("Error fetching autocomplete results:", error);
    })
}

const routePolyline = ref('');

const dismissMessage = () => {
successMessage.value = "";
alertMessage.value = "";
};

const stopovers = reactive([]);
const maxStopovers = 5;

const addStopover = () => {

console.log("stopovers-length");
console.log(stopovers.length);

if (stopovers.length < maxStopovers) {


stopovers.push({ address: "", latitude: null, longitude: null });
if(stopovers.length==0){
addStopMarker(0);
}
stopovers.forEach((_, i) => {
addStopMarker(i);
});

} else {
Swal.fire(t('maximum_stopovers_reached'), t('you_can_add_up_to_5_stopovers_only'), 'warning');
}
};

const removeStopover = (index) => {
// Remove marker from map
if (stopMarkers[index]) {
stopMarkers[index].setMap(null);
// stopMarkers.splice(index, 1);
}

// Remove stopover from array
stopovers.splice(index, 1);

// Reinitialize autocomplete for remaining stops

stopovers.forEach((_, i) => {
addStopMarker(i);
});

// Redraw the route
if(index>0){
drawRoute();
}
};


const driverMarkers = ref({});


watch(() => etaParam.pick_lat,async ( pickLat) => {
if (pickLat && etaParam.drop_lat) {
  drawRoute();
  serviceVerify();


}
});

watch(() => etaParam.drop_lat,async (value) => {
if (value && etaParam.pick_lat) {
  drawRoute();
  serviceVerify();

}
});
const drawRoute = async() => {

etaParam.distance = 0;
etaParam.duration = 0;
if (routePolyline.value.length) {
    routePolyline.value = '';
}

if ( !pickupLocation.value || !dropLocation.value ) {
        return;
    }

    const url = 'https://routes.googleapis.com/directions/v2:computeRoutes';

    const requestBody = {
        origin: {
            location: {
            latLng: {
                    latitude: pickupLocation.value.lat,
                    longitude: pickupLocation.value.lng
                }
            },
        },
        destination: {
            location: {
            latLng:{
                    latitude: dropLocation.value.lat,
                    longitude: dropLocation.value.lng
                }
            },
        },
        travelMode: 'DRIVE',
        routingPreference: 'TRAFFIC_AWARE',
        computeAlternativeRoutes: false,
        routeModifiers: {
            avoidTolls: false,
            avoidHighways: false,
            avoidFerries: false,
        },
        languageCode: 'en-US',
        units: 'IMPERIAL',
    };

    requestBody.intermediates = stopovers.map((stop) => ({
        location: new google.maps.LatLng(stop.latitude, stop.longitude),
    }));

    const headers = {
        'Content-Type': 'application/json',
        'X-Goog-Api-Key': props.map_key,
        'X-Goog-FieldMask': 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline,routes.viewport',
    };

    axios.post(url, requestBody, { headers })
    .then(response => {
        const route = response.data.routes?.[0];
        if(route) {

            etaParam.distance = route.distanceMeters;
            etaParam.duration = Math.round(parseInt(route.duration.slice(0,-1)) / 60);


            if(route.polyline?.encodedPolyline) {

                routePolyline.value = route.polyline.encodedPolyline;
            }
            loadVehicleTypes();

            if (errors.value.map) {
                delete errors.value.map;
            }
        }
    })
    .catch(error => {
        errors.value.map = t("service_not_available");
        console.error('Error:', error);
    });

};


const loadVehicleTypes  = async () =>{

const { pick_lat, pick_lng, drop_lat, drop_lng,distance, duration } = etaParam.data();

const payload = {
pick_lat,
pick_lng,
distance,
duration,
};

payload.transport_type = form.transport_type;

if (drop_lat) {
payload.drop_lat = drop_lat;
}

if (drop_lng) {
payload.drop_lng = drop_lng;
}

if(stopovers.length>0){
    payload.stops=JSON.stringify(stopovers);
}

const response = await axios.post(`/dispatch/request/eta`, payload);

vehicleTypes.value = response.data.data;

if(vehicleTypes.value.length>0){
    if(vehicleTypes.value[0].unit_in_words == 'MILES'){
        etaParam.distance_in_unit = (etaParam.distance * 0.621371/1000).toFixed(2);
        unit.value = t('miles');
    }else{
        etaParam.distance_in_unit = (etaParam.distance/1000).toFixed(2);
        unit.value = t('km');
    }
}

  fetchNearbyDrivers(pickupLocation.value);

};
const setEtaTime = (driver,distance) => {

const time = calculateTime(distance);
vehicleTypes.value.forEach((vehicleType) => {
if (driver.vehicle_types && driver.vehicle_types.includes(vehicleType.type_id) && (!vehicleType.driver_distance || vehicleType.driver_distance > distance)) {
    vehicleType.driver_time = time;
  }
});
return true;
}

function animateMarkerMovement(marker, startLat, startLng, endLat, endLng, duration = 1000) {

  const startTime = performance.now();

  function animate(currentTime) {
    const elapsed = currentTime - startTime;
    const t = Math.min(elapsed / duration, 1); // t in [0,1]

    const lat = startLat + (endLat - startLat) * t;
    const lng = startLng + (endLng - startLng) * t;

    marker.lat = lat;
    marker.lng = lng;

    if (t < 1) {
      requestAnimationFrame(animate);
    }
  }

  requestAnimationFrame(animate);
}

watch(() => form.vehicle_type,async ( value) => {
      fetchNearbyDrivers(pickupLocation.value);
});
const fetchNearbyDrivers = (pickupLocation) => {
    vehicleTypes.value.forEach((vehicleType) => {
      vehicleType.driver_time = null;
    });

const driversRef = firebase.database().ref('drivers');

driversRef.on('value', (snapshot) => {
snapshot.forEach((childSnapshot) => {
const driver = childSnapshot.val();
const driverGeoHash = driver.g;
const driverLocation = decodeGeohash(driverGeoHash);

if (driverLocation) {
const distance = calculateDistance(pickupLocation.lat, pickupLocation.lng, driverLocation.lat, driverLocation.lon);

var flag = true;
if(form.vehicle_type){
  flag = false;
  var type_id = vehicleTypes.value?.find((type)=> {
    return (type.zone_type_id === form.vehicle_type);
  })?.type_id;
  if(type_id && driver.vehicle_types && driver.vehicle_types.includes(type_id)){
    flag = true;
  }
}

if (distance <= 10 && driver.is_available && driver.is_active && flag) {
  setEtaTime(driver,distance);
  const prev = driverMarkers.value[driver.id];

  if (prev) {
    if(prev.lat !==  driverLocation.lat && prev.lng !== driverLocation.lon) {
      animateMarkerMovement(prev, prev.lat, prev.lng, driverLocation.lat, driverLocation.lon);
    }
  } else {
    driverMarkers.value[driver.id] = {
      lat: driverLocation.lat,
      lng: driverLocation.lon,
      type_icon: driver.vehicle_type_icon,
    };
  }




} else if (driverMarkers.value[driver.id]) {

delete driverMarkers.value[driver.id];


}

}

});
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

const reverseGeocodeAndUpdateInput = (position, inputId) => {
const geocoder = new google.maps.Geocoder();
geocoder.geocode({ location: position }, (results, status) => {
if (status === 'OK') {
if (results[0]) {
document.getElementById(inputId).value = results[0].formatted_address;
} else {
console.error(t('no_reverse_geocode_results_found'));
}
} else {
console.error(t('geocoder_failed_due_to') + status);
}
});
};


// Submit a Booking

const makeBooking = async ()=>{
errors.value = validationRef.value.validate();

if(etaParam.maximum_distance > 0 && etaParam.maximum_distance < etaParam.distance_in_unit){
    errors.value.maximum_distance = t('exceeds_maximum_distance_for_ride');
}else{
    delete errors.value.maximum_distance;
}

if (Object.keys(errors.value).length > 0) {
    return;
}

const { pick_lat, pick_lng, drop_lat, drop_lng, distance, duration } = etaParam.data();

if (pick_lat) {
form.pick_lat = pick_lat;
}
if (pick_lng) {
form.pick_lng = pick_lng;
}
if (drop_lat) {
form.drop_lat = drop_lat;
}
if (drop_lng) {
form.drop_lng = drop_lng;
}

if(stopovers.length>0){
form.stops = JSON.stringify(stopovers);
}

if (distance) {
form.distance = distance;
}

if (duration) {
form.duration = duration;
}


try {
        let response;
        response = await axios.post(`/web-create-request`, form.data());

  const createdRequestId = response.data.data.id; // Adjust this based on the structure of your response
        if (response.data.success === true) {


      let timerInterval;
      Swal.fire({
        title: "Booking Successfull",
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
            window.location.href = `/history/view/${createdRequestId}`;
        }
      });
          
          form.reset();
          etaParam.reset();
          pickupLocation.value=null;
          dropLocation.value=null;
          routePolyline.value='';
          driverMarkers.value={};
          vehicleTypes.value = [];

        } else {
          alertMessage.value = t('failed_to_make_booking_contact_admin');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_creating_vehicle_price'), error);
          alertMessage.value = t('failed_to_make_booking_contact_admin');
        }
      }

}

onMounted(async() => {
  await initI18n('en');
})
onMounted(() => {
    var firebaseConfig = {
        apiKey: props.firebaseSettings.firebase_api_key,
        authDomain: props.firebaseSettings.firebase_auth_domain,
        databaseURL: props.firebaseSettings.firebase_database_url,
        projectId: props.firebaseSettings.firebase_project_id,
        storageBucket:  props.firebaseSettings.firebase_storage_bucket,
        messagingSenderId: props.firebaseSettings.firebase_messaging_sender_id,
        appId: props.firebaseSettings.firebase_app_id,
        };
    firebase.initializeApp(firebaseConfig);


    if (props.transport_type_regular.length > 0) {
      form.transport_type = props.transport_type_regular[0];
    }

});


const mobile = ref('');

const searchQuery = ref('');

const filteredCountries = computed(() => {
});

const selectCountry = (country) => {
selectedCountry.value = country;
form.country=country.dial_code;
};

const validateNumber = debounce(async () => {
// event.target.value = event.target.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
const response = await axios.get(`/dispatch/fetch-user-detail`, { params:{mobile:form.mobile} });
console.log(response.data.data);

if(response.data.data){
console.log("if")
form.name = response.data.data.name;
form.user_id = response.data.data.id;
}else{
console.log("else")
form.user_id=null;
form.name=null;
}

}, 300); // Adjust the debounce delay as needed


const showModal = ref(false);

// Watch for vehicle type selection
watch(() => form.vehicle_type, (newValue) => {

  if(etaParam.maximum_distance > 0 && etaParam.maximum_distance < etaParam.distance_in_unit){
      errors.value.maximum_distance = t('exceeds_maximum_distance_for_ride');
  }else{
      delete errors.value.maximum_distance;
  }
  showModal.value = !!newValue; // Show modal when a vehicle type is selected
});

return {
form,
modalShow,
successMessage,
alertMessage,
dismissMessage,
validationRef,
validationRules,
errors,
mobile,
selectedCountry,
driverMarkers,
pickupLocation,
dropLocation,
routePolyline,
searchQuery,
filteredCountries,
selectCountry,
validateNumber,
showAiportTerminal,
showTrainStationEnterance,
etaParam,
vehicleTypes,
makeBooking,
stopovers,
addStopover,
removeStopover,
fetchAutocompleteResults,
handleInput,
selectSuggestion,
pickSuggestions,
dropSuggestions,
stopSuggestions,
showModal,
formattedDuration,
unit,
};
},


};
</script>

<template>
<BCard>
  <Head title="Taxi Ride" />
    <BCardHeader class="border-0">
    <!-- menu Offcanvas -->
       <UserWebMenu :user="user" />
    <!-- menu end -->
    </BCardHeader>

<BRow>
  <BCol lg="12">
    <BCard no-body id="tasksList">
      <BCardHeader class="border-0">
        <BRow>
          <BCol lg="6">
          </BCol>
          <BCol lg="6">
                <div class="row mt-4">
                    <div class="col-lg-6 col-sm-6">
                        <div class="p-0 border border-dashed rounded">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <div class="avatar-title rounded bg-transparent text-success fs-24" >
                                        <i class="ri-map-pin-time-line" ></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                  <p class="text-muted mb-1">{{$t("duration")}} :<span class="fs-16 fw-bold ms-2"> {{ formattedDuration }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-6 col-sm-6">
                        <div class="p-0 border border-dashed rounded">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                      <i class=" ri-pin-distance-line"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-1">{{$t("distance")}} :<span class="fs-16 fw-bold ms-2">{{etaParam.distance_in_unit}} {{unit}}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
            </BCol>
          </BRow>
        </BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
              <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
        <div class="row">
          <div class="col-12 col-lg-6" style="max-height:500px;overflow-y: auto;">
            <div class="row">
            <!-- Ride info -->
              <div class="d-flex align-items-center mt-4">
                 <h4 class="card-title mb-3 flex-grow-1">{{$t("ride_info")}}</h4>
              </div>
          <div class="col-6">
            <div class="mb-3">
              <label for="type" class="form-label">{{$t("booking_type")}}</label>
                <select id="type" class="form-select" v-model="form.is_later">
                  <option disabled value="">{{$t("choose_type")}}</option>
                  <option value=0>{{$t("instant_booking")}}</option>
                  <option value=1>{{$t("book_later")}}</option>
                </select>
              <span v-for="(error, index) in errors.is_later" :key="index" class="text-danger">{{ error }}</span>
            </div>
          </div>
        <div class="col-6">
          <div class="col-12">
            <div class="mb-3">
              <label for="type" class="form-label">{{$t("transport_type")}}</label>
                <select id="type" class="form-select" v-model="form.transport_type">
                  <option disabled value="">{{$t("choose_transport_type")}}</option>
                    <option 
                    v-for="(type, index) in transport_type_regular" 
                    :key="index" 
                    :value="type"
                    >
                    {{ type.charAt(0).toUpperCase() + type.slice(1) }}
                    </option>
                    </select>
                    <span 
                    v-for="(error, index) in errors.transport_type" 
                    :key="index" 
                    class="text-danger"
                    >
                    {{ error }}
              </span>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between">
              <label for="pickup" class="form-label">{{ $t("pickup_location") }}</label>
              <!-- <a v-if="form.ride_type=='regular' && etaParam.drop_lat" class="btn btn-primary mb-3" @click="addStopover">{{$t("add_stops")}}</a> -->
            </div>
            <div class="autocomplete-container">
              <div class="input-group">
                <input
                  type="text"
                  class="form-control"
                  v-model="form.pick_address"
                  :placeholder="$t('enter_pickup')"
                  id="pickup"
                  autocomplete="off"
                  @input="handleInput('pickup')"
                />
              </div>
              <div v-if="pickSuggestions.length > 0" class="autocomplete-results">
                <div
                  v-for="suggestion in pickSuggestions"
                  :key="suggestion.placeId"
                  class="autocomplete-item"
                  @click="selectSuggestion(suggestion,'pickup')"
                >
                  {{ suggestion.formattedAddress }}
                </div>
              </div>
            </div>
            <!-- <span v-for="(error, index) in errors.pick_address" :key="index" class="text-danger">{{ error }}</span> -->
            <div class="col-12" v-if="errors.maximum_distance">
              <div class="mb-3">
              <span v-for="(error, index) in errors.maximum_distance" :key="index" class="text-danger">{{ error }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12" v-if="form.ride_type=='regular'" v-for="(stop, index) in stopovers" :key="index">
            <div class="mb-3">
              <label :for="`stop-${index}`" class="form-label">{{$t("stop_location")}}</label>
              <div class="d-flex align-items-center">
              <input type="text" class="form-control mx-1" v-model="stop.address" :placeholder="'Enter stop ' + (index + 1)" :id="`stop-${index}`" >
              <i class="bx bx-trash text-danger fs-22 btn" @click="removeStopover(index)"></i>
            </div>
          <span v-if="form.errors[`stop-${index}`]" class="text-danger">{{ form.errors[`stop-${index}`] }}</span>
        </div>
    </div>
    <div class="col-12" v-if="showAiportTerminal">
      <div class="mb-3">
        <label for="terminal" class="form-label">{{$t("aiport_terminal_info")}}</label>
        <input type="text" class="form-control" v-model="form.terminal" placeholder="$t('enter_terminal_info')" id="terminal" >
      </div>
    </div>
    <div class="col-12" v-if="showTrainStationEnterance">
      <div class="mb-3">
      <label for="enterance" class="form-label">{{$t("station_enterance_info")}}</label>
      <input type="text" class="form-control" v-model="form.enterance" placeholder="$t('enter_enterance_info')" id="enterance" >
      </div>
    </div>
  <div class="col-12" v-if="form.ride_type=='regular'">
    <div class="mb-3">
      <label for="drop" class="form-label">{{$t("drop_location")}}</label>
      <div class="autocomplete-container">
        <div class="input-group">
          <input
            type="text"
            class="form-control"
            v-model="form.drop_address"
            :placeholder="$t('enter_drop')"
            id="drop"
            autocomplete="off"
            @input="handleInput('drop')"
          />
        </div>
        <div v-if="dropSuggestions.length > 0" class="autocomplete-results">
          <div
            v-for="suggestion in dropSuggestions"
            :key="suggestion.placeId"
            class="autocomplete-item"
            @click="selectSuggestion(suggestion,'drop')"
          >
            {{ suggestion.formattedAddress }}
          </div>
        </div>
      </div>
      <span v-for="(error, index) in errors.drop_address" :key="index" class="text-danger">{{ error }}</span>
    </div>
  </div>
<!-- POC info -->
<h4 v-if="form.transport_type=='delivery'" class="card-title mb-2 flex-grow-1 mt-4">{{$t("poc_info")}}</h4>
  <div v-if="form.transport_type=='delivery'" class="px-2 mb-2">
    <span class="badge bg-dark-subtle text-dark p-1 fs-12 text-center">
      <div class="form-check m-2 mx-3">
          
          <input class="form-check-input" type="checkbox" id="poc" v-model="isPocSame">
          <label class="form-check-label mt-1" for="poc">
            {{$t("check_if_poc_info_as_same_as_personal_info")}}
          </label>
      </div>
    </span>
  </div>
<div v-if="form.transport_type=='delivery'" class="col-6">
  <div class="mb-3">
     <label for="poc_name" class="form-label">{{$t("drop_poc_name")}}</label>
      <input type="text" class="form-control" v-model="form.drop_poc_name" :placeholder="$t('enter_poc_name')" id="poc_name" :disabled="isPocSame" >
    <span v-for="(error, index) in errors.drop_poc_name" :key="index" class="text-danger">{{ error }}</span>
  </div>
</div>
<div v-if="form.transport_type=='delivery'" class="col-6">
  <div class="mb-3">
  <label for="poc_mobile" class="form-label">{{$t("drop_poc_mobile")}}</label>
  <input type="text" class="form-control" v-model="form.drop_poc_mobile" :placeholder="$t('enter_poc_mobile')" id="poc_mobile" :disabled="isPocSame" >
  <span v-for="(error, index) in errors.poc_mobile" :key="index" class="text-danger">{{ error }}</span>
  </div>
</div>
<h4 v-if="form.transport_type=='delivery'" class="card-title mb-2 flex-grow-1 mt-4">{{$t("select_goods")}}</h4>
   <div v-if="form.transport_type=='delivery'" class="col-6">
      <div class="mb-3">
        <label for="goods_type" class="form-label">{{$t("goods_type")}}</label>
          <select id="goods_type" class="form-select" v-model="form.goods_type_id">
          <option disabled value="">{{$t("choose_type")}}</option>
          <option v-for="goodsType in goodsTypes" :key="goodsType.id" :value="goodsType.id">{{ goodsType.goods_type_name }}</option>
          </select>
        <span v-for="(error, index) in errors.goods_type_id" :key="index" class="text-danger">{{ error }}</span>
      </div>
</div>
<div v-if="form.transport_type=='delivery'" class="col-6">
  <div class="mb-3">
    <label for="goods_type_quantity" class="form-label">{{$t("goods_quantity")}}</label>
    <input type="text" class="form-control" v-model="form.goods_type_quantity" :placeholder="$t('enter_quantity')" id="goods_type_quantity" >
    <span v-for="(error, index) in errors.goods_type_quantity" :key="index" class="text-danger">{{ error }}</span>
  </div>
</div>
<div class="col-12" v-if="form.is_later === '1'">
  <div class="mb-3">
    <label for="dispatch-datepicker" class="form-label">{{$t("date")}}</label>
    <flat-pickr :placeholder="$t('select_date')" v-model="form.trip_start_time" :config="dateTimeConfig"
    class="form-control flatpickr-input" id="dispatch-datepicker"></flat-pickr>
  </div>
</div>

<!-- Vehicle Select -->
        <div v-if="vehicleTypes.length > 0"  class="col-12">
      <h4 class="card-title mb-3 flex-grow-1 mt-4">{{$t("select_vehicle")}}</h4>

          <div class="mb-3" style="max-width:650px; overflow-x: auto;">
            <div class="d-flex mt-5">
              <div v-for="(vehicleType, index) in vehicleTypes" :key="index" class="select-checkbox-btn text-center">
                  <label :for="'vehicle_' + vehicleType.zone_type_id" class="select-checkbox-btn-wrapper">
                  <input
                  :id="'vehicle_' + vehicleType.zone_type_id"
                  name="types"
                  type="radio"
                  :value="vehicleType.zone_type_id"
                  v-model="form.vehicle_type"
                  class="select-checkbox-btn-input"
                  />
                  <span class="select-checkbox-btn-content">
              <a class="w-32 me-4 cursor-pointer">
            <div class="text-center mt-2 ms-4"><i class="bx bx-time-five mx-1"></i>{{ vehicleType.driver_time || '--' }}
            </div>
          <div class="w-32 h-32 flex-none image-fit rounded-circle">
            <img alt="" class="rounded-circle img-fluid" :src="vehicleType.vehicle_icon" />
          </div>

          <div class="text-center mt-2 amount text-dark">{{ vehicleType.currency }} {{ vehicleType.total }}</div>          <div class="text-center mt-2">{{ vehicleType.name }}</div>
          </a>
          </span>
        </label>
    </div>
   </div>
  </div>
    <!-- Bottom Modal with Slide Animation -->
    <transition name="slide-up">
    <div v-if="showModal" class="bottom-modal">
      <div class="modal-content">
        <!-- <h5>Booking Confirmation</h5> -->
        <button type="submit" @click="makeBooking" class="btn btn-primary" style="width:50%;margin:auto;">
          {{ $t("make_booking") }}
        </button>
      </div>
    </div>
  </transition>
</div>

<!-- <div class="col-lg-12">
  <div v-if="form.vehicle_type" class="text-center">
  <button type="submit"  @click="makeBooking" class="btn btn-primary">{{$t("make_booking")}}</button>
  </div>
 </div> -->
 
 </div>
</div>

<!-- map  -->
<div class="col-12 col-lg-6">
<div class="mb-3 text-center m-auto">
<div id="map" style="height: 500px;">
<googleMap
    :baseUrl="baseUrl"
    :default_location="default_location"
    :pick_location="pickupLocation"
    :drop_location="dropLocation"
    :nearbyDrivers="driverMarkers"
    :polyline="routePolyline"
    :map_key="map_key"
    :libraries="['marker','geometry']"
>
{{$t("map_loading")}}
</googleMap>
</div>
</div>
</div>  
</div>

</FormValidation>

</form>
</BCardBody>
</BCard>
</BCol>
</BRow>
</BCard>
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

<style scoped>
.autocomplete-container {
  position: relative;
}

.autocomplete-results {
  border: 1px solid #ccc;
  max-height: 200px;
  overflow-y: auto;
  position: absolute;
  width: 100%;
  background-color: white;
  z-index: 1000;
}

.autocomplete-item {
  padding: 5px;
  cursor: pointer;
}

.autocomplete-item:hover {
  background-color: #f0f0f0;
}

.input-group-append {
  display: flex;
  align-items: center;
}

/* Bottom Slide-up Modal */
.bottom-modal {
  position: fixed;
  bottom: 60px;
  left: 0;
  width: 49%;
  background: white;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
  padding: 20px;
  text-align: center;
  transition: transform 0.4s ease-in-out;
  transform: translateY(0);
  z-index: 1000;
}

/* Modal Header */
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

/* Close Button */
.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}

/* Slide-up Transition */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.4s ease-in-out;
}

.slide-up-enter-from {
  transform: translateY(100%);
}

.slide-up-enter-to {
  transform: translateY(0);
}

.slide-up-leave-to {
  transform: translateY(100%);
}
/* Mobile View (480px and below) */
@media (max-width: 480px) {
.bottom-modal {
    position: fixed;
    bottom: 200px;
    left: 0;
    width: 100%;
    background: white;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
    padding: 20px;
    text-align: center;
    transition: transform 0.4s ease-in-out;
    transform: translateY(0);
    z-index: 1000;
}
}
</style>
