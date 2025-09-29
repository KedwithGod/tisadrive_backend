<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import searchbar from "@/Components/widgets/searchbar.vue";
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";
import { useI18n } from 'vue-i18n';
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

export default {
    data() {
        return {
            rightOffcanvas: false,
            showModal: false,
            selectedFile: null,
            editor: ClassicEditor,
            editorData: "",
            zoomLevel: 1,
        };
    },
    methods: {
    openPreview(fileName) {
      this.selectedFile = fileName;
      this.showModal = true;
    },
    closePreview() {
      this.showModal = false;
      this.selectedFile = null;
    },
    handleWheelZoom(e) {
    e.preventDefault();
    if (e.deltaY < 0) {
      // Zoom in
      this.zoomLevel += 0.1;
    } else {
      // Zoom out
      this.zoomLevel = Math.max(1, this.zoomLevel - 0.1);
    }
  },
    stripHtmlTags(content) {
        const parser = new DOMParser();
        const parsedContent = parser.parseFromString(content, 'text/html');
        return parsedContent.body.textContent || "";
        },
        isFromUser(message) {
            return message.sender_id === message.user_id;
        },
        isFromEmployee(message) {
            return message.employee_id !== null && message.employee_id !== '';
        },
        isFromAdmin(message) {
            return !this.isFromUser(message) && !this.isFromEmployee(message);
        },
        getSenderName(message) {
            if (this.isFromUser(message)) {
            return this.results.user?.name;
            }
            return this.results.admin_name ?? 'Admin/Employee';
        },
        getProfilePicture(message) {
            if (this.isFromUser(message)) {
            return this.results.user?.profile_picture || null;
            }
            return this.results.admin_details?.profile_picture || null;
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
        ckeditor: CKEditor.component,

    },
//     data() {
//     return {
      
//     };
//   },
    props: {
        successMessage: String,
        alertMessage: String,
        app_for:String,
        employees:Array,
        ticket: Object,
        user: Object,
        supportTicket: Array,
        reply_message:Array ,
        support_ticket:Object,
        attachment:Array
     },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref('');
        const filter = useForm({
            transport_type: null,
            dispatch_type: null,
            status: null,
        });
        const results = ref(props.supportTicket);
        const reply = ref(props.reply_message);
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const showEmployess = ref(false);
        const employees_name = ref([]);
        const ticket_id = ref();
        const validationRef = ref(null);
        const errors = ref({});
        const attachments = ref(props.attachment);


        const form = useForm({
            message:  props.support_ticket ? props.support_ticket.message || "" : "",
        });

        const validationRules = {
            message: { required: true },
        };
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        // const fetchDatas = async (page = 1) => {
        //     try {
        //         const params = filter.data();
        //         // params.search = searchTerm.value;
        //         // params.page = page;
        //         const response = await axios.get(`/support-tickets/individual_list`, { params });
        //         results.value = response.data.results;
        //         paginator.value = response.data.paginator;
        //         modalFilter.value = false;
        //     } catch (error) {
        //         console.error(t('error_fetching_title'), error);
        //     }
        // };

        const toggleActiveStatus = async (id, status) => {
            Swal.fire({
                title: t('are_you_sure'),
                text: t('you_are_about_to_change_status'),
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: t('yes'),
                cancelButtonText: t('cancel')
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await axios.post(`/title/update-status`, { id: id, status: status });
                        const index = results.value.findIndex(item => item.id === id);
                        if (index !== -1) {
                            results.value[index].active = status; // Update the active status locally
                        }
                        Swal.fire(t('changed'), t('status_updated_successfully'), "success");
                    } catch (error) {
                        console.error(t('error_updating_status'), error);
                        Swal.fire(t('error'), t('failed_to_update_status'), "error");
                    }
                }
            });
        };

        const handleSubmit = async () => {

        try {
            let response;

            response = await axios.post(`/support-tickets/ticket/reply/${props.supportTicket.id}`, form.data());

            if (response.status === 201) {
            successMessage.value = t('ticket_created_successfully');
            form.reset();
            router.get(`/support-tickets/view-details/${results.value.id}`);
            } else {
            alertMessage.value = t('failed_to_create_ticket');
            }
        } catch (error) {
            if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
            } else {
            console.error(t('error_creating_ticket'), error);
            alertMessage.value = t('failed_to_create_ticket_catch');
            }
        }
        };

        const isImage = (filename) => {
            return /\.(jpg|jpeg|png|gif|svg|pdf)$/i.test(filename);
        };

        const isVideo = (filename) => {
            return /\.(mp4|webm|ogg)$/i.test(filename);
         };
         const isPDF = (filename) => {
            return /\.pdf$/i.test(filename);
        };
        const getFullImageUrl = (imageKey) => {
            if (!imageKey) {
                return ''; // Return an empty string if no image
            }
            // Return the full URL for the image
            return `/storage/uploads/support/support-tickets-files/${imageKey}`;
        }
        return {
            results,
            modalShow,
            deleteItemId,
            successMessage,
            alertMessage,
            dismissMessage,
            searchTerm,
            paginator,
            modalFilter,
            filter,
            toggleActiveStatus,
            validationRules,
            validationRef,
            form,  
            handleSubmit ,
            errors,
            reply,
            getFullImageUrl,
            attachments,
            isImage,
            isVideo,
            isPDF
        };
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
    sortedMessages() {
      return [...this.reply_messages].sort((a, b) => 
        new Date(a.converted_created_at) - new Date(b.converted_created_at)
      );
    },
    showReply(){
        const firstMessage = this.reply_message?.[0];

if (!firstMessage) {
    // No messages yet, so we don't show the reply
    return false;
}

const { sender_id, user_id, employee_id } = firstMessage;

// Ensure none are null or undefined before checking logic
return sender_id !== user_id && sender_id !== employee_id;
    }
  },
    mounted() {
        // this.fetchDatas();
    },
    // methods: {
    //     stripHtmlTags(content) {
    //     const parser = new DOMParser();
    //     const parsedContent = parser.parseFromString(content, 'text/html');
    //     return parsedContent.body.textContent || "";
    //     },
    // },
};
</script>

