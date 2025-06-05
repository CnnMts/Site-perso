<script setup>
import { reactive, ref, onMounted, defineEmits, watch } from 'vue';

const emit = defineEmits(['search']);

const state = reactive({
  isLoggedIn: false,
});

const isMobileMenuOpen = ref(false);
const searchQuery = ref('');
const searchError = ref('');
let debounceTimeout = null;

function toggleMobileMenu() {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
}

function handleSearch() {
  if (!searchQuery.value.trim()) {
    searchError.value = "Veuillez saisir un terme de recherche.";
    return;
  }
  searchError.value = '';
  emit('search', searchQuery.value.trim());
}

function getCookie(name) {
  const cookie = document.cookie
    .split('; ')
    .find(row => row.startsWith(name + '='));
  return cookie ? cookie.split('=')[1] : null;
}

function parseJwt(token) {
  try {
    const base64 = token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/');
    const json = decodeURIComponent(
      atob(base64)
        .split('')
        .map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
        .join('')
    );
    return JSON.parse(json);
  } catch {
    return null;
  }
}

watch(searchQuery, (newQuery) => {
  clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(() => {
    if (!newQuery.trim()) {
      emit('search', '');
    } else {
      searchError.value = '';
      emit('search', newQuery.trim());
    }
  }, 300);
});

onMounted(() => {
  const token = getCookie('pmaUser');
  if (!token) return;

  const payload = parseJwt(token);
  const now = Math.floor(Date.now() / 1000);
  if (payload && payload.exp > now) {
    state.isLoggedIn = true;
  }
});
</script>

<template>
  <nav class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 shadow-md">
    <div class="max-w-screen-xl mx-auto px-4 py-4 flex flex-col items-center">
      <div class="w-full flex items-center justify-between">
        <div class="flex items-center gap-4">
          <a href="/">
            <img src="../assets/navIcon/Logo.webp" alt="Logo" class="h-12 object-contain drop-shadow-md" />
          </a>
          <button class="md:hidden text-white text-3xl" @click="toggleMobileMenu">
            ☰
          </button>
        </div>
        <div class="hidden md:flex flex-grow justify-center px-4 flex-col items-center">
          <div class="flex w-[400px] bg-white rounded-full shadow-md overflow-hidden focus-within:ring-2 focus-within:ring-pink-400">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Rechercher un produit..."
              class="flex-grow px-4 py-2 text-gray-700 focus:outline-none rounded-l-full"
              @keydown.enter="handleSearch"
            />
            <button
              @click="handleSearch"
              class="bg-pink-500 hover:bg-pink-600 text-white px-4 rounded-r-full transition-colors"
            >
              🔍
            </button>
          </div>
          <div class="text-red-500 text-sm mt-1 min-h-[1.25rem]">
            {{ searchError }}
          </div>
        </div>
        <div class="hidden md:flex items-center gap-6 text-white text-lg">
          <a href="/contact" title="Support">
            <img src="../assets/navIcon/support.png" alt="Support" class="h-7 w-7" />
          </a>
          <a href="/cart" title="Panier">
            <img src="../assets/navIcon/shopping-cart.png" alt="Panier" class="h-7 w-7" />
          </a>
          <template v-if="!state.isLoggedIn">
            <a
              href="/login"
              class="flex items-center gap-2 px-4 py-2 bg-purple-500 text-white rounded-full hover:bg-purple-700 transition"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
              </svg>
              S'identifier
            </a>
          </template>
          <template v-else>
            <a href="/account" title="Mon compte">
              <img src="../assets/navIcon/compte.png" alt="Compte" class="h-7 w-7" />
            </a>
          </template>
        </div>
      </div>
      <div v-if="isMobileMenuOpen" class="md:hidden bg-white text-gray-800 px-4 py-4 space-y-4">
        <div class="flex justify-around items-center text-lg">
          <a href="/contact">
            <img src="../assets/navIcon/support.png" alt="Support" class="h-6 w-6" />
          </a>
          <template v-if="!state.isLoggedIn">
            <a href="/login">
              <img src="../assets/navIcon/compte.png" alt="Connexion" class="h-6 w-6" />
            </a>
          </template>
          <template v-else>
            <a href="/account">
              <img src="../assets/navIcon/compte.png" alt="Compte" class="h-6 w-6" />
            </a>
          </template>
          <a href="/cart">
            <img src="../assets/navIcon/shopping-cart.png" alt="Panier" class="h-6 w-6" />
          </a>
        </div>
        <div>
          <div class="flex bg-white rounded-full shadow-md overflow-hidden focus-within:ring-2 focus-within:ring-pink-400">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Rechercher un produit..."
              class="flex-grow px-4 py-2 text-gray-700 focus:outline-none rounded-l-full"
              @keydown.enter="handleSearch"
            />
            <button
              @click="handleSearch"
              class="bg-pink-500 hover:bg-pink-600 text-white px-4 rounded-r-full transition-colors"
            >
              🔍
            </button>
          </div>
          <div class="text-red-500 text-sm mt-1 min-h-[1.25rem]">
            {{ searchError }}
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>
