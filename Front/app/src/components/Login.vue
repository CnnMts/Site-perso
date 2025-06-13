<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-violet-500 via-purple-500 to-pink-500">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
      <h1 class="text-3xl font-bold text-center text-violet-700 mb-6">Connexion</h1>
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            type="email"
            id="email"
            v-model="email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500"
          />
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
          <input
            type="password"
            id="password"
            v-model="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
          />
        </div>
        <button
          type="submit"
          class="w-full py-2 px-4 bg-gradient-to-r from-violet-600 to-pink-500 text-white font-semibold rounded-lg shadow-md hover:from-violet-700 hover:to-pink-600 transition duration-200"
        >
          Se connecter
        </button>
        <p v-if="errorMessage" class="text-red-600 text-sm mt-2 text-center">
          {{ errorMessage }}
        </p>
        <p class="text-sm text-center mt-4 text-gray-700">
          Vous n'êtes pas inscrit ?
          <router-link to="/register" class="text-violet-600 hover:underline font-medium">
            Inscrivez-vous
          </router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script>
import userModel from '@/models/userModel';

export default {
  name: 'LoginForm',
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
    };
  },
  methods: {
    async handleSubmit() {
      const userData = {
        email: this.email,
        password: this.password,
      };

      const user = await userModel.logUser(userData);

      if (user) {
        this.email = '';
        this.password = '';
        this.errorMessage = '';
        this.$router.push('/');
      } else {
        this.errorMessage = "Une erreur est survenue lors de la connexion.";
      }
    },
  },
};
</script>
