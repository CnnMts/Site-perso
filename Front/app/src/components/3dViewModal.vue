<template>
  <div class="viewer-wrapper">
    <div ref="threeCanvas" class="viewer-container"></div>

    <div class="controls">
      <button class="toggle-btn" @click="toggleRotation">
        {{ rotate ? 'Désactiver' : 'Activer' }} la rotation
      </button>

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
  name: '3DViewer',
  props: {
    image: String,
    product: String
  },
  data() {
    return {
      modelReady: false,
      rotate: true,
      controls: null,
      productId: 1,
      productName: 'Mug personnalisé',
      price: 20,
      quantity: 1,
      userId: 1,
      statusId: 1,
      paymentMethodId: 1,
    };
  },
 watch: {
  image(newVal) {
    if (!this.mug || !this.modelReady) return;
    
    if (this.texture) {
      this.texture.dispose();
    }
    this.texture = new THREE.Texture(this.$parent.canvas.lowerCanvasEl);
    this.texture.needsUpdate = true;
    this.texture.flipY = false;
    
    this.mug.traverse(child => {
      if (child.isMesh && child.name === 'Texture') {
        child.material.map = this.texture;
        child.material.needsUpdate = true;
      }
    });
  }
},
  mounted() {
     this.mug = null;
    this.scene = null;
    this.camera = null;
    this.renderer = null;
    this.initThreeJS();
},
  methods: {
    toggleRotation() {
      this.rotate = !this.rotate;
    },

    initThreeJS() {
  const container = this.$refs.threeCanvas;
  container.innerHTML = '';

  this.scene = new THREE.Scene();
  this.scene.background = new THREE.Color(0xffffff);

  this.camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000);
  this.camera.position.z = 0.3;

  this.renderer = new THREE.WebGLRenderer({ antialias: true });
  this.renderer.setSize(500, 500);
  container.appendChild(this.renderer.domElement);

  this.controls = new OrbitControls(this.camera, this.renderer.domElement);
  this.controls.enableRotate = true;
  this.controls.enablePan = true;

  this.scene.add(new THREE.AmbientLight(0xffffff, 1));

  const loader = new GLTFLoader();
 loader.load(
  '/Models/White-Mug.glb',
  (gltf) => {
    this.mug = gltf.scene;
    this.mug.position.set(0, -0.03, 0);
    this.mug.name = 'mug';

    this.scene.add(this.mug);
    this.modelReady = true;

    this.applyTexture(); 
  },
  undefined,
  (err) => {
    console.error("Erreur chargement GLB:", err);
  }
);



  const animate = () => {
    requestAnimationFrame(animate);
    if (this.rotate && this.mug) {
      this.mug.rotation.y += 0.004;
    }
    this.controls.update();
    this.renderer.render(this.scene, this.camera);
  };

  animate();
},
    
    applyTexture() {
  if (!this.modelReady || !this.mug) return;
  if (!this.image) {
    this.mug.traverse((child) => {
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
  texture.flipY = false;

  this.mug.traverse((child) => {
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


    async addToCart() {
      if (!this.image) {
        console.error("Aucune image disponible pour l'ajout au panier.");
        return;
      }

      try {
        const orderData = {
          user_id: this.userId,
          status_id: this.statusId,
          total_price: this.price,
          payment_method_id: this.paymentMethodId,
          picture: this.image,
        };

        const orderResponse = await orderModel.addOrder(orderData);
        const orderId = orderResponse.id;

        const orderItemData = {
          order_id: orderId,
          product_id: this.productId,
          name: this.productName,
        };

        const orderItemResponse = await orderItemModel.addOrderItem(orderItemData);
        console.log('Commande ajoutée :', { orderResponse, orderItemResponse });
      } catch (error) {
        console.error("Erreur ajout panier :", error);
      }
    }
  }
};

</script>

<style scoped>
.viewer-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  background: #f5f7fa;
  border-radius: 16px;
  box-shadow: 0 0px 30px rgba(0, 0, 0, 0.5);
  max-width: 540px;
  margin: 0 auto;
}

.viewer-container {
  width: 500px;
  height: 500px;
  border-radius: 16px;
  background: linear-gradient(135deg, #e0e6f7, #ffffff);
  box-shadow:
    inset 0 0 25px rgba(255, 255, 255, 0.8),
    0 20px 35px rgba(0, 0, 0, 0.5);
  transition: box-shadow 0.3s ease;
}

.viewer-container:hover {
  box-shadow:
    inset 0 0 35px rgba(255, 255, 255, 0.9),
    0 30px 60px rgba(0, 0, 0, 0.7);
}

.controls {
  margin-top: 20px;
  display: flex;
  gap: 15px;
  justify-content: center;
  flex-wrap: wrap;
}

.toggle-btn,
.add-to-cart-btn {
  padding: 10px 20px;
  border-radius: 30px;
  border: none;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.25s ease, color 0.25s ease, box-shadow 0.25s ease;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.toggle-btn {
  background-color: #3a86ff;
  color: white;
}

.toggle-btn:hover {
  background-color: #265ecf;
  box-shadow: 0 6px 15px rgba(38, 94, 207, 0.5);
}

.add-to-cart-btn {
  background-color: #ffbe0b;
  color: #333;
}

.add-to-cart-btn:hover {
  background-color: #e6a600;
  box-shadow: 0 6px 15px rgba(230, 166, 0, 0.5);
}

</style>
