<template>
  <div class="container-mid">
    <h1 class="title">Tableau de bord</h1>

    <div class="orders">
      <div v-for="order in orders" :key="order.id" class="order-card">
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
          >
            Télécharger
          </a>
        </div>

        <div class="info-section">
          <p><strong>Commande #{{ order.id }}</strong></p>
          <p>Client : {{ order.user_name  }} {{ order.user_firstname }}</p>
          <p>Date : {{ formatDate(order.updated_at) }}</p>
          <p>Total : {{ order.total_price }} €</p>
        </div>
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

    async getNameUserById(id){
      const response = await userModel.getUserById('id');
      const userId = response || null ;
    }
  }
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
</style>
