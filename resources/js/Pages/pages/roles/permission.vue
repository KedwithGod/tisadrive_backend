<template>
    <Layout>
      <Head title="Permissions" />
      <PageHeader title="Permissions" pageTitle="Roles" pageLink="/roles"/>
  
      <BRow>
        <BCol lg="12">
          <BCard no-body id="tasksList">
            <BCardHeader class="border-0">
              <BCol>
                <div class="form-group mt-3 d-flex justify-content-between">
                  <div>
                    <h3>{{$t("role")}}: <b>{{ role.name }}</b></h3>
                  </div>
                  <Link href="/roles">
                    <button class="btn btn-primary mt-3 float-end btn-sm">{{$t("back")}}</button>
                  </Link>
                </div>
              </BCol>
            </BCardHeader>
            <BCardBody class="border border-dashed border-end-0 border-start-0">
              <table class="table table-bordered permissions-table">
                <thead>
                  <tr>
                    <!-- <th>#</th> -->
                    <th>{{$t("main_menu")}}</th>
                    <th>{{$t("slugs")}}</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-for="(mainMenu, index) in getMainMenus" :key="index">
                    <tr v-for="(permission, i) in getPermissionsByMainMenu(mainMenu)" :key="permission.id">
                      <!-- Serial Number -->
                      <!-- <td>{{ i + 1 }}</td> -->
                      <!-- Main Menu -->
                      <td v-if="i === 0" :rowspan="getRowSpan(mainMenu)">
                            <input class="form-check-input" type="checkbox" :id="mainMenu" 
                            :checked="isMenuChecked(mainMenu)"
                            @change="toggleMenu(mainMenu)">
                            <label class="form-check-label mx-2" :for="mainMenu">
                                {{ mainMenu }}
                            </label>
                      </td>
                      <!-- Slugs -->
                      <td>
                        <div class="form-check form-check-success mb-3">
                            <input class="form-check-input" type="checkbox" :id="permission.id" 
                            :checked="isChecked(permission.slug)"
                            @change="toggleSlug(permission.slug, permission.main_menu)">
                            <label class="form-check-label mx-2" :for="permission.id">
                                {{ permission.slug }}
                            </label>
                        </div>
                        <!-- <input
                          type="checkbox"
                          :checked="isChecked(permission.slug)"
                          @change="toggleSlug(permission.slug, permission.main_menu)"
                        />
                        {{ permission.slug }} -->
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
              <button @click="savePermissions" class="btn btn-primary mt-3 float-end">
                {{ permissionRole.length === 0 ? $t('save_permissions') : $t('update_permissions')}}
              </button>
            </BCardBody>
          </BCard>
        </BCol>
      </BRow>
  
      <!-- Success Message -->
      <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show bounce" role="alert" id="alertMsg">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i> 
          <strong>Success</strong> - {{ successMessage }}
          <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
           <!-- Progress bar -->
           <div class="progress-container">
                    <div class="progress-bar" :style="{ width: progressWidth + '%' }"></div>
                </div>
        </div>
      </div>
    </Layout>
  </template>
  
  <script>
  import { defineComponent, ref, reactive, onMounted, computed } from 'vue';
  import { Link, Head } from '@inertiajs/vue3';
  import Layout from "@/Layouts/main.vue";
  import PageHeader from "@/Components/page-header.vue";
  import axios from 'axios';
  
  export default defineComponent({
    components: {
      Layout,
      PageHeader,
      Link,
      Head
    },
    props: {
      role: Object,
      permissions: Array,
      permissionRole: Array,
    },
    setup(props) {
      const checkedPermissions = ref(new Set());
      const successMessage = ref('');
      const progressWidth = ref(100);  // Start with 100% width for progress bar
  
      const getMainMenus = computed(() => {
        return Array.from(new Set(props.permissions.map(item => item.main_menu)));
      });
  
      const getPermissionsByMainMenu = (mainMenu) => {
        return props.permissions.filter(item => item.main_menu === mainMenu);
      };
  
      const getRowSpan = (mainMenu) => {
        return props.permissions.filter(item => item.main_menu === mainMenu).length;
      };
      const isMenuChecked = (mainMenu) => {
        let mainPermissions = props.permissions.filter(item => item.main_menu === mainMenu);
        return mainPermissions.every(permission => checkedPermissions.value.has(permission.slug))
      }
  
      const toggleMenu = (mainMenu) => {
        if(isMenuChecked(mainMenu)){
          props.permissions.filter(item => item.main_menu === mainMenu).forEach((permission)=> {
            if (checkedPermissions.value.has(permission.slug)) {
              checkedPermissions.value.delete(permission.slug);
            }
          })
        } else {
          props.permissions.filter(item => item.main_menu === mainMenu).forEach((permission)=> {
            if (!checkedPermissions.value.has(permission.slug)) {
              checkedPermissions.value.add(permission.slug);
            }
          })
        }
      };
      const toggleSlug = (slug, mainMenu) => {
        if (checkedPermissions.value.has(slug)) {
          checkedPermissions.value.delete(slug);
        } else {
          checkedPermissions.value.add(slug);
        }
      };
  
      const isChecked = (slug) => {
        return checkedPermissions.value.has(slug);
      };
  
      const savePermissions = () => {
        const payload = {
          permissions: Array.from(checkedPermissions.value)
        };
  
        axios.post(`/permissions/${props.role.id}`, payload)
          .then(response => {
            if (response.status >= 200 && response.status < 300) {
              successMessage.value = response.data.message;
        // Reset progress bar to full width
        progressWidth.value = 100;

// Update the progress bar over 5 seconds (5000 milliseconds)
const intervalId = setInterval(() => {
  progressWidth.value -= 1;  // Decrease width by 1% every 50ms (100% over 5000ms)
  if (progressWidth.value <= 0) {
    clearInterval(intervalId);
  }
}, 20);  // 50ms interval

// Automatically dismiss the success message after 5 seconds
setTimeout(() => {
  dismissMessage();
}, 2000);
            }
          })
          .catch(error => {
            console.error(error);
          });
      };
  
      onMounted(() => {
        if (props.permissionRole && props.permissionRole.length > 0) {
          props.permissionRole.forEach(permission => {
            checkedPermissions.value.add(permission.slug);
          });
        }
      });
  
      const dismissMessage = () => {
        successMessage.value = "";
        progressWidth.value = 0;  // Reset progress bar
      };
  
      return {
        getMainMenus,
        getPermissionsByMainMenu,
        getRowSpan,
        isChecked,
        isMenuChecked,
        toggleSlug,
        toggleMenu,
        savePermissions,
        successMessage,
        dismissMessage,
        progressWidth
      };
    }
  });
  </script>
  
  <style scoped>
  .permissions-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    margin-top: 1rem;
  }
  
  .permissions-table th,
  .permissions-table td {
    padding: 0.75rem;
    text-align: left;
    vertical-align: middle;
  }
  
  .custom-alert {
    max-width: 600px;
    float: right;
    position: fixed;
    top: 90px;
    right: 20px;
  }
  /* Bounce animation */
@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-20px);
  }
  60% {
    transform: translateY(-10px);
  }
}

.bounce {
  animation: bounce 1s ease;
}

.custom-alert {
  max-width: 600px;
  float: right;
  position: fixed;
  top: 90px;
  right: 20px;
  z-index: 1000;
}
.rtl .custom-alert {
    max-width: 600px;
    float: right;
    position: fixed;
    top: 100px;
    right: 80px;
}
/* Progress Bar Styles */
.progress-container {
  position: relative;
  height: 4px;
  background-color: #e9ecef;
  margin-top: 10px;
  border-radius: 2px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background-color: #28a745;
  transition: width 0.05s ease;
}
  </style>
  