<script>
import { onMounted } from 'vue';
import Layout from "@/Layouts/main.vue";
import { useI18n } from 'vue-i18n';
import PageHeader from "@/Components/page-header.vue";

export default {
    components:{
        Layout,
        PageHeader,
    },
    props: {
        zone: String,
        peakzone: Object,
        googleMapKey: String, // Define the googleMapKey prop
    },
    setup(props) {
        const { googleMapKey } = props;
        const { t } = useI18n();
        let map, currentPolygon;

        let polygons = [];

        const zone = JSON.parse(props.zone);


        const initializeMap = () => {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 0, lng: 0 },
                zoom: 10,
            });
            const bounds = new google.maps.LatLngBounds();
                // Adjust map center and zoom to fit the polygon
            zone.forEach((polygon) => {
                console.log(polygon);

            const polygonCoordinates = polygon.map(point => ({
                lat: point.lat,
                lng: point.lng,
            }))


            currentPolygon = new google.maps.Polygon({
                paths: polygonCoordinates,
                editable: false,
                draggable: false,
                map: map,
            });
            polygons.push(currentPolygon);

            currentPolygon.getPath().forEach(coord => bounds.extend(coord));
            })
            map.fitBounds(bounds);

        };

        onMounted(() => {
            if (!googleMapKey) {
                console.error(t('google_map_api_key_is_null_or_undefined'));
                return;
            }


            if (!document.querySelector(`script[src*="maps.googleapis.com/maps/api/js?key=${googleMapKey}"]`)) {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapKey}&libraries=places,visualization`;
                script.async = true;
                script.defer = true;

                script.onload = () => {
                    initializeMap();
                };

                script.onerror = () => {
                    console.error(t('google_maps_script_failed_to_load'));
                };

                document.head.appendChild(script);
            } else {
                initializeMap();
            }
        });

        return {
            peakzone: props.peakzone,
        };
    },
};
</script>


<template>
    <Layout>
        <Head :title="$t('peakzone')" />
        <PageHeader :title="$t('peakzone')" :pageTitle="$t('peakzone')" />
            <div class="col-lg-12">
                <div class="card">
                    <div class="car-header">
                        <div class="card">
                            <div class="card-header">
                                <label>{{ $t('peakzone') }}</label>
                            </div>
                            <div class="card-body">
                                <div class="row mt-4">
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="p-2 border border-dashed rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                        <i class="ri-earth-line"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted mb-1">{{ $t('zone') }}</p>
                                                    <h5 class="mb-0">{{peakzone.zone_name}}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="p-2 border border-dashed rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                        <i class="ri-time-line"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted mb-1">{{ $t('start_time') }}</p>
                                                    <h5 class="mb-0">{{peakzone.start_time}}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="p-2 border border-dashed rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                        <i class="ri-time-line"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted mb-1">{{ $t('end_time') }}</p>
                                                    <h5 class="mb-0">{{peakzone.end_time}}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="p-2 border border-dashed rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                        <i class="ri-map-pin-line"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted mb-1">{{ $t('name') }}</p>
                                                    <h5 class="mb-0">{{peakzone.name}}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="map" style="height: 600px;"></div>
                    </div>
                </div>
            </div>
    </Layout>
</template>


<style scoped>
.text-danger {
    padding-top: 5px;
}
</style>
