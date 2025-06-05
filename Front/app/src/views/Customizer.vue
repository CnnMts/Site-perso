<template>
  <div class="min-h-screen bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 p-4 sm:p-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-8 w-full mx-0">
      <div class="bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 p-4 sm:p-6 rounded-xl shadow-xl flex flex-col items-start w-full">
        <h2 class="text-xl sm:text-2xl font-bold text-purple-600 mb-4">Personnalisation</h2>
        <div class="w-full mb-4">
          <canvas
            ref="canvasRef"
            :width="canvasWidth"
            :height="canvasHeight"
            class="border rounded shadow-md w-full"
          ></canvas>
        </div>
        <label class="mt-4 sm:mt-6 cursor-pointer inline-block bg-gradient-to-r from-pink-500 to-purple-500 text-white px-3 py-1 sm:px-4 sm:py-2 rounded-full shadow hover:from-pink-600 hover:to-purple-600 transition">
          📁 Importer une image
          <input
            type="file"
            @change="onFileChange"
            class="hidden"
          />
        </label>
      </div>
      <div class="flex items-center justify-center bg-gray-100 p-4 sm:p-6 rounded-xl shadow-xl w-full">
        <DViewModal :image="previewImage" :product="productType" />
      </div>
    </div>
  </div>
</template>




<script>
import * as fabric from "fabric";
import _ from "lodash";
import DViewModal from "@/components/3dViewModal.vue";

export default {
  name: "Customizer",
  components: {
    DViewModal,
  },
  data() {
    return {
      canvas: null,
      imgObj: null,
      background: null,
      fileInputKey: 0,
      productType: "mug",
      canvasWidth: 500,
      canvasHeight: 275,
      backgroundColor: "#ffffff",
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
        preserveObjectStacking: true,
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
        this.canvasWidth = 850;
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
        fill: this.backgroundColor || "#ffffff",
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

      const scale =
        imgAspect > canvasAspect
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
        tl: true,
        tr: true,
        bl: true,
        br: true,
      });
      this.canvas.setActiveObject(this.imgObj);
      this.canvas.renderAll();
      this.updatePreviewImage();
    },

    updatePreviewImage: _.debounce(function () {
      if (this.canvas) {
        this.previewImage = this.canvas.toDataURL({ format: "png", quality: 1 });
      }
    }, 100),

    applyCustomization() {
      this.updatePreviewImage();
    },
  },
};
</script>
