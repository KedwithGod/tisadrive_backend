<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref , onMounted} from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import searchbar from "@/Components/widgets/searchbar.vue";
import ImageModal from "@/Components/ImageModal.vue";
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";
import { useI18n } from 'vue-i18n';
export default {
    data() {
        return {
            rightOffcanvas: false,
            SearchQuery: '',
            selectedColor: "#0ab39c", // Default color
        };
    },
    methods: {
    editData(result) {
      this.$inertia.get(this.route('landing_header.edit', { id: result.id }));
    },
    stripHtmlTags(content) {
      const parser = new DOMParser();
      const parsedContent = parser.parseFromString(content, 'text/html');
      return parsedContent.body.textContent || "";
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
        searchbar,
        ImageModal

    },
    props: {
        successMessage: String,
        alertMessage: String,

        landingHeader: {
            type: Object,
            required: true,
        },


    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref('');
        const filter = useForm({
            transport_type: null,
            dispatch_type: null,
            status: null,
            limit:10
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const paginatorOption = ref({}); // Spread the results to make them reactive

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const toggleActiveStatus = async (id, status) => {
            try {
                await axios.post(`/landing-header/update-status`, { id: id, status: status });
                const index = results.value.findIndex(item => item.id === id);
                if (index !== -1) {
                    results.value[index].active = status; // Update the active status locally
                }
                // Optionally, you may want to re-fetch all data to ensure consistency
                // fetchDatas(); 
            } catch (error) {
                console.error(t('error_updating_status'), error);
            }
        };

        const filterData = () => {
            modalFilter.value = true;
        };


        const clearFilter = () => {
            filter.reset();
            fetchDatas();
        };


        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/landing-header/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('landingHeader_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_landingHeader'), 'error');
            }
        };

        const deleteModal = async (itemId) => {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Yes, delete it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await deleteData(itemId);
                    } catch (error) {
                        console.error(t('error_deleting_data'), error);
                        Swal.fire(t('error'), t('failed_to_delete_the_data'), "error");
                    }
                }
            });
        };

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/landing-header/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_landing_header'), error);
            }
        };

        const handlePageChanged = async (page) => {
          localStorage.setItem("/landing-header/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/landing-header/edit/${result.id}`); 
        };

        const settings = ref({
      landing_header_bg_color: "",
      landing_header_text_color: "",
      landing_header_active_text_color: "",
      landing_footer_bg_color: "",
      landing_footer_text_color: "",
    });
    // const successMessage = ref("");

    // Fetch initial settings from the backend
    const fetchSettings = async () => {
      try {
        const response = await axios.get("/landing-header/get-color-settings");
        settings.value = response.data.settings;
      } catch (error) {
        console.error("Failed to fetch settings:", error.response?.data || error);
      }
    };

    // Update settings on the backend
    const updateSettings = async () => {
      try {
        const response = await axios.post(
          "/landing-header/update-color-settings",
          settings.value
        );
        successMessage.value = response.data.successMessage;

        // Optionally, refetch settings to ensure the UI reflects the latest data
        fetchSettings();
      } catch (error) {
        console.error("Failed to update settings:", error.response?.data || error);
      }
    };

    onMounted(() => {
      fetchSettings();
    });
    const updatePaginatorOptions = () => {
        paginatorOption.value = [10, 25, 50, 100,200,500]; // Default static options
    };
    // **Handle per-page changes**
    const changeEntriesPerPage = () => {
        fetchDatas(); // Fetch new data
    };


        return {
            results,
            modalShow,
            deleteItemId,
            successMessage,
            alertMessage,
            filterData,
            deleteModal,
            closeModal,
            deleteData,
            dismissMessage,
            searchTerm,
            fetchSearch,
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            filter,
            handlePageChanged,
            toggleActiveStatus,
            editData,
            settings,
            updateSettings,
            paginatorOption,
            changeEntriesPerPage
        };
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
  mounted() {
        this.fetchDatas();
        const savedPage = localStorage.getItem("/landing-header/list");
        if(savedPage){
            this.handlePageChanged(Number(savedPage));
        }
        else{
            this.handlePageChanged(1);
        }
    },
};
</script>

<template>
    <Layout>

        <Head title="Landing Header" />
        <PageHeader :title="$t('index')" :pageTitle="$t('landing_header_footer')" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
             
                    <BCardHeader class="border-0">
                        <BRow class="g-2">
                            <div class="card-body">
                                        <h4 class="card-title mb-4">{{$t("header_footer_color")}}</h4>
                                        <div class="d-flex align-items-center">
                                        <div class="color-picker me-3">
                                      <!-- Color Picker -->
                                      <label for="colorPicker" class="visually-hidden">Choose a Color</label>
                                      <input
                                        type="color"
                                        id="colorPicker"
                                        v-model="selectedColor"
                                        class="rounded-color-picker"
                                      />

                                      <!-- Hex Code Display -->
                                      <label for="colorCode" class="visually-hidden">Color Code</label>
                                      <input
                                        type="text"
                                        id="colorCode"
                                        v-model="selectedColor"
                                        class="color-code-input"
                                        readonly
                                      />
                                    </div>
                                    <div>("You can choose and copy color code from here and paste to below input fields")</div>
                                  </div>
                                    </div>
                                    
                                    
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div>
                        <form @submit.prevent="updateSettings">
                            <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="landing_header_bg_color" class="form-label">{{$t("landing_header_bg")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('landing_header_bg')" id="landing_header_bg_color" v-model="settings.landing_header_bg_color" />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="landing_header_text_color" class="form-label">{{$t("landing_header_text")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('landing_header_text')" id="landing_header_text_color" v-model="settings.landing_header_text_color" />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="landing_header_active_text_color" class="form-label">{{$t("landing_header_act_text")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('landing_header_act_text')" id="landing_header_active_text_color" v-model="settings.landing_header_active_text_color" />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="landing_footer_bg_color" class="form-label">{{$t("landing_footer_bg")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('landing_footer_bg')" id="landing_footer_bg_color" v-model="settings.landing_footer_bg_color" />
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="landing_footer_text_color" class="form-label">{{$t("landing_footer_text")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('landing_footer_text')" id="landing_footer_text_color" v-model="settings.landing_footer_text_color" />
                    </div>
                  </div>
                  <div class="col-lg-12" v-if="permissions.includes('add_header_footer')">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ settings ? $t('update') : $t('save') }}</button>
                    </div>
                  </div>
                </div>
        </form>
    </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0">
                        <BRow class="g-2">
                            <BCol class="col-12" md="6">
                                <div class="card" height="100px">
                                            <ImageModal imageSrc="/images/header.png" imageAlt="Header-Footer" width="100px" />
                                    <div class="card-body">
                                        <h4 class="card-title mb-2">{{$t("header_footer_page")}}</h4>
                                    </div>
                                </div>
                            </BCol>
                           
                            
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">

                                    <Link href="/landing-header/create" v-if="permissions.includes('add_header_footer')">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> {{$t("add")}}</BButton>
                                    </Link>
                                </div>
                            </BCol>
                        </BRow>
                        <BCol md="3">
                            <div class="d-flex align-items-center mt-3">
                                <label class="me-2 text-muted">{{$t("show")}}</label>
                                <select v-model="filter.limit" @change="changeEntriesPerPage" class="form-select form-select-sm w-auto">
                                <option v-for="option in paginatorOption" :key="option" :value="option">
                                    {{ option }}
                                </option>
                                </select>
                                <label class="ms-2 text-muted">{{$t("entries")}}</label>
                            </div>
                        </BCol>
                        <BCol class="col-12 text-center" md="12">
                              <a href="" class="text-success fs-18" data-bs-toggle="modal" data-bs-target="#low">{{$t("how_it_works")}} ?</a>
                        </BCol>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{$t("title")}}</th>
                                        <th scope="col"> {{$t("language")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ result.home }}</td>
                                        <td>{{ result.language }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#" @click.prevent="editData(result)" v-if="permissions.includes('edit_header_footer')">
                                                        <i class="bx bxs-edit-alt align-center text-muted me-2"></i>{{$t("edit")}}
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="deleteModal(result.id)" v-if="permissions.includes('delete_header_footer') && results.length > 1">
                                                        <i class="bx bxs-trash align-center text-muted me-2"></i>{{$t("delete")}}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                            <h5>{{$t("no_data_found")}}</h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <div>
            <!-- Success Message -->
            <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" data="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Success</strong> - {{
                        successMessage }}
                    <button type="button" class="btn-close btn-close-success" @click="dismissMessage"
                        aria-label="Close Success Message"></button>
                </div>
            </div>

            <!-- Alert Message -->
            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" data="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage
                    }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage"
                        aria-label="Close Alert Message"></button>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <Pagination :paginator="paginator" @page-changed="handlePageChanged" />

<!-- Modals -->
<div id="low" class="modal fade modal-xl" tabindex="-1" aria-labelledby="lowLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">How Landingsite works when Language change?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                
            </div>
            <div class="m-auto">
            <img src="@assets/images/header.png" alt="Loading..." style="width:500px" />
            </div>
            <div class="modal-body">
                <h5 class="fs-15">
                  When preparing to change the language on your landing website, it’s essential to update the content for the new language in the CMS section across all below pages.
                </h5>
                <div class="d-flex mt-4">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">
                          Header-Footer Section
                        </p>
                    </div>
                </div>
                <div class="d-flex mt-4">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">
                          Home Section
                        </p>
                    </div>
                </div>
                <div class="d-flex mt-4">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">
                          About Us Section
                        </p>
                    </div>
                </div>
                <div class="d-flex mt-4">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">
                          Driver Section
                        </p>
                    </div>
                </div>
                <div class="d-flex mt-4">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">
                          User Section
                        </p>
                    </div>
                </div>
                <div class="d-flex mt-4">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">
                          Contact Section
                        </p>
                    </div>
                </div>
                <div class="d-flex mt-4">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">
                          Quicklinks Section
                        </p>
                    </div>
                </div>
                <h5 class="fs-15 mt-2">
                  If you don't update contents for the new language in the CMS section across all pages,you will get error on Landing Webiste like below
                </h5>
                <div class="m-auto text-center">
                  <img src="@assets/images/error.png" alt="Loading..." style="width:800px" />
                </div>
                <!-- <p class="text-muted"> {{$t("bidding_low_percentage")}} = <strong>50 %</strong></p>
                <p class="text-muted"> Recommended Price of {{$t("bidding_low_percentage")}} =<strong>150 of 50% = $75</strong> </p>
                <p class="text-muted"> Least Bidding Amount = <strong> Recommended Price for ride - Recommended Price  of {{$t("bidding_low_percentage")}}</strong></p>
                <p class="text-muted"> Least Bidding Amount = <strong> $150 - $75</strong>= <h6>$75</h6></p>
                <p class="text-muted"> Least Bidding Amount = <strong>$75</strong></p> -->
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
a{
    cursor: pointer;
}
.text-danger {
    padding-top: 5px;
}

</style>
<style scoped>
.color-picker {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 1rem;
  font-family: Arial, sans-serif;
}

.visually-hidden {
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
  position: absolute;
}

.rounded-color-picker {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.rounded-color-picker:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
}

.color-code-input {
  width: 120px;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
  text-align: center;
  font-size: 1rem;
  color: #333;
  background-color: #f9f9f9;
}

.color-code-input:focus {
  outline: none;
  border-color: #777;
}
</style>