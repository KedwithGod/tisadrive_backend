<template>
    <div>
        <!-- Thumbnail as a background image -->
    <div
      class="thumbnail" :style="{ backgroundImage: `url(${imageSrc})` }" @click="openModal" :alt="imageAlt">
    </div>
      <!-- <img :src="imageSrc" :alt="imageAlt" @click="openModal" width="100" class="thumbnail img-fluid" /> -->
      <!-- The Modal -->
      <div v-if="isModalOpen" class="modal" @click="closeModal">
        <img class="modal-content" :src="modalImageSrc" :alt="modalImageAlt" />
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      imageSrc: {
        type: String,
        required: true,
      },
      imageAlt: {
        type: String,
        required: true,
      },
    },
    data() {
      return {
        isModalOpen: false,
        modalImageSrc: '',
        modalImageAlt: '',
      };
    },
    methods: {
      openModal() {
        this.isModalOpen = true;
        this.modalImageSrc = this.imageSrc;
        this.modalImageAlt = this.imageAlt;
      },
      closeModal() {
        this.isModalOpen = false;
      },
    },
  };
  </script>
  
  <style scoped>
  .thumbnail {
  width: 100%; /* Thumbnail width */
  height: 150px; /* Thumbnail height */
  background-size: cover;
  background-position: top;
  border-radius: 5px;
  cursor: pointer;
  transition: opacity 0.3s;
  display: block;
  margin: auto;
}
  
  .thumbnail:hover {
    opacity: 1;
  }
  
  /* The Modal (background) */
  .modal {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed; /* Stay in place */
    z-index: 5; /* Sit on top */
    left: 0px;
    top: 0px;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    background-color: rgba(0, 0, 0, 0.9); /* Black w/ opacity */
    padding:100px;
  }
  
  /* Modal Content (image) */
  .modal-content {
    margin: auto;
    display: block;
    width: 65%;
    animation-name: zoom;
    animation-duration: 0.6s;
  }
  
  /* Caption of Modal Image */
  #caption {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
    text-align: center;
    color: #ccc;
    padding: 10px 0;
    height: 150px;
  }
  
  /* Add Animation */
  @-webkit-keyframes zoom {
    from {
      -webkit-transform: scale(1);
    }
    to {
      -webkit-transform: scale(2);
    }
  }
  
  @keyframes zoom {
    from {
      transform: scale(0.4);
    }
    to {
      transform: scale(1);
    }
  }
  
  /* The Close Button */
  .close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
  }
  
  .close:hover,
  .close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
  }
  
  /* 100% Image Width on Smaller Screens */
  @media only screen and (max-width: 700px) {
    .modal-content {
      width: 100%;
    }
  }
  </style>
  