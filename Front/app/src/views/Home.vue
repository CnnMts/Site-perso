<template>
  <div>
    <NavBar />
    <h1>Bienvenue sur la page d'accueil, {{ userName }}</h1>
    <div class="product-list">
      <ProductCard v-for="prod in item" :key="prod.id" :product="prod" />
    </div>
  </div>
</template>

<script>
import NavBar from '@/components/NavBar.vue';
import ProductCard from '@/components/ProductCard.vue'; 
import productModel from '@/models/productModel';

export default {
  name: 'Home',
  components: {
    NavBar,
    ProductCard  
  },
  data() {
    return {
      userName: '',
      item: []
    };
  },
  async created() {
    this.userName = localStorage.getItem('name') || 'Invité';
    try {
      const response = await productModel.getProduct();
      this.item = response.map(prod => ({
        ...prod,
        picture_url: prod.picture_url ? prod.picture_url.trim() : ''
      }));
    } catch (error) {
      console.error('Erreur lors de la récupération du produit:', error);
    }
  }
};
</script>

<style scoped>
.product-list {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}
</style>
