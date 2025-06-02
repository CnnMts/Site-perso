<template>
  <div class="container-left">
    <div class="logo">Admin</div>
    <nav class="nav-links">
      <a href="/dash" class="nav-item">Dashboard</a>
      <a href="/dash/users" class="nav-item">Utilisateurs</a>
      <a href="/dash/product" class="nav-item">Produits</a>
     <a href="#" class="nav-item" @click.prevent="logout">Déconnexion</a>


    </nav>
  </div>
</template>

<script>
export default {
  methods: {
    async logout() {
      try {
        fetch("http://127.0.0.1:9999/auth/logout", {
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


<style scoped>
.container-left {
  width: 240px;
  background-color: #1f2937; 
  color: white;
  height: 100vh;
  display: flex;
  flex-direction: column;
  padding: 20px;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.logo {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 40px;
  text-align: center;
}

.nav-links {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.nav-item {
  color: white;
  text-decoration: none;
  padding: 10px 15px;
  border-radius: 8px;
  transition: background-color 0.3s;
}

.nav-item:hover {
  background-color: #374151; 
}
</style>
