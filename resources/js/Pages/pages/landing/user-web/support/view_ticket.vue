<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch,onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import { useI18n } from 'vue-i18n';
import FormValidation from "@/Components/FormValidation.vue";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import UserWebMenu from "@/Components/UserWebMenu.vue";
import ImageUp from "@/Components/ImageUp.vue";
import { BCard, BCardBody, BCardFooter, BCardHeader } from 'bootstrap-vue-next';
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";



export default {
    components: {
        Layout,
        PageHeader,
        Head,
        Multiselect,
        FormValidation,
        UserWebMenu,
        ImageUp,
        ckeditor: CKEditor.component,

    },
    props: {
        support_ticket: Object,
        successMessage: String,
        alertMessage: String,
        app_for: String,
        title: Array,
        languages: Array,
        titleType:String,
        user: Object,
        supportTicket: Array,
        reply_message:Array,
        attachment:Array
    },
    data() {
    return {
      editor: ClassicEditor,
      showModal: false,
      selectedFile: null,
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
  },
    setup(props) {
        const { t } = useI18n();
        const { languages, fetchData } = useSharedState(); // Destructure the shared state
        const activeTab = ref('English');
        const errors = ref({});
        const validationRef = ref(null);
        const title = ref(props.title);
        const results = ref(props.supportTicket);
        console.log("results",results.value);
        const reply = ref(props.reply_message);
        console.log("reply",props.user);
        const attachments = ref(props.attachment);

        const form = useForm({
            message:  props.support_ticket ? props.support_ticket.message || "" : "",
        });


        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const validationRules = {
            message: { required: true },
        };
        // Construct the full URL for the vehicle type icon
        const searchQuery = ref('');


        const validateForm = () => {
            const errors = {};
            for (const key in validationRules) {
                if (validationRules[key].required && !form[key]) {
                    errors[key] = t('this_field_is_required');
                    // errors[key] = 'This field is required';
                }
            }
            return errors;
        };

        const handleSubmit = async () => {

            try {
                let response;

                response = await axios.post(`/ticket/reply/${props.supportTicket.id}`, form.data());

                if (response.status === 201) {
                successMessage.value = t('ticket_created_successfully');
                form.reset();
                router.get(`/ticket/view/${results.value.id}`);
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

         const setActiveTab = (tab) => {
        activeTab.value = tab;
        }
        
        onMounted(async () => {
        if (Object.keys(languages).length == 0) {
            await fetchData();
        }
        });

        const filterTitle = () =>{

            if (props.titleType === 'request') {
                title.value = props.title.filter(t => t.title_type === "request");
            } 
            if (props.titleType === 'general') {
                title.value = props.title.filter(t => t.title_type === "general");
            }
        }

        const handleFileUpload = (event) => {
            form.attachment = Array.from(event.target.files) // Convert FileList to array
        }
        

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
            form,
            successMessage,
            alertMessage,
            errors,
            handleSubmit,
            validationRef,
            validationRules,
            searchQuery,
            setActiveTab,
            activeTab,
            languages,
            title,
            filterTitle,
            handleFileUpload,
            results,
            reply,
            attachments,
            getFullImageUrl,
            isImage,
            isVideo,
            isPDF
        };
    },
    mounted() {
        this.filterTitle();
    },
    computed: {
    sortedMessages() {
      return [...this.reply_messages].sort((a, b) => 
        new Date(a.converted_created_at) - new Date(b.converted_created_at)
      );
    }
  },
//   methods: {
//     stripHtmlTags(content) {
//     const parser = new DOMParser();
//     const parsedContent = parser.parseFromString(content, 'text/html');
//     return parsedContent.body.textContent || "";
//     },
// },

};

</script>

<template>
    <BCard style="margin-bottom: 0px;">
        <Head title="Taxi Ride" />
        <BCardHeader class="border-0">
            <!-- menu Offcanvas -->
            <UserWebMenu :user="user" />
            <!-- menu end -->
        </BCardHeader>
        <BCardBody>
            <div class="d-flex align-items-center justify-content-between">
            <h4 class="text-uppercase">{{$t("view_ticket")}} : {{ results.ticket_id }}</h4>
            <BLink href="/get-support" class="btn btn-primary float-end">{{ $t("back") }}</BLink>
            </div>
        </BCardBody>
        <div class="row">
            <div class="col-lg-9">
                <BCard class="border">
                    <BCardHeader class="border-0">
                        <div class="row row-cols-auto justify-content-between align-items-center g-3">
                            <div class="col">
                                <img :src="user?.profile_picture" class="img-fluid avatar-md rounded-circle" width="100" alt="user-profile">     
                                <span class="fs-20 ms-2">{{ results.user_name }}</span>                       
                            </div>
                            <div class="col">
                                <h6 class="ms-auto">{{ results.converted_created_at }}</h6>
                            </div>
                        </div>
                    </BCardHeader>
                    <BCardBody>
                        <h5>{{ results.title }}</h5>
                        <h6>{{  results.description }}</h6>
                        <!-- <div class="image-container d-flex align-items-center justify-content-between">
                            <div v-for="(attachment, index) in attachments" :key="index.id" class="d-flex align-items-center justify-content-between">
                            <img :src="getFullImageUrl(attachment.image_name)" :alt="attachment.image_name ? 'attachments' : 'No image available'" class="img-fluid"> 
                        </div>
                        </div> -->
                        <!-- <div class="image-container">
    <div v-for="(attachment, index) in attachments" :key="index">
        <template v-if="isImage(attachment.image_name)">
            <img :src="getFullImageUrl(attachment.image_name)" :alt="attachment.image_name ? 'attachments' : 'No image available'" height="100px" width="200"> 
        </template>
        <template v-else-if="isVideo(attachment.image_name)">
            <video controls height="100px" width="200">
                <source :src="getFullImageUrl(attachment.image_name)" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </template>
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

                <h4 class="mb-4 ms-2">{{ $t('reply_message') }}</h4>
            <BCard class="border" style="max-height: 500px; overflow-y: auto;">
                <div v-if="reply.length > 0" class="card p-3 border mt-2" v-for="(message, index) in reply" :key="index">
                    <div class="border-0" 
                        :class="message.sender_id === user.id ? 'text-end' : 'text-start'">
                        <div class="row row-cols-auto align-items-center g-3"
                            :class="message.sender_id === user.id ? 'flex-row-reverse' : ''">
                            
                            <div class="col">
                                <img :src="message.sender_id === user.id ? user.profile_picture : results.admin_details.profile_picture"
                                    class="img-fluid avatar-xs rounded-circle" width="100" alt="user-profile">     
                                <span class="fs-16 ms-2" style="font-weight: 600;">
                                    {{ message.sender_id === user.id ? user.name : results.admin_name }}
                                </span>                       
                            </div>
                        </div>
                    </div>

                    <div :class="message.sender_id === user.id ? 'text-end' : 'text-start ms-4'">
                        <h6 class="badge bg-light text-body fs-12 p-2 mt-2">{{stripHtmlTags( message.message) }}</h6>
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
                                <p class="text-muted mb-0">{{$t("created")}}:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">{{ results.converted_created_at }}</h6>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
                <!-- <BCard no-body id="tasksList" class="border border-animate">
                    <BCardHeader>
                        <h4 class="text-center text-uppercase">{{ $t("supporting_time") }}</h4>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="border p-3 rounded border-1 mb-2">
                            <h5 class="border-bottom border-primary m-1 text-center border-bottom-dashed text-uppercase border-2">
                                Monday - Friday
                                <span class="dot-line"></span>
                            </h5>
                            <div class="d-flex">
                                <div class="ms-4 fs-16">11.00AM</div>
                                <div class="ms-auto me-4 fs-16">5.00PM</div>
                            </div>                           
                        </div>
                        <div class="border p-1 rounded border-1 mb-2">
                            <h5 class="border-bottom border-primary pb-2 m-1 text-center border-bottom-dashed text-uppercase border-2">
                                Saturday-Sunday
                                <span class="dot-line1"></span>
                            </h5>
                            <div class="text-center fs-14 mt-2">Closed</div>                         
                        </div> 
                    </BCardBody>
                    <BCardFooter>
                        <h4 class="text-uppercase text-center">Support Ticket Guidelines</h4>
                        <div class="mt-4">
                            <div class="fs-16">Response Time</div>
                            <p class="mt-3 fs-14">
                                Our support team is available six days a week, from 11:00 AM to 5:00 PM (IST, GMT+5:30).
                                We aim to respond to each ticket as quickly as possible. However, response times may be
                                delayed by one to two business days during weekends or government holidays.
                            </p>
                        </div> 
                        
                        <div class="mt-4">
                            <div class="fs-16">Important Notice</div>
                            <ul class="mt-3 fs-14">
                               <li>Ticket response time may take up to two business days.</li>
                               <li>If a ticket remains unaddressed for more than two business days or is unrelated to our support scope, it may be locked.</li>
                               <li>Duplicate tickets for the same issue may also result in ticket locking.</li>
                            </ul>
                            <div class="fs-14">Thank you for your patience and cooperation.</div>
                        </div> 
                    </BCardFooter>
                </BCard> -->
            </div>
        </div>
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
.image-container {
    display: grid;
    gap: 10px; /* Adds spacing between images */
    flex-wrap: nowrap; /* Ensures images wrap to the next line if needed */
}

.rtl .custom-alert {
  max-width: 600px;
  float: left;
  top: -300px;
  right: 10px;
}
.dot-line {
    width: 86%;
    height: 2px;
    display: block;
    background-color: var(--success);
    position: relative;
    margin: 0 auto 10px auto;
    transform: translateY(-2px);
}
.dot-line {
    width: 86%;
    height: 3px;
    display: block;
    background-color: var(--side_menu);
    position: relative;
    transform: translateY(-2px);
    top:14px;
}
.dot-line::after {
    content: '';
    position: absolute;
    display: block;
    width: 10px;
    height: 10px;
    background-color: var(--side_menu);
    border-radius: 50%;
    right: 0;
    top: -4px;
}
.dot-line::before {
    content: '';
    position: absolute;
    display: block;
    width: 10px;
    height: 10px;
    background-color: var(--side_menu);
    border-radius: 50%;
    left: 0;
    top: -4px;
}

.dot-line1 {
    width: 86%;
    height: 3px;
    display: block;
    position: relative;
    transform: translateY(-2px);
    top:14px;
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
    