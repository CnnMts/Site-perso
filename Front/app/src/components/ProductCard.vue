<template>
  <div
    v-if="product.display === 1"
    class="cursor-pointer bg-blue-50 border border-gray-700 rounded-lg w-[245px] p-4 shadow-md text-center hover:shadow-lg transition"
    @click="goToProductDetail"
  >
    <img
      :src="product.picture_url || '/Assets/Accessoire/default.jpg'"
      alt="Image produit"
      class="w-full h-[250px] object-cover rounded-lg"
    />
    <div class="mt-3">
      <h3 class="text-lg font-bold my-2">{{ product.name }}</h3>
      <p class="text-orange-600 text-base">{{ product.sale_price }} €</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProductCard",
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  methods: {
    goToProductDetail() {
      this.$router.push({ name: 'product-detail', params: { id: this.product.id } });
    }
  }
};
</script>

<style scoped>
/* Plus besoin de style ici, tout est en Tailwind */
</style>
