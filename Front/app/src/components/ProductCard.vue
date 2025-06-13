<template>
  <div
    v-if="product.display === 1"
    class="cursor-pointer bg-white border border-gray-200 rounded-xl w-[245px] p-4 shadow hover:shadow-xl transition duration-300 ease-in-out"
    @click="goToProductDetail"
  >
    <img
      :src="product.picture_url || '/Assets/Accessoire/default.jpg'"
      alt="Image produit"
     class="w-full h-[250px] object-cover rounded-md"
    />
    <div class="mt-3">
     <h3 class="text-lg font-semibold text-gray-800 truncate mb-1">{{ product.name }}</h3>
     <p class="text-indigo-600 font-bold text-base">{{ product.sale_price }} €</p>
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

</style>
