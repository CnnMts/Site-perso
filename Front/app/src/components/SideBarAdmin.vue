<template>
  <div class="w-60 h-screen flex flex-col p-5 shadow-md
              bg-gradient-to-b from-purple-700 to-pink-500 text-white">
    <div class="text-2xl font-bold mb-10 text-center drop-shadow-md">
      Admin
    </div>
    <nav class="flex flex-col gap-4">
      <a href="/dash" class="rounded-lg px-4 py-2 transition
                          hover:bg-pink-400 hover:shadow-lg">
        Dashboard
      </a>
      <a href="/dash/users" class="rounded-lg px-4 py-2 transition
                                hover:bg-pink-400 hover:shadow-lg">
        Utilisateurs
      </a>
      <a href="/dash/product" class="rounded-lg px-4 py-2 transition
                                 hover:bg-pink-400 hover:shadow-lg">
        Produits
      </a>
       <a href="/dash/form" class="rounded-lg px-4 py-2 transition
                                 hover:bg-pink-400 hover:shadow-lg">
        Messages formulaires
      </a>
      <a href="#" @click.prevent="logout" class="rounded-lg px-4 py-2 transition
                                               hover:bg-pink-400 hover:shadow-lg cursor-pointer">
        Déconnexion
      </a>
    </nav>
  </div>
</template>

<script>
import userModel from '@/models/userModel';

export default {
  methods: {
    async logout() {
      try {
        const response = await userModel.logOut();
        if (!response.ok) {
          throw new Error(`Erreur serveur: ${response.status}`);
        }
      } catch (error) {
      }
    }
  }
}
</script>