<template>
    <Layout>

        <Head title="Tickets" />
        <PageHeader :title="$t('view_ticket')" :pageTitle="$t('support_management')" pageLink="/support-tickets" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                </BCard>
                <div class="row">
            <div class="col-lg-9">
                <BCard class="border">
                    <BCardHeader class="border-0">
                        <div class="row row-cols-auto justify-content-between align-items-center g-3">
                            <div class="col d-flex align-items-center">
                                <img :src="results.user.profile_picture" class="img-fluid avatar-md rounded-circle" width="100" alt="user-profile">
                                <div>    
                                <span class="fs-20 ms-2">{{ results.user_name }}</span>                       
                                <p class="fs-14 text-muted ms-2">{{ results.user_type }}</p>  
                                </div>                      
                            </div>
                            <div class="col">
                                <h6 class="ms-auto">{{ results.converted_created_at }}</h6>
                            </div>
                        </div>
                    </BCardHeader>
                    <BCardBody>
                        <h5>{{ results.title }}</h5>
                        <h6>{{  results.description }}</h6>
                        <!-- <div class="image-container">
                            <div v-for="(attachment, index) in attachments" :key="index.id" >
                            <img :src="getFullImageUrl(attachment.image_name)" :alt="attachment.image_name ? 'attachments' : 'No image available'" height="100px" width="200">
                            </div> 
                        </div> -->
                        <div class="mt-5">
                                <h5 class="mb-3">{{$t("attachments")}} : </h5>
                                <!-- Show files if available, otherwise display "No files here" -->
                                <div v-if="attachments.length > 0" class="file-container d-flex flex-wrap gap-3">
                                <div 
                                    v-for="(attachment, index) in attachments" 
                                    :key="index" 
                                    class="file-box"
                                    @click="openPreview(attachment.image_name)"
                                >
                                    <!-- Image -->
                                    <template v-if="isImage(attachment.image_name)">
                                    <img :src="getFullImageUrl(attachment.image_name)" class="file-preview">
                                    </template>

                                    <!-- Video -->
                                    <template v-else-if="isVideo(attachment.image_name)">
                                    <video class="file-preview">
                                        <source :src="getFullImageUrl(attachment.image_name)" type="video/mp4">
                                        Your browser does not support video playback.
                                    </video>
                                    </template>

                                    <!-- PDF -->
                                    <template v-else-if="isPDF(attachment.image_name)">
                                    <embed :src="getFullImageUrl(attachment.image_name)" type="application/pdf" class="file-preview">
                                    </template>

                                    <!-- Other File Types (Show as Download Button) -->
                                    <template v-else>
                                    <div class="file-preview d-flex align-items-center justify-content-center">
                                        <i class="fas fa-file-alt fa-2x"></i>
                                    </div>
                                    </template>
                                </div>
                                </div>

                                <!-- Show message if no files exist -->
                                <div v-else class="no-files text-center text-muted fs-18">
                                    <i class=" ri-file-unknow-line" style="font-size:32px"></i>
                                <p>{{$t("no_attachments_here")}}</p> 
                                </div>
                            </div>

                            <!-- Popup Modal -->
                            <div v-if="showModal" class="modal-overlay" @click="closePreview">
                                <div class="modal-content">
                                <span class="close-btn" @click="closePreview">&times;</span>
                                
                                <!-- Display Image -->
                                <template v-if="isImage(selectedFile)">
                                    <img :src="getFullImageUrl(selectedFile)" class="popup-content" :style="{ transform: `scale(${zoomLevel})` }"
                                    @wheel="handleWheelZoom">
                                </template>

                                <!-- Display Video -->
                                <template v-else-if="isVideo(selectedFile)">
                                    <video controls class="popup-content">
                                    <source :src="getFullImageUrl(selectedFile)" type="video/mp4" :style="{ transform: `scale(${zoomLevel})` }"
                                    @wheel="handleWheelZoom">
                                    </video>
                                </template>

                                <!-- Display PDF -->
                                <template v-else-if="isPDF(selectedFile)">
                                    <embed :src="getFullImageUrl(selectedFile)" type="application/pdf" class="popup-content" :style="{ transform: `scale(${zoomLevel})` }"
                                    @wheel="handleWheelZoom">
                                </template>

                                <!-- Show Download Button for Other File Types -->
                                <template v-else>
                                    <a :href="getFullImageUrl(selectedFile)" target="_blank" class="btn btn-primary">Download File</a>
                                </template>

                                </div>
                            </div>
                            <!-- Popup Modal End-->
                    </BCardBody>
                </BCard>
                <div >
                    <h4 class="mb-4 ms-2">{{ $t('reply_message') }}</h4>
                <BCard class="border" style="max-height: 500px; overflow-y: auto;">
                    <div v-if="reply.length > 0" class="card p-3 border mt-2" v-for="(message, index) in reply" :key="index">
                        <div class="border-0" 
                            :class="isFromUser(message) ? 'text-start' : 'text-end'">
                            <div class="row row-cols-auto align-items-center g-3"
                                :class="!isFromUser(message) ? 'flex-row-reverse' : ''">
                                
                                <div class="col">
                                    <img 
                                    :src="getProfilePicture(message)" 
                                    class="img-fluid avatar-xs rounded-circle" 
                                    width="100" 
                                    alt="user-profile"
                                    >
                                    <span class="fs-16 ms-2" style="font-weight: 600;">
                                        {{ getSenderName(message) }}
                                    </span>                       
                                </div>
                            </div>
                        </div>

                        <div class="" :class="isFromUser(message) ? 'text-start ms-4' : 'text-end'">
                            <h6 class="badge bg-light text-body fs-12 p-2 mt-2">{{ stripHtmlTags(message.message) }}</h6>
                            <div class="col">
                                <h6>{{ message.converted_created_at }}</h6>
                            </div>
                        </div>
                    </div>
                <div v-else class="no-files text-center text-muted fs-18">
                    <i class="ri-chat-delete-line" style="font-size:32px"></i>
                    <p>{{$t("no_messages_here")}}</p> 
                </div>
            </BCard>
                <BCard class="border">
                    <BCardHeader class="border-0">
                        <h4>
                            <i class =' ri-reply-fill bx-sm me-2'></i>{{ $t("reply") }}
                        </h4>
                    </BCardHeader>
                    <BCardBody>
                        <form @submit.prevent="handleSubmit">
                            <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                                <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                    <label for="description" class="form-label">{{$t("message")}}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <!-- <textarea class="form-control" id="message" rows="3" :placeholder="$t('enter_message')" v-model="form.message"></textarea> -->
                                    <ckeditor :disabled="app_for === 'demo'" v-model="form.message"  :editor="editor"></ckeditor>
                                    <span v-for="(error, index) in errors.message" :key="index" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                    <button :disabled="results.status == 3 || results.status == 1" type="submit" class="btn btn-primary">{{ $t('send') }}</button>
                                    </div>
                                </div>
                                </div>
                            </FormValidation>
                            </form>
                    </BCardBody>
                </BCard>
                </div>
               
            </div>
            <div class="col-lg-3">
                <BCard class="border">
                    <BCardHeader class="border-bottom-0">
                        <div class="row"></div>
                        <div class="row row-cols-auto justify-content-between align-items-center g-3">
                            <div class="col">
                                <h4>{{ $t("ticket_details") }}</h4>                      
                            </div>
                            <div class="col">
                                <div class="ms-auto">
                                <BBadge variant="success" class="text-uppercase" v-if = "results.status == 1">{{$t("pending")}}</BBadge>
                                <BBadge variant="warning" class="text-uppercase" v-if = "results.status == 2">{{$t("acknowledge")}}</BBadge>
                                <BBadge variant="danger" class="text-uppercase" v-if = "results.status == 3">{{$t("closed")}}</BBadge>
                            </div>                            
                        </div>
                        </div>
                    </BCardHeader>
                    <BCardBody>
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">{{$t("ticket_id")}}:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">{{ results.ticket_id }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2" v-if = "results.request_id">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">{{$t("request_id")}}:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">{{ results.request_id ?? '-' }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">{{$t("ticket_title")}}:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">{{ results.title }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">{{$t("assign_to")}}:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0" v-if = "results.assign_to">{{ results.admin_details.first_name }}</h6>
                                <h6 class="mb-0" v-else>{{ $t("not_assigned")}}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">{{$t("support_type")}}:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">{{ results.support_type }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">{{$t("from")}}:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">{{ results.user_type }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">{{$t("created")}}:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">{{ results.converted_created_at }}</h6>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </div>
        </div>
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
.image-container {
    display: flex;
    gap: 10px; /* Adds spacing between images */
    flex-wrap: wrap; /* Ensures images wrap to the next line if needed */
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
/* .text-danger {
    padding-top: 5px;
} */

</style>

<style scoped>
.file-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.file-box {
  width: 150px;
  height: 150px;
  border: 2px solid #ddd;
  border-radius: 10px;
  overflow: hidden;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
}
.file-preview {
  width: 100%;
  height: 100%;
  margin:auto;
  object-fit: contain;
}
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  max-width: 80%;
  text-align: center;
}
.popup-content {
  width: 20%;
  height: 20%;
  margin:auto;
}
.close-btn {
  position: absolute;
  top: 15px;
  right: 20px;
  font-size: 24px;
  cursor: pointer;
}
.zoom-controls {
  position: absolute;
  top: 20px;
  left: 20px;
  z-index: 10000;
}
</style>
