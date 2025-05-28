<template>
  <div class="container-mid">
    <h1>Liste des utilisateurs</h1>
    <div v-for="user in users" :key="user.id" class="user-card">
      <p><strong>Nom :</strong> {{ user.name }}</p>
      <p><strong>Email :</strong> {{ user.email }}</p>
      <p><strong>Rôle :</strong> {{ role }}</p>

      <button @click="deleteUser(user.id)" class="delete-button">
        Supprimer
      </button>

      <hr>
    </div>
  </div>
</template>

<script>
import roleModel from '@/models/roleModel';
import userModel from '@/models/userModel';

export default {
  data() {
    return {
      users: [],
    };
  },
  async mounted() {
    await this.loadUsers();
  },
  methods: {
    async loadUsers() {
      try {
        const response = await userModel.getUsers();
        this.users = response || [];
        console.log(this.users);
      } catch (error) {
        console.error("Erreur lors du chargement des utilisateurs :", error);
      }
    },
    async deleteUser(id) {
        if (confirm('Es-tu sûr de vouloir supprimer cet utilisateur ?')) {
          try {
              const result = await userModel.deleteUserById(id);
              this.users = this.users.filter(user => user.id !== id);
            } catch (error) {
              console.error('Erreur lors de la suppression de l’utilisateur :', error);
            }
          }
        }
    },

    async getRoleByUser(user) {
    try {
        const role = await roleModel.getRoleById(user.role_id);
        return role;
    } catch (error) {
        console.error('Erreur lors de la récupération du rôle pour l\'utilisateur :', error);
        throw error;
    }
}

};
</script>

<style scoped>
.user-card {
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  margin-bottom: 10px;
}

.delete-button {
  margin-top: 10px;
  background-color: #ef4444;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.delete-button:hover {
  background-color: #b91c1c;
}
</style>
