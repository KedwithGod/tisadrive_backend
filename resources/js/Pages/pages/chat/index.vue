<script>
import simplebar from 'simplebar-vue';
import { required, helpers } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import avatar2 from "@/assets/images/users/avatar-2.jpg";
import axios from 'axios'; // Import axios for API requests
import Swal from "sweetalert2";


export default {
  props: {
    conversations: Array,
    firebaseConfig: Object,
    selectedConversationId: Object,
  },
  setup(props) {
    
    const { t } = useI18n();
    const conversationsData = ref(props.conversations); // Make it a reactive reference
    const messagesData = ref([]); // New reactive reference for messages
    const form = ref({
      message: "",
      audio:null,
    });


    const selectedConversationId = ref(props.selectedConversationId ?? null); // New reactive reference for the selected conversation ID
    const username = ref(""); // Reactive reference for username
    const profile = ref(avatar2); // Reactive reference for profile image
    const v$ = useVuelidate();

    const fetchMessages = async (conversationId) => {
      try {
        const response = await axios.get(`/chat/messages/${conversationId}`);
        messagesData.value = response.data;
        const conversation = conversationsData.value.find(conv => conv.id === conversationId);
        conversation.unread = 0;
        scrollToBottom('users-chat');
      } catch (error) {
        console.error('Error fetching messages:', error);
      }
    };


const handleAudioUpload = (event) => {
  const file = event.target.files[0];
  if (file && file.type.startsWith('audio/')) {
    form.value.audio = file; // Changed from form.audio to form.value.audio
  } else {
    alert('Please select a valid audio file');
  }
};

// Add to your methods
const formatDuration = (seconds) => {
  if (!seconds) return '0:00';
  const mins = Math.floor(seconds / 60);
  const secs = Math.floor(seconds % 60);
  return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
};





      
    

    const scrollToBottom = (id) => {
      setTimeout(() => {
        const simpleBar = document.getElementById(id).querySelector("#chat-conversation .simplebar-content-wrapper");
        const offsetHeight = document.getElementsByClassName("chat-conversation-list")[0].scrollHeight - window.innerHeight + 600;
        if (offsetHeight) {
          simpleBar.scrollTo({
            top: offsetHeight,
            behavior: "smooth",
          });
        }
      }, 300);
    };
    const isSubmitting = ref(false);
    const userSearch = ref(false);
    const searchQuery = ref('');
    const users = ref([]);

    const onSearch = async() => {
      const query = searchQuery.value.trim();
      if(query.length > 2) {
        try{
          const response = await axios.get(`/chat/search-user?search=${query}`);
          users.value = response.data.data;
        }catch(error){
          users.value = [];
          console.log('no users found');
          console.error(error);
        }
      }else{
        users.value = [];
      }
    }
    const toggleSearch = async() => {
      userSearch.value=!userSearch.value;
      if(searchQuery.value.length>0){
        searchQuery.value = '';
      }
      if(users.value.length > 0){
        users.value = [];
      }
    }

    const createChat = async(user) => {
      try{
        const response = await axios.post(`/chat/createChat`,{user_id : user.id});
        const conversation = response.data;

        const timestamp = new Date(conversation.created_at);
        const formattedTime = formatTime(timestamp);

        if(conversation) {
          let chatterData = {
            id: conversation.id,
            unread: 0,
            subject: conversation.subject,
            role: user.role_name,
            profile_picture: conversation.profile_picture,
            last_message_time: formattedTime, // Use the formatted time for last message
          };

          conversationsData.value.unshift(chatterData);
          await selectConversation(conversation.id);

        }
        toggleSearch();
      }catch(error){
        users.value = [];
        console.log('no users found');
        console.error(error);
      }
    }

    // Function to format the date into "22 Nov 12:49 PM"
    function formatTime(date) {
      const options = {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      };
      return date.toLocaleString('en-GB', options).replace(',', ''); // Format like 22 Nov 12:49 PM
    }
   /* const formSubmit = async (event) => {

      if (event) event.preventDefault();
      v$.value.$touch();
      if (v$.value.$invalid) return;

      if (isSubmitting.value) return;

      isSubmitting.value = true;

      const message = form.value.message;
      const audio=form.value.audio;
      const currentDate = new Date();
      const hours = (currentDate.getHours() < 10 ? "0" : "") + currentDate.getHours();
      const minutes = (currentDate.getMinutes() < 10 ? "0" : "") + currentDate.getMinutes();
      
      const newMessage = {
        align: "right",
        message,
        audio,
        time: `${hours}:${minutes}`,
        conversationId: selectedConversationId.value // Include the conversation ID for the new message
      };

      try {
        await axios.post('/chat/send-admin', newMessage); // Adjust API endpoint accordingly
        form.value.message = "";
        form.value.audio = null;
        await fetchMessages(selectedConversationId.value); // Fetch updated messages after sending
        scrollToBottom('users-chat');
      } catch (error) {
        console.error('Error sending message:', error);
      }finally {
        isSubmitting.value = false;
      }
    };

    */
/*
const isSubmitting = ref(false);

function handleAudioUpload(event) {
  const file = event.target.files[0];
  if (file && file.type.startsWith('audio/')) {
    form.value.audio = file;
  } else {
    alert('Please select a valid audio file');
  }
}

async function formSubmit() {
  isSubmitting.value = true;
  try {
    const formData = new FormData();
    formData.append('message', form.value.message);
    if (form.value.audio) {
      formData.append('audio', form.value.audio);
    }

    await axios.post('/your/chat/send/route', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    form.value.message = '';
    form.value.audio = null;
  } catch (error) {
    console.error('Error sending message:', error);
  } finally {
    isSubmitting.value = false;
  }
}*/
const formSubmit = async (event) => {
  if (event) event.preventDefault();
  
  // Don't require text message if audio is being sent
  if (!form.value.message && !form.value.audio) {
    v$.value.$touch();
    return;
  }

  if (isSubmitting.value) return;
  isSubmitting.value = true;

  const formData = new FormData();
  if (form.value.message) formData.append('message', form.value.message);
  if (form.value.audio) formData.append('audio', form.value.audio);
  formData.append('conversationId', selectedConversationId.value);

  try {
    const response = await axios.post('/chat/send-admin', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    // Clear form
    form.value.message = "";
    form.value.audio = null;
    
    // Refresh messages
    await fetchMessages(selectedConversationId.value);
    scrollToBottom('users-chat');
  } catch (error) {
    console.error('Error sending message:', error);
  } finally {
    isSubmitting.value = false;
  }
};



    //audio option handle




    // Method to handle conversation selection
    const selectConversation = async (conversationId) => {
      selectedConversationId.value = conversationId;

      const selectedConversation = conversationsData.value.find(conv => conv.id === conversationId);

      if (selectedConversation) {
        // Update username and profile directly
        username.value = selectedConversation.subject;
        profile.value = selectedConversation.profile_picture;
      }

      await fetchMessages(conversationId); // Fetch messages for the selected conversation
    };

    const cancel = () => {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-success me-2",
          cancelButton: "btn btn-danger ml-2",
        },
        buttonsStyling: false,
      });

      swalWithBootstrapButtons
        .fire({
          title: "Are you sure?",
          text: "You need to close this chat!",
          icon: "warning",
          confirmButtonText: "Yes, close it!",
          cancelButtonText: "No, cancel!",
          showCancelButton: true,
        })
        .then(async (result) => {
          if (result.isConfirmed) {
            try {
              // Axios call to close the chat
              await axios.post('/chat/close-chat', { conversationId: selectedConversationId.value }); // Adjust the endpoint and payload as needed
              // Optionally remove the conversation from the list or update UI state
              conversationsData.value = conversationsData.value.filter(conv => conv.id !== selectedConversationId.value);
              swalWithBootstrapButtons.fire("Closed!", "Your chat has been closed.", "success");
              selectedConversationId.value = null;
              messagesData.value = [];
            } catch (error) {
              console.error(error);
              swalWithBootstrapButtons.fire("Error!", "There was a problem closing the chat.", "error");
            }
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire("Cancelled", "Your chat conversation is safe :)", "error");
          }
        });
    };


