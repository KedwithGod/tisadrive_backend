<script>
import { ref, watch, onMounted } from "vue";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Slider from "@vueform/slider";
import { useI18n } from 'vue-i18n';

export default {
    components: {
        Layout,
        PageHeader,
        Slider,
    },
    props: {
        requestData: {
            type: Array,
            default: () => [],
        },
        default_lat:String,
        default_lng:String,
        map_key: String,
    },
    setup(props) {
        const { t } = useI18n();
        const map = ref(null);
        const heatmap = ref(null);
        const selectedGradient = ref('Default');
        const opacity = ref(70);
        const radius = ref(40);

        const gradients = ref([
            {
                name: "Default",
                colors: [
                    'rgba(0, 255, 255, 0)',
                    'rgba(0, 255, 255, 1)',
                    'rgba(0, 191, 255, 1)',
                    'rgba(0, 127, 255, 1)',
                    'rgba(0, 63, 255, 1)',
                    'rgba(0, 0, 255, 1)',
                    'rgba(63, 0, 255, 1)',
                    'rgba(127, 0, 255, 1)',
                    'rgba(191, 0, 255, 1)',
                    'rgba(255, 0, 255, 1)',
                    'rgba(255, 0, 191, 1)',
                    'rgba(255, 0, 127, 1)',
                    'rgba(255, 0, 63, 1)',
                    'rgba(255, 0, 0, 1)',
                ]
            },
            {
                name: "Warm",
                colors: [
                    'rgba(255, 255, 0, 0)',
                    'rgba(255, 255, 0, 1)',
                    'rgba(255, 191, 0, 1)',
                    'rgba(255, 127, 0, 1)',
                    'rgba(255, 63, 0, 1)',
                    'rgba(255, 0, 0, 1)',
                    'rgba(191, 0, 0, 1)',
                    'rgba(127, 0, 0, 1)',
                ]
            },
            {
                name: "Cool",
                colors: [
                    'rgba(0, 255, 255, 0)',
                    'rgba(0, 255, 255, 1)',
                    'rgba(0, 191, 255, 1)',
                    'rgba(0, 127, 255, 1)',
                    'rgba(0, 63, 255, 1)',
                    'rgba(0, 0, 255, 1)',
                ]
            }
        ]);

        watch([opacity, radius, selectedGradient], ()=>{
            updateHeatmap();
        })

        const updateHeatmap = () => {
            if (heatmap.value) {
                heatmap.value.setOptions({
                    gradient: gradients.value.find(g => g.name === selectedGradient.value).colors,
                    opacity: opacity.value/100,
                    radius: radius.value,
                });
            }
        };

        const initializeHeatmap = async() => {
            const mapOptions = {
                center: { lat: parseFloat(props.default_lat), lng: parseFloat(props.default_lng) },
                zoom: 13,
                mapTypeId: 'roadmap',
            };

            map.value = new google.maps.Map(document.getElementById('map'), mapOptions);

            if (props.requestData.length) {
                const heatmapData = props.requestData.map(item => {
                    return new google.maps.LatLng(item.pick_lat, item.pick_lng);
                });

                heatmap.value = new google.maps.visualization.HeatmapLayer({
                    data: heatmapData,
                    gradient: gradients.value.find(g => g.name === selectedGradient.value).colors,
                    opacity: opacity.value/100,
                    radius: radius.value,
                });

                heatmap.value.setMap(map.value);
            }
        };

onMounted(async() => {
    const mapKey = props.map_key;

    // Check if the script is already added to avoid adding it multiple times
    if (!document.querySelector(`script[src*="maps.googleapis.com/maps/api/js?key=${mapKey}"]`)) {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${mapKey}&libraries=places,visualization`;
        script.async = true;
        script.defer = true;

        script.onload = () => {
            initializeHeatmap();
        };

        script.onerror = () => {
            console.error(t('google_maps_script_failed_to_load'));
        };

        document.head.appendChild(script);
    } else {
        // If script is already loaded, initialize the heatmap directly
        initializeHeatmap();
    }
});


        return {
            map,
            heatmap,
            gradients,
            selectedGradient,
            opacity,
            radius,
        };
    },
};
</script>

<template>
    <Layout>
        <Head title="Heat Map" />
        <PageHeader :title="$t('heat_map')" :pageTitle="$t('map')"/>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div id="map" style="height: 400px;">{{$t("map_loading")}}</div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
        <div class="row">  
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">{{$t("visibility")}}</h4>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 mb-5">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="gradient" class="form-label fs-14">{{$t("gradient")}}</label>
                                        <select class="form-select" v-model="selectedGradient">
                                            <option v-for="gradient in gradients" :key="gradient.name" :value="gradient.name">
                                                {{ gradient.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 mb-3">
                                <div>
                                    <h5 class="fs-14 mb-3">{{$t("opacity")}}</h5>
                                    <Slider :type="range" v-model="opacity" data-slider-size="lg"/>
                                </div>
                            </div>
                            <!-- end col -->
                            <div class="col-xl-6 col-lg-6 mb-3">
                                <div class="mt-4 mt-lg-0">
                                    <h5 class="fs-14 mb-3">{{$t("radius")}}</h5>
                                    <Slider :type="range" v-model="radius" data-slider-size="lg"/>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>
            <!-- end col -->
        </div>
    </Layout>
</template>

<style>
#map {
    height: 400px;
    margin-bottom: 10px;
}

.controls {
    display: flex;
    flex-direction: column;
    margin-top: 10px;
}

.controls label {
    margin-top: 5px;
}

.controls input[type="range"],
.controls select {
    width: 100%;
    margin-bottom: 10px;
}
</style>
