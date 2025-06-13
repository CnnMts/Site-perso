<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-violet-500 via-purple-500 to-pink-500">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
      <h1 class="text-3xl font-bold text-center text-violet-700 mb-6">Créer un compte</h1>
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
          <input
            type="text"
            id="name"
            v-model="name"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500"
          />
        </div>

        <div>
          <label for="firstname" class="block text-sm font-medium text-gray-700">Prénom</label>
          <input
            type="text"
            id="firstname"
            v-model="firstname"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
          />
        </div>

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
          <label for="address" class="block text-sm font-medium text-gray-700">Adresse</label>
          <input
            type="text"
            id="address"
            v-model="address"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
          />
        </div>

        <div>
          <label for="zip_code" class="block text-sm font-medium text-gray-700">Code Postal</label>
          <input
            type="text"
            id="zip_code"
            v-model="zip_code"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500"
          />
        </div>

        <div>
          <label for="city" class="block text-sm font-medium text-gray-700">Ville</label>
          <input
            type="text"
            id="city"
            v-model="city"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
          <input
            type="password"
            id="password"
            v-model="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500"
          />
        </div>

        <div>
          <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
          <input
            type="password"
            id="confirmPassword"
            v-model="confirmPassword"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
          />
        </div>

        <button
          type="submit"
          class="w-full py-2 px-4 bg-gradient-to-r from-violet-600 to-pink-500 text-white font-semibold rounded-lg shadow-md hover:from-violet-700 hover:to-pink-600 transition duration-200"
        >
          S'inscrire
        </button>

        <p v-if="errorMessage" class="text-red-600 text-sm mt-2 text-center">
          {{ errorMessage }}
        </p>
        <p v-if="successMessage" class="text-green-600 text-sm mt-2 text-center">
        {{ successMessage }}
        </p>


        <p class="text-sm text-center mt-4 text-gray-700">
          Vous avez déjà un compte ?
          <router-link to="/login" class="text-violet-600 hover:underline font-medium">
            Connectez-vous
          </router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script>
import userModel from '../models/userModel';

export default {
  name: 'RegisterForm',
  data() {
    return {
      firstname: '',
      name: '',
      email: '',
      password: '',
      address: '',
      zip_code: '',
      city: '',
      confirmPassword: '',
      errorMessage: '',
      successMessage: ''
    };
  },
  methods: {
    async handleSubmit() {
      if (this.password !== this.confirmPassword) {
        this.errorMessage = "Les mots de passe ne correspondent pas.";
        return;
      }

      const userData = {
        firstname: this.firstname,
        name: this.name,
        email: this.email,
        password: this.password,
        address: this.address,
        zip_code: this.zip_code,
        city: this.city,
      };

      const createdUser = await userModel.createUser(userData);

      if (createdUser) {
        this.firstname = '';
        this.name = '';
        this.email = '';
        this.address = '';
        this.zip_code = '';
        this.city = '';
        this.password = '';
        this.confirmPassword = '';
        this.errorMessage = '';
        
        this.successMessage = "Inscription réussie ! Redirection...";
        setTimeout(() => {
          this.successMessage = '';
          this.$router.push('/login');
        }, 2000);
      } else {
        this.errorMessage = "Une erreur est survenue lors de la création de l'utilisateur.";
      }
    },
  },
};
</script>

