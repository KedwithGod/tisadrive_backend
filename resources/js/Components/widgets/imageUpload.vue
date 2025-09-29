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
        @change="handleImageChoosen"
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
  data() {
    return {
      imageFile: null,
      imageFileUrl: null,
    };
  },
  methods: {
    chooseImage() {
      this.$refs.imageInput.click();
    },
    handleImageChoosen(e) {
      const files = e.target.files;
      
			if(files[0] !== undefined) {
				if(files[0].name.lastIndexOf('.') <= 0) {
					return
        }
        
        const fr = new FileReader();
        
        fr.readAsDataURL(files[0]);
        
				fr.addEventListener('load', () => {
          this.imageFileUrl = fr.result; // For DOM display purpose.
					this.imageFile = files[0]; // To be sent to server.
				});
			} else {
			  this.clearImage();
      }
    },
    clearImage() {
      this.imageFile = null;
      this.imageFileUrl = null;
    }
  }
};
</script>

<style lang="scss">


  // a,
  // .clear-button {
  //   color: #4fc08d;
  // }

  .clear-button {
    background: none;
    border: solid 1px;
    border-radius: 2em;
    font: inherit;
    padding: 0.75em 2em;
    cursor: pointer;
    outline: none;
  }

  .uploader-wrapper, .image-wrapper {
    margin: 50px auto;
  }
  
  .image-dropzone {
    display: grid;
    justify-content: center;
    align-items: center;
    width: 320px;
    height: 240px;
    // margin: 0 auto;
    border: 2px dashed #ccc;
    cursor: pointer;
  }
  
  .image-wrapper {
    display: flex;
    flex-direction: column;
    align-items: start;
     
    .clear-button {
      margin-top: 30px;
    }
  }
  .img-up{
    font-size: 60px;
    text-align: center;
  }
</style>