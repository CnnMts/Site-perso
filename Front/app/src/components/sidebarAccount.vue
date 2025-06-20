<template>
  <div class="flex min-h-screen text-white">
    <aside class="fixed top-0 left-0 w-72 h-full bg-gray-900 shadow-xl px-6 py-8 border-r border-pink-500/20">

      <a href="/">
        <h2 class="text-2xl font-bold text-pink-400 mb-8">Mon Compte</h2>
      </a>
      <nav class="flex flex-col gap-4">
        <button
          class="w-full text-left px-4 py-2 rounded-lg transition-all duration-150"
          :class="selected === 'settings' ? 'bg-pink-700 text-pink-300 font-semibold' : 'hover:bg-gray-700'"
          @click="selected = 'settings'"
        >
          Paramètres du compte
        </button>
        <button
          class="w-full text-left px-4 py-2 rounded-lg transition-all duration-150"
          :class="selected === 'payment' ? 'bg-pink-700 text-pink-300 font-semibold' : 'hover:bg-gray-700'"
          @click="selected = 'payment'"
        >
          Paiement & Livraison
        </button>
        <button
          class="w-full text-left px-4 py-2 rounded-lg transition-all duration-150"
          :class="selected === 'history' ? 'bg-pink-700 text-pink-300 font-semibold' : 'hover:bg-gray-700'"
          @click="selected = 'history'"
        >
          Historique des commandes
        </button>
      </nav>
    </aside>

    <div class="flex-1 p-12 ml-44">
      <section
        v-if="selected === 'settings'"
        class="px-6 py-8 rounded-2xl shadow-[0_0_30px_rgba(236,72,153,0.15)] border border-pink-500/20 max-w-xl mx-auto"
      >
        <h3 class="text-4xl font-extrabold text-pink-400 mb-8 border-b-4 border-pink-500 pb-2">
          Paramètres du compte
        </h3>

        <div v-if="user" class="space-y-6">
          <div>
            <label class="block font-semibold mb-2 text-pink-300">Nom</label>
            <input
              v-model="user.name"
              type="text"
              class="w-full border border-pink-500 bg-gray-800 rounded-md px-4 py-3 text-white placeholder-pink-300 focus:outline-none focus:ring-2 focus:ring-pink-500 transition"
              placeholder="Entrez votre nom"
            />
          </div>
          <div>
            <label class="block font-semibold mb-2 text-pink-300">Prénom</label>
            <input
              v-model="user.firstname"
              type="text"
              class="w-full border border-pink-500 bg-gray-800 rounded-md px-4 py-3 text-white placeholder-pink-300 focus:outline-none focus:ring-2 focus:ring-pink-500 transition"
              placeholder="Entrez votre prénom"
            />
          </div>
          <div>
            <label class="block font-semibold mb-2 text-pink-300">Email</label>
            <input
              v-model="user.email"
              type="email"
              class="w-full border border-pink-500 bg-gray-800 rounded-md px-4 py-3 text-white placeholder-pink-300 focus:outline-none focus:ring-2 focus:ring-pink-500 transition"
              placeholder="Entrez votre email"
            />
          </div>
          <button
            @click="updateUser"
            class="mt-6 w-full px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-500 hover:from-pink-600 hover:to-purple-600 text-white font-bold rounded-md shadow-md transition"
          >
            Enregistrer les modifications
          </button>
          <p v-if="save === true" class="mt-4 text-green-400 font-semibold text-center">
            Modification(s) enregistrée(s) avec succès
          </p>
        </div>
        <div v-else class="text-pink-400 italic text-center mt-12">Chargement des informations...</div>
      </section>

      <section
        v-else-if="selected === 'payment'"
        class="px-6 py-8 bg-gray-800 rounded-2xl shadow-[0_0_30px_rgba(236,72,153,0.15)] border border-pink-500/20 max-w-lg mx-auto"
      >
        <h3 class="text-4xl font-extrabold text-pink-400 mb-8 border-b-4 border-pink-500 pb-2">
          Paiement & Livraison
        </h3>
        <div v-if="user" class="space-y-6">
          <div>
            <label class="block font-semibold mb-2 text-pink-300">Adresse</label>
            <input
              v-model="user.address"
              type="text"
              class="w-full border border-pink-500 bg-gray-800 rounded-md px-4 py-3 text-white placeholder-pink-300 focus:outline-none focus:ring-2 focus:ring-pink-500 transition"
              placeholder="Entrez votre adresse"
            />
          </div>
          <div>
            <label class="block font-semibold mb-2 text-pink-300">Code Postal</label>
            <input
              v-model="user.zip_code"
              type="text"
              class="w-full border border-pink-500 bg-gray-800 rounded-md px-4 py-3 text-white placeholder-pink-300 focus:outline-none focus:ring-2 focus:ring-pink-500 transition"
              placeholder="Entrez votre code postal"
            />
          </div>
          <div>
            <label class="block font-semibold mb-2 text-pink-300">Ville</label>
            <input
              v-model="user.city"
              type="text"
              class="w-full border border-pink-500 bg-gray-800 rounded-md px-4 py-3 text-white placeholder-pink-300 focus:outline-none focus:ring-2 focus:ring-pink-500 transition"
              placeholder="Entrez votre ville"
            />
          </div>
          <button
            @click="updateUserDelivery"
            class="mt-6 w-full px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-500 hover:from-pink-600 hover:to-purple-600 text-white font-bold rounded-md shadow-md transition"
          >
            Enregistrer les modifications
          </button>
          <p v-if="save === true" class="mt-4 text-green-400 font-semibold text-center">
            Modification(s) enregistrée(s) avec succès
          </p>
        </div>
        <div v-else class="text-pink-400 italic text-center mt-12">Chargement des informations...</div>
      </section>

      <section
        v-else-if="selected === 'history'"
        class="px-6 py-8 bg-gray-800 rounded-2xl shadow-[0_0_30px_rgba(236,72,153,0.15)] border border-pink-500/20 max-w-4xl ml-96"
      >
        <h3 class="text-4xl font-extrabold text-pink-400 mb-8 border-b-4 border-pink-500 pb-2 mt-2">
          Historique des commandes
        </h3>
        <div v-if="orders.length > 0" class="space-y-8">
          <div
            v-for="order in orders"
            :key="order.order_id"
            class="p-6 bg-gray-900 rounded-xl shadow-md border border-pink-500 hover:shadow-xl transition-shadow duration-300"
          >
            <p class="text-xl font-semibold text-pink-300 mb-2">
              <span class="text-pink-500">Numéro :</span> {{ order.order_id }}
            </p>
            <p class="mb-1">
              <span class="font-semibold text-pink-500">Total :</span>
              <span class="text-pink-300">{{ order.total_price }} €</span>
            </p>
            <p class="mb-1">
              <span class="font-semibold text-pink-500">Statut :</span>
              <span class="text-pink-300">{{ getStatusText(order.status_id) }}</span>
            </p>
            <p>
              <span class="font-semibold text-pink-500">Date :</span>
              <span class="text-pink-300">{{ formatFrenchDateWithYear(order.updated_at) }}</span>
            </p>

            <div v-if="order.items?.length" class="mt-4">
              <h4 class="text-lg font-bold text-pink-500 mb-3 border-b border-pink-500 pb-1">
                Articles :
              </h4>
              <ul class="list-disc list-inside text-pink-300 space-y-1">
                <li
                  v-for="item in order.items"
                  :key="item.item_id"
                  class="hover:text-pink-500 transition-colors duration-200"
                >
                  {{ item.product_name }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div v-else class="text-pink-400 italic text-center mt-12">Aucune commande effectuée.</div>
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
      userId: null,
      orders: [],
      save: false
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
        this.userId = userId;

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

  async updateUser() {
    const payload = {
      name: this.user.name,
      firstname: this.user.firstname,
      email: this.user.email
    };

    try {
      const response = await userModel.updateUserById(this.userId, payload);
      if(response !== null){
        this.save = true;
      }
    } catch (error) {
      console.error('Erreur lors de la mise à jour de l’utilisateur :', error);
    }
  },

   async updateUserDelivery() {
    const payload = {
      address: this.user.address,
      zip_code: this.user.zip_code,
      city: this.user.city
    };

    try {
      const response = await userModel.updateUserDelivery(this.userId, payload);
      if(response !== null){
        this.save = true;
      }
    } catch (error) {
      console.error('Erreur lors de la mise à jour de l’utilisateur :', error);
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
