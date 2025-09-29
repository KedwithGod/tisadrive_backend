<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/dispatchmain.vue";
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

export default {
  props: {
    currencySymbol: Object,
    firebaseConfig: Object,

  },
  data() {
    return {
      selectedServiceLocation: null, // To store the selected service location
    };
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
        const firebaseConfig = props.firebaseConfig;
        if (!firebase.apps.length) {
          firebase.initializeApp(firebaseConfig);
        }
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
                        {{ totalDrivers?.total }} 
                      </h4>
                      <Link href="/approved-drivers" class="text-decoration-underline">{{ $t("view_all") }}</Link>
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
                         {{ totalDrivers?.approve_percentage }} %
                      </h5>
                    </div>  
                  </div>
                  <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                      <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                        {{ totalDrivers?.approved }} 
                      </h4>
                      <Link href="/approved-drivers" class="text-decoration-underline">{{ $t("view_all") }}</Link>
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
                         {{ totalDrivers?.decline_percentage }} %
                      </h5>
                    </div>
                  </div>
                  <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                      <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                         {{ totalDrivers?.declined }}
                      </h4>
                      <Link href="/pending-drivers" class="text-decoration-underline">{{ $t("view_all") }}</Link>
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
                         {{ totalUsers ?? 0 }} 
                      </h4>
                      <Link href="/users" class="text-decoration-underline">{{ $t("view_all") }}</Link>
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

<!-- Notified sos -->
          <BRow>
            <BCard no-body>
              <div class="card-header align-items-center d-flex">
                  <h4 class="card-title mb-0 flex-grow-1">{{ $t("notified_sos") }}</h4>
              </div>
              <BCardBody>
                <BCol v-if="sosRequests.length>0">
                  <div class="table-responsive">
                      <table class="table align-middle position-relative table-nowrap">
                          <thead class="table-active">
                              <tr>
                                  <th scope="col"> {{$t("date")}}</th>
                                  <th scope="col"> {{$t("user_name")}}</th>
                                  <th scope="col"> {{$t("driver_name")}}</th>
                                  <th scope="col"> {{$t("user_type")}}</th>
                                  <th scope="col"> {{$t("action")}}</th>
                              </tr>
                          </thead>
                          <tbody v-if="sosRequests.length > 0">
                              <tr v-for="(result, index) in sosRequests" :key="index">
                                  <td>{{ result.date}}</td> 
                                  <td>{{ result.userName}}</td> 
                                  <td>{{ result.driverName }}</td>
                                  <td>
                                      <BBadge class="text-uppercase">{{ result.is_user ? $t('user') : $t('driver') }} </BBadge>
                                  </td>
                                  <td>
                                      <div class="dropdown">
                                          <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                          </a>
                                          <div class="dropdown-menu dropdown-menu-end">
                                              <Link class="dropdown-item" type="button" :href="`rides-request/view/${result.request_id}`" >
                                                  <i class=" bx bx-show-alt align-center text-muted me-2"></i>  {{$t("view")}}
                                              </Link>
                                          </div>
                                      </div>
                                  </td>
                                </tr>
                          </tbody>
                          <tbody v-else>
                              <tr>
                                  <td colspan="10" class="text-center">
                                      <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                      <h5> {{$t("no_data_found")}}</h5>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                  </div>

                </BCol>
                <BCol xl="12" v-else>
                    <div class="mt-auto text-center">
                        <img src="@assets/images/search-file.gif" width="120"alt="no-data" class="img-fluid">
                        <h5>{{ $t("no_data_found") }}</h5>
                    </div>
                </BCol>
              </BCardBody>
            </BCard>
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
                             {{ todayEarnings?.total.toFixed(2) }} </h2>
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
                             {{ todayEarnings?.cash.toFixed(2) }} </h2>
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
                             {{ todayEarnings?.wallet.toFixed(2) }} </h2>
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
                             {{ todayEarnings?.card.toFixed(2) }} </h2>                     
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
                             {{ todayEarnings?.admin_commision.toFixed(2) }} </h2>                     
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
                             {{ todayEarnings?.driver_commision.toFixed(2) }} </h2>                                           </div>
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
                             {{ overallEarnings?.total.toFixed(2) }} </h2>  
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
                             {{ overallEarnings?.cash.toFixed(2) }} </h2>                     
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
                             {{ overallEarnings?.wallet.toFixed(2) }} </h2>                     
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
                             {{ overallEarnings?.card.toFixed(2) }} </h2>                     
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
                             {{ overallEarnings?.admin_commision.toFixed(2) }} </h2>                     
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
                             {{ overallEarnings?.driver_commision.toFixed(2) }} </h2>                     
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
                                {{ cancelledtrips?.total_cancelled }}
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
                                {{ cancelledtrips?.auto_cancelled }}
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
                                {{ cancelledtrips?.user_cancelled }}
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
                                {{ cancelledtrips?.driver_cancelled }}
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
                                {{ cancelledtrips?.dispatcher_cancelled }}
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