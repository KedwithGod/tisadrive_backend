<script>
import { Head } from '@inertiajs/vue3';
import { ref, watch, toRaw, onMounted } from "vue";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Slider from "@vueform/slider";
import { useI18n } from 'vue-i18n';
import googleMap from '@/Components/googleMap.vue';

export default {
    components: {
        Head,
        Layout,
        PageHeader,
        googleMap,
        Slider,
    },
    props: {
        requestData: {
            type: Object,
            default: () => {},
        },
        map_type:String,
        default_location:Object,
        map_key: String,
        baseUrl: String,
    },
    setup(props) {
        const { t } = useI18n();
        const map = ref(null);
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
            heatmapOptions.value = {
                gradient: gradients.value.find(g => g.name === selectedGradient.value).colors,
                opacity: opacity.value/100,
                radius: radius.value,
            };
        })

        const heatmapOptions = ref({
            gradient: gradients.value.find(g => g.name === selectedGradient.value).colors,
            opacity: opacity.value/100,
            radius: radius.value,
        });



        return {
            map,
            heatmapOptions,
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
                        <div style="height: 400px;">
                            <googleMap
                                v-if="map_type == 'google_map'"
                                :default_location="default_location"
                                :baseUrl="baseUrl"
                                :map_key="map_key"
                            >
                                {{$t("map_loading")}}
                            </googleMap>
                        </div>
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
                                    <Slider type="range" v-model="opacity" data-slider-size="lg"/>
                                </div>
                            </div>
                            <!-- end col -->
                            <div class="col-xl-6 col-lg-6 mb-3">
                                <div class="mt-4 mt-lg-0">
                                    <h5 class="fs-14 mb-3">{{$t("radius")}}</h5>
                                    <Slider type="range" v-model="radius" data-slider-size="lg"/>
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
