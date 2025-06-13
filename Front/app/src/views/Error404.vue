<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-600 to-pink-500 flex flex-col items-center justify-center px-4">
    <div class="text-center text-white">
      <h1 class="text-8xl font-extrabold mb-4 drop-shadow-lg">404</h1>
      <p class="text-xl mb-6">Oups ! La page que vous cherchez n'existe pas.</p>
      <button
        @click="goHome"
        class="px-6 py-3 bg-white text-purple-700 font-semibold rounded shadow hover:bg-gray-100 transition"
      >
        Retour à l'accueil
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound',
  methods: {
    goHome() {
      this.$router.push('/');
    }
  }
}
</script>
