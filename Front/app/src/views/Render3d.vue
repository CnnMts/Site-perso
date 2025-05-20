<template>
  <div class="modal-backdrop" @click.self="close">
    <div class="modal-content">
      <div ref="threeCanvas" class="viewer-container"></div>
      <button class="toggle-btn" @click="toggleRotation">
        {{ rotate ? 'Désactiver' : 'Activer' }} la rotation
      </button>
      <button class="close-btn" @click="close">Fermer</button>
      <button class="add-to-cart-btn" @click="addToCart">Ajouter au Panier</button>
    </div>
  </div>
</template>

<script>
import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import orderModel from '@/models/orderModel';
import orderItemModel from '@/models/orderItemModel';

export default {
  name: '3DViewerModal',
  emits: ['close'],
  data() {
    return {
      rotate: true,
      controls: null,
      productId: 1,
      productName: 'Mug personnalisé',
      productImage: '',
      price: 20,
      quantity: 1,
      userId: 1,
      statusId: 1,
      paymentMethodId: 1,
    };
  },
  mounted() {
    this.initThreeJS();
  },
  methods: {
    close() {
      this.$emit('close');
    },
    toggleRotation() {
      this.rotate = !this.rotate;
    },
    initThreeJS() {
  const scene = new THREE.Scene();
  const savedData = JSON.parse(localStorage.getItem('customizedImage'));

  if (!savedData || !savedData.base64) {
    console.error("Aucune image personnalisée trouvée dans le localStorage.");
    return;
  }

  scene.background = new THREE.Color(savedData.backgroundColor === '#ffffff' ? 0x000000 : 0xffffff);

  const camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000);
  camera.position.z = 0.3;

  const renderer = new THREE.WebGLRenderer({ antialias: true });
  renderer.setSize(500, 500);
  this.$refs.threeCanvas.appendChild(renderer.domElement);

  this.controls = new OrbitControls(camera, renderer.domElement);
  this.controls.enableRotate = true;
  this.controls.enablePan = true;
  this.controls.mouseButtons.RIGHT = THREE.MOUSE.PAN;
  this.controls.mouseButtons.LEFT = THREE.MOUSE.ROTATE;

  scene.add(new THREE.AmbientLight(0xffffff));
  const loader = new GLTFLoader();

loader.load(
        '/Models/White-Mug.glb',
        (gltf) => {
          const object = gltf.scene;

          const texture = new THREE.TextureLoader().load(savedData.base64);
          texture.flipY = false;
          texture.wrapS = THREE.ClampToEdgeWrapping;
          texture.wrapT = THREE.ClampToEdgeWrapping;
          

          object.traverse((child) => {
            if (child.isMesh) {
              console.log(child.name)
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

          object.position.set(0, -0.03, 0);
          object.name = 'mug';
          scene.add(object);
        },
        undefined,
        (error) => {
          console.error('Erreur de chargement GLB :', error);
        }
      );

      const animate = () => {
        requestAnimationFrame(animate);
        const mug = scene.getObjectByName('mug');
        if (this.rotate && mug) {
          mug.rotation.y += 0.004;
        }
        this.controls.update();
        renderer.render(scene, camera);
      };

      animate();
    },

    async addToCart() {
      const savedData = JSON.parse(localStorage.getItem('customizedImage'));
      if (!savedData || !savedData.base64) {
        console.error("Aucune image personnalisée trouvée dans le localStorage.");
        return;
      }

      try {
        const orderData = {
          user_id: this.userId,
          status_id: this.statusId,
          total_price: this.price ,
          payment_method_id: this.paymentMethodId,
          picture: savedData.base64,
        };

        const orderResponse = await orderModel.addOrder(orderData);
        const orderId = orderResponse.id;

        const orderItemData = {
          order_id: orderId,
          product_id: this.productId,
          name: this.productName,
        };

        const orderItemResponse = await orderItemModel.addOrderItem(orderItemData);
        console.log('Commande créée et item ajouté:', { orderResponse, orderItemResponse });
      } catch (error) {
        console.error('Erreur lors de l\'ajout au panier:', error);
      }
    }
  }
};
</script>

<style scoped>
/* Style ici... */
</style>
