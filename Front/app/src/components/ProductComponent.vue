<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4 text-purple-700">Gestion des Produits</h1>

    <button
      @click="openModal"
      class="bg-gradient-to-r from-violet-600 via-purple-700 to-pink-500 text-white px-4 py-2 rounded mb-4 hover:from-pink-500 hover:to-violet-600 transition-colors"
    >
       Ajouter un produit
    </button>

    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div
        class="bg-white p-6 rounded shadow-md w-full max-w-md relative"
      >
        <button
          @click="closeModal"
          class="absolute top-2 right-2 text-purple-500 hover:text-pink-600"
          aria-label="Fermer"
        >
          ✕
        </button>

        <h2 class="text-xl font-semibold mb-4 text-purple-800">
          {{ form.id ? 'Modifier' : 'Ajouter' }} un produit
        </h2>

        <form @submit.prevent="handleSubmit" class="space-y-3">
          <div>
            <label class="block text-sm font-medium mb-1 text-violet-700">
              Nom du produit
            </label>
            <input
              v-model="form.name"
              placeholder="Ex : Tasse Blanche"
              class="border border-violet-300 p-2 w-full rounded focus:ring-2 focus:ring-pink-400 focus:border-pink-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1 text-violet-700">Stock</label>
            <input
              v-model.number="form.stock"
              type="number"
              min="0"
              placeholder="Ex : 20"
              class="border border-violet-300 p-2 w-full rounded focus:ring-2 focus:ring-pink-400 focus:border-pink-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1 text-violet-700">
              Prix de vente (€)
            </label>
            <div class="relative">
              <input
                v-model.number="form.sale_price"
                type="number"
                class="border border-violet-300 p-2 w-full pr-10 rounded focus:ring-2 focus:ring-pink-400 focus:border-pink-500"
                placeholder="Ex : 12.00"
              />
              <span class="absolute right-3 top-2.5 text-pink-400 font-semibold">
                €
              </span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1 text-violet-700">
              Prix d'achat (€)
            </label>
            <div class="relative">
              <input
                v-model.number="form.purchase_price"
                type="number"
                class="border border-violet-300 p-2 w-full pr-10 rounded focus:ring-2 focus:ring-pink-400 focus:border-pink-500"
                placeholder="Ex : 1.50"
              />
              <span class="absolute right-3 top-2.5 text-pink-400 font-semibold">
                €
              </span>
            </div>
          </div>

          <button
            type="submit"
            class="bg-gradient-to-r from-violet-600 via-purple-700 to-pink-500 text-white px-4 py-2 rounded w-full hover:from-pink-500 hover:to-violet-600 transition-colors"
          >
            {{ form.id ? 'Modifier' : 'Ajouter' }}
          </button>
        </form>
      </div>
    </div>

    <ul class="space-y-2">
      <li
        v-for="product in products"
        :key="product.id"
        class="border border-violet-300 p-3 rounded flex justify-between items-center bg-white shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="text-purple-700">
          <strong>{{ product.name }}</strong><br />
          Stock : {{ product.stock }} |
          Vente : {{ product.sale_price }}€ |
          Achat : {{ product.purchase_price }}€
        </div>
        <div class="space-x-4">
          <button
            @click="fillForm(product)"
            class="text-violet-600 hover:text-pink-600 font-semibold transition-colors"
          >
            Edit
          </button>
          <button
            @click="deleteProduct(product.id)"
            class="text-red-600 hover:text-red-800 font-semibold transition-colors"
          >
            Delete
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>


<script>
export default {
  data() {
    return {
      showModal: false,
      products: [],
      form: {
        id: null,
        name: '',
        stock: 0,
        sale_price: 0,
        purchase_price: 0
      }
    }
  },

  methods: {
    async fetchProducts() {
      try {
        const res = await fetch('http://127.0.0.1:9999/product')
        this.products = await res.json()
      } catch (err) {
        this.logError('chargement des produits', err)
      }
    },

    async addProduct() {
      const payload = this.buildPostPayload()

      try {
        await fetch('http://127.0.0.1:9999/product', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        })
        await this.afterFormSubmit()
      } catch (err) {
        this.logError('ajout du produit', err)
      }
    },

    async updateProduct() {
     const payloadUpdate = this.buildPatchPayload()
      const url =
        `http://127.0.0.1:9999/product/${this.form.id}`

      try {
        await fetch(url, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payloadUpdate)
        })
        await this.afterFormSubmit()
      } catch (err) {
        this.logError('mise à jour du produit', err)
      }
    },

    async deleteProduct(id) {
      try {
        await fetch(
          `http://127.0.0.1:9999/product/${id}`,
          { method: 'DELETE' }
        )
        await this.fetchProducts()
      } catch (err) {
        this.logError('suppression du produit', err)
      }
    },

    handleSubmit() {
      if (this.form.id) {
        this.updateProduct()
      } else {
        this.addProduct()
      }
    },

    buildPatchPayload() {
  return {
    name: this.form.name,
    stock: parseInt(this.form.stock),
    sale_price: parseFloat(this.form.sale_price),
    purchase_price: parseFloat(this.form.purchase_price)
  }
},

buildPostPayload() {
  return {
    name: this.form.name,
    category_id: this.form.category_id || 1,
    sale_price: parseFloat(this.form.sale_price),
    purchase_price: parseFloat(this.form.purchase_price),
    stock: parseInt(this.form.stock),
    stock_alert: this.form.stock_alert || 5,
    sales_nbr: 0,
    display: 1,
    picture_url: this.form.picture_url || ''
  }
},


    afterFormSubmit() {
      this.resetForm()
      this.fetchProducts()
      this.closeModal()
    },

    fillForm(product) {
      this.form = {
        id: product.id,
        name: product.name,
        stock: product.stock,
        sale_price: product.sale_price,
        purchase_price: product.purchase_price
      }
      this.openModal()
    },

    resetForm() {
      this.form = {
        id: null,
        name: '',
        stock: 0,
        sale_price: 0,
        purchase_price: 0
      }
    },

    openModal() {
      this.showModal = true
    },

    closeModal() {
      this.resetForm()
      this.showModal = false
    },

    logError(context, err) {
      console.error(`Erreur lors de ${context}`, err)
    }
  },

  mounted() {
    this.fetchProducts()
  }
}
</script>
