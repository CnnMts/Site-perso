<template>
  <div class="min-h-screen ">
    <NavBar @search="handleSearch" />
    
    <section class="max-w-7xl mx-auto px-6 py-16 text-center animate-fade-in-down">
      <h1 class="text-5xl sm:text-6xl font-extrabold tracking-wide text-pink-500 drop-shadow-[0_0_10px_rgba(236,72,153,0.5)] mt-12">
        Bienvenue {{ userName }}
      </h1>
      <p class="text-xl text-gray-300 mt-4 max-w-2xl mx-auto">
        <span> Nos produits futuristes soigneusement conçus pour exprimer votre style.</span>
      </p>
    </section>
    <div
  class="flex flex-col items-center bg-gradient-to-br from-gray-800 via-gray-900 to-black rounded-3xl p-10 mx-auto max-w-5xl mb-20  border border-pink-500/20 shadow-[0_0_30px_rgba(236,72,153,0.15)] animate-fade-in-up transition-transform duration-700 hover:scale-[1.01]"
>
      <h2 class="text-4xl font-bold text-pink-400 mb-4 tracking-wide text-center">
         Personnalise ta tasse
      </h2>
      <p class="text-gray-300 text-center max-w-2xl mb-6">
        Crée un design unique avec ton image, un message ou un logo. Livraison rapide, impression de haute qualité.
      </p>
      <img
        src="/Assets/Accessoire/Mug_Landingpage.jpg"
        alt="Mug personnalisé"
        class="w-64 h-auto rounded-xl mb-8 shadow-[0_0_30px_rgba(255,255,255,0.05)] transition duration-500"
      />
      <router-link
        to="/landing"
        class="relative inline-block px-8 py-4 text-lg font-bold text-white bg-gradient-to-r from-pink-500 to-fuchsia-600 rounded-full hover:scale-100 hover:shadow-[0_0_20px_rgba(236,72,153,0.4)] transition duration-300"
      >
        Personnaliser maintenant
        <span class="absolute inset-0 border border-pink-500 rounded-full animate-pulse opacity-10"></span>
      </router-link>
    </div>

    <section class="max-w-7xl mx-auto px-6 pb-16 mt-32 mb-32">
      <div v-if="filteredItems.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center max-w-7xl mx-auto animate-fade-in-up">
        <ProductCard
          v-for="prod in filteredItems"
          :key="prod.id"
          :product="prod"
          class="transform hover:scale-105 transition duration-300 bg-gray-800 rounded-2xl p-4 shadow-lg border border-pink-500/10 hover:shadow-pink-500/20"
        />
      </div>
      <div
        v-else
        class="text-center mt-24 text-pink-400 text-xl font-medium animate-bounce"
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
