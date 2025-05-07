<template>
  <div v-if="product" class="product-detail">
    <h1>Détails du produit</h1>
    <img :src="product.picture_url || '/Assets/Accessoire/default.jpg'" alt="Image du produit" />
    <h3>{{ product.name }}</h3>
    <p>Prix: {{ product.sale_price }} €</p>
    <p>Description: </p> 
  </div>
  <router-link :to="`/customizer/${productId}`">
  <button class="btn-customize">Personnaliser ce produit</button>
</router-link>

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
  }
};
</script>

<style scoped>
.product-detail img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
}
.btn-customize {
  background-color: #3b82f6;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  margin-top: 1rem;
  transition: background 0.3s;
}

.btn-customize:hover {
  background-color: #2563eb;
}

</style>
