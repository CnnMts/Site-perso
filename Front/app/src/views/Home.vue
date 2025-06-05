<template>
  <div class="min-h-screen bg-gradient-to-br from-white via-slate-50 to-pink-50">
    <NavBar @search="handleSearch" />
    <section class="max-w-7xl mx-auto px-6 py-12">
      <div class="text-center mb-12">
        <h1 class="text-4xl sm:text-5xl font-bold text-gray-800 tracking-tight">
          Bienvenue {{ userName }}
        </h1>
        <p class="text-lg text-gray-600 mt-2">
          Découvrez nos derniers produits soigneusement sélectionnés pour vous.
        </p>
      </div>
      <div v-if="filteredItems.length" class="flex flex-wrap justify-center gap-8">
        <ProductCard
          v-for="prod in filteredItems"
          :key="prod.id"
          :product="prod"
          class="hover:scale-105 transform transition duration-300 ease-in-out bg-white shadow-md rounded-xl p-4 border border-gray-100"
        />
      </div>
      <div
        v-else
        class="text-center mt-24 text-gray-500 text-xl font-medium animate-pulse"
      >
        Aucun produit disponible pour le moment.
      </div>
    </section>
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
      userName: null,
      items: [],  
      filteredItems: [],  
      currentSearch: '',   
    };
  },
  methods: {
    handleSearch(searchTerm) {
      this.currentSearch = searchTerm.toLowerCase();
      this.filteredItems = this.items.filter(prod =>
        prod.name.toLowerCase().includes(this.currentSearch)
      );
    }
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

        if (currentUser?.firstname) {
          this.userName = currentUser.firstname;
        }
      }
      const response = await productModel.getProduct();
      this.items = response.map(prod => ({
        ...prod,
        picture_url: prod.picture_url?.trim() || ''
      }));
      this.filteredItems = this.items;
    } catch (error) {
      console.error('Erreur dans le created() :', error);
    }
  }
};
</script>
