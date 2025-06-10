<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Mon Panier</h1>

    <div v-if="loading" class="text-gray-500">Chargement du panier...</div>

    <div v-else-if="!cart">
      <p class="text-red-500">Aucun panier actif trouvé.</p>
    </div>

    <div v-else-if="cart.status_id === 3">
      <div class="text-red-600">
        <p>Vous n'avez plus de commande en cours</p>
      </div>
    </div>

    <div v-else>
      <ul class="space-y-4">
        <li
          v-for="item in cart.items"
          :key="item.item_id"
          class="border p-4 rounded-lg shadow"
        >
          <div class="flex justify-between">
            <div>
              <p class="font-semibold">{{ item.product_name }}</p>
              <p class="text-sm text-gray-600">ID produit : {{ item.product_id }}</p>
            </div>
            <div class="text-right">
              <p class="text-lg font-bold">
                {{ (Number(item.sale_price) || 0).toFixed(2) }} €
              </p>
            </div>
          </div>
        </li>
      </ul>

      <div class="mt-6 border-t pt-4 text-right">
        <p class="text-xl font-semibold">
          Total : {{ (Number(cart.total_price) || 0).toFixed(2) }} €
        </p>
        <button
          @click="changeStatus"
          class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Payer
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import orderModel from '../models/orderModel';

export default {
  data() {
    return {
      cart: null,
      loading: true,
      userId: null,
    };
  },
  methods: {
    getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
      return null;
    },
    async fetchCart() {
      try {
        const token = this.getCookie('pmaUser');
        if (!token) {
          console.error('Token missing in cookie pmaUser');
          this.loading = false;
          return;
        }

        const payload = token.split('.')[1];
        const decodedPayload = atob(payload);
        const payloadObj = JSON.parse(decodedPayload);
        this.userId = payloadObj.id;

        if (!this.userId) {
          console.error('Token ID is missing in the token payload');
          this.loading = false;
          return;
        }

        const data = await orderModel.getCartClient(this.userId);
        this.cart = data;
      } catch (err) {
        console.error('Erreur récupération panier :', err);
        this.cart = null;
      } finally {
        this.loading = false;
      }
    },
    async changeStatus() {
      if (!this.cart || !this.cart.order_id) {
        alert('Aucune commande active');
        return;
      }

      try {
        const orderId = this.cart.order_id;
        if (!this.userId) throw new Error('Utilisateur non défini');
        const order = await orderModel.getCartClient(this.userId);
        if (!order) throw new Error('Erreur récupération commande');
        if (order.status_id === 3) {
          alert('Commande déjà payée.');
          return;
        }
        await orderModel.updateOrderStatus(orderId, 3);
        alert('Merci pour votre achat.');
        await this.fetchCart();
        this.$router.push('/');
      } catch (error) {
        console.error(error);
        alert('Erreur lors de la mise à jour du statut.');
      }
    },
  },
  mounted() {
    this.fetchCart();
  },
};
</script>
