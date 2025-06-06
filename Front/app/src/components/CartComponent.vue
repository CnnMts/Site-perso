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
    const token = getCookie('pmaUser')
    if (!token) {
      console.error('Token missing in cookie pmaUser')
      loading.value = false
      return
    }

    const payload = token.split('.')[1]
    const decodedPayload = atob(payload)
    const payloadObj = JSON.parse(decodedPayload)
    const userId = payloadObj.id

    if (!userId) {
      console.error('Token ID is missing in the token payload')
      loading.value = false
      return
    }

    const response = await fetch(`http://127.0.0.1:9999/order/cart/${userId}`)
    const text = await response.text()
    cart.value = JSON.parse(text)
    console.log(cart.value)
  } catch (err) {
    console.error('Erreur récupération panier :', err)
    cart.value = null
  } finally {
    loading.value = false
  }
}

const changeStatus = async () => {
  if (!cart.value || !cart.value.order_id) {
    alert('Aucune commande active')
    return
  }

  try {
    const orderId = cart.value.order_id
    const resOrder = await fetch(`http://127.0.0.1:9999/orders/${orderId}`)
    if (!resOrder.ok) throw new Error('Erreur récupération commande')

    const order = await resOrder.json()
    console.log(order.status_id)

    if (order.status_id === 3) {
      alert('Commande déjà payée.')
      return
    }
    const paid = 3;
    const resPatch = await fetch(`http://127.0.0.1:9999/ordersUpStatus/${orderId}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ status_id: paid })

    })

    if (!resPatch.ok) throw new Error('Erreur mise à jour statut')

    alert('Statut de la commande mis à jour en "payé" (3).')
    await fetchCart()
  } catch (error) {
    console.error(error)
    alert('Erreur lors de la mise à jour du statut.')
  }
}

onMounted(fetchCart)
</script>

