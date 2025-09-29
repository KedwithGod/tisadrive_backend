<template>
    <div>
      <div id="file-container">
        <div id="file-slot" @click="triggerFileInput" v-if="files.length < 8">
          <div class="d-flex flex-column align-items-center">
            <span>Upload File</span>
            <span>(Max: 8 files)</span>
            <span style="font-size:8px">png,jpg,jpeg,mp4,pdf,doc</span>
          </div>
          <input type="file" ref="fileInput" @change="handleFileChange" multiple 
            accept="image/*, video/mp4, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document"
            style="display: none;" />
        </div>
  
        <div v-for="(file, index) in files" :key="index" class="file-wrapper">
          <div class="file-preview" @click="openModal(index)">
            <img v-if="isImage(file)" :src="file.dataURL" />
            <video v-else-if="isVideo(file)" :src="file.dataURL" controls />
            <iframe v-else-if="isPDF(file)" :src="file.dataURL" width="100%" height="100%"></iframe>
            <div v-else class="file-icon">
              <i v-if="isDOCX(file)" class="fas fa-file-word"></i>
              <i v-else class="fas fa-file"></i>
              <span>{{ file.name }}</span>
            </div>
          </div>
          <div class="file-options" @click="removeFile(index)">X</div>
        </div>
      </div>
  
      <!-- MODAL FOR ALL FILE TYPES -->
      <div v-if="isModalOpen" id="fullscreen-modal">
        <img v-if="isImage(currentFile)" :src="currentFile.dataURL" id="fullscreen-image" />
        <video v-else-if="isVideo(currentFile)" :src="currentFile.dataURL" controls id="fullscreen-video"></video>
        <iframe v-else-if="isPDF(currentFile)" :src="currentFile.dataURL" width="80%" height="80%"></iframe>
        <a v-else-if="isDOCX(currentFile)" :href="currentFile.dataURL" target="_blank" class="docx-preview">
          <i class="fas fa-file-word"></i> Open DOCX
        </a>
  
        <button id="prev-file" @click="prevFile">&#10094;</button>
        <button id="next-file" @click="nextFile">&#10095;</button>
        <button id="close-modal" @click="closeModal">X</button>
      </div>
  
      <div v-if="showToast" id="toast">You can only upload up to 8 files.</div>
    </div>
  </template>
  
  <script>
  
  
  export default {
    data() {
      return {
        files: [],
        currentFileIndex: null,
        isModalOpen: false,
        showToast: false,
      };
    },
    methods: {
      triggerFileInput() {
        this.$refs.fileInput.click();
      },
      handleFileChange(event) {
        const selectedFiles = event.target.files;
        const currentFileCount = this.files.length;
        const allowedUploads = 8 - currentFileCount;
  
        if (selectedFiles.length > allowedUploads) {
          this.showToastMessage();
          return;
        }
        const newFiles = []; // Temporary array to collect new files
  
        Array.from(selectedFiles).forEach((file) => {
          if (this.validateFile(file)) {
            const reader = new FileReader();
            reader.onload = (e) => {
              const fileData = { 
                file, 
                dataURL: e.target.result,
                name: file.name, 
                type: file.type };
                this.files.push(fileData);
                newFiles.push(fileData); // Add to the newFiles array

                // Emit event when all files are processed
                if (newFiles.length === selectedFiles.length) {
                    this.$emit("files-selected", newFiles);
                }
              
            };
            reader.readAsDataURL(file);
          } else {
            this.showToastMessage();
          }
        });
      },
      validateFile(file) {
        return ["image", "video", "pdf", "msword", "wordprocessingml"].some(type => file.type.includes(type));
      },
      removeFile(index) {
        this.files.splice(index, 1);
      },
      openModal(index) {
        this.currentFileIndex = index;
        this.isModalOpen = true;
      },
      closeModal() {
        this.isModalOpen = false;
      },
      prevFile() {
        if (this.currentFileIndex > 0) {
          this.currentFileIndex--;
        }
      },
      nextFile() {
        if (this.currentFileIndex < this.files.length - 1) {
          this.currentFileIndex++;
        }
      },
      showToastMessage() {
        this.showToast = true;
        setTimeout(() => {
          this.showToast = false;
        }, 3000);
      },
      isImage(file) {
        return file.type.includes("image");
      },
      isVideo(file) {
        return file.type.includes("video");
      },
      isPDF(file) {
        return file.type.includes("pdf");
      },
      isDOCX(file) {
        return file.type.includes("word");
      },
    },
    computed: {
      currentFile() {
        return this.files[this.currentFileIndex] || {};
      },
    },
    mounted() {
      const fileContainer = this.$el.querySelector("#file-container");

  
      document.addEventListener("keydown", (event) => {
        if (this.isModalOpen) {
          if (event.key === "ArrowLeft") {
            this.prevFile();
          } else if (event.key === "ArrowRight") {
            this.nextFile();
          } else if (event.key === "Escape") {
            this.closeModal();
          }
        }
      });
    },
  };
  </script>
  
  <style scoped>
  #file-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }
  
  .file-wrapper,
  #file-slot {
    flex: 0 0 calc(15% - 10px);
    aspect-ratio: 1 / 1;
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    margin: 0;
    border: 1px dashed #b0b0b0;
    padding: 10px;
  }
  
  .file-preview img,
  .file-preview video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
  }
  
  .file-preview iframe {
    width: 100%;
    height: 100%;
  }
  
  .file-icon {
    font-size: 24px;
    text-align: center;
    margin-top: 10px;
  }
  
  .file-name {
    font-size: 12px;
    text-align: center;
  }
  
  .file-options {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: rgba(255, 255, 255, 0.5);
    padding: 5px;
    cursor: pointer;
  }
  
  #file-slot {
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
  
  #fullscreen-image,
  #fullscreen-video {
    max-width: 80%;
    max-height: 80%;
  }
  
  #prev-file,
  #next-file,
  #close-modal {
    position: absolute;
    background-color: rgba(255, 255, 255, 0.7);
    border: none;
    cursor: pointer;
    z-index: 101;
  }
  
  #prev-file {
    left: 100px;
  }
  
  #next-file {
    right: 10px;
  }
  
  #close-modal {
    top: 100px;
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
  /* Tablets (768px and below) */
@media (max-width: 768px) {
  .file-wrapper,
  #file-slot {
    flex: 0 0 calc(30% - 10px); /* Increase size for better visibility */
  }
}

/* Mobile Devices (576px and below) */
@media (max-width: 576px) {
  .file-wrapper,
  #file-slot {
    flex: 0 0 calc(45% - 10px); /* Make it bigger for small screens */
  }
}

/* Extra Small Mobile Devices (400px and below) */
@media (max-width: 400px) {
  .file-wrapper,
  #file-slot {
    flex: 0 0 calc(45% - 10px); /* Full width on very small screens */
  }
}
  </style>
  