<template>
  <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1
      class="text-3xl font-extrabold mb-6 border-b-4 border-violet-400 pb-2 text-gray-900 text-center"
    >
      Mon Panier
    </h1>

    <div v-if="loading" class="text-gray-500 italic text-lg text-center">
      Chargement du panier...
    </div>

    <div v-else-if="!cart" class="text-red-600 font-semibold text-lg text-center">
      Aucun panier actif trouvé.
    </div>

    <div v-else-if="cart.status_id === 3" class="font-semibold text-lg text-center">
      Vous n'avez plus de commande en cours
    </div>

    <div v-else>
      <ul class="space-y-4">
        <li
          v-for="(group, index) in groupedItems"
          :key="index"
          class="border border-gray-300 rounded-lg p-4 shadow hover:shadow-lg transition-shadow duration-200"
        >
          <div class="flex flex-col sm:flex-row justify-between items-center">
            <div class="w-full sm:w-3/4">
              <p
                @click="toggleDetail(group.name)"
              class="font-semibold text-lg text-gray-800 hover:text-transparent hover:bg-clip-text 
                      hover:bg-gradient-to-r hover:from-pink-500 hover:to-violet-600 
                      active:from-pink-600 active:to-violet-700 
                      break-words cursor-pointer select-none transition-all duration-300"
              >
                {{ group.name }}<span v-if="group.count > 1"> x{{ group.count }}</span>
              </p>
            </div>
            <div
              class="mt-3 sm:mt-0 w-full sm:w-1/4 text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-violet-600  font-bold text-xl sm:text-right"
            >
              {{ group.totalPrice.toFixed(2) }} €
            </div>
          </div>

          <transition name="fade">
            <div v-show="expanded[group.name]" class="mt-4 border-t border-gray-200 pt-3">
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div
                  v-for="item in group.items"
                  :key="item.item_id"
                  class="border rounded-md overflow-hidden shadow-sm"
                >
                  <img
                    v-if="item.picture"
                    :src="formatBase64Image(item.picture)"
                    alt="Photo produit"
                    class="w-full h-24 object-cover"
                  />
                </div>
              </div>
            </div>
          </transition>
        </li>
      </ul>

      <div
        class="mt-8 border-t border-gray-300 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4"
      >
        <p class="text-2xl font-extrabold text-gray-900">
          Total : {{ (Number(cart.total_price) || 0).toFixed(2) }} €
        </p>
        <button
          @click="changeStatus"
          class="bg-gradient-to-r from-violet-600 to-pink-500 hover:from-violet-700 hover:to-pink-600 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition-all duration-300 w-full sm:w-auto"
        >
          Payer
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import orderModel from '../models/orderModel';
import Swal  from 'sweetalert2';
export default {
  data() {
    return {
      cart: null,
      loading: true,
      userId: null,
      expanded: {},
    };
  },
  computed: {
    groupedItems() {
      if (!this.cart || !this.cart.items) return [];
      const map = new Map();
      this.cart.items.forEach((item) => {
        const key = item.product_name;
        if (map.has(key)) {
          const group = map.get(key);
          group.count += 1;
          group.totalPrice += Number(item.sale_price) || 0;
          group.items.push(item);
        } else {
          map.set(key, {
            name: item.product_name,
            product_id: item.product_id,
            image: item.picture,
            count: 1,
            totalPrice: Number(item.sale_price) || 0,
            items: [item],
          });
        }
      });
      return Array.from(map.values());
    },
  },
  methods: {
    formatBase64Image(image) {
      if (!image) return null;
      if (image.startsWith('data:image')) return image;
      return `data:image/png;base64,${image}`;
    },
    toggleDetail(productName) {
      this.expanded = { ...this.expanded, [productName]: !this.expanded[productName] };
    },
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
          this.loading = false;
          return;
        }

        const payload = token.split('.')[1];
        const decodedPayload = atob(payload);
        const payloadObj = JSON.parse(decodedPayload);
        this.userId = payloadObj.id;

        if (!this.userId) {
          this.loading = false;
          return;
        }

        const data = await orderModel.getCartClient(this.userId);
        this.cart = data;
        console.log(this.cart)
      } catch {
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
        await Swal.fire({
        icon: 'success',
        title: 'Merci pour votre achat',
        confirmButtonText: 'OK'
      });
        await this.fetchCart();
        this.$router.push('/');
      } catch {
        alert('Erreur lors de la mise à jour du statut.');
      }
    },
  },
  mounted() {
    this.fetchCart();
  },
};
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  max-height: 0;
  overflow: hidden;
}
.fade-enter-to,
.fade-leave-from {
  opacity: 1;
  max-height: 500px;
  overflow: hidden;
}
</style>
