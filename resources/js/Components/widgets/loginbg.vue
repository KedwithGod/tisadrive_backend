<template>
    <div>
      <div v-if="!imageFileUrl" class="uploader-wrapper">
        <div class="image-dropzone" @click="chooseImage">
          <p class="text-muted">Click to Upload</p>
          <i class="ri-upload-cloud-fill img-up"></i>
        </div>
        <input
          type="file"
          style="display: none"
          ref="imageInput"
          accept="image/png,image/jpeg,image/jpg"
          @change="handleImageChosen"
        />
      </div>
      <div v-else class="image-wrapper">
        <img
          width="320"
          :src="imageFileUrl"
          class="uploaded-image"
        />
        <button class="clear-button" @click="clearImage">Clear</button>
      </div>
      <span v-if="error" class="text-danger">{{ error }}</span>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      modelValue: {
        type: File,
        default: null,
      },
      initialImageUrl: {
        type: String,
        default: null,
      },
    },
    data() {
      return {
        imageFile: null,
        imageFileUrl: this.initialImageUrl,
        error: null,
      };
    },
    watch: {
      imageFile(newValue) {
        this.$emit('update:modelValue', newValue);
      },
    },
    methods: {
      chooseImage() {
        this.$refs.imageInput.click();
      },
      handleImageChosen(e) {
        const files = e.target.files;
        if (files[0]) {
          const file = files[0];
          const fr = new FileReader();
          fr.onload = () => {
            const img = new Image();
            img.onload = () => {
              if (img.width === 512 && img.height === 512) {
                this.imageFileUrl = fr.result;
                this.imageFile = file;
                this.error = null; // Clear any previous error message
              } else {
                this.error = 'Image must be 512x512 pixels.';
                this.clearImage();
              }
            };
            img.src = fr.result;
          };
          fr.readAsDataURL(file);
        }
      },
      clearImage() {
        this.imageFile = null;
        this.imageFileUrl = null;
      },
    },
  };
  </script>
  
  <style scoped>
  .uploader-wrapper,
  .image-wrapper {
    margin: 50px auto;
  }
  
  .image-dropzone {
    display: grid;
    justify-content: center;
    align-items: center;
    width: 320px;
    height: 240px;
    border: 2px dashed #ccc;
    cursor: pointer;
  }
  
  .image-wrapper {
    display: flex;
    flex-direction: column;
    align-items: start;
  }
  
  .img-up {
    font-size: 60px;
    text-align: center;
  }
  
  .clear-button {
    background: none;
    border: solid 1px;
    border-radius: 2em;
    font: inherit;
    padding: 0.75em 2em;
    cursor: pointer;
    outline: none;
  }
  </style>
  