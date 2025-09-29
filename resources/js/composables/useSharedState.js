import { reactive, toRefs } from 'vue';
import axios from 'axios'; // Make sure to install axios and import it

const state = reactive({
  languages: null,
  locations: null,
  selectedLocation: null,
  notifications: [],
  chats: [],
  unreadNotification: 0,
  unreadChat: 0,
  permissions: [],
});

export function useSharedState() {
  const fetchData = async () => {
    if (state.languages == null) {
      const response = await fetch('/current-languages');
      const json = await response.json();
      state.languages = json.data.map(language => ({
        value: language.id,
        label: language.name,
        code: language.code,
        default: language.default_status,
        direction: language.direction
      }));
    }
  };

  const audio = new Audio("/audio/notification.mp3");
  let isAudioPlaying = false;

  const playAudioOnce = () => {
    if (!isAudioPlaying) {
      isAudioPlaying = true;
      audio.play().then(() => {
        isAudioPlaying = false;
      }).catch((error) => {
        console.error("Audio playback failed:", error);
        isAudioPlaying = false;
      });
  
      audio.onended = () => {
        isAudioPlaying = false;
      };
    }
  };
  const fetchPermissions = async () => {
    if (state.permissions == null) {
      const response = await fetch('/user/permissions');
      const json = await response.json();
      state.permissions = json.data;
    }
  };

  const fetchLocations = async () => {
    if (state.locations == null) {
      const response = await fetch('/current-locations');
      const json = await response.json();
      state.locations = json.data.map(loc => ({
        value: loc.id,
        label: loc.name,
      }));
    }
  };

  const setServiceLocation = (locationId) => {
    state.selectedLocation = locationId;
    localStorage.setItem('selectedLocation', locationId);
  };

  if (localStorage.getItem('selectedLocation')) {
    state.selectedLocation = localStorage.getItem('selectedLocation');
  }
  const createChatListener = (chatRef, chat_id) => {
    chatRef.child(chat_id).on('value', async function (snapshot) {
      const value = snapshot.val();
  
      if (value && value.sender_type === 'user') {
        let selectedConversation = state.chats.find((conv) => conv.id === value.conversation_id);
        if (selectedConversation) {
            selectedConversation.unread = parseInt(selectedConversation.unread) + 1;
            selectedConversation.last_sent = value.created_at;
            selectedConversation.last_message = value.message;
  
          if (audio.paused) {
            playAudioOnce();
          }
        }
      }
    });
  };

  const convertedTime = (time) => {
    if(time){
      const date = new Date(time);
    
      const hours = date.getHours().toString().padStart(2, '0');
      const minutes = date.getMinutes().toString().padStart(2, '0');

      return `${hours}:${minutes}`;
    }
    return '';
  }
  
  // Firebase notification fetching
  const fetchFirebaseSettings = async () => {
    try {
      // Fetch Firebase settings from your backend
      const response = await axios.get('/firebase/get');
      const firebaseSettings = response.data.response;
  
      // Firebase configuration object from fetched settings
      const firebaseConfig = {
        apiKey: firebaseSettings.firebase_api_key,
        authDomain: firebaseSettings.firebase_auth_domain,
        databaseURL: firebaseSettings.firebase_database_url,
        projectId: firebaseSettings.firebase_project_id,
        storageBucket: firebaseSettings.firebase_storage_bucket,
        messagingSenderId: firebaseSettings.firebase_messaging_sender_id,
        appId: firebaseSettings.firebase_app_id,
      };
  
      // Initialize Firebase if not already initialized
      if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
      }
  
      // Reference to Firebase notifications
      const notificationsRef = firebase.database().ref('admin-notification');
      // console.log("Snapshot Data:", notificationsRef.get()); // Log the snapshot data

// Listen for changes in notifications
      notificationsRef.on('value', (snapshot) => {
        const data = snapshot.val(); // Correct way to get snapshot data
        
        if (data) {
          // Convert the object into an array of notifications and filter by read: false
          const notificationsArray = Object.keys(data)
            .map(key => ({
              id: key,
              ...data[key],
            }))
            .filter(notification => !notification.read)
            .sort((a, b) => b.updated_at - a.updated_at); // Filter out only unread notifications

          // Update state with unread notifications
          state.notifications = notificationsArray;
          state.unreadNotification = notificationsArray.length; // Count unread notifications

        } else {
          console.log("No notifications found.");
        }
      });
      const chatRef = firebase.database().ref('conversation');
      let processedChats = new Set();
      let timeout = false;
      const chatResponse = await fetch('/chat/fetchChat').then((response)=> {
        return response.json()
      }).then((result) => {
        return result;
      });
      state.chats = chatResponse;

      state.chats.forEach((chat) => {
        if(chat.unread){
          state.unreadChat += parseInt(chat.unread);
        }
        processedChats.add(chat.id);
        chatRef.child(chat.id).on('value', async function(snapshot) {
          var value = snapshot.val();

          if(timeout && value && value.sender_type == 'user'){
            let selectedConversation = state.chats.find(conv => conv.id === value.conversation_id);
            if (selectedConversation) {
                selectedConversation.unread = parseInt(selectedConversation.unread) + 1;
                selectedConversation.last_seen = convertedTime(value.created_at);
                selectedConversation.last_message = value.message;
                state.unreadChat++;
                playAudioOnce();
            }
          }
        })
      })
      

      setTimeout(() => {
        timeout = true;
      },4000)
      chatRef.on('child_added',async function(snapshot) {
        if(timeout) {
          const value = snapshot.val();

          if (processedChats.has(value.conversation_id)) {
            return;
          }

          const response = await axios.get(`/chat/verify-chat?conversationId=${value.conversation_id}`);
          const conversation = response.data.data;
          if(conversation) {
            processedChats.add(value.conversation_id);
  
            const user = await fetchUser(value.sender_id);
            let chatterData = {
                id : value.conversation_id,
                unread : 1,
                subject : user.name,
                profile_picture : user.profile_picture,
                last_seen : convertedTime(value.created_at),
                last_message : value.message,
            };
            createChatListener(chatRef,value.conversation_id);
            state.unreadChat ++;
            state.chats.unshift(chatterData);
            playAudioOnce();
          }

        }
      })
  
    } catch (error) {
      console.error('Error initializing Firebase or fetching settings:', error);
    }
  };

