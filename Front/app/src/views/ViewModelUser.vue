<template>
  <div>
    <h1>Liste des utilisateurs</h1>
    <ul>
      <li v-for="user in users" :key="user.id">
        {{ user.name }} - {{ user.email }} - {{ user.password }} - {{ user.role_id }}
      </li>
    </ul>
  </div>
</template>

<script>
import userModel from '../models/userModel'; 

export default {
  name: 'UserView', 
  
  data() {
    return {
      users: []  
    };
  },
  
  async created() {
    try {
      
      this.users = await userModel.getUsers();
    } catch (error) {
      console.error('Erreur lors de la récupération des utilisateurs:', error);
    }
  }
};
</script>

<style scoped>

</style>
