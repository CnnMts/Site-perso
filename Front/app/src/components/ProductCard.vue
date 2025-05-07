<template>
  <div v-if="product.display === 1" class="product-card" @click="goToProductDetail">
    <img :src="product.picture_url || '/Assets/Accessoire/default.jpg'" alt="Image produit" class="product-image" />
    <div class="product-info">
      <h3 class="product-name">{{ product.name }}</h3>
      <p class="product-price">{{ product.sale_price }} €</p>
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
.product-card {
  cursor: pointer;
  border: 1px solid #ddd;
  border-radius: 8px;
  width: 245px;
  padding: 15px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.product-image {
  width: 100%;
  height: 250px;
  object-fit: cover;
  border-radius: 8px;
}

.product-info {
  margin-top: 10px;
}

.product-name {
  font-size: 18px;
  font-weight: bold;
  margin: 10px 0;
}

.product-price {
  font-size: 16px;
  color: #ff5722;
}
</style>
