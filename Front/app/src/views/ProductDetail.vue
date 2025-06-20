<template>
  <div class="min-h-screen  py-6 px-4 sm:py-12 sm:px-6 lg:px-8">
    <div v-if="product" class="max-w-md sm:max-w-xl lg:max-w-3xl mx-auto shadow-lg rounded-xl overflow-hidden">
      <div class="p-4 sm:p-6 flex flex-col items-center text-center bg-gray-900 bg-opacity-10 ">
        <h1 class="text-2xl sm:text-3xl font-bold text-indigo-600 mb-3 sm:mb-4">Détails du produit</h1>
        <img
          :src="product.picture_url || '/Assets/Accessoire/default.jpg'"
          alt="Image du produit"
          class="w-full max-w-xs sm:max-w-sm md:max-w-md rounded-lg  mb-4 sm:mb-6"
        />
        <h3 class="text-xl sm:text-2xl font-semibold text-pink-700 mb-2">{{ product.name }}</h3>
        <p class="text-lg text-pink-600 font-medium mb-3 sm:mb-4">Prix : {{ product.sale_price }} €</p>
        <p class="text-sm sm:text-base text-violet-900 mb-4 sm:mb-6">Description : {{ product.description || 'Aucune description disponible.' }}</p>

        <button
          @click="goToCustomizer"
          class="bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 text-white px-4 py-2 sm:px-6 sm:py-2 rounded-full shadow hover:from-pink-600 hover:to-indigo-600 transition-all"
        >
          Personnaliser ce produit
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import productModel from '@/models/productModel';

export default {
  name: "ProductDetail",
  data() {
    return {
      product: null,
      productId: this.$route.params.id
    };
  },
  async created() {
    const productData = await productModel.getProductById(this.productId);
    this.product = productData;
  },
  methods: {
    goToCustomizer() {
      this.$router.push(`/customizer/${this.productId}`);
    }
  }
};
</script>
