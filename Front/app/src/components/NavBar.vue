<script setup>
import { ref } from 'vue'

const isMobileMenuOpen = ref(false)
</script>

<template>
  <nav class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 shadow-md">
    <div class="max-w-screen-xl mx-auto px-4 py-4 flex items-center justify-between">


      <div class="flex items-center gap-4">
        <a href="/">
          <img src="../assets/navIcon/Logo.webp" alt="Logo" class="h-12 object-contain drop-shadow-md" />
        </a>
        <button class="md:hidden text-white text-3xl" @click="isMobileMenuOpen = !isMobileMenuOpen">
          ☰
        </button>
      </div>
      <div class="hidden md:flex flex-grow justify-center px-4">
        <div class="flex w-[400px] bg-white rounded-full shadow-md overflow-hidden focus-within:ring-2 focus-within:ring-pink-400">
          <input
            type="text"
            placeholder="Rechercher un produit..."
            class="flex-grow px-4 py-2 text-gray-700 focus:outline-none rounded-l-full"
          />
          <button class="bg-pink-500 hover:bg-pink-600 text-white px-4 rounded-r-full transition-colors">
            🔍
          </button>
        </div>
      </div>

      <div class="hidden md:flex items-center gap-6 text-white text-lg">
        <a href="/contact" title="Support">
          <img src="../assets/navIcon/support.png" alt="Support" class="h-7 w-7" />
        </a>
        <img src="../assets/navIcon/hearth.png" alt="Favoris" class="h-7 w-7 cursor-pointer" />
        <img src="../assets/navIcon/compte.png" alt="Compte" class="h-7 w-7 cursor-pointer" />
        <img src="../assets/navIcon/shopping-cart.png" alt="Panier" class="h-7 w-7 cursor-pointer" />
      </div>
    </div>

    <div v-if="isMobileMenuOpen" class="md:hidden bg-white text-gray-800 px-4 py-4 space-y-4">
      <div class="flex w-full bg-gray-100 rounded-full shadow-sm overflow-hidden">
        <input
          type="text"
          placeholder="Rechercher..."
          class="flex-grow px-4 py-2 text-gray-700 focus:outline-none rounded-l-full"
        />
        <button class="bg-pink-500 text-white px-4 rounded-r-full">
          🔍
        </button>
      </div>
      <div class="flex justify-around items-center text-lg">
        <a href="/contact">
          <img src="../assets/navIcon/support.png" alt="Support" class="h-6 w-6" />
        </a>
        <img src="../assets/navIcon/hearth.png" alt="Favoris" class="h-6 w-6" />
        <img src="../assets/navIcon/compte.png" alt="Compte" class="h-6 w-6" />
        <img src="../assets/navIcon/shopping-cart.png" alt="Panier" class="h-6 w-6" />
      </div>
    </div>
  </nav>
</template>
