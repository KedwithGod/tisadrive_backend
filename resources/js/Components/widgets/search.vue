<template>
    <div class="search1" :class="{ active: isActive, searching: isSearching }" @click="focusInput">
      <input placeholder="search..." class="input" type="text" v-model="inputValue" @input="handleInput" @keyup.enter="handleEnter" ref="searchInput" />
      <svg viewBox="0 0 700 100" class="magnifying-glass">
        <path
          class="magnifying-glass-path"
          d="m 59.123035,59.123035 c -10.561361,10.56136 -27.684709,10.56136 -38.24607,0 -10.56136,-10.561361 -10.56136,-27.684709 0,-38.24607 10.561361,-10.56136 27.684709,-10.56136 38.24607,0 10.56136,10.561361 10.56136,27.684709 0,38.24607 l 28.876965,28.876965 c 6.304625,7.101523 5.754679,-0.187815 13.07143,-0.5 h 582.04101"
        />
        <path
          class="x"
          d="m 673.46803,25.714286 -37.17876,38.816532 c 0,0 -5.08857,5.60515 -5.68529,11.841734 -1.06622,11.143538 13.02902,11.127448 13.02902,11.127448"
        />
        <path
          class="x"
          d="m 635.08021,25.714286 37.17876,38.816532 c 0,0 5.08857,5.60515 5.68529,11.841734 1.06622,11.143538 -13.02902,11.127448 -13.02902,11.127448"
        />
      </svg>
      <div class="overlay overlay-1" @click.stop="toggleSearch"></div>
      <div class="overlay overlay-2" @click.stop="clearInput"></div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        isActive: false,
        isSearching: false,
        inputValue: '',
      };
    },
    methods: {
      toggleSearch() {
        this.isActive = !this.isActive;
        if (this.isActive) {
          this.$nextTick(() => {
            this.$refs.searchInput.focus();
          });
        }
      },
      focusInput() {
        if (this.isActive) {
          this.$nextTick(() => {
            this.$refs.searchInput.focus();
          });
        }
      },
      clearInput() {
        this.inputValue = '';
        this.isSearching = false;
        this.$refs.searchInput.focus();
      },
      handleInput() {
        this.isSearching = this.inputValue.length > 0;
      },
      handleEnter() {
        this.$refs.searchInput.blur();
      },
      handleBodyClick(e) {
        if (!this.$el.contains(e.target) && this.inputValue.length === 0) {
          this.isActive = false;
          this.isSearching = false;
          this.inputValue = '';
        }
      },
    },
    mounted() {
      document.body.addEventListener('click', this.handleBodyClick);
    },
    beforeDestroy() {
      document.body.removeEventListener('click', this.handleBodyClick);
    },
  };
  </script>
  
  <style scoped>
  .search1 {
    position: absolute;
    width: 200px;
    right: 160px;
    top:5px;
   
  }
  .magnifying-glass {
    position: absolute;
    transform: translateX(40%);
    transition: transform 500ms;
  }
  .search1.active .magnifying-glass {
    transform: translateX(-45%);
  }
  .magnifying-glass-path {
    fill: none;
    stroke-dasharray: 210 808;
    stroke-linecap: round;
    stroke-width: 2;
    stroke: #000;
    transition: stroke-dasharray 500ms, stroke-dashoffset 500ms;
  }
  .search1.active .magnifying-glass-path {
    stroke-dasharray: 580 808;
    stroke-dashoffset: -224px;
  }
  .x {
    fill: none;
    stroke-dasharray: 56 92;
    stroke-dashoffset: -92px;
    stroke-linecap: round;
    stroke-width: 6;
    stroke: #000;
    transition: stroke-dashoffset 500ms;
    visibility: hidden;
  }
  .search1.active .x {
    visibility: visible;
  }
  .search1.searching .x {
    stroke-dashoffset: 0;
  }
  .input {
    border: 0;
    font-size: 1.2em;
    right: 130px;
    outline: 0 !important;
    position: absolute;
    top: 0px;
    background: transparent;
    width: 125px;
    visibility: hidden;
  }
  .search1.active .input {
    border: 0;
    font-size: 1.2em;
    right: 130px;
    outline: 0 !important;
    position: absolute;
    top: 0px;
    background: transparent;
    width: 125px;
    visibility: visible;
  }
  .overlay {
    background: rgba(0, 0, 0, 0);
    border-radius: 50%;
    cursor: pointer;
    position: absolute;
    transition: background 300ms;
  }
  .overlay-1 {
    height: 50px;
    left: 70px;
    top: -10px;
    width: 50px;
  }
  .overlay-2 {
    height: 32px;
    left: 81px;
    top: -3px;
    width: 32px;
  }
  .overlay:hover {
    background: rgba(0, 0, 0, 0.08);
  }
  .search1.active .overlay-1 {
    visibility: hidden;
  }
  .search1:not(.active) .overlay-2,
  .search1:not(.searching) .overlay-2 {
    visibility: hidden;
  }
  /* Media Queries */
@media (max-width: 1200px) {
  .search1 {
    width: 180px;
    right: 140px;
  }
  .input {
    width: 110px;
  }
}
@media (max-width: 992px) {
  .search1 {
    width: 160px;
    right: 120px;
  }
  .input {
    width: 90px;
  }
}
@media (max-width: 768px) {
  .search1 {
    width: 140px;
    right: 100px;
  }
  .input {
    width: 80px;
  }
}
@media (max-width: 576px) {
  .search1 {
    width: 120px;
    right: 80px;
  }
  .input {
    width: 70px;
  }
}
@media (max-width: 480px) {
  .search1 {
    width: 100px;
    right: 5px;
    top:12px;
  }
  .input {
    width: 60px;
  }
  .search1.active .magnifying-glass[data-v-3a130a05] {
    transform: translateX(7%);
}
.overlay-1[data-v-3a130a05] {
    height: 40px;
    left: 26px;
    top: -13px;
    width: 40px;
}
.search1.active .input[data-v-3a130a05] {
    border: 0;
    font-size: 1.2em;
    right: 16px;
    outline: 0 !important;
    position: absolute;
    top: -9px;
    background: transparent;
    width: 60px;
    visibility: visible;
}
}
  </style>