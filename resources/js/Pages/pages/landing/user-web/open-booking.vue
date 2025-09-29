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
import { initI18n } from '@/i18n';
import { useI18n } from 'vue-i18n';
import UserWebMenu from "@/Components/UserWebMenu.vue";
import L from "leaflet";
import 'leaflet-routing-machine';
import "leaflet/dist/leaflet.css";

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
default_lat:String,
default_lng:String,
successMessage: String,
alertMessage: String,
validate: Function, // Define the prop to receive the method,
transport_type_for_ride:Array,
ride_type_for_ride:Array,
is_rental:Boolean,
goodsTypes:Array,
user: Object,
firebaseSettings:Object,
transport_type_regular: Array,
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
// Use i18n instance
const { t } = useI18n();
const selectedCountry = ref({
dial_code: props.default_dial_code || '',
flag: props.default_flag || ''
});
const isPocSame = ref(false);

  const currentLat = ref(parseFloat(props.default_lat));
  const currentLng = ref(parseFloat(props.default_lng));
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


  });
const unit = ref('-');
// For Airport
const showAiportTerminal = ref(false);
const showTrainStationEnterance = ref(false);


watch(isPocSame, (newVal) => {
      if (newVal) {
        form.drop_poc_name = form.name;
        form.drop_poc_mobile = form.mobile;
      } else {
        form.drop_poc_name = '';
        form.drop_poc_mobile = '';
      }
    });

    watch(
      () => form.name,
      (newVal) => {
        if (isPocSame.value) {
          form.drop_poc_name = newVal;
        }
      }
    );

    watch(
      () => form.mobile,
      (newVal) => {
        if (isPocSame.value) {
          form.drop_poc_mobile = newVal;
        }
      }
    );
    watch(
      () => form.vehicle_type,
      (value) =>{
        fetchNearbyDrivers({ lat: etaParam.pick_lat , lng: etaParam.pick_lng});
      }
    )

const pickupMarker = ref(null); // Define pickupMarker in setup
const dropMarker = ref(null); // Define dropMarker in setup
const stopMarkers = ref({}); // Define dropMarker in setup
const pickupSuggestions = ref([]); // Store pickup suggestions
const stopSuggestions = ref([]); // Store stop suggestions
const dropSuggestions = ref([]); // Store stop suggestions
let control = null;

const validationRules = {
pick_address: { required: true },
mobile: { required: true },
vehicle_type: { required: true }
};
const errors = ref({});
const validationRef = ref(null);
const vehicleTypes = ref([]);

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


