<template>
  <div class="container">
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
import userModel from '@/models/userModel';
import { jwtDecode } from 'jwt-decode';

export default {
  name: 'Home',
  components: {
    NavBar,
    ProductCard  
  },
  data() {
    return {
      userName: 'Invité',
      item: []
    };
  },
  async created() {
    try {
      const token = document.cookie
        .split('; ')
        .find(row => row.startsWith('pmaUser'))
        ?.split('=')[1];

      if (token) {
        const decoded = jwtDecode(token);
        const userId = decoded.id;
        const currentUser = await userModel.getUserById(userId);

        if (currentUser && currentUser.firstname) {
          this.userName = currentUser.firstname;
        }
      }
      const response = await productModel.getProduct();
      this.item = response.map(prod => ({
        ...prod,
        picture_url: prod.picture_url ? prod.picture_url.trim() : ''
      }));
    } catch (error) {
      console.error('Erreur dans le created() :', error);
    }
  }
};
</script>

<style scoped>

.container{
  height: 98vh;
}
.product-list {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}
</style>
