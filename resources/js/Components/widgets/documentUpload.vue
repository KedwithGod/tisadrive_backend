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
    </div>
  </template>
  
  <script>
  export default {
    props: {
      modelValue: File,
    },
    data() {
      return {
        imageFileUrl: null,
      };
    },
    methods: {
      chooseImage() {
        this.$refs.imageInput.click();
      },
      handleImageChosen(e) {
        const file = e.target.files[0];
        if (file) {
          const fr = new FileReader();
          fr.readAsDataURL(file);
          fr.addEventListener('load', () => {
            this.imageFileUrl = fr.result;
            this.$emit('update:modelValue', file);
          });
        } else {
          this.clearImage();
        }
      },
      clearImage() {
        this.imageFileUrl = null;
        this.$emit('update:modelValue', null);
      },
    },
  };
  </script>
  
  <style lang="scss">
  .clear-button {
    // color: #4fc08d;
    background: none;
    border: solid 1px;
    border-radius: 2em;
    font: inherit;
    padding: 0.75em 2em;
    cursor: pointer;
    outline: none;
  }
  
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
  </style>
  