const loadVehicleTypes = async () => {
const { pick_lat, pick_lng, distance, duration, drop_lat, drop_lng } = etaParam.data();

const payload = {
  pick_lat,
  pick_lng,
  distance,
  duration,
  if_dispatch: 1,
  transport_type: form.transport_type,
};

if (drop_lat) {
  payload.drop_lat = drop_lat;
}

if (drop_lng) {
  payload.drop_lng = drop_lng;
}

if (stopovers.length > 0) {
  payload.stops = JSON.stringify(stopovers);
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
};

const modalShow = ref(false);

const successMessage = ref(props.successMessage || '');
const alertMessage = ref(props.alertMessage || '');

const dismissMessage = () => {
successMessage.value = "";
alertMessage.value = "";
};

const stopovers = reactive([]);
const maxStopovers = 5;

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


const addStopover = () => {

if (stopovers.length < maxStopovers) {


stopovers.push({ address: "", latitude: null, longitude: null });

} else {
Swal.fire(t('maximum_stopovers_reached'), t('you_can_add_up_to_5_stopovers_only'), 'warning');
}
};

const removeStopover = (index) => {
if (stopMarkers.value?.[index]) {
    stopMarkers.value[index].remove();
}

stopovers.splice(index, 1);

drawRoute();
};

const drawRoute = () => {
      if (!pickupMarker.value || !dropMarker.value) return;
      if (!pickupMarker.value.getLatLng() || !dropMarker.value.getLatLng()) return;
      loadVehicleTypes();
        const bounds = L.latLngBounds([
                pickupMarker.value.getLatLng(),
                ...stopovers.map(stop => L.latLng(stop.latitude, stop.longitude)),
                dropMarker.value.getLatLng(),
            ]);
        map.value.fitBounds(bounds, { padding: [50, 50] });
        if (control !== null && map.value) {
            map.value.removeControl(control);
        }


        control = L.Routing.control({
            waypoints: [
                pickupMarker.value.getLatLng(),
                ...stopovers.map(stop => L.latLng(stop.latitude, stop.longitude)),
                dropMarker.value.getLatLng(),
            ],
            routeWhileDragging: false,
            createMarker: function() { return null; },
            addWaypoints: false,
            lineOptions: {
              styles: [{ color: 'blue', weight: 4 }]
            },

        }).on('routesfound', function(e) {
            const route = e.routes[0];
            etaParam.distance = route.summary.totalDistance;
            etaParam.duration = route.summary.totalTime;
        }).addTo(map.value);
    };


const createPickupMarker = (latlng = {lat : currentLat.value ,lng : currentLat.value}) => {
      if (pickupMarker.value) {
        pickupMarker.value.remove();
      }

      const pickupIcon = L.icon({
        iconUrl: '/image/map/pickup.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      });

      pickupMarker.value = L.marker([latlng.lat, latlng.lng], { icon: pickupIcon, draggable:false } ).addTo(map.value);
        if(dropMarker.value){
          drawRoute();
        }
    };


    const createDropMarker = (latlng = {latitude : currentLat.value, longitude : currentLat.value}) => {
      if (dropMarker.value) {
        dropMarker.value.remove();
      }

      const dropIcon = L.icon({
        iconUrl: '/image/map/drop.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      });
      dropMarker.value = L.marker([latlng.lat, latlng.lng], { icon:  dropIcon, draggable:false } ).addTo(map.value);

        if(pickupMarker.value){
          drawRoute();
        }
    };

    const fetchPickupSuggestions = async () => {
      const query = form.pick_address;
      if (query.length > 2) {
        const response = await axios.get(
          `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`
        );
        pickupSuggestions.value = response.data.map((suggestion) => ({
          display_name: suggestion.display_name,
          lat: suggestion.lat,
          lon: suggestion.lon,
        }));
      } else {
        pickupSuggestions.value = [];
      }
    };

    const fetchDropSuggestions = async () => {
      const query = form.drop_address;
      if (query.length > 2) {
        const response = await axios.get(
          `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`
        );
        dropSuggestions.value = response.data.map((suggestion) => ({
          display_name: suggestion.display_name,
          lat: suggestion.lat,
          lon: suggestion.lon,
        }));
      } else {
        dropSuggestions.value = [];
      }
    };

    const fetchStopSuggestions = async (index) => {
      const query = stopovers[index].address;
      if (query.length > 2) {
        const response = await axios.get(
          `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`
        );
        stopSuggestions.value[index] = response.data.map((suggestion) => ({
          display_name: suggestion.display_name,
          lat: suggestion.lat,
          lon: suggestion.lon,
        }));
      } else {
        stopSuggestions.value[index] = [];
      }
    };

    const selectPickupSuggestion = (suggestion) => {
      form.pick_address = suggestion.display_name;
      const position = { lat: parseFloat(suggestion.lat), lng: parseFloat(suggestion.lon) };
      pickupSuggestions.value = [];

      etaParam.pick_lat = position.lat;
      etaParam.pick_lng = position.lng;
      if (pickupMarker.value) {
        pickupMarker.value.setLatLng(position);
        map.value.setView(position, 12);
      } else {
        map.value.setView(position, 12);
        createPickupMarker(position);
        fetchNearbyDrivers(position);
      }
      if (dropMarker.value) {
        drawRoute();
        serviceVerify();
      }
      loadVehicleTypes();
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

    const selectDropSuggestion = (suggestion) => {
      form.drop_address = suggestion.display_name;
      const position = { lat: parseFloat(suggestion.lat), lng: parseFloat(suggestion.lon) };
      dropSuggestions.value = [];
      etaParam.drop_lat = position.lat;
      etaParam.drop_lng = position.lng;

      if (dropMarker.value) {
        dropMarker.value.setLatLng(position);
      } else {
        map.value.setView(position, 12);
        createDropMarker(position);
      }
      if (pickupMarker.value) {
        serviceVerify();
        drawRoute();
      }
    };

    const addStopMarker = (index , position) => {
      if (stopMarkers.value?.[index]) {
        stopMarkers.value[index].remove();
      }

      const stopIcon = L.icon({
        iconUrl: '/image/map/'+index+'.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      });

      const stopMarker = L.marker([position.lat, position.lng], { icon: stopIcon, draggable:false } ).addTo(map.value);

      return stopMarker;
    }

    const selectStopSuggestion = (index, suggestion) => {
        console.log(stopovers[index]);
        
      stopovers[index].address = suggestion.display_name;
      stopovers[index].latitude = suggestion.lat;
      stopovers[index].longitude = suggestion.lon;
      const position = { lat: parseFloat(suggestion.lat), lng: parseFloat(suggestion.lon) };
      const marker = addStopMarker(index,position);
      stopMarkers[index] = marker;
      stopSuggestions.value[index] = [];
      drawRoute();
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
    const fetchNearbyDrivers = async (pickupLocation) => {
    const driversRef = firebase.database().ref('drivers');
    vehicleTypes.value.forEach((vehicleType) => {
      vehicleType.driver_time = null;
      vehicleType.driver_distance = null;
    });
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
          if(distance <= 10){
            setEtaTime(driver,distance);
          }
          if (distance <= 10 && driver.is_available && driver.is_active && flag) {
            const driverLatLng = L.latLng(driverLocation.lat, driverLocation.lon);
            const vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;

            const icon = L.icon({
              iconUrl: vehicleTypeIconUrl,
              iconSize: [30, 30],
            });
            if (driverMarkers[driver.id]) {
              animateDriverMovement(driverMarkers[driver.id], driverMarkers[driver.id].getLatLng(), driverLatLng, driver.bearing,vehicleTypeIconUrl);
              driverMarkers[driver.id].setIcon(icon);
            } else {
              const driverMarker = L.marker(driverLatLng, {
                icon: icon
              }).addTo(map.value);
              driverMarkers[driver.id] = driverMarker;
              animateDriverMovement(driverMarker, driverMarker.getLatLng(), driverLatLng, driver.bearing,vehicleTypeIconUrl);
            }

          } else if (driverMarkers[driver.id]) {
            driverMarkers[driver.id].remove();
            delete driverMarkers[driver.id];
          }
        }
      });
    });
    driversRef.on('child_changed', (childSnapshot) => {
      const driver = childSnapshot.val();
        const driverGeoHash = driver.g;
        const driverLocation = decodeGeohash(driverGeoHash);
        if (!driver.is_active && driverLocation) {
        vehicleTypes.value.forEach((vehicleType) => {
          if (driver.vehicle_types.some(type => type === vehicleType.id)) {
            fetchNearbyDrivers({lat :etaParam.pick_lat ,lng : etaParam.pick_lng});
          }
        });
      }

      if(driver.lat && driver.lng)
      updateDriverMarker(driver);
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
  const setEtaTime = (driver,distance) => {

    const time = calculateTime(distance);
    vehicleTypes.value.forEach((vehicleType) => {
    if (driver.vehicle_types && driver.vehicle_types.includes(vehicleType.type_id) && (!vehicleType.driver_distance || vehicleType.driver_distance > distance)) {
        vehicleType.driver_time = time;
      }
    });
  }
// Initialize Map
const initializeMap = async () => {

  if (map.value) {
    map.value.remove(); // Removes the map instance and unbinds all event listeners
    map.value = null; // Set map to null for a fresh initialization
  }
  map.value = L.map('map',{
      zoomControl: false,
      dragging: false,
      scrollWheelZoom: false,
      doubleClickZoom: false,
      touchZoom: false
  }).setView([currentLat.value, currentLng.value], 12);
  
  map.value.dragging.disable();
  map.value.scrollWheelZoom.disable();
  map.value.doubleClickZoom.disable();
  map.value.touchZoom.disable();

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
  }).addTo(map.value);

if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
        function(position) {
            currentLat.value = position.coords.latitude;
            currentLng.value = position.coords.longitude;
            map.value.setView({lat: currentLat.value, lng: currentLng.value},12);
        },
        function(error) {
            console.error("Error getting location: ", error);
        },
        {
            // Options (optional)
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        }
        );
    }

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

const { pick_lat, pick_lng, drop_lat, drop_lng } = etaParam.data();

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

console.log("booking-info");
console.log(form.data());


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
          vehicleTypes.value = [];
          await initializeMap();

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
         await initializeMap();
      })
onMounted(() => {


    var firebaseConfig = {
        apiKey: props.firebaseSettings['firebase_api_key'],
        authDomain: props.firebaseSettings['firebase_auth_domain'],
        databaseURL: props.firebaseSettings['firebase_database_url'],
        projectId: props.firebaseSettings['firebase_project_id'],
        storageBucket:  props.firebaseSettings['firebase_storage_bucket'],
        messagingSenderId: props.firebaseSettings['firebase_messaging_sender_id'],
        appId: props.firebaseSettings['firebase_app_id'],
    };
    if(!firebase.apps.length){

        firebase.initializeApp(firebaseConfig);
    }

    if (props.transport_type_for_ride.length > 0) {
    form.transport_type = props.transport_type_for_ride[0];
    }

    if (props.ride_type_for_ride.length > 0) {
    form.ride_type = props.ride_type_for_ride[0];
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
searchQuery,
filteredCountries,
selectCountry,
validateNumber,
pickupSuggestions,
dropSuggestions,
stopSuggestions,
fetchPickupSuggestions,
fetchDropSuggestions,
fetchStopSuggestions,
selectPickupSuggestion,
selectDropSuggestion,
selectStopSuggestion,
loadVehicleTypes,
etaParam,
vehicleTypes,
makeBooking,
stopovers,
addStopover,
removeStopover,
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
                        v-for="(type, index) in transport_options" 
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
            <input type="text" id="pickup" class="form-control" v-model="form.pick_address" @input="fetchPickupSuggestions" :placeholder="$t('enter_pickup')">
            <ul v-if="pickupSuggestions.length > 0">
                <li  v-for="(suggestion, index) in pickupSuggestions" :key=index @click="selectPickupSuggestion(suggestion)"
                 :value="suggestion.display_name">{{ suggestion.display_name }}</li>
            </ul>
            
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
              <input type="text" class="form-control mx-1" v-model="stop.address" @input="fetchStopSuggestions(index)" :placeholder="'Enter stop ' + (index + 1)" :id="`stop-${index}`" >
              <i class="bx bx-trash text-danger fs-22 btn" @click="removeStopover(index)"></i>
            </div>
            <ul v-if="stopSuggestions[index]?.length > 0">
                <li v-for="(suggestion, key) in stopSuggestions[index]" :key=key @click="selectStopSuggestion(index,suggestion)"
                 :value="suggestion.display_name">{{ suggestion.display_name }}</li>
            </ul>
          <span v-if="form.errors[`stop-${index}`]" class="text-danger">{{ form.errors[`stop-${index}`] }}</span>
        </div>
    </div>
  <div class="col-12" v-if="form.ride_type=='regular'">
    <div class="mb-3">
      <label for="drop" class="form-label">{{$t("drop_location")}}</label>
      <input type="text" class="form-control" v-model="form.drop_address"  @input="fetchDropSuggestions" :placeholder="$t('enter_drop')" id="drop" >
            <ul v-if="dropSuggestions.length > 0">
                <li v-for="(suggestion, index) in dropSuggestions" :key=index @click="selectDropSuggestion(suggestion)"
                 :value="suggestion.display_name">{{ suggestion.display_name }}</li>
            </ul>
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
        <div v-if="vehicleTypes.length > 0" class="col-12">
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
          <div class="text-center mt-2 amount text-dark">{{ vehicleType.currency }} {{ vehicleType.total }}</div>
          <div class="text-center mt-2">{{ vehicleType.name }}</div>
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
<div id="map" style="height: 500px;"> {{$t("map_loading")}}</div>
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
/* ul {
    list-style-type: none;
    max-height: 150px;
    overflow-x: auto; Enable horizontal scrolling if needed
    overflow-y: auto; Enable vertical scrolling if needed
    border: 1px solid #ccc; Optional styling for visualization
} */
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

.leaflet-routing-container {
  display:none
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
