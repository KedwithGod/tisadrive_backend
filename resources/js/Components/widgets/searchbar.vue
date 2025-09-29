<template>
  <div class="searchBox" :class="{ expanded: isExpanded }" @click.stop="expandSearchBox">
    <input
      class="searchInput"
      type="text"
      :placeholder="$t(placeholder)"
      v-model="searchQuery"
      @input="handleInput"
    />
    <button v-if="searchQuery" class="closeButton" @click="clearSearch">
      &times;
    </button>
    <button class="searchButton" @click="performSearch">
      <i class="ri-search-line search-icon"></i>
    </button>
    
  </div>
</template>

<script>
import { debounce } from 'lodash';
import { ref, watch, onMounted } from "vue";
export default {
  props: {
    placeholder: {
      type: String,
      default: 'Search...'
    }
  },
  data() {
    return {
      searchQuery: '',
      isExpanded: false,
    };
  },
  methods: {
    performSearch() {
      this.$emit('search',this.searchQuery);
    },
    expandSearchBox() {
      this.isExpanded = true;
      document.addEventListener('click', this.handleOutsideClick);
    },
    handleOutsideClick(event) {
      if (!this.$el.contains(event.target)) {
        this.isExpanded = false;
        document.removeEventListener('click', this.handleOutsideClick);
      }
    },
    
    handleInput: debounce(function () {
      this.$emit('search',this.searchQuery);
    }, 300),

    clearSearch() {
      this.searchQuery = '';
      this.$emit('search',this.searchQuery);
      this.isExpanded = false;
      document.removeEventListener('click', this.handleOutsideClick);
    },
  },
};
</script>

<style scoped>
/* custom search */
.searchBox {
  background: #ffffff;
  height: 40px;
  border-radius: 40px;
  border: 0.1rem solid #ced4da;
  right: 0; /* Align to the right */
  overflow: hidden; /* Prevents content overflow */
  display: flex;
  align-items: center;
  transition: width 0.4s, padding 0.4s; /* Smooth transition */
  width: 40px; /* Initial width */
}

.searchBox.expanded {
  width: 240px; /* Expanded width */
}

.searchButton {
  color: #5c5c5c;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: #ffffff;
  display: flex;
  justify-content: center;
  align-items: center;
  /* transition: 0.4s; */
  transition: background 0.4s, color 0.4s;
  border: none;
  position: relative;
  left: -1px;
}

.expanded .searchButton {
    color: #5c5c5c;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #ffffff;
    display: flex;
    justify-content: start;
    align-items: center;
    transition: 0.4s;
    border: none;
    position: relative;
    left: 0px;
}

.searchInput {
  border: none;
  background: none;
  outline: none;
  color: rgb(58, 58, 58);
  font-size: 14px;
  transition: width 0.4s; /* Smooth transition */
  line-height: 40px;
  width: 0px;
  transform-origin: right; /* Ensure the input expands from the right side */
  flex-grow: 1;
  padding: 0 10px;
}

.searchBox.expanded .searchInput {
  width: 100%;
}

.closeButton {
  background: none;
  border: none;
  color: #888;
  font-size: 20px;
  cursor: pointer;
  padding: 0 10px;
  display: flex;
  align-items: center;
}

.closeButton:hover {
  color: #333;
}
.searchBox:not(.expanded) .searchInput,
.searchBox:not(.expanded) .closeButton {
  display: none;
}

@media screen and (max-width: 620px) {
  .searchBox.expanded {
    width: 150px; /* Adjust for smaller screens */
  }
}
</style>

