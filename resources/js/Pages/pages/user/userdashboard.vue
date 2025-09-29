<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, onMounted, watch, computed } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import useVuelidate from "@vuelidate/core";
import { UsersIcon } from '@zhuowenli/vue-feather-icons';
import getChartColorsArray from "@/common/getChartColorsArray";
import { useSharedState } from '@/composables/useSharedState';
import { useI18n } from 'vue-i18n';
import { layoutComputed } from "@/state/helpers";
import { mapGetters } from 'vuex';

export default {
  props: {
    currencySymbol: Object,
    totalUsers: Number,   
    firebaseConfig: Object,

  },
  data() {
    return {
      selectedServiceLocation: null, // To store the selected service location
    };
  },
  computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
    layoutType: {
      get() {
        return this.$store ? this.$store.state.layout.layoutType : {} || {};
      },
    },
  },
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    flatPickr,
    Link,
    UsersIcon
  },
  setup(props) {
    const { t } = useI18n();
    const { playAudioOnce, selectedLocation } = useSharedState();
    const series = ref([]);
    const chartOptions = ref({});
    const overall = ref([]);
    const overallChartOptions = ref({});
    const cancellation = ref([]);
    const cancelChartOptions = ref({});
    const sosRequests = ref([]);
    const seriesOverallTrip = ref([]);
    const chartOptionsOverallTrip = ref({});

    // Fetch data for today earnings chart
    const fetchTodayEarnings = async () => {
      try {
        const response = await axios.get('/dashboard/today-earnings');
        series.value = [
          Number(response.data.today_completed),
          Number(response.data.today_cancelled),
          Number(response.data.today_scheduled),
        ];
        chartOptions.value = {
          labels: [t('completed'), t('cancelled'), t('scheduled')],
          chart: {
            type: "donut",
            height: 219,
          },
          plotOptions: {
            pie: {
              size: 100,
              donut: {
                size: "75%",
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
        };
      } catch (error) {
        console.error(t('error_fetching_today_earnings'), error);
      }
    };

        // Fetch data for overall earnings chart
        const fetchOverallTrips = async () => {
      try {
        const response = await axios.get('/dashboard/today-earnings'); // Same endpoint used
        seriesOverallTrip.value = [
          Number(response.data.overall.completed),
          Number(response.data.overall.cancelled),
          Number(response.data.overall.scheduled),
        ];
        chartOptionsOverallTrip.value = generateChartOptions('overall');
      } catch (error) {
        console.error(t('error_fetching_overall_earnings'), error);
      }
    };

    // Helper function to generate chart options
    const generateChartOptions = (type) => {
      return {
        labels: [t('completed'), t('cancelled'), t('scheduled')],
        chart: {
          type: 'donut',
          height: 219,
        },
        plotOptions: {
          pie: {
            size: 100,
            donut: {
              size: '75%',
            },
          },
        },
        dataLabels: {
          enabled: false,
        },
        legend: {
          show: false,
          position: 'bottom',
          horizontalAlign: 'center',
          markers: {
            width: 20,
            height: 6,
            radius: 2,
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
        colors: getChartColorsArray(
          type === 'today'
            ? '["--vz-primary", "--vz-warning", "--vz-info"]'
            : '["--vz-secondary", "--vz-danger", "--vz-success"]'
        ),
      };
    };

    const sos_update = async(sos) => {
      try {

        const response = await axios.get(`rides-request/detail/${sos.req_id}`);
        if (response.status === 200) {
          let trip = response.data.request;
          let sosData = {
            isUser: sos.is_user,
            isDriver: sos.is_driver,
            userName: trip.userDetail?.data?.name,
            driverName: trip.driverDetail?.data?.name,
            request_id: sos.req_id,
            date: response.data.current_time,
          };
          const existingIndex = sosRequests.value.findIndex(
            (request) => request.request_id === sosData.request_id
          );
          if (existingIndex === -1) {
            sosRequests.value.push(sosData);
          } else {
            sosRequests.value[existingIndex] = sosData;
          }
          playAudioOnce();
            Swal.fire({
              title: t('notified_sos'),
              text: t('sos_has_been_notified_proceed_to_details'),
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#34c38f",
              cancelButtonColor: "#f46a6a",
              confirmButtonText: t('check'),
              cancelButtonText: t('cancel')
          }).then(async (result) => {
              if (result.isConfirmed) {
                  router.get('/rides-request/view/'+sos.req_id);
              }
          });
        }
      }catch (error) {
        console.error(error);
      }
    }


    // Fetch data for overall earnings chart
    const fetchOverallEarnings = async () => {
      try {
        const response = await axios.get('/dashboard/overall-earnings');
        overall.value = [
          {
            name: t('overall_earnings'),
            data: response.data.earnings.values,
          },
        ];
        overallChartOptions.value = {
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
            categories: response.data.earnings.months, // x Axis months
          },
          yaxis: {
            labels: {
              formatter: function (value) {
                return value.toFixed(1);
              },
            },
            tickAmount: 5,
            min: 0,
            max: Math.max(...response.data.earnings.values) * 1.1, // Adjust max value dynamically
          },
          colors: getChartColorsArray('["--vz-success"]'),
          fill: {
            opacity: 0,
            colors: ["#0AB39C", "#F06548"],
            type: "solid",
          },
        };
      } catch (error) {
        console.error(t('error_fetching_overall_earnings'), error);
      }
    };

    // Fetch data for cancellation chart
    const fetchCancellationData = async () => {
      try {
        const response = await axios.get('/dashboard/cancel-chart');
        cancellation.value = [
          {
            name: t('cancelled_due_to_no_drivers'),
            type: "bar",
            data: response.data['a'],
          },
          {
            name: t('cancelled_by_users'),
            type: "bar",
            data: response.data['u'],
          },
          {
            name: t('cancelled_by_drivers'),
            type: "bar",
            data: response.data['d'],
          },
        ];
        cancelChartOptions.value = {
          chart: {
            height: 374,
            type: "line",
            toolbar: {
              show: false,
            },
          },
          stroke: {
            curve: "smooth",
            dashArray: [0, 0, 0],
            width: [0, 0, 0],
          },
          fill: {
            opacity: [1, 1, 1],
          },
          markers: {
            size: [0, 0, 0],
            strokeWidth: 2,
            hover: {
              size: 4,
            },
          },
          xaxis: {
            categories: response.data['y'],
            axisTicks: {
              show: false,
            },
            axisBorder: {
              show: false,
            },
          },
          grid: {
            show: true,
            xaxis: {
              lines: {
                show: true,
              },
            },
            yaxis: {
              lines: {
                show: false,
              },
            },
            padding: {
              top: 0,
              right: -2,
              bottom: 15,
              left: 10,
            },
          },
          legend: {
            show: true,
            horizontalAlign: "center",
            offsetX: 0,
            offsetY: -5,
            markers: {
              width: 9,
              height: 9,
              radius: 6,
            },
            itemMargin: {
              horizontal: 10,
              vertical: 0,
            },
          },
          plotOptions: {
            bar: {
              columnWidth: "30%",
              barHeight: "70%",
            },
          },
          colors: getChartColorsArray('["--vz-primary", "--vz-warning", "--vz-success"]'),
          tooltip: {
            shared: true,
            y: [
              {
                formatter: function (y) {
                  if (typeof y !== "undefined") {
                    return y.toFixed(0);
                  }
                  return y;
                },
              },
              {
                formatter: function (y) {
                  if (typeof y !== "undefined") {
                    return y.toFixed(0);
                  }
                  return y;
                },
              },
              {
                formatter: function (y) {
                  if (typeof y !== "undefined") {
                    return y.toFixed(0);
                  }
                  return y;
                },
              },
            ],
          },
        };
      } catch (error) {
        console.error(t('error_fetching_cancellation_data'), error);
      }
    };

    // Call APIs on component mount
    onMounted(async() => {
      try{
        let shouldProcessSosChildAdded = false;
        setTimeout(()=> {
          shouldProcessSosChildAdded = true;
        },4000);
        await fetchTodayEarnings();
        await fetchOverallEarnings();
        await fetchCancellationData();
        await fetchOverallTrips();
        const firebaseConfig = props.firebaseConfig;
        if (!firebase.apps.length) {
          firebase.initializeApp(firebaseConfig);
        }
        const sosRef = firebase.database().ref('SOS');
        
        sosRef.on('child_changed', async function(snapshot) {
          var sosData = snapshot.val();
          if (shouldProcessSosChildAdded)
          {
            await sos_update(sosData);
          } 
        });
        sosRef.on('child_added', async function(snapshot) {
            var sosData = snapshot.val();
            if (shouldProcessSosChildAdded)
            {
                await sos_update(sosData);
            } 
        });
      }catch (error) {
        console.error(error);
      }
    });

    return {
      series,
      chartOptions,
      overall,
      sosRequests,
      overallChartOptions,
      cancellation,
      cancelChartOptions,
      seriesOverallTrip,
      chartOptionsOverallTrip,
    };
  },

  methods: {},
};
</script>


<template>
  <Layout>
    <PageHeader :title="$t('dashboard')" :pageTitle="$t('dashboard')" />
        <BRow>
            <BCol xl="3" md="6">
              <BCard no-body class="card-animate">
                <BCardBody>
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                      <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                        {{ $t("drivers_registered") }}
                      </p>
                    </div>
                    <div class="flex-shrink-0">
                      <h5 class="text-success fs-14 mb-0">
                        <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                        100%
                      </h5>
                    </div>
                  </div>
                  <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                      <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                      22
                      </h4><br>
                      <!-- <Link href="/approved-drivers" class="text-decoration-underline">{{ $t("view_all") }}</Link> -->
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                      <span class="avatar-title bg-success-subtle rounded fs-3">
                        <i class="bx bx-user-circle text-success"></i>
                      </span>
                    </div>
                  </div>
                </BCardBody>
              </BCard>
            </BCol>

            <BCol xl="3" md="6">
              <BCard no-body class="card-animate">
                <BCardBody>
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                      <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                        {{ $t("approved_drivers") }}
                      </p>
                    </div>
                    <div class="flex-shrink-0">
                      <h5 class="text-success fs-14 mb-0">
                        <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                         20 %
                      </h5>
                    </div>  
                  </div>
                  <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                      <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                        30
                      </h4>
                      <Link href="/approved-drivers" class="text-decoration-underline" v-if="permissions.includes('view-approved-drivers')">{{ $t("view_all") }}</Link>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                      <span class="avatar-title bg-info-subtle rounded fs-3">
                        <i class="bx bx-user-circle text-info"></i>
                      </span>
                    </div>
                  </div>
                </BCardBody>
              </BCard>
            </BCol>

            <BCol xl="3" md="6">
              <BCard no-body class="card-animate">
                <BCardBody>
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                      <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                        {{ $t("drivers_approval_waiting") }}
                      </p>
                    </div>
                    <div class="flex-shrink-0">
                      <h5 class="text-danger fs-14 mb-0">
                        <i class="ri-arrow-right-down-line fs-13 align-middle"></i>
                         20 %
                      </h5>
                    </div>
                  </div>
                  <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                      <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                         30
                      </h4>
                      <Link href="/pending-drivers" class="text-decoration-underline" v-if="permissions.includes('view-approval-pending-drivers')">{{ $t("view_all") }}</Link>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                      <span class="avatar-title bg-warning-subtle rounded fs-3">
                        <i class="bx bx-user-circle text-warning"></i>
                      </span>
                    </div>
                  </div>
                </BCardBody>
              </BCard>
            </BCol>

            <BCol xl="3" md="6">
              <BCard no-body class="card-animate">
                <BCardBody>
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                      <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                        {{ $t("users_registered") }}
                      </p>
                    </div>
                    <div class="flex-shrink-0">
                    </div>
                  </div>
                  <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                      <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                         {{ totalUsers }} 
                      </h4>
                      <Link href="/users" class="text-decoration-underline" v-if="permissions.includes('view-users')">{{ $t("view_all") }}</Link>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                      <span class="avatar-title bg-primary-subtle rounded fs-3">
                        <i class="bx bx-user-circle text-primary"></i>
                      </span>
                    </div>
                  </div>
                </BCardBody>
              </BCard>
            </BCol>
          </BRow>


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
                             200 </h2>
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
                             200 </h2>
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
                             200 </h2>
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
                             200 </h2>                     
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
                             300 </h2>                     
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
                             100 </h2>                                           </div>
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

<!-- Overall Trips Chart -->
<BCol xl="12" md="12">
    <BCard no-body class="card-height-100">
      <BCardHeader class="align-items-center d-flex py-0">
        <BCardTitle class="mb-0 flex-grow-1 p-3">{{ $t("overall_trips") }}</BCardTitle>
      </BCardHeader>
      <BCardBody>
        <apexchart class="apex-charts" dir="ltr" height="219" :series="seriesOverallTrip" :options="chartOptionsOverallTrip"></apexchart>
        <div class="table-responsive mt-3">
          <table class="table table-borderless table-sm table-centered align-middle table-nowrap mb-0">
            <tbody class="border-0">
              <tr>
                <td>
                  <h4 class="text-truncate fs-14 fs-medium mb-0">
                    <i class="ri-stop-fill align-middle fs-18 text-secondary me-2"></i>{{ $t("completed_rides") }}
                  </h4>
                </td>
              </tr>
              <tr>
                <td>
                  <h4 class="text-truncate fs-14 fs-medium mb-0">
                    <i class="ri-stop-fill align-middle fs-18 text-danger me-2"></i>{{ $t("cancelled_rides") }}
                  </h4>
                </td>
              </tr>
              <tr>
                <td>
                  <h4 class="text-truncate fs-14 fs-medium mb-0">
                    <i class="ri-stop-fill align-middle fs-18 text-success me-2"></i>{{ $t("scheduled_rides") }}
                  </h4>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </BCardBody>
    </BCard>
  </BCol>
</BRow>


<!-- total earnings -->

<BRow>
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
                             200 </h2>  
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
                             33 </h2>                     
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
                             22 </h2>                     
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
                             33 </h2>                     
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
                             222 </h2>                     
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
                          <p class="fw-medium text-muted mb-0">{{ $t("drivers_earnings") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                          <span class="counter-value" data-target="97.66">{{currencySymbol}}</span>
                             222 </h2>                     
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
</BRow>


<!-- Cancellation Chart -->
<BRow>
  <BCol xl="6" md="6">
    <BCard no-body>
    <BCardBody class="p-0">
      <BRow class="g-0">
        <BCol xxl="12">
          <div class="">
            <BCardHeader class="align-items-center d-flex">
              <BCardTitle class="mb-0 flex-grow-1">{{ $t("cancellation_chart") }}</BCardTitle>
            </BCardHeader>
            <apexchart class="apex-charts" height="350" dir="ltr" :series="cancellation" :options="cancelChartOptions"></apexchart>
          </div>
        </BCol>       
      </BRow>
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
                          <p class="fw-medium text-muted mb-0">{{ $t("total_request_cancelled") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                                20
                          </h2>
                      </div>
                      <div>
                      <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                          <i class="bx bx-user text-success icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div><!-- end card body -->
          </div> <!-- end card-->
      </div> <!-- end col-->
<!-- 
      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("cancelled_due_to_no_drivers") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                                {{ cancelledtrips.auto_cancelled }}
                          </h2>
                      </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                          <i class="bx bx-user text-danger icon-lg"></i>
                        </span>
                      </div>
                      </div>
                  </div>
              </div>
          </div>
      </div> -->
      <div class="col-md-6">
          <div class="card card-animate">
              <div class="card-body">
                  <div class="d-flex justify-content-between">
                      <div>
                          <p class="fw-medium text-muted mb-0">{{ $t("cancelled_by_users") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                                22
                          </h2>
                      </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle rounded-circle fs-2">
                          <i class="bx bx-user text-warning icon-lg"></i>
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
                          <p class="fw-medium text-muted mb-0">{{ $t("cancelled_by_drivers") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                                11
                          </h2>
                      </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                          <i class="bx bx-user text-info icon-lg"></i>
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
                          <p class="fw-medium text-muted mb-0">{{ $t("dispatcher_cancelled") }}</p>
                          <h2 class="mt-4 ff-secondary fw-semibold">
                                33
                          </h2>
                      </div>
                      <div>
                        <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                          <i class="bx bx-user text-info icon-lg"></i>
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
  </Layout>
</template>