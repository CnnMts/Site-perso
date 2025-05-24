<template>
  <div class="customizer-wrapper">
    <div class="left-panel">
      <h2>Personnalisation</h2>
      <canvas ref="canvasRef" width="900" height="275" />
      <input type="file" @change="onFileChange" />
    </div>

    <div class="right-panel">
      <DViewModal :image="previewImage" :product="productType" />
      
    </div>
  </div>
</template>

<script>
import * as fabric from "fabric";
import _ from 'lodash';
import DViewModal from "@/components/3dViewModal.vue";

export default {
  name: "Customizer",
  components: {
    DViewModal
  },
  data() {
    return {
      canvas: null,
      imgObj: null,
      background: null,
      fileInputKey: 0,
      productType: "mug",
      canvasWidth: 900,
      canvasHeight: 275,
      backgroundColor: "#ffffff",
      previewImage: null,
      previewImage: "", 

    };
  },
  async mounted() {
    if (!this.$route.params.id) {
      console.error("Product ID is missing");
      return;
    }

    await this.$nextTick(() => {
      this.canvas = new fabric.Canvas(this.$refs.canvasRef, {
      renderOnAddRemove: true,
      selection: true,
      preserveObjectStacking: true
      });

      this.updateCanvasSize(this.$route.params.id);
      this.createBackground();

      this.canvas.on("object:moving", () => {
      this.updatePreviewImage();
      });
    });
  },
  methods: {
    updateCanvasSize(productId) {
      if (productId == 1) {
        this.productType = "mug";
        this.canvasWidth = 900;
        this.canvasHeight = 275;
      } else if (productId == 2) {
        this.productType = "tumbler";
        this.canvasWidth = 730;
        this.canvasHeight = 272;
      } else {
        console.warn("Produit inconnu, taille par défaut utilisée");
      }

      if (this.canvas) {
        this.canvas.setWidth(this.canvasWidth);
        this.canvas.setHeight(this.canvasHeight);
        this.canvas.renderAll();
      }
      if (this.background) {
        this.background.set({
          width: this.canvasWidth,
          height: this.canvasHeight,
        });
        this.canvas.sendToBack(this.background);
      }
    },

    createBackground() {
  const bgRect = new fabric.Rect({
    left: 0,
    top: 0,
    width: this.canvas.getWidth(),
    height: this.canvas.getHeight(),
    fill: this.backgroundColor || '#ffffff',
    selectable: false,
    evented: false,
  });

  this.canvas.add(bgRect);
  requestAnimationFrame(() => {
    const lastIndex = this.canvas._objects.length - 1;
    const lastObject = this.canvas._objects[lastIndex];
    this.canvas._objects.splice(lastIndex, 1); 
    this.canvas._objects.unshift(lastObject);
    this.canvas.requestRenderAll();
  });
},

    updateBackgroundColor() {
      if (this.background) {
        this.background.set("fill", this.backgroundColor);
        this.canvas.renderAll();
        this.updatePreviewImage();
      }
    },

    onFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          const imgElement = new Image();
          imgElement.src = e.target.result;
          imgElement.onload = () => {
            this.addImageToCanvas(imgElement);
            this.fileInputKey++;
          };
        };
        reader.readAsDataURL(file);
      }
      event.target.value = null;
    },

    addImageToCanvas(imgElement) {
      const imgInstance = new fabric.Image(imgElement);
      const imgWidth = imgInstance.width;
      const imgHeight = imgInstance.height;

      const canvasAspect = this.canvasWidth / this.canvasHeight;
      const imgAspect = imgWidth / imgHeight;

      const scale = imgAspect > canvasAspect
        ? this.canvasWidth / imgWidth
        : this.canvasHeight / imgHeight;

      imgInstance.set({
        left: (this.canvasWidth - imgWidth * scale) / 2,
        top: (this.canvasHeight - imgHeight * scale) / 2,
        scaleX: scale,
        scaleY: scale,
      });

      this.imgObj = imgInstance;
      this.canvas.add(this.imgObj);
      this.imgObj.setControlsVisibility({ tl: true, tr: true, bl: true, br: true });
      this.canvas.setActiveObject(this.imgObj);
      this.canvas.renderAll();
      this.updatePreviewImage();
    },

    updatePreviewImage: _.debounce(function () {
      if (this.canvas) {
          this.previewImage = this.canvas.toDataURL({ format: 'png', quality: 1 });
      }
      }, 100),



    applyCustomization() {
      this.updatePreviewImage();
    }
  }
};
</script>

<style scoped>
canvas {
  transition: all 0.05s linear;
}

.customizer-wrapper {
  display: flex;
  gap: 20px;
}
.left-panel, .right-panel {
  flex: 1;
}
.canvas-container {
  margin: 20px;
  border: 1px solid #ccc;
}
button {
  padding: 10px;
  background-color: #4CAF50;
  color: white;
  border: none;
  cursor: pointer;
}
button:hover {
  background-color: #45a049;
}
</style>