const convertedTime = (time) => {
  if (time) {
    const date = new Date(time);
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
  }
  return ""; // Default value if time is invalid or empty
};

    const fetchUser = async (userId) => {
      try{
        const response = await axios.get(`/chat/fetch-user?user_id=${userId}`);
        return response.data;
      }catch(error) {
        console.error(error);
      }
    }
onMounted(async () => {
  try {
    if (selectedConversationId.value) {
      await selectConversation(selectedConversationId.value);
    }

    const firebaseConfig = props.firebaseConfig;
    if (!firebase.apps.length) {
      firebase.initializeApp(firebaseConfig);
    }

    const chatRef = firebase.database().ref('conversation');
    let timeout = false;

    // Format time for the existing conversationsData
    conversationsData.value.forEach((chat) => {
      chat.last_message_time = formatTime(new Date(chat.last_message_time)); // Format existing time
    });

    // Listen to changes in conversation messages
    conversationsData.value.forEach((chat) => {
      chatRef.child(chat.id).on('value', async function(snapshot) {
        const value = snapshot.val();

        if (timeout && value && value.sender_type !== 'admin') {
          if (value.conversation_id == selectedConversationId.value) {
            const timestamp = new Date(value.created_at);
            const formattedTime = formatTime(timestamp);

            let chatData = {
              id: value.message_id,
              align: 'left',
              message: value.message,
              audio_url:value.audio_url,
              profile_picture: profile.value,
              time: formattedTime, // Formatted time
            };
            messagesData.value.push(chatData);
            
            scrollToBottom('users-chat');
          } else {
            let selectedConversation = conversationsData.value.find(conv => conv.id === value.conversation_id);
            if (selectedConversation) {
              selectedConversation.unread = parseInt(selectedConversation.unread) + 1; // Update unread count
              selectedConversation.last_message_time = formatTime(new Date(value.created_at)); // Update last message time
            } else {
              console.error("Conversation not found!");
            }
          }
        }
      });
    });

    setTimeout(function () {
      timeout = true;
    }, 2000);

    chatRef.on('child_added', async function(snapshot) {
      if (timeout) {
        const value = snapshot.val();
        const user = await fetchUser(value.sender_id);

        const timestamp = new Date(value.created_at);
        const formattedTime = formatTime(timestamp);

        if (value.conversation_id == selectedConversationId.value) {
          let chatData = {
            id: value.message_id,
           
            align: 'left',
            role: user.name,
            message: user.name,
            
            profile_picture: user.profile_picture,
            time: formattedTime,
          };
          messagesData.value.push(chatData);
          scrollToBottom('users-chat');
        } else {
          try {
            const response = await axios.get(`/chat/verify-chat?conversationId=${value.conversation_id}`);
            const conversation = response.data.data;
            if(conversation) {
              let chatterData = {
                id: conversation.id,
                unread: 1,
                subject: conversation.subject,
                role: user.role_name,
                profile_picture: conversation.profile_picture,
                last_message_time: formattedTime,
              };
              conversationsData.value.unshift(chatterData);
            }
          }catch(error){
            console.log('no conversation found');
            console.error(error);
          }
        }
      }
    });


  } catch (error) {
    console.error(error);
  }
});


    return {
      v$,
      form,
      handleAudioUpload,
      conversationsData,
      messagesData,
      formatDuration,
      formSubmit,
      isSubmitting,
      selectedConversationId,
      scrollToBottom,
      convertedTime,
      selectConversation,
      username, // Return username
      profile, // Return profile
      cancel, // Return the cancel method
      userSearch,
      toggleSearch,
      searchQuery,
      users,
      onSearch,
      createChat,
    };
  },

  components: {
    Layout,
    simplebar,
    Head,
  },

