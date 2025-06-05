<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Mon Panier</h1>

    <div v-if="loading" class="text-gray-500">Chargement du panier...</div>

    <div v-else-if="!cart">
      <p class="text-red-500">Aucun panier actif trouvé.</p>
    </div>

    <div v-else>
      <ul class="space-y-4">
        <li v-for="item in cart.items" :key="item.item_id" class="border p-4 rounded-lg shadow">
          <div class="flex justify-between">
            <div>
              <p class="font-semibold">{{ item.product_name }}</p>
              <p class="text-sm text-gray-600">ID produit : {{ item.product_id }}</p>
            </div>
            <div class="text-right">
              <p class="text-lg font-bold">{{ (Number(item.sale_price) || 0).toFixed(2) }} €</p>
            </div>
          </div>
        </li>
      </ul>

      <div class="mt-6 border-t pt-4 text-right">
        <p class="text-xl font-semibold">Total : {{ (Number(cart.total_price) || 0).toFixed(2) }} €</p>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const cart = ref(null)
const loading = ref(true)

function getCookie(name) {
  const value = `; ${document.cookie}`
  const parts = value.split(`; ${name}=`)
  if (parts.length === 2) return parts.pop().split(';').shift()
  return null
}

const fetchCart = async () => {
 try {
    const token = getCookie('pmaUser');
    if (!token) {
        console.error("Token missing in cookie pmaUser");
        loading.value = false;
        return;
    }
    const payload = token.split('.')[1];
    const decodedPayload = atob(payload);

    const payloadObj = JSON.parse(decodedPayload);
    const tokenid = payloadObj.id;

    if (!tokenid) {
        console.error("Token ID is missing in the token payload");
        loading.value = false;
        return;
    }

   const response = await fetch(`http://127.0.0.1:9999/order/cart/${tokenid}`)
    const text = await response.text()
    try {
      cart.value = JSON.parse(text)
    } catch (e) {
      console.error("Erreur de parsing JSON :", e)
      cart.value = null
    }

  } catch (err) {
    console.error("Erreur récupération panier :", err)
    cart.value = null
  } finally {
    loading.value = false
  }
}


onMounted(fetchCart)
</script>
