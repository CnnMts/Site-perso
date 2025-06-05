<template>
  <div class="container-mid">
    <h1 class="title">Tableau de bord</h1>

    <div class="orders">
      <div
        v-for="order in orders"
        :key="order.id"
        class="order-card"
        @click="openModal(order)"
      >
        <div class="image-section">
          <img
            v-if="order.picture"
            :src="order.picture"
            alt="Image à sublimer"
            class="preview-image"
          />
          <canvas v-else class="preview-canvas"></canvas>
          <a
            v-if="order.picture"
            :href="order.picture"
            :download="`commande_${order.id}.png`"
            class="download-button"
            @click.stop
          >
            Télécharger
          </a>
        </div>

        <div class="info-section">
          <p><strong>Commande #{{ order.id }}</strong></p>
          <p>Client : {{ order.user_name }} {{ order.user_firstname }}</p>
          <p>Date : {{ formatDate(order.updated_at) }}</p>
          <p>Total : {{ order.total_price }} €</p>
        </div>
      </div>
    </div>

    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <h2>Détail de la commande #{{ selectedOrder.id }}</h2>
        <p><strong>Client :</strong> {{ selectedOrder.user_name }} {{ selectedOrder.user_firstname }}</p>
        <p><strong>Date :</strong> {{ formatDate(selectedOrder.updated_at) }}</p>
        <p><strong>Total :</strong> {{ selectedOrder.total_price }} €</p>
        <h3>Articles associés :</h3>
        <ul>
          <li v-for="item in orderItems" :key="item.id">
            {{ item.name }} - Quantité : {{ item.quantity }} - Prix : {{ item.price }} €
          </li>
        </ul>
        <img
          v-if="selectedOrder.picture"
          :src="selectedOrder.picture"
          alt="Image commande"
          class="modal-image"
        />
        <button @click="closeModal">Fermer</button>
      </div>
    </div>
  </div>
</template>

<script>
import orderModel from '@/models/orderModel';
import userModel from '@/models/userModel';

export default {
   data() {
    return {
      orders: [],
      selectedOrder: null,
      orderItems: [], 
      showModal: false,
    };
  },
  async mounted() {
    try {
      const response = await orderModel.getOrders();
      const ordersWithUsernames = await Promise.all(
        response.map(async (order) => {
          try {
            const user = await userModel.getUserById(order.user_id);
            return {
              ...order,
              user_name: user?.name || "Inconnu",
              user_firstname: user?.firstname || "Inconnu",
            };
          } catch {
            return {
              ...order,
              user_name: "Inconnu",
              user_firstname: "Inconnu",
            };
          }
        })
      );

      this.orders = ordersWithUsernames;
    } catch (error) {
      console.error("Erreur lors du chargement des commandes :", error);
    }
  },
  methods: {
    formatDate(dateString) {
      return new Date(dateString).toLocaleString("fr-FR");
    },
    async openModal(order) {
      this.selectedOrder = order;
      this.showModal = true;
        try {
          const response = await fetch(`http://127.0.0.1:9999/ordersItems?orderId=${order.id}`);
          if (!response.ok) throw new Error("Erreur API");
            const data = await response.json();
            const foundOrder = data.find(o => o.id === order.id);
            const groupedItems = {};
              (foundOrder?.items || []).forEach(item => {
              if (groupedItems[item.name]) {
                groupedItems[item.name].quantity += 1;
              } else {
                 groupedItems[item.name] = { name: item.name, quantity: 1 };
              }
          });
          this.orderItems = Object.values(groupedItems);
        } catch (error) {
        console.error("Erreur lors du chargement des items :", error);
        this.orderItems = [];
        }
    },

    closeModal() {
      this.selectedOrder = null;
      this.orderItems = [];
      this.showModal = false;
    },
  },
};
</script>

<style scoped>
.container-mid {
  flex: 1;
  padding: 30px;
  background-color: #f9fafb;
  overflow-y: auto;
}

.title {
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 20px;
  color: #111827;
}

.orders {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.order-card {
  display: flex;
  flex-direction: row;
  align-items: center;
  background-color: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 10px 10px rgba(0, 0, 0, 0.5);
  gap: 20px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.order-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4);
}



.image-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 180px;
  gap: 10px;
}

.preview-image {
  width: 150px;
  height: 150px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #d1d5db;
}

.preview-canvas {
  width: 150px;
  height: 150px;
  background-color: #e5e7eb;
  border-radius: 8px;
}

.download-button {
  display: inline-block;
  padding: 6px 12px;
  background-color: #2563eb;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-size: 14px;
  transition: background-color 0.2s;
}

.download-button:hover {
  background-color: #1e40af;
}

.info-section {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  text-align: center;
}

.modal-image {
  width: 100%;
  margin-top: 20px;
  border-radius: 8px;
}

.modal-content button {
  margin-top: 20px;
  background-color: #ef4444;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.modal-content button:hover {
  background-color: #b91c1c;
}
</style>
