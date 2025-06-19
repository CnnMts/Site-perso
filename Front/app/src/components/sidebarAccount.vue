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
      <section v-if="selected === 'settings'" class="Setting-account px-6 py-8 bg-gradient-to-r from-purple-100 via-pink-100 to-purple-50 rounded-lg shadow-lg max-w-xl mx-auto">
        <h3 class="text-4xl font-extrabold text-purple-800 mb-8 border-b-4 border-pink-400 pb-2">
        Paramètres du compte
        </h3>

      <div v-if="user" class="space-y-6">
        <div>
          <label class="block font-semibold mb-2 text-pink-600">Nom</label>
          <input 
            v-model="user.name" 
            type="text" 
            class="w-full border border-purple-300 rounded-lg px-4 py-3 text-purple-700 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-pink-400 transition" 
            placeholder="Entrez votre nom"
          />
        </div>
        <div>
          <label class="block font-semibold mb-2 text-pink-600">Prénom</label>
          <input 
            v-model="user.firstname" 
            type="text" 
            class="w-full border border-purple-300 rounded-lg px-4 py-3 text-purple-700 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-pink-400 transition" 
            placeholder="Entrez votre prénom"
            />
          </div>
          <div>
            <label class="block font-semibold mb-2 text-pink-600">Email</label>
            <input 
              v-model="user.email" 
              type="email" 
              class="w-full border border-purple-300 rounded-lg px-4 py-3 text-purple-700 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-pink-400 transition" 
              placeholder="Entrez votre email"
            />
          </div>
          <button
            @click="updateUser"
            class="mt-6 w-full px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-bold rounded-lg shadow-md transition"
          >
            Enregistrer les modifications
          </button>
          <p v-if="save === true" class="mt-4 text-green-600 font-semibold text-center">
            Modification(s) enregistrée(s) avec succès
          </p>
        </div>
        <div v-else class="text-purple-400 italic text-center mt-12">
          Chargement des informations...
        </div>
      </section>

      <section v-else-if="selected === 'payment'" class="Payment-delivery px-6 py-8 bg-gradient-to-r from-purple-100 via-pink-100 to-purple-50 rounded-lg shadow-lg max-w-lg mx-auto">
        <h3 class="text-4xl font-extrabold text-purple-800 mb-8 border-b-4 border-pink-400 pb-2">
          Paiement & Livraison
        </h3>
        <div v-if="user" class="space-y-6">
          <div>
            <label class="block font-semibold mb-2 text-pink-600">Adresse</label>
            <input 
              v-model="user.address" 
              type="text" 
              class="w-full border border-purple-300 rounded-lg px-4 py-3 text-purple-700 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-pink-400 transition"
              placeholder="Entrez votre adresse"
            />
          </div>
          <div>
            <label class="block font-semibold mb-2 text-pink-600">Code Postal</label>
            <input 
              v-model="user.zip_code" 
              type="text" 
              class="w-full border border-purple-300 rounded-lg px-4 py-3 text-purple-700 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-pink-400 transition"
              placeholder="Entrez votre code postal"
            />
          </div>
          <div>
          <label class="block font-semibold mb-2 text-pink-600">Ville</label>
          <input 
            v-model="user.city" 
            type="text" 
            class="w-full border border-purple-300 rounded-lg px-4 py-3 text-purple-700 placeholder-purple-400 focus:outline-none focus:ring-2 focus:ring-pink-400 transition"
            placeholder="Entrez votre ville"
          />
        </div>
        <button
          @click="updateUserDelivery"
          class="mt-6 w-full px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-bold rounded-lg shadow-md transition"
        >
          Enregistrer les modifications
        </button>
          <p v-if="save === true" class="mt-4 text-green-600 font-semibold text-center">
            Modification(s) enregistrée(s) avec succès
          </p>
        </div>
        <div v-else class="text-purple-400 italic text-center mt-12">
        Chargement des informations...
        </div>
      </section>

     <section v-else-if="selected === 'history'" class="history-of-command px-6 py-8 bg-gradient-to-r from-purple-100 via-pink-100 to-purple-50 rounded-lg shadow-lg">
        <h3 class="text-4xl font-extrabold text-purple-800 mb-8 border-b-4 border-pink-400 pb-2">
          Historique des commandes
        </h3>
        <div v-if="orders.length > 0" class="space-y-8">
          <div
            v-for="order in orders"
          :key="order.order_id"
          class="p-6 bg-white rounded-xl shadow-md border border-purple-300 hover:shadow-xl transition-shadow duration-300"
        >
          <p class="text-xl font-semibold text-purple-700 mb-2">
            <span class="text-pink-500">Numéro :</span> {{ order.order_id }}
          </p>
          <p class="mb-1">
            <span class="font-semibold text-pink-600">Total :</span> 
            <span class="text-purple-700">{{ order.total_price }} €</span>
          </p>
          <p class="mb-1">
            <span class="font-semibold text-pink-600">Statut :</span> 
            <span class="text-purple-700">{{ getStatusText(order.status_id) }}</span>
          </p>
          <p>
            <span class="font-semibold text-pink-600">Date :</span> 
            <span class="text-purple-700">{{ formatFrenchDateWithYear(order.updated_at) }}</span>
          </p>

          <div v-if="order.items?.length" class="mt-4">
            <h4 class="text-lg font-bold text-pink-500 mb-3 border-b border-pink-300 pb-1">
              Articles :
            </h4>
            <ul class="list-disc list-inside text-purple-600 space-y-1">
              <li v-for="item in order.items" :key="item.item_id" class="hover:text-pink-600 transition-colors duration-200">
                {{ item.product_name }}
              </li>
            </ul>
          </div>
        </div>
      </div>

        <div v-else class="text-purple-400 italic text-center mt-12">
          Aucune commande effectuée.
        </div>
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
