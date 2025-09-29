<template>
  <BRow>
    <BCol lg="12">
      <div>
        <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified" role="tablist">
          <BRow v-for="language in languages" :key="language.code">
            <BCol lg="12">
              <li class="nav-item" role="presentation">
                <a class="nav-link" @click="setActiveTab(language.label)" :class="{ active: activeTab === language.label }" role="tab" aria-selected="true">
                  {{ language.label }}
                </a>
              </li>
            </BCol>
          </BRow>
        </ul>
        <div class="tab-content text-muted" v-for="language in languages" :key="language.code">
          <div v-if="activeTab === language.label" class="tab-pane active show" :id="`${language.label}`" role="tabpanel">
            <div class="col-6 mt-3">
              <div class="mb-3">
                <label :for="`name-${language.code}`" class="form-label">Name</label>
                <input type="text" class="form-control" :placeholder="`Enter Name in ${language.label}`" :id="`name-${language.code}`"
                  v-model="form.tabFields[language.code].name" :required="language.code === 'en'">
              </div>
            </div>
          </div>
        </div>
      </div>
    </BCol>
  </BRow>
</template>

<script>
import { onMounted } from 'vue';
import { useSharedState } from '@/composables/useSharedState'; // Import the composable

export default {
  props: {
    form: Object,
  },
  data() {
    return {
      activeTab: 'English',
      languages: [] // Initialize an empty array for languages
    };
  },
  setup() {
    const { languages, fetchData } = useSharedState(); // Destructure the shared state

    onMounted(async () => {
      if (!languages.value.length) {
        await fetchData();
      }
      // Emit the languages-loaded event to the parent component
      this.$emit('languages-loaded', languages.value);
      this.languages = languages.value;
    });

    return {
      languages
    };
  },
  methods: {
    setActiveTab(tab) {
      this.activeTab = tab;
    }
  }
};
</script>

<style scoped>
.nav-tabs .nav-link {
  color: #000;
  border: 1px solid transparent;
  transition: background-color 0.3s ease;
}

.nav-tabs .nav-link:hover {
  background-color: #e9ecef;
}

.nav-tabs .nav-link.active {
  color: #28a745;
  border-bottom: 2px solid #28a745;
}
</style>