validations: {
  form: {
    message: {
      required: helpers.withMessage("Message or audio is required", (value, { parent }) => {
        return value || parent.audio; // Only require message if no audio
      }),
    },
  },
},



};
</script>


<template>
  
<Layout>
<div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
  <div class="chat-leftsidebar">
    <div class="px-4 pt-4 mb-4">
      <div class="d-flex align-items-center gap-4">
        <div v-if="!userSearch" class="flex-grow-1">
          <h5 class="mb-0">{{$t("chats")}}</h5>
        </div>
        <div v-else class="flex-grow-1">
          <h5 class="mb-0">{{$t("users")}}</h5>
        </div>
        <div v-if="!userSearch" class="flex-shrink-0">
          <div v-b-tooltip.hover title="Add Contact">
            <BButton type="button" @click="toggleSearch" variant="soft-success" size="sm">
              <i class="ri-add-line align-bottom"></i>
            </BButton>
          </div>
        </div>
        <div v-else class="flex-shrink-0">
          <div v-b-tooltip.hover title="Cancel">
            <BButton type="button" @click="toggleSearch" variant="soft-success" size="sm">
              <i class="ri-close-line align-bottom"></i>
            </BButton>
          </div>
        </div>
 <!-- search contact -->
    <div v-if="userSearch" class="dropdown-menu-xl">

      <div class="search-box position-relative">
        <input type="text" class="form-control bg-light border-light" placeholder="Search users..."
          id="searchMessage" v-model="searchQuery" @input="onSearch" />
        <i class="ri-search-2-line search-icon"></i>
        
       
      </div>
    </div>
