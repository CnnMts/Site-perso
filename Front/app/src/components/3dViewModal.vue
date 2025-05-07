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
import { OBJLoader } from 'three/examples/jsm/loaders/OBJLoader.js';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import orderModel from '@/models/orderModel';  

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

      const loader = new OBJLoader();
      loader.load(
        '/Models/11oz-Mug.obj',
        (object) => {
          const texture = new THREE.TextureLoader().load(savedData.base64);

          object.traverse((child) => {
            if (child.isMesh) {
              child.material = new THREE.MeshBasicMaterial({ map: texture });
            }
          });

          object.position.set(0, -0.03, 0);
          object.name = 'mug';
          scene.add(object);
        },
        undefined,
        (error) => {
          console.error('Erreur de chargement OBJ :', error);
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

      // Données à envoyer lors de l'ajout au panier
      const orderData = {
        productId: this.productId,
        productName: this.productName,
        productImage: savedData.base64,  
        quantity: 1,  
        price: 20.00,  
        customerName: 'Matis', 
        address: '123 rue exemple', 
      };

      try {
        const response = await orderModel.addOrder(orderData);
        if (response) {
          console.log('Commande ajoutée avec succès:', response);
        }
      } catch (error) {
        console.error('Erreur lors de l\'ajout au panier:', error);
      }
    }
  }
};
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  position: relative;
}
.viewer-container {
  width: 500px;
  height: 500px;
}
.toggle-btn,
.close-btn,
.add-to-cart-btn {
  margin-top: 10px;
  margin-right: 10px;
  padding: 10px;
  background-color: #4caf50;
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}
.close-btn {
  background-color: #f44336;
}
.add-to-cart-btn {
  background-color: #2196f3;
}
</style>
