<template>
  <div class="flex min-h-screen bg-gray-50 text-gray-800">
    <aside class="w-72 bg-white shadow-xl px-6 py-8 border-r border-gray-200">
      <a href="/"><h2 class="text-2xl font-bold text-indigo-600 mb-8">Mon Compte</h2></a>
      <nav class="flex flex-col gap-4">
        <button
          class="w-full text-left px-4 py-2 rounded-lg transition-all duration-150"
          :class="selected === 'settings' ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'hover:bg-gray-100'"
          @click="selected = 'settings'"
        >
          Paramètres du compte
        </button>
        <button
          class="w-full text-left px-4 py-2 rounded-lg transition-all duration-150"
          :class="selected === 'payment' ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'hover:bg-gray-100'"
          @click="selected = 'payment'"
        >
          Paiement & Livraison
        </button>
        <button
          class="w-full text-left px-4 py-2 rounded-lg transition-all duration-150"
          :class="selected === 'history' ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'hover:bg-gray-100'"
          @click="selected = 'history'"
        >
          Historique des commandes
        </button>
      </nav>
    </aside>
    <div class="flex-1 p-10">
      <section v-if="selected === 'settings'" class="Setting-account">
        <h3 class="text-3xl font-bold text-gray-700 mb-6">Paramètres du compte</h3>
        <div v-if="user" class="space-y-2 text-lg">
          <p><strong>Nom :</strong> {{ user.name }}</p>
          <p><strong>Prénom :</strong> {{ user.firstname }}</p>
          <p><strong>Email :</strong> {{ user.email }}</p>
        </div>
        <div v-else class="text-gray-500">Chargement des informations...</div>
      </section>
      <section v-else-if="selected === 'payment'" class="Payment-delivery">
        <h3 class="text-3xl font-bold text-gray-700 mb-6">Paiement & Livraison</h3>
        <div v-if="user" class="space-y-2 text-lg">
          <p><strong>Adresse :</strong> {{ user.address }}</p>
          <p><strong>Code postal :</strong> {{ user.zip_code }}</p>
          <p><strong>Ville :</strong> {{ user.city }}</p>
        </div>
      </section>
      <section v-else-if="selected === 'history'" class="history-of-command">
        <h3 class="text-3xl font-bold text-gray-700 mb-6">Historique des commandes</h3>

        <div v-if="orders.length > 0" class="space-y-6">
          <div
            v-for="order in orders"
            :key="order.order_id"
            class="p-6 bg-white rounded-lg shadow border border-gray-200"
          >
            <p class="text-lg"><strong>Numéro :</strong> {{ order.order_id }}</p>
            <p><strong>Total :</strong> {{ order.total_price }} €</p>
            <p><strong>Statut :</strong> {{ getStatusText(order.status_id) }}</p>
            <p><strong>Date :</strong> {{ formatFrenchDateWithYear(order.updated_at) }}</p>
            <div v-if="order.items?.length" class="mt-4">
              <h4 class="text-md font-semibold mb-2"> Articles :</h4>
              <ul class="list-disc list-inside text-gray-700">
                <li v-for="item in order.items" :key="item.item_id">
                  {{ item.product_name }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div v-else class="text-gray-500">Aucune commande effectuée.</div>
      </section>
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
