<script>
export default {
props: {
initialImageUrl: {
type: String,
default: '',
},
flexStyle: {
      type: String,
      default: '0 0 calc(57% - 10px)',
    },
    aspectRatio: {
      type: String,
      default: '1 / 1',
    },
    imageType: { // Add a new prop to specify the type of image
      type: String,
      default: 'website', // Default to 'website' images
    },
},
data() {
return {
imageFile: null,
isModalOpen: false,
showToast: false,
};
},
methods: {
triggerFileInput() {
this.$refs.fileInput.click();
},
handleFileChange(event) {
const file = event.target.files[0];
if (file) {
if (this.validateImage(file) && this.validateImageSize(file)) {
const reader = new FileReader();
reader.onload = (e) => {
this.imageFile = {
dataURL: e.target.result,
name: file.name,
};
this.$emit('image-selected', file); // Emit event with the selected file
};
reader.readAsDataURL(file);
} else {
this.showToastMessage();
}
}
},
validateImage(file) {
return /^image\//.test(file.type);
},
validateImageSize(file) {
// Add your size validation logic here if needed
return true;
},
removeImage() {
this.imageFile = null;
this.$emit('image-removed'); // Emit event when the image is removed
},
removeInitialImage() {
this.$emit('image-removed'); // Emit event when the initial image is removed
},
openModal() {
this.isModalOpen = true;
},
closeModal() {
this.isModalOpen = false;
},
showToastMessage() {
this.showToast = true;
setTimeout(() => {
this.showToast = false;
}, 3000);
},
getFullImageUrl(filename) {
  switch (this.imageType) {
    case 'onboarding':
      return `/storage/uploads/onboarding/${filename}`;  
    case 'invoice':
    return `/storage/uploads/invoice/${filename}`;   
    default:
      return `/storage/uploads/website/images/${filename}`;
  }

},
},
};
</script>
<template>
<div>
<div id="image-container">
<div v-if="!imageFile && !initialImageUrl" :style="{ flex: flexStyle, aspectRatio: aspectRatio }" id="image-slot" @click="triggerFileInput">
  <div class="d-flex flex-column align-items-center">
  <span>{{$t("upload_image")}} </span>
<span><i class="ri-image-add-fill fs-24"></i></span></div>
<input type="file" id="image-upload" ref="fileInput" @change="handleFileChange" style="display: none;" />
</div>
<div v-else-if="initialImageUrl && !imageFile" :style="{ flex: flexStyle, aspectRatio: aspectRatio }" class="image-wrapper">
<img :src="getFullImageUrl(initialImageUrl)" @click="openModal" />
<div class="image-options bg-light border border-danger text-danger rounded fs-5" @click="removeInitialImage">X</div>
      <div class="image-upload-icon bg-light border border-success text-success rounded fs-5" @click="triggerFileInput">
          <i class=" ri-upload-fill"></i>
        </div>
        
        <!-- Hidden input for uploading new image -->
        <input type="file" ref="fileInput" @change="handleFileChange" style="display: none;" />
</div>
<div v-else :style="{ flex: flexStyle, aspectRatio: aspectRatio }" class="image-wrapper">
<img :src="imageFile.dataURL" @click="openModal" />
<div class="image-options bg-light border border-danger text-danger rounded fs-5" @click="removeImage">X</div>
        <div class="image-upload-icon bg-light border border-success text-success rounded fs-5" @click="triggerFileInput">
          <i class=" ri-upload-fill"></i>
        </div>

        <!-- Hidden input for uploading new image -->
        <input type="file" ref="fileInput" @change="handleFileChange" style="display: none;" />
</div>
</div>

<div v-if="isModalOpen" id="fullscreen-modal">
<img :src="imageFile ? imageFile.dataURL : getFullImageUrl(initialImageUrl)" alt="" id="fullscreen-image" />
<button id="close-modal" @click="closeModal">X</button>
</div>

<div v-if="showToast" id="toast">
Delete
</div>
</div>
</template>

<style scoped>
#image-container {
display: flex;
flex-wrap: wrap;
gap: 10px;
}

.image-wrapper,
#image-slot {
flex: 0 0 calc(57% - 10px);
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
color:#ffffff;
background-color: rgb(185, 53, 53);
padding: 5px 7px;
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
top: 50px;
left: 10px;
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

#close-modal {
position: absolute;
top: 25px;
right: 25px;
background-color: rgba(255, 255, 255, 0.7);
border: none;
cursor: pointer;
z-index: 101;
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
.image-upload-icon {
  position: absolute;
  top: 5px;
  right: 40px; /* Adjust spacing for the upload icon */
  color: #fffdfd; /* Green color for upload icon */
  background-color: rgb(43, 146, 2);
  padding: 5px;
  cursor: pointer;
}
</style>
