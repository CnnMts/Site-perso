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
      <a href="#" @click.prevent="logout" class="rounded-lg px-4 py-2 transition
                                               hover:bg-pink-400 hover:shadow-lg cursor-pointer">
        Déconnexion
      </a>
    </nav>
  </div>
</template>

<script>
export default {
  methods: {
    async logout() {
      try {
        await fetch("http://127.0.0.1:9999/auth/logout", {
          method: "POST",
          credentials: "include"
        });
        alert("Vous êtes déconnecté");
        window.location.href = "/login";
      } catch (error) {
        console.error("Erreur de déconnexion :", error);
      }
    }
  }
}
</script>
