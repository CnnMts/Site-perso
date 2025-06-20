<template>
  <div class="flex flex-col items-center p-4 sm:p-5 bg-gray-600 rounded-2xl shadow-[0_0px_30px_rgba(0,0,0,0.5)] max-w-md sm:max-w-2xl mx-auto">
    <div
      ref="threeCanvas"
      class="w-full h-[300px] sm:w-[400px] sm:h-[400px] md:w-[500px] md:h-[500px] rounded-2xl bg-gradient-to-br from-blue-100 to-white shadow-[inset_0_0_25px_rgba(255,255,255,0.8),0_20px_35px_rgba(0,0,0,0.5)] transition-shadow duration-300 hover:shadow-[inset_0_0_35px_rgba(255,255,255,0.9),0_30px_60px_rgba(0,0,0,0.7)]"
    ></div>

    <div class="mt-4 sm:mt-5 flex gap-3 sm:gap-4 justify-center flex-wrap">
      <button
        @click="toggleRotation"
        class="px-4 py-2 sm:px-5 sm:py-2 rounded-full border-0 font-semibold text-white cursor-pointer
               bg-blue-700 shadow-md
               hover:bg-blue-800 hover:shadow-lg
               transition-colors transition-shadow duration-250 ease-in-out"
      >
        {{ rotate ? 'Désactiver' : 'Activer' }} la rotation
      </button>

      <button
        @click="addToCart"
        class="px-4 py-2 sm:px-5 sm:py-2 rounded-full border-0 font-semibold text-gray-50 cursor-pointer
               bg-purple-800 shadow-md
               hover:bg-purple-900 hover:shadow-lg
               transition-colors transition-shadow duration-250 ease-in-out"
      >
        Ajouter au Panier
      </button>
    </div>
  </div>
</template>


<script>
import * as THREE from 'three';
import { DRACOLoader } from 'three/examples/jsm/loaders/DRACOLoader.js';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import Swal  from 'sweetalert2';

import orderModel from '@/models/orderModel';
import orderItemModel from '@/models/orderItemModel';
import { jwtDecode } from 'jwt-decode';