<!-- search contact end -->
  </div>
</div>

    <simplebar v-if="userSearch" class="chat-room-list" data-simplebar>

      <div class="d-flex align-items-center px-4 mb-2">
        <div class="flex-grow-1">
          <h4 class="mb-0 fs-11 text-muted text-uppercase">
            {{$t("users")}}
          </h4>
        </div>
      </div>

      <div class="chat-message-list">
        <div class="list-unstyled chat-list chat-user-list">
          <li class v-for="(user, index) in users" :key="user.id" @click="createChat(user)">
            <BLink href="javascript: void(0);">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 chat-user-img online align-self-center me-2 ms-0">
                  <div class="avatar-xxs avatar-xs" v-if="user.profile_picture">
                    <img :src="`${user.profile_picture}`" class="rounded-circle img-fluid userprofile avatar-xs" alt />
                  </div>
                  <div class="avatar-xxs avatar-xs" v-if="!user.profile_picture">
                    <div class="avatar-title rounded-circle bg-danger userprofile avatar-xs">
                      {{ user.profile_picture }}
                    </div>
                  </div>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                  <p class="text-truncate mb-1">
                    {{ user.name }} 
                  </p>
                  <p class="text-muted mb-1">
                    {{ user.role_name }} 
                  </p>
                </div>

              </div>
            </BLink>
          </li>
        </div>
      </div>
    </simplebar>
    <simplebar v-else class="chat-room-list" data-simplebar>
      <div class="d-flex align-items-center px-4 mb-2">
        <div class="flex-grow-1">
          <h4 class="mb-0 fs-11 text-muted text-uppercase">
            {{$t("direct_messages")}}
          </h4>
        </div>
      </div>

      <div class="chat-message-list">
        <div class="list-unstyled chat-list chat-user-list">
          <li class v-for="data of conversationsData" :key="data.id" @click="selectConversation(data.id)"
          :class="{ active: selectedConversationId === data.id }" >
            <BLink href="javascript: void(0);">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 chat-user-img online align-self-center me-2 ms-0">
                  <div class="avatar-xxs avatar-xs" v-if="data.profile_picture">
                    <img :src="`${data.profile_picture}`" class="rounded-circle img-fluid userprofile avatar-xs" alt />
                  </div>
                  <div class="avatar-xxs avatar-xs" v-if="!data.profile_picture">
                    <div class="avatar-title rounded-circle bg-danger userprofile avatar-xs">
                      {{ data.profile_picture }}
                    </div>
                  </div>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                  <p class="text-truncate mb-1">
                    {{ data.subject }} 
                  </p>
                  <p class="text-muted mb-1">
                    {{ data.role }} 
                  </p>
                </div>
              <div class="d-flex align-items-center">
                <span v-if="data.unread > 0" class="position-absolute translate-middle badge border border-light rounded-circle bg-success p-1"> {{ data.unread }}</span>
              </div>

                <div class="flex-shrink-0">
                  <BBadge variant="dark-subtle" v-if="data.unread > 0" class="bg-dark-subtle text-body rounded p-1">{{ data.last_seen ?? convertedTime(data.last_message_time)
                  }}</BBadge>
                </div>
              </div>
            </BLink>
          </li>
        </div>
      </div>

     
    </simplebar>
  </div>
  
  <div class="user-chat w-100 overflow-hidden">
    <div class="chat-content d-lg-flex">
      <div class="w-100 overflow-hidden position-relative">
        <div class="position-relative" v-if="selectedConversationId">
          <div class="p-3 user-chat-topbar">
            <BRow class="align-items-center">
              <BCol sm="4" cols="8">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 d-block d-lg-none me-3">
                    <BLink href="javascript: void(0);" class="user-chat-remove fs-18 p-1"><i
                        class="ri-arrow-left-s-line align-bottom"></i></BLink>
                  </div>
                  <div class="flex-grow-1 overflow-hidden" >
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0 chat-user-img online user-own-img align-self-center me-3 ms-0">
                        <img :src="profile ? profile : require('@/assets/images/users/user-dummy-img.jpg')"
                          class="rounded-circle avatar-xs" alt="" />
                        <span class="user-status"></span>
                      </div>
                      <div class="flex-grow-1 overflow-hidden">
                        <h5 class="text-truncate mb-0 fs-16">
                          <BLink class="text-reset username">{{ username }}
                          </BLink>
                        </h5>
                      </div>
                    </div>
                  </div>
                </div>
              </BCol>
              <BCol sm="8" cols="4">
                <ul class="list-inline user-chat-nav text-end mb-0">

                  <li class="list-inline-item m-0">
                    <BDropdown variant="link" class="btn btn-ghost-secondary btn-icon" toggle-class=" arrow-none"
                      menu-class="dropdown-menu" aria-haspopup="true">
                      <template #button-content><i class="ri-settings-2-fill"></i>
                      </template>
                      <BDropdownItem><i class=" ri-mail-close-fill align-bottom text-danger me-2"></i>
                        <span class="text-danger" id="sa-params" @click="cancel">Close Chat</span></BDropdownItem>
                    </BDropdown>
                  </li>
                </ul>
              </BCol>
            </BRow>
          </div>
