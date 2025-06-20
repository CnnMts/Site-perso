<template>
  <div class="flex-1 p-8 max-w-8xl overflow-y-auto ml-[350px]">
  <h1 class="text-xl font-bold mb-8 bg-gradient-to-r from-purple-500 via-pink-500 to-pink-400 bg-clip-text text-transparent">
  Tableau de bord
  </h1>


  <div class="flex flex-col gap-6">
  <div
    v-for="order in orders"
    :key="order.id"
    class="rounded-xl p-6 shadow-md flex cursor-pointer gap-6
       border border-transparent
       hover:shadow-xl hover:-translate-y-1 transition-transform duration-200"
    @click="openModal(order)"
  >
    <div class="flex flex-col flex-1 gap-1 text-purple-900">
      <p class="font-semibold text-lg bg-clip-text text-transparent
                bg-gradient-to-r from-purple-600 via-pink-600 to-pink-500">
        Commande #{{ order.id }}
      </p>
      <p>Client : {{ order.user_name }} {{ order.user_firstname }}</p>
      <p>Date : {{ formatDate(order.updated_at) }}</p>
      <p>Total : {{ order.total_price }} €</p>
    </div>
  </div>
</div>


    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50"
      @click.self="closeModal"
    >
      <div
        class="bg-white rounded-2xl p-8 max-w-lg w-full max-h-[90vh] overflow-y-auto text-center"
      >
        <h2 class="text-2xl font-bold mb-4">Détail de la commande #{{ selectedOrder.id }}</h2>
        <p class="mb-1"><strong>Client :</strong> {{ selectedOrder.user_name }} {{ selectedOrder.user_firstname }}</p>
        <p class="mb-1"><strong>Date :</strong> {{ formatDate(selectedOrder.updated_at) }}</p>
        <p class="mb-6"><strong>Total :</strong> {{ selectedOrder.total_price }} €</p>

        <h3 class="text-xl font-semibold mb-4">Articles associés :</h3>
        <div
          class="flex flex-wrap justify-center gap-6 mb-6"
        >
          <div
            v-for="item in orderItems"
            :key="item.name"
            class="bg-purple-100 rounded-xl p-4 w-36 flex flex-col items-center shadow"
          >
            <img
              v-if="item.picture"
              :src="item.picture"
              :alt="`Image de ${item.name}`"
              class="w-28 h-28 object-cover rounded-lg border border-purple-300 mb-3"
            />
            <div class="text-gray-800 text-sm text-center">
              <p class="font-semibold">{{ item.name }}</p>
              <p>Quantité : {{ item.quantity }}</p>
              <a
                v-if="item.picture"
                :href="item.picture"
                :download="`item_${item.name}.png`"
                class="inline-block mt-2 px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition"
                @click.stop
              >Télécharger l'image</a>
            </div>
          </div>
        </div>

        <img
          v-if="selectedOrder.picture"
          :src="selectedOrder.picture"
          alt="Image commande"
          class="w-full rounded-lg mb-6 object-contain max-h-60 mx-auto"
        />
        <button
          @click="closeModal"
          class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition"
        >
          Fermer
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import orderModel from '@/models/orderModel';
import orderItemModel from '@/models/orderItemModel';
import userModel from '@/models/userModel';

export default {
  data() {
    return {
      orders: [],
      selectedOrder: null,
      orderItems: [],
      showModal: false,
      token: null,
    };
  },

  async mounted() {
    function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
      return null;
    }

    this.token = getCookie('pmaUser');

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

      if (!this.token) {
        console.error("Token manquant !");
        this.orderItems = [];
        return;
      }
      orderItemModel.setToken(this.token);
      try {
        const data = await orderItemModel.getOrderByOrderId(order.id, this.token);

        if (!Array.isArray(data)) {
          console.error("La réponse n'est pas un tableau :", data);
          this.orderItems = [];
          return;
        }
        this.orderItems = data;

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
