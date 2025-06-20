<template>
  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-violet-700 mb-6">Liste des utilisateurs</h1>
    <div 
      v-for="user in users" 
      :key="user.id" 
      class="p-4 mb-4 rounded-lg border border-violet-300 shadow"
    >
    <p>
      <strong class="text-violet-800">Nom : </strong> 
      <span class="text-pink-600">{{ user.name }}</span>
    </p>

      <p>
      <strong class="text-violet-800">Email : </strong> 
      <span class="text-pink-600">{{ user.email }}</span>
    </p>
      <p>
      <strong class="text-violet-800">Role : </strong> 
      <span class="text-pink-600">{{ user.role }}</span>
    </p>

      <button
        @click="deleteUser(user.id)"
        class="mt-4 bg-gradient-to-r from-violet-600 to-pink-500 hover:from-pink-500 hover:to-violet-600 text-white font-semibold py-2 px-4 rounded transition-colors duration-300"
      >
        Supprimer
      </button>

      <hr class="mt-4 border-violet-200" />
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
        this.users.forEach(user => this.changeRoleIdtoRole(user));
      } catch (error) {
        console.error("Erreur lors du chargement des utilisateurs :", error);
      }
    },
    async deleteUser(id) {
      if (confirm('Es-tu sûr de vouloir supprimer cet utilisateur ?')) {
        try {
          await userModel.deleteUserById(id);
          this.users = this.users.filter(user => user.id !== id);
        } catch (error) {
          console.error('Erreur lors de la suppression de l’utilisateur :', error);
        }
      }
    },
    async changeRoleIdtoRole(user) {
  try {
    const response = await roleModel.getRoleById(user.role_id);
    user.role = response.name || "NONE";
  } catch (error) {
    console.error("Erreur lors de la récupération du rôle :", error);
    user.role = "NONE";
  }
}

  },
};

</script>
