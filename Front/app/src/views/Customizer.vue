<template>
  <div>
    <div>
      <button @click="saveImage">Confirmer</button>
    </div>

    <div class="canvas-container" :style="canvasContainerStyle">
      <canvas ref="canvas" :width="canvasWidth" :height="canvasHeight"></canvas>
    </div>

    <div class="controls">
      <input :key="fileInputKey" type="file" @change="onFileChange" />
      <label for="bgColor">Couleur du fond :</label>
      <input id="bgColor" type="color" v-model="backgroundColor" @input="updateBackgroundColor" />
    </div>
  </div>
</template>

<script>
import * as fabric from "fabric";

export default {
  name: "Customizer",
  data() {
    return {
      canvas: null,
      imgObj: null,
      background: null,
      fileInputKey: 0,
      productType: "mug",
      canvasWidth: 730,
      canvasHeight: 272,
      backgroundColor: "#ffffff",
    };
  },
  computed: {
    canvasContainerStyle() {
      return {
        width: `${this.canvasWidth}px`,
        height: `${this.canvasHeight}px`,
      };
    }
  },
  async mounted() {
    if (!this.$route.params.id) {
      console.error("Product ID is missing");
      return;
    }


    await this.$nextTick(() => {
      this.canvas = new fabric.Canvas(this.$refs.canvas);
      this.updateCanvasSize(this.$route.params.id);
      this.createBackground();
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
        if (this.canvas && typeof this.canvas.sendToBack === "function") {
          this.canvas.sendToBack(this.background);
        }
      }
    },

    createBackground() {
      this.background = new fabric.Rect({
        left: 0,
        top: 0,
        width: this.canvasWidth,
        height: this.canvasHeight,
        fill: this.backgroundColor,
        selectable: false,
        evented: false,
      });

      if (this.canvas) {
        this.canvas.add(this.background);
        if (this.canvas && typeof this.canvas.sendToBack === "function") {
          this.canvas.sendToBack(this.background);
        }
        this.canvas.renderAll();
      }
    },

    updateBackgroundColor() {
      if (this.background) {
        this.background.set("fill", this.backgroundColor);
        this.canvas.renderAll();
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

      let scale = imgAspect > canvasAspect
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

      this.imgObj.setControlsVisibility({
        tl: true, tr: true, bl: true, br: true,
      });

      this.canvas.setActiveObject(this.imgObj);
      this.canvas.renderAll();
    },

    saveImage() {
      if (this.canvas && this.imgObj) {
        const dataURL = this.canvas.toDataURL({
          format: 'png',
          quality: 1,
        });

        const imgData = this.imgObj;
        const imgTransform = {
          left: imgData.left,
          top: imgData.top,
          scaleX: imgData.scaleX,
          scaleY: imgData.scaleY,
          angle: imgData.angle,
        };

        const imageData = {
          base64: dataURL,
          transform: imgTransform,
          width: imgData.width ,
          height: imgData.height,
          backgroundColor: this.backgroundColor,
        };

        localStorage.setItem("customizedImage", JSON.stringify(imageData));
        console.log("Image + backgroundColor enregistrés.");
      }
      window.location.href = '/render';
    }
  }
};
</script>

<style scoped>
.canvas-container {
  margin: 20px;
  border: 1px solid #ccc;
}

.controls {
  margin-top: 20px;
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
