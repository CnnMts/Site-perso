<template>
  <div class="flex min-h-screen bg-gray-100">
    <aside class="w-64 bg-white shadow-lg p-6">
      <h2 class="text-xl font-semibold mb-6">Mon Compte</h2>
      <nav class="space-y-4">
        <button class="w-full text-left p-2 rounded hover:bg-gray-200" @click="selected = 'settings'">
          Paramètres du compte
        </button>
        <button class="w-full text-left p-2 rounded hover:bg-gray-200" @click="selected = 'payment'">
          Paiement & Livraison
        </button>
        <button class="w-full text-left p-2 rounded hover:bg-gray-200" @click="selected = 'history'">
          Historique des commandes
        </button>
      </nav>
    </aside>

    <div class="flex-1 p-8">
      <!-- Paramètres -->
      <div v-if="selected === 'settings'" class="Setting-account">
        <h3 class="text-2xl font-bold mb-4">Paramètres du compte</h3>
        <div v-if="user">
          <p><strong>Nom :</strong> {{ user.name }}</p>
          <p><strong>Prénom :</strong> {{ user.firstname }}</p>
          <p><strong>Email :</strong> {{ user.email }}</p>
        </div>
        <div v-else>
          <p>Chargement des informations...</p>
        </div>
      </div>

      <!-- Paiement & Livraison -->
      <div v-else-if="selected === 'payment'" class="Payment-delivery">
        <h3 class="text-2xl font-bold mb-4">Paiement & Livraison</h3>
        <div v-if="user">
          <p><strong>Adresse :</strong> {{ user.address }}</p>
          <p><strong>Code postal :</strong> {{ user.zip_code }}</p>
          <p><strong>Ville :</strong> {{ user.city }}</p>
        </div>
      </div>

      <!-- Historique des commandes -->
      <div v-else-if="selected === 'history'" class="history-of-command">
        <h3 class="text-2xl font-bold mb-4">Historique des commandes</h3>

        <div v-if="orders.length > 0">
          <div v-for="order in orders" :key="order.order_id" class="mb-8 p-4 bg-white rounded shadow">
            <p><strong>Numéro de Commande :</strong> {{ order.order_id }}</p>
            <p><strong>Total :</strong> {{ order.total_price }} €</p>
            <p><strong>Statut :</strong> {{ getStatusText(order.status_id) }}</p>
            <p><strong>Fait le :</strong> {{ formatFrenchDateWithYear(order.updated_at) }}</p>
            <h4 class="text-xl font-semibold mt-4 mb-2">Articles :</h4>
            <ul>
              <li v-for="item in order.items" :key="item.item_id">
                - {{ item.product_name }} ({{ item.sale_price }} €)
              </li>
            </ul>
          </div>
        </div>
        <div v-else>
          <p>Aucune commande effectuée.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script scoped>
import orderModel from '@/models/orderModel';
import userModel from '@/models/userModel';

function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}

export default {
  name: 'SideBarAccount',
  data() {
    return {
      selected: 'settings',
      user: null,
      orders: []
    }
  },
  async mounted() {
    await this.loadUserAndCart();
  },
  methods: {
    async loadUserAndCart() {
      const token = getCookie('pmaUser');
      if (!token) {
        console.error("Token manquant dans le cookie pmaUser");
        return;
      }
      try {
        const payload = token.split('.')[1];
        const decodedPayload = atob(payload);
        const payloadObj = JSON.parse(decodedPayload);
        const userId = payloadObj.id;

        if (!userId) {
          console.error("ID manquant dans le token");
          return;
        }

        this.user = await userModel.getUserById(userId);

        try {
          this.orders = await orderModel.getOrdersByUserId(userId);
        } catch (err) {
          this.orders = [];
          console.error("Erreur lors de la récupération des commandes :", err);
        }

        console.log("Utilisateur :", this.user);
        console.log("Commandes :", this.orders);
      } catch (error) {
        console.error("Erreur lors de la récupération des données :", error);
      }
    },
    getStatusText(status_id) {
      switch(status_id) {
        case 1: return 'En cours de validation';
        case 2: return 'Validé';
        case 3: return 'Payé';
        case 4: return 'Expédié';
        default: return 'Inconnu';
      }
    },
    formatFrenchDateWithYear(dateString) {
      if (!dateString) return 'Date inconnue';
      const days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
      const months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 
                      'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
      const date = new Date(dateString.replace(' ', 'T'));
      if (isNaN(date)) return 'Date invalide';
      const dayName = days[date.getDay()];
      const dayNumber = date.getDate();
      const monthName = months[date.getMonth()];
      const year = date.getFullYear();
      const hour = date.getHours();
      const minutes = date.getMinutes().toString().padStart(2, '0');
      return `${dayName} ${dayNumber} ${monthName} ${year} à ${hour}h${minutes}`;
    }
  }
}
</script>
