<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Gestion des Produits</h1>

    <button
      @click="openModal"
      class="bg-green-600 text-white px-4 py-2 rounded mb-4"
    >
      ➕ Ajouter un produit
    </button>

    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center
             justify-center z-50"
    >
      <div
        class="bg-white p-6 rounded shadow-md w-full max-w-md relative"
      >
        <button
          @click="closeModal"
          class="absolute top-2 right-2 text-gray-500 hover:text-black"
        >
          ✖
        </button>

        <h2 class="text-xl font-semibold mb-4">
          {{ form.id ? 'Modifier' : 'Ajouter' }} un produit
        </h2>

        <form @submit.prevent="handleSubmit" class="space-y-3">
          <div>
            <label class="block text-sm font-medium mb-1">
              Nom du produit
            </label>
            <input
              v-model="form.name"
              placeholder="Ex : Tasse Blanche"
              class="border p-2 w-full"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Stock</label>
            <input
              v-model.number="form.stock"
              type="number"
              min="0"
              placeholder="Ex : 20"
              class="border p-2 w-full"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">
              Prix de vente (€)
            </label>
            <div class="relative">
              <input
                v-model.number="form.sale_price"
                type="number"
                step="0.01"
                class="border p-2 w-full pr-10"
                placeholder="Ex : 12.00"
              />
              <span class="absolute right-3 top-2.5 text-gray-500">
                €
              </span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">
              Prix d'achat (€)
            </label>
            <div class="relative">
              <input
                v-model.number="form.purchase_price"
                type="number"
                step="0.01"
                class="border p-2 w-full pr-10"
                placeholder="Ex : 1.50"
              />
              <span class="absolute right-3 top-2.5 text-gray-500">
                €
              </span>
            </div>
          </div>

          <button
            type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded w-full"
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
        class="border p-3 rounded flex justify-between items-center bg-white"
      >
        <div>
          <strong>{{ product.name }}</strong><br />
          Stock : {{ product.stock }} |
          Vente : {{ product.sale_price }}€ |
          Achat : {{ product.purchase_price }}€
        </div>
        <div class="space-x-2">
          <button
            @click="fillForm(product)"
            class="text-yellow-600 hover:text-yellow-800"
          >
            ✏️
          </button>
          <button
            @click="deleteProduct(product.id)"
            class="text-red-600 hover:text-red-800"
          >
            🗑️
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
