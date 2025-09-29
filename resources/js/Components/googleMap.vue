<script>
import { ref, onMounted, watch } from "vue";
import { GoogleMap, AdvancedMarker, Polyline, InfoWindow } from 'vue3-google-map'

export default {
    name: 'googleMap',
    components: {
        GoogleMap,
        Polyline,
        AdvancedMarker,
        InfoWindow,
    },
    props: {
        pick_location:Object,
        default_location:Object,
        drop_location:Object,
        polyline: String,
        map_key: String,
        baseUrl: String,
        current_location:Object,
        libraries:Array,
        stops:{
            type:Array,
            default:[],
        },
        nearbyDrivers:{
            type:Object,
            default: {},
        },
        draggable:{
            type:Boolean,
            default:false,
        },
    },
    setup(props){
        const mapRef = ref(null);

        const default_location = ref(props.default_location);
        const pick_location = ref(props.pick_location);
        const drop_location = ref(props.drop_location);
        const stops = ref(props.stops);
        const drivers = ref(props.nearbyDrivers);
        
        const driverOptions = ref([]);


        const pickOption = ref(null);
        const dropOption = ref(null);
        const stopOptions = ref([]);

        const bounds = ref(null);

        const polyLine = ref(props.polyline);
        const pathCoordinates = ref([]);


        const onMapLoad = async () =>{

            if (!window.google || !window.google.maps) {
                console.warn("Google Maps API not yet loaded");
                return;
            }
            const GoogleMap = window.google.maps;

            stopOptions.value = [];
            driverOptions.value = [];
            pathCoordinates.value = [];
            bounds.value = new GoogleMap.LatLngBounds();

            if(pick_location.value && drop_location.value){
                bounds.value = new GoogleMap.LatLngBounds();
            }

            if(pick_location.value) {

                const pickupIcon = document.createElement('img');
                pickupIcon.src = props.baseUrl+'/image/map/pickup.png';
                pickupIcon.style.width = '30px';
                pickupIcon.style.height = '30px';

                const pickPosition = props.pick_location;
                
                pickOption.value = {
                    content:pickupIcon,
                    position: pickPosition,
                    gmpDraggable:props.draggable,
                };
                
                if(pick_location.value){
                    bounds.value.extend(pickPosition);
                }
                
            }else{
                pickOption.value = null;
            }

            if(drop_location.value) {

                const dropIcon = document.createElement('img');
                dropIcon.src = props.baseUrl+'/image/map/drop.png';
                dropIcon.style.width = '30px';
                dropIcon.style.height = '30px';

                const dropPosition = drop_location.value;

                if(drop_location.value){
                    bounds.value.extend(dropPosition);
                }

                dropOption.value = {
                    content:dropIcon,
                    position: dropPosition,
                    gmpDraggable:props.draggable,
                };
            }else{
                dropOption.value = null;
            }
            
            if(stops.value){
                stops.value.forEach((stop,index) => {


                    const stopIcon = document.createElement('img');
                    stopIcon.src = props.baseUrl+'/image/map/'+index+'.png';
                    stopIcon.style.width = '30px';
                    stopIcon.style.height = '30px';
                    
                    const markerOption = {
                        position: { lat: stop.lat, lng: stop.lng },
                        content:stopIcon,
                        gmpDraggable:props.draggable,
                    }


                    if(stop.lat && stop.lng){
                        bounds.value.extend({ lat: stop.lat, lng: stop.lng });
                    }

                    stopOptions.value.push({options: markerOption});

                });

            }else{
                stopOptions.value = [];
            }
            if(polyLine.value){
                
                // Decode polyline -> returns array of [lat, lng] pairs
                const decodedPath = new GoogleMap.geometry.encoding.decodePath(polyLine.value);

                // Convert to array of { lat, lng } objects
                pathCoordinates.value = decodedPath.map((path) => ({ lat: path.lat(), lng:path.lng() }));


                if(pick_location.value && drop_location.value){
                    pathCoordinates.value.forEach(coord => bounds.value.extend(coord));
                }


            }else{
                pathCoordinates.value = []
            }
            if (drivers.value && Object.keys(drivers.value).length > 0) {
                Object.values(drivers.value).forEach((driver) => {
                    const driverIcon = document.createElement('img');
                    driverIcon.src = props.baseUrl + `/image/map/${driver.type_icon}.png`;
                    driverIcon.style.width = '30px';
                    driverIcon.style.height = '30px';

                    if (driver.rotation !== undefined) {
                        driverIcon.style.transform = `rotate(${driver.rotation}deg)`;
                    }

                    const markerOption = {
                        position: { lat: driver.lat, lng: driver.lng },
                        content: driverIcon,
                    };

                    driverOptions.value.push({ options: markerOption, showInfo: false, });
                });
            }else{
                driverOptions.value = []
            }

            if(pick_location.value || drop_location.value){
                mapRef.value.map.fitBounds(bounds.value);
            }

        }
        watch(()=> mapRef.value?.ready, async (ready)=>{
            if(ready){
                await onMapLoad();
            }
        })
        watch(() => props.pick_location, (newVal) => {
            pick_location.value = newVal;
            onMapLoad()
        });

        watch(() => props.drop_location, (newVal) => {
            drop_location.value = newVal;
            onMapLoad()
        });

        watch(() => props.stops, (newVal) => {
            stops.value = newVal;
            onMapLoad()
        });

        watch(() => props.nearbyDrivers, (newVal) => {
            drivers.value = newVal;
            onMapLoad()
        }, { deep: true });

        watch(() => props.polyline, () => {
            polyLine.value = props.polyline;
            onMapLoad()
        });



        const handleDragEnd = (event,type,index=null) => {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            const position = {lat:lat,lng:lng};


            if(type == 'pickup'){
                pickOption.value.position= position;
            }else if(type == 'drop'){
                dropOption.value.position= position;
            }else{
                stopOptions.value[index].position = position;
            }
        }
        return {
            default_location,
            pickOption,
            dropOption,
            stopOptions,
            pathCoordinates,
            driverOptions,
            handleDragEnd,
            mapRef,
        }
    }
};
</script>

<template>
    <div class="map-container">
        <GoogleMap
            ref="mapRef"
            :api-key="map_key"
            mapId="DEMO_MAP_ID"
            style="width: 100%; height: 100%"
            :center="default_location"
            :zoom="12"
            :libraries="libraries"
        >
            

            <AdvancedMarker
                v-if="pickOption"
                :options="pickOption"
                @dragend="(e) => handleDragEnd(e, 'pickup')"
            />


            <AdvancedMarker
                v-if="dropOption"
                :options="dropOption"
                @dragend="(e) => handleDragEnd(e, 'drop')"
            />



            <!-- Stops -->
            <template v-for="(stop, i) in stopOptions" :key="i">
            <AdvancedMarker
                :options="stop.options"
                @dragend="e => handleDragEnd(e,'stop', i)"
            />
            </template>

            <!-- Nearby Drivers -->
            <template v-for="(drv, i) in driverOptions" :key="'drv'+i">
            <AdvancedMarker :options="drv.options" @click="drv.showInfo = !drv.showInfo">
                <InfoWindow v-if="drv.showInfo" :options="{content: drv.info}" :open="true"/>
            </AdvancedMarker>
            </template>

            
            <Polyline
                v-if="pathCoordinates.length"
                :options="{
                path: pathCoordinates,
                strokeColor: '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 4
                }"
            />


        </GoogleMap>
    </div>
</template>

<style>
.map-container {
  width: 100%;
  height: 100%;
  position: relative;
}
</style>
