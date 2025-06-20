<template>
  <NavBar />
  <div class="min-h-screen text-white p-6 mt-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8  mx-auto animate-fade-in-up mt-12">
      <div
        class="p-6 rounded-3xl shadow-[0_0_30px_rgba(236,72,153,0.15)] border border-pink-500/20 flex flex-col items-start"
      >
        <div ref="canvasContainer" class="w-full mb-4" :class="aspectRatioClass">
          <canvas
          ref="canvasRef"
          :width="canvasWidth"
          :height="canvasHeight"
          class="border rounded shadow-md w-full h-full">
        </canvas>
        </div>

        <label
          class="mt-4 sm:mt-6 cursor-pointer inline-block bg-gradient-to-r from-pink-500 to-purple-600 text-white px-4 py-2 rounded-full shadow hover:from-pink-600 hover:to-purple-700 transition"
        >
          Importer une image
          <input type="file" @change="onFileChange" class="hidden" />
        </label>
      </div>
      <div
        class="flex items-center justify-center p-6 rounded-3xl shadow-[0_0_20px_rgba(236,72,153,0.1)] border border-pink-500/10"
      >
        <DViewModal :image="previewImage" :product="productType" />
      </div>
    </div>
  </div>
</template>






<script>
import * as fabric from "fabric";
import _ from "lodash";
import DViewModal from "@/components/3dViewModal.vue";
import NavBar from "@/components/NavBar.vue";

export default {
  name: "Customizer",
  components: {
    DViewModal,
    NavBar
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
  computed: {
  aspectRatioClass() {
    const ratios = {
      mug: 'aspect-[950/275]',
      tumbler: 'aspect-[730/272]',
    };
    return ratios[this.productType] || 'aspect-[950/275]'; 
  },
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

    const resizeObserver = new ResizeObserver(() => {
      this.resizeCanvasToContainer();
    });
    resizeObserver.observe(this.$refs.canvasContainer);
  });
},
  methods: {
    updateCanvasSize(productId) {
      if (productId == 1) {
        this.productType = "mug";
        this.canvasWidth = 950;
        this.canvasHeight = 275;
      } else if (productId == 2) {
        this.productType = "tumbler";
        this.canvasWidth = 730;
        this.canvasHeight = 272;
      }
      else if (productId == 3) {
        this.productType = "Tshirt";
        this.canvasWidth = 850;
        this.canvasHeight = 275;
      }else {
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

    resizeCanvasToContainer() {
      const container = this.$refs.canvasContainer;
      if (!container) return;
      const { clientWidth, clientHeight } = container;
      this.canvas.setWidth(clientWidth);
      this.canvas.setHeight(clientHeight);
      this.canvas.renderAll();
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
