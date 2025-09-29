<template>
  <div>
    <div id="image-container">
      <div id="image-slot" @click="triggerFileInput" v-if="images.length < 8">
        <div class="d-flex flex-column align-items-center">
          <span>Upload Image</span>
          <span>(1500px * 1000px)</span>
        </div>
        <input type="file" id="image-upload" ref="fileInput" @change="handleFileChange" multiple style="display: none;" />
      </div>
      <div v-for="(image, index) in images" :key="index" class="image-wrapper">
        <img :src="image.dataURL" @click="openModal(index)" />
        <div class="image-options" @click="removeImage(index)">X</div>
      </div>
    </div>

    <div v-if="isModalOpen" id="fullscreen-modal">
      <img :src="currentImage" alt="" id="fullscreen-image" />
      <button id="prev-image" @click="prevImage">&#10094;</button>
      <button id="next-image" @click="nextImage">&#10095;</button>
      <button id="close-modal" @click="closeModal">X</button>
    </div>

    <div v-if="showToast" id="toast">
      Sólo es posible cargar hasta 8 imágenes
    </div>
  </div>
</template>

<script>
import Sortable from "sortablejs";

export default {
  props: {
    initialImages: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      images: [],
      currentImageIndex: null,
      isModalOpen: false,
      showToast: false,
    };
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
    handleFileChange(event) {
      const files = event.target.files;
      const currentImages = this.images.length;
      const allowedUploads = 8 - currentImages;

      if (files.length > allowedUploads) {
        this.showToastMessage();
        return;
      }

      Array.from(files).forEach((file) => {
        if (this.validateImage(file) && this.validateImageSize(file)) {
          const reader = new FileReader();
          reader.onload = (e) => {
            this.images.push({ dataURL: e.target.result, name: file.name });
            this.$emit("image-selected", file); // Emit event with the selected file
          };
          reader.readAsDataURL(file);
        } else {
          this.showToastMessage();
        }
      });
    },
    validateImage(file) {
      return /^image\//.test(file.type);
    },
    validateImageSize(file) {
      // Add your size validation logic here if needed
      return true;
    },
    removeImage(index) {
      this.images.splice(index, 1);
      this.$emit("image-removed"); // Emit event when the image is removed
    },
    openModal(index) {
      this.currentImageIndex = index;
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
    },
    prevImage() {
      if (this.currentImageIndex > 0) {
        this.currentImageIndex--;
      }
    },
    nextImage() {
      if (this.currentImageIndex < this.images.length - 1) {
        this.currentImageIndex++;
      }
    },
    showToastMessage() {
      this.showToast = true;
      setTimeout(() => {
        this.showToast = false;
      }, 3000);
    },
  },
  computed: {
    currentImage() {
      return this.images[this.currentImageIndex]?.dataURL || "";
    },
  },
  mounted() {
    const imageContainer = this.$el.querySelector("#image-container");
    new Sortable(imageContainer, {
      animation: 150,
      handle: ".image-wrapper",
      filter: "#image-slot",
      ghostClass: "sortable-ghost",
      chosenClass: "sortable-chosen",
    });

    document.addEventListener("keydown", (event) => {
      if (this.isModalOpen) {
        if (event.key === "ArrowLeft") {
          this.prevImage();
        } else if (event.key === "ArrowRight") {
          this.nextImage();
        } else if (event.key === "Escape") {
          this.closeModal();
        }
      }
    });

    // Load initial images
    this.initialImages.forEach((imageUrl) => {
      this.images.push({ dataURL: imageUrl, name: "" });
    });
  },
};
</script>

<style scoped>
#image-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.image-wrapper,
#image-slot {
  flex: 0 0 calc(25% - 10px);
  aspect-ratio: 1 / 1; /* To keep it square */
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  margin: 0;
  border: 1px dashed #b0b0b0;
  padding: 10px;
}

.image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  cursor: pointer;
}

.image-options {
  position: absolute;
  top: 5px;
  right: 5px;
  background-color: rgba(255, 255, 255, 0.5);
  padding: 5px;
  cursor: pointer;
}

#image-slot {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f7f7f7;
  border: 2px dashed #ccc;
  cursor: pointer;
}

#fullscreen-modal {
  display: flex;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  align-items: center;
  justify-content: center;
  z-index: 100;
}

#fullscreen-image {
  max-width: 80%;
  max-height: 80%;
}

#prev-image,
#next-image,
#close-modal {
  position: absolute;
  background-color: rgba(255, 255, 255, 0.7);
  border: none;
  cursor: pointer;
  z-index: 101;
}

#prev-image {
  left: 10px;
}

#next-image {
  right: 10px;
}

#close-modal {
  top: 10px;
  right: 10px;
}

#toast {
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #333;
  color: white;
  padding: 10px 20px;
  border-radius: 5px;
}

.sortable-ghost {
  opacity: 0.5;
}

.sortable-chosen {
  transform: scale(1.05);
}
</style>
