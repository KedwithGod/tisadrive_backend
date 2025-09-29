<template>
    <Layout>
        <Head title="Documents" />
        <PageHeader :title="$t('documents')" :pageTitle="$t('documents')" pageLink="/manage-owners"/>

        <div class="row">
            <div class="col-lg-12">
                <div class="card" id="tasksList">
                    <div class="card-header border-0"></div>
                    <div class="card-body border border-dashed border-end-0 border-start-0">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{$t("document_name")}}</th>
                                        <th scope="col">{{$t("identify_number")}}</th>
                                        <th scope="col">{{$t("expiry_date")}}</th>
                                        <th scope="col">{{$t("status")}}</th>
                                        <th scope="col">{{$t("comment")}}</th>
                                        <th scope="col">{{$t("document")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(document, index) in documents" :key="document.id">
                                        <td>{{ document.name }}</td>
                                        <td>{{ document.identify_number || 'N/A' }}</td>
                                        <td>{{ document.expiry_date || 'N/A' }}</td>
                                        <td>
                                            <span 
                                                :class="{
                                                    badge: true,
                                                    'bg-success': document.document_status === 1, // Approved
                                                    'bg-secondary': document.document_status === 2 || document.document_status == null, // Not Uploaded or Missing
                                                    'bg-warning': document.document_status === 3 || document.document_status === 4, // Waiting for Approval
                                                    'bg-danger': document.document_status === 5, // Declined
                                                    'bg-dark': document.document_status === 6 // Expired
                                                }"
                                            >
                                                {{ 
                                                    document.document_status === 1 ? 'Approved' :
                                                    document.document_status === 2 || document.document_status == null ? 'Not Uploaded' :
                                                    document.document_status === 3 || document.document_status === 4 ? 'Waiting for Approval' :
                                                    document.document_status === 5 ? 'Declined' :
                                                    document.document_status === 6 ? 'Expired' :
                                                    'Unknown'
                                                }}
                                            </span>
                                        </td>
                                        <td>{{ document.comment || 'N/A' }}</td>
                                        <td>
                                            <BButton class="btn btn-soft-info btn-sm m-2" size="sm"data-bs-toggle="tooltip" v-b-tooltip.hover
                                            title="view" @click="viewDocument(document)">
                                                <i class="bx bx-show-alt align-center"></i>
                                            </BButton>
                                            <BButton class="btn btn-soft-success btn-sm m-2" size="sm" data-bs-toggle="tooltip" v-b-tooltip.hover
                                            title="upload" :href="`/manage-owners/document-upload/${document.id}/${ownerId}`">
                                                <i class="bx bx-upload align-center"></i>
                                            </BButton>
                                        </td>
<td>
  <!-- Show only Decline button if document_status is 1 (Approved) -->
  <button 
    v-if="document.document_status === 1" 
    type="button" 
    class="btn btn-outline-danger waves-effect waves-light btn-sm" 
    @click="declineDocument(document.id)">
    {{$t("decline")}}
  </button>

  <!-- Show only Approve button if document_status is 5 (Declined) -->
  <button 
    v-if="document.document_status === 5" 
    type="button" 
    class="btn btn-outline-success waves-effect waves-light btn-sm me-1" 
    @click="approveDocument(document.id)">
    {{$t("approve")}}
  </button>

  <!-- Do not show any buttons if document_status is 2 or null -->
  <template v-if="!(document.document_status === 2 || document.document_status == null)">
    <!-- Show both Approve and Decline buttons if document_status is 3, 4, or 6 -->
    <button 
      v-if="document.document_status === 3 || document.document_status === 4 || document.document_status === 6" 
      type="button" 
      class="btn btn-outline-success waves-effect waves-light btn-sm me-1" 
      @click="approveDocument(document.id)">
      {{$t("approve")}}
    </button>
    <button 
      v-if="document.document_status === 3 || document.document_status === 4 || document.document_status === 6" 
      type="button" 
      class="btn btn-outline-danger waves-effect waves-light btn-sm" 
      @click="declineDocument(document.id)">
      {{$t("decline")}}
    </button>
  </template>
</td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <!-- Modal -->
<!-- Modal -->
<div v-if="showModal" class="modal fade show" tabindex="-1" role="dialog" style="display: block;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                <h5 class="modal-title">{{$t("view_document")}}</h5>
                <a href="#" class="close fs-18" @click="closeModal" aria-label="Close">
                    <span class="fs-22" aria-hidden="true">&times;</span>
                </a>
                </div>
            <div class="modal-body">
                  <!-- With Crossfade Animation -->
                <div id="carouselExampleFade" class="carousel slide carousel-fade py-5" data-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleFade" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleFade" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="car-img" v-if="imageUrl">
                                <img  :src="imageUrl" :alt="documentNameFront" class="img-fluid mb-2" />
                                <h5 class="text-center">{{ documentNameFront  }}</h5>
                            </div>
                            <div v-else class="d-grid"><i class="ri-image-fill m-auto fs-22"></i><h6 class="text-center m-auto">{{$t("no_front_image_available")}}</h6></div>
                            
                        </div>
                        <div class="carousel-item">
                            <div class="car-img" v-if="backImageUrl">
                                <img  :src="backImageUrl" :alt="documentNameBack" class="img-fluid mb-2" />
                                <h5 class="text-center">{{ documentNameBack }}</h5>
                            </div>
                            <div v-else class="d-grid"><i class="ri-image-fill m-auto fs-22"></i><h6 class="text-center m-auto">{{$t("no_back_image_available")}}</h6></div>
                            
                        </div>
                    </div>
                    <a class="carousel-control-prev bg-dark" style="height:30px"  href="#carouselExampleFade" role="button" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next bg-dark" style="height:30px" href="#carouselExampleFade" role="button" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeModal">{{$t("close")}}</button>
                <!-- Add download buttons for both images if available -->
                <a v-if="imageUrl" :href="imageUrl" download class="btn btn-primary">
                    {{$t("download_front_image")}}
                </a>
                <a v-if="backImageUrl" :href="backImageUrl" download class="btn btn-primary">
                    {{$t("download_back_image")}}
                </a>
            </div>
        </div>
    </div>
</div>

        <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show">
            <div class="alert-content">
                <i class="ri-notification-off-line me-3 align-middle"></i>
                <strong>Success</strong> - {{ successMessage }}
                <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
            </div>
        </div>

        <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show">
            <div class="alert-content">
                <i class="ri-notification-off-line me-3 align-middle"></i>
                <strong>Alert</strong> - {{ alertMessage }}
                <button type="button" class="btn-close btn-close-danger" @click="dismissMessage" aria-label="Close Alert Message"></button>
            </div>
        </div>

        <!-- <Pagination :paginator="paginator" @page-changed="handlePageChanged" /> -->
    </Layout>
</template>

<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref } from "vue";
import axios from "axios";
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
        Pagination,
        Link,
    },
    props: {
        successMessage: String,
        alertMessage: String,
        ownerId: Number,
        documents: Array,
    },
    setup(props) {
        const { t } = useI18n();
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');
        const showModal = ref(false);
        const imageUrl = ref(null);
        const backImageUrl = ref(null);  // To hold the back image URL


        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const approveDocument = async (documentId) => {
            const status = 1;
            const response = await axios.get(`/manage-owners/document-toggle/${documentId}/${props.ownerId}/${status}`);
            if(response.data.allDocumentsApproved == true){
                successMessage.value = t('document_approved');
                router.get(`/manage-owners`); // Redirect or navigate to another route
            }
            else{
                router.get(`/manage-owners/document/${props.ownerId}`); // Redirect or navigate to another route
            }

        };

        const declineDocument = async (documentId) => {
            const status = 5;
            const response = await axios.get(`/manage-owners/document-toggle/${documentId}/${props.ownerId}/${status}`); 
            if(response.data.allDocumentsDisapproved == true){
                alertMessage.value = t('document_declined');
                router.get(`/manage-owners`);  // Redirect or navigate to another route
            }
            else{
                router.get(`/manage-owners/document/${props.ownerId}`); // Redirect or navigate to another route
            }
        };

        const updateDocuments = async () => {
            await axios.get(`/manage-owners/update/${props.ownerId}`);
            router.get(`/manage-owners`);
        };

        const documentNameFront = ref(null);
        const documentNameBack = ref(null);

        // Updated method to view document and show modal
        const viewDocument = (document) => {
            // Assuming 'document.image' contains the URL of the image
            imageUrl.value = document.image;
            backImageUrl.value = document.back_image; // Back image URL
            showModal.value = true;
            documentNameFront.value = document.document_name_front ?? t("document"); 
            documentNameBack.value = document.document_name_back ?? t("document");
        };

        // Method to close the modal
        const closeModal = () => {
            showModal.value = false;
            imageUrl.value = null;
            backImageUrl.value = null;
        };

        return {
            successMessage,
            alertMessage,
            dismissMessage,
            approveDocument,
            declineDocument,
            updateDocuments,
            viewDocument,
            closeModal,
            showModal, // Export ref to use in the template
            imageUrl,  // Export ref to use in the template
            backImageUrl, // Export ref for back image
            documentNameFront,
            documentNameBack
        };
    }
};
</script>


<style scoped>
.custom-alert {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
}
.custom-alert .alert-content {
    display: flex;
    align-items: center;
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
    .carousel-control-prev, .carousel-control-next {
    position: absolute;
    top: 120px;
    bottom: 0;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 5%;
    padding: 0;
    color: #fff;
    text-align: center;
    background: none;
    border: 0;
    opacity: 0.5;
    transition: opacity 0.15s ease;
}
.car-img{
    display: grid;
    place-items: center;
}
</style>