<!-- chat body -->
          <div class="position-relative" id="users-chat">
            <simplebar class="chat-conversation p-3 p-lg-4" id="chat-conversation" data-simplebar ref="current">
            <ul class="list-unstyled chat-conversation-list">
  <li v-for="message in messagesData" :key="message.id" :class="['chat-list', message.align]">
    <div class="conversation-list d-flex align-items-center">
      <div class="chat-avatar" v-if="message.align !== 'right'">
        <img :src="message.profile_picture" alt="" />
      </div>
      <div class="user-chat-content">
        <div class="ctext-wrap">
          <div class="ctext-wrap-content">
            <!-- Text message -->
            <p class="mb-0 ctext-content" v-if="message.message">
              {{ message.message }}
            </p>
            
            <!-- Audio message -->
            <div v-if="message.audio_url" class="mt-2 audio-message">
              <audio controls>
                <source :src="message.audio_url" type="audio/mpeg">
                Your browser does not support audio
              </audio>
              <span class="audio-duration" v-if="message.audio_duration">
                {{ formatDuration(message.audio_duration) }}
              </span>
            </div>
          </div>
        </div>
        <div class="conversation-name">
          <small class="text-muted time">{{ message.time }}</small>
          <span class="text-success check-icon">
            <i class="bx bx-check"></i>
          </span>
        </div>
      </div>
    </div>
  </li>
</ul>

            </simplebar>
          </div>
<!-- chat body end -->

<!-- chat input -->
 
<div class="chat-input-section p-3 p-lg-4">
  <form @submit.prevent="formSubmit">
    <BRow class="g-0 align-items-center">
      <BCol>
        <div class="d-flex align-items-center gap-2">
          <!-- Audio upload button with feedback -->
          <label class="btn btn-outline-secondary mb-0">
            <i class="ri-mic-line"></i>
            <input 
              type="file" 
              accept="audio/*" 
              @change="handleAudioUpload" 
              style="display: none;"
              :disabled="isSubmitting"
            >
          </label>
          
          <!-- Show selected file name -->
          <div v-if="form.audio" class="selected-file">
            <span class="badge bg-light text-dark">
              {{ form.audio.name }}
              <button 
                type="button" 
                class="btn-close btn-close-black ms-1" 
                @click="form.audio = null"
                style="font-size: 0.7rem;"
              ></button>
            </span>
          </div>
          
          <!-- Message input -->
          <input 
            v-model="form.message" 
            type="text" 
            class="form-control chat-input bg-light border-light"
            :placeholder="form.audio ? 'Add optional message...' : $t('enter_message')" 
            :disabled="isSubmitting"
          />
        </div>
      </BCol>
      <BCol cols="auto">
        <BButton 
          :disabled="isSubmitting || (!form.message && !form.audio)"
          variant="success" 
          type="submit" 
          class="chat-send"
        >
          <i v-if="!isSubmitting" class="ri-send-plane-2-fill align-bottom"></i>
          <span v-else class="spinner-border spinner-border-sm" role="status"></span>
        </BButton>
      </BCol>
    </BRow>
  </form>
</div>

         
<!-- chat input -->
        </div>
        <div class="position-relative" v-else>

        </div>
      </div>
    </div>
  </div>
</div>

</Layout>
 
</template>
