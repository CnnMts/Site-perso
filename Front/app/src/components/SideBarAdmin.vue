<template>
  <div class="fixed top-0 left-0 w-72 h-full bg-gray-900 shadow-xl px-6 py-8 border-r border-pink-500/20 text-white">
    <div class="text-2xl  mb-10 text-center drop-shadow-md font-extrabold tracking-wide text-pink-500 drop-shadow-[0_0_10px_rgba(236,72,153,0.5)]">
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
