<template>
  <div class="login-form">
    <h1>Connexion</h1>
    <form @submit.prevent="handleSubmit">
     
      <div>
        <label for="email">Email</label>
        <input type="email" id="email" v-model="email" required />
      </div>
      <div>
        <label for="password">Mot de passe</label>
        <input type="password" id="password" v-model="password" required />
      </div>
  
      <button type="submit">Connecter</button>

      
      <div v-if="errorMessage" class="error-message">
        {{ errorMessage }}
      </div>
    </form>
  </div>
</template>

<script>
import userModel from '@/models/userModel'; 

export default {
  name: 'register',

  data() {
    return {
      email: '', 
      password: '',
      errorMessage: '',
    };
  },
  methods: {
    async createUser(userData) {
      try {
        const response = await fetch('http://127.0.0.1:9999/auth/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(userData),
        });

        if (!response.ok) {
          throw new Error('Erreur lors de la connexion.');
        }

        const data = await response.json();
        return data; 
      } catch (error) {
        console.error(error.message);
        this.errorMessage = error.message;
        return null;
      }
    },

    async handleSubmit() {
  const userData = {
    email: this.email,
    password: this.password,
  };
  const createdUser = await this.createUser(userData);

  if (createdUser) {
    try {
      const user = await userModel.getNameByEmail(this.email);
      if (user ) {
        localStorage.setItem('name', user);
        alert(`Bienvenue ${user} ! Connexion réussie !`);
        window.location.href = '/';
      } else {
        console.error("Nom introuvable pour l'utilisateur.");
      }
    } catch (error) {
      console.error("Erreur lors de la récupération du nom :", error.message);
    }

    this.email = '';
    this.password = '';
    this.errorMessage = '';
  } else {
    this.errorMessage = "Une erreur est survenue lors de la connexion.";
  }
},

  },
};
</script>




<style scoped>
.register-form {
  width: 300px;
  margin: 0 auto;
}

input {
  width: 100%;
  padding: 8px;
  margin: 5px 0;
}

button {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: #45a049;
}

.error-message {
  color: red;
  font-size: 14px;
  margin-top: 10px;
}
</style>