// Mark a specific notification as read and handle snapshot first
const handleNotificationClick = async (notificationId) => {
  try {
    // Fetch the notification snapshot from Firebase
    const snapshot = await firebase.database().ref(`admin-notification/${notificationId}`).once('value');
    const notification = snapshot.val();

    if (notification) {
      console.log('Snapshot Data:', notification); // Log the notification data

      // Check if the notification is already read
      if (!notification.read) {
        // Update notification as read in Firebase
        await firebase.database().ref(`admin-notification/${notificationId}`).update({ read: true });
        
        // Optional: Delay to ensure the update completes (if necessary)
        await new Promise(resolve => setTimeout(resolve, 100)); // 100ms delay

        // Optionally remove the notification from Firebase after marking it as read
        await firebase.database().ref(`admin-notification/${notificationId}`).remove();
        
        // Update local state if needed
        notification.read = true;
        state.unreadNotification = state.notifications.filter(n => !n.read).length;

        console.log('Notification marked as read:', notificationId);
      }

      // Redirect to the notification's URL if available
      if (notification.url) {
        window.location.href = notification.url;
      }

    } else {
      console.error('Notification not found in Firebase:', notificationId);
    }

  } catch (error) {
    console.error('Error fetching, updating, or removing notification:', error);
  }
};



const fetchUser = async (userId) => {
  try{
    const response = await axios.get(`/chat/fetch-user?user_id=${userId}`);
    return response.data;
  }catch(error) {
    console.error(error);
  }
}



const markChatAsRead = async (chatId) => {
  try{
    const selectedChat = state.chats.find(conv => conv.id == chatId);
    state.unreadChat -= selectedChat.unread;
    state.chats = state.chats.filter(conv => conv.id !== chatId);
  }catch(error) {
    console.error(error);
  }
}

  // Mark all notifications as read
  const handleMarkAllAsRead = async (type = 'notification') => {
    try {
      // Get the reference to all notifications
      if(state.unreadNotification > 0 && type == 'notification'){

        const notificationsRef = firebase.database().ref('admin-notification');

        // Fetch all notifications from Firebase
        const notificationSnapshot = await notificationsRef.once('value');
        const notifications = notificationSnapshot.val();

        // Update each notification to mark it as read
        if (notifications) {
          const updates = {};
          Object.keys(notifications).forEach(key => {
            updates[`${key}/read`] = true;
          });

          // Apply the updates in Firebase
          await notificationsRef.update(updates);

          // Update local state
          state.notifications.forEach(n => n.read = true);
          state.unreadNotification = 0;
          window.location.reload();
        }
      }
      if(state.unreadChat > 0 && type == 'chat'){
        await fetch('/chat/readAll').then((response)=> {
          return response.json()
        }).then((result) => {
          return result;
        });

        state.chats = [];
        state.unreadChat = 0;
      }
    } catch (error) {
      console.error('Error marking all notifications as read:', error);
    }
  };

  return {
    ...toRefs(state),
    fetchData,
    fetchLocations,
    setServiceLocation,
    fetchFirebaseSettings,
    handleNotificationClick,
    handleMarkAllAsRead,
    markChatAsRead,
    playAudioOnce,
    fetchPermissions
  };
}