export default {
  name: '3DViewer',
  props: {
    image: String,
    product: String
  },
  data() {
    return {
      modelReady: false,       // To know if the 3D model is loaded 
      rotate: true,            // Enables/disables auto-rotation
      controls: null,          // Camera Controls (Orbit)
      productId: null,         // Id product
      productName: 'Mug personnalisé',
      price: 20,
      quantity: 1,
      userId: null,            // Logged in user ID (via JWT)
      statusId: 1,
      paymentMethodId: 1,
    };
  },

  watch: {
    // Updates the texture applied to the 3D model as soon as the image changes
    image(newVal) {
      if ((!this.mug && !this.tshirt) || !this.modelReady) return;

      if (this.texture) {
        this.texture.dispose();
      }
      this.texture = new THREE.Texture(this.$parent.canvas.lowerCanvasEl);
      this.texture.needsUpdate = true;
      this.texture.flipY = false;

      const model = this.productId === '3' ? this.tshirt : this.mug;

      model.traverse(child => {
        if (child.isMesh && child.name === 'Texture') {
          child.material.map = this.texture;
          child.material.needsUpdate = true;
        }
      });
    }
  },

  // Retrieves the user from the cookie at component creation time
  created() {
    const token = this.getCookie('pmaUser');
    if (token) {
      try {
        const decoded = jwtDecode(token);
        this.userId = decoded.id;
      } catch (err) {
        console.error("Erreur lors du décodage du token :", err);
      }
    } else {
      console.error("Aucun token trouvé dans les cookies.");
    }
  },

  // Initializing 3D rendering when mounting the component
  mounted() {
    const url = window.location.pathname;
    const parts = url.split('/');
    this.productId = parts[2]; // retrieves the product ID from the URL

    this.mug = null;
    this.scene = null;
    this.camera = null;
    this.renderer = null;
    this.initThreeJS(); // Start Three.js
  },

  methods: {
    // Allows you to stop or start the rotation of the model
    toggleRotation() {
      this.rotate = !this.rotate;
    },

    // Retrieves a cookie from its name
    getCookie(name) {
      const cookieArr = document.cookie.split(';');
      for (let i = 0; i < cookieArr.length; i++) {
        const cookiePair = cookieArr[i].split('=');
        if (name === cookiePair[0].trim()) {
          return decodeURIComponent(cookiePair[1]);
        }
      }
      return null;
    },

    // Main function to initialize the 3D scene
    initThreeJS() {
      const container = this.$refs.threeCanvas;
      container.innerHTML = '';

      this.scene = new THREE.Scene();
      this.scene.background = new THREE.Color(0x000000);

      this.camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000);
      this.camera.position.z = 0.3;

      this.renderer = new THREE.WebGLRenderer({ antialias: true });
      this.renderer.setSize(500, 500);
      container.appendChild(this.renderer.domElement);

      this.controls = new OrbitControls(this.camera, this.renderer.domElement);
      this.controls.enableRotate = true;
      this.controls.enablePan = true;

      this.scene.add(new THREE.AmbientLight(0xffffff, 1));

      // Camera position depending on the product (t-shirt or mug)
      if (this.productId === '3') {
        this.camera.position.set(0, 3, 5.5);
        this.controls.target.set(0, 3, 5.5);
      } else {
        this.camera.position.set(0, 0, 0.3);
        this.controls.target.set(0, 0, 0);
      }
      this.controls.update();

      // Loading the 3D model via a decompressor (DRACO compression) to lighten the files
      const manager = new THREE.LoadingManager();
      manager.onError = (url) => {
        console.error(`Erreur chargement : ${url}`);
      };

      const dracoLoader = new DRACOLoader(manager);
      dracoLoader.setDecoderPath('/draco/');

      const loader = new GLTFLoader(manager);
      loader.setDRACOLoader(dracoLoader);

      let modelPath = '/Models/Mug-White.glb';
      if (this.productId === '3') {
        modelPath = '/Models/Tshirt_White.glb';
      }

      loader.load(
        modelPath,
        (gltf) => {
          if (this.productId === '3') {
            this.tshirt = gltf.scene;
            this.tshirt.position.set(0, 0, 0);
            this.tshirt.scale.set(1, 1, 1);
            this.scene.add(this.tshirt);
          } else {
            this.mug = gltf.scene;
            this.mug.position.set(0, -0.03, 0);
            this.mug.scale.set(1, 1, 1);
            this.scene.add(this.mug);
          }
          this.modelReady = true;
          this.applyTexture(); // apply texture if available
        },
        undefined,
        (err) => {
          console.error("Erreur chargement GLB:", err);
        }
      );

      // Animated render function (loop
      const animate = () => {
        requestAnimationFrame(animate);
        if (this.rotate) {
          if (this.productId === '3' && this.tshirt) {
            this.tshirt.rotation.y += 0.001; 
          } else if (this.mug) {
            this.mug.rotation.y += 0.001;
          }
        }
        this.controls.update();
        this.renderer.render(this.scene, this.camera);
      };
      animate();
    },

    // Applies a texture image (or white color by default) to the 3D model
    applyTexture() {
      if (!this.modelReady) return;
      const model = this.productId === '3' ? this.tshirt : this.mug;
      if (!model) return;

      if (!this.image) {
        model.traverse(child => {
          if (child.isMesh) {
            child.material = new THREE.MeshStandardMaterial({
              color: 0xffffff,
              metalness: 0,
              roughness: 1,
            });
          }
        });
        return;
      }

      const texture = new THREE.TextureLoader().load(this.image);
      model.traverse(child => {
        if (child.isMesh) {
          if (child.name === 'Texture') {
            child.material = new THREE.MeshStandardMaterial({
              map: texture,
              metalness: 0,
              roughness: 1,
            });
          } else {
            child.material = new THREE.MeshStandardMaterial({
              color: 0xffffff,
              metalness: 0,
              roughness: 1,
            });
          }
        }
      });
    },

    // Sends order information (product + image) to the API
    async addToCart() {
      if (!this.image) {
        console.error("Aucune image disponible pour l'ajout au panier.");
        return;
      }

      const url = window.location.pathname;
      const parts = url.split('/');
      this.productId = parts[2];

      try {
        const orderData = {
          user_id: this.userId,
          status_id: this.statusId,
          total_price: this.price,
          payment_method_id: this.paymentMethodId,
        };

        const orderResponse = await orderModel.addOrder(orderData);
        const orderId = orderResponse.id;

        if (!orderId) {
          throw new Error("L'ID de la commande est undefined.");
        }

        const orderItemData = {
          order_id: orderId,
          product_id: this.productId,
          name: this.productName,
          picture: this.image,
        };

        const orderItemResponse = await orderItemModel.addOrderItem(orderItemData);

        await Swal.fire({
          icon: 'success',
          title: 'Commande effectuée',
          text: 'Votre commande a bien été ajoutée au panier.',
          confirmButtonText: 'OK'
        });
        this.$router.push('/cart');
      } catch (error) {
        await Swal.fire({
          icon: 'error',
          title: 'Connectez-vous',
          text: 'Il faut être connecté pour commander .',
          confirmButtonText: 'OK'
        });
        this.$router.push('/login');
        console.error("Erreur ajout panier :", error);
      }
    }
  }
};
</script>
