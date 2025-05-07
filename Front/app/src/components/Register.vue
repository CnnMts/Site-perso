<template>
  <div class="register-form">
    <h1>Créer un compte</h1>
    <form @submit.prevent="handleSubmit">
      <div>
        <label for="name">Nom</label>
        <input type="text" id="name" v-model="name" required />
      </div>
      <div>
        <label for="firstname">Prénom</label>
        <input type="text" id="firstname" v-model="firstname" required />
      </div>
      <div>
        <label for="email">Email</label>
        <input type="email" id="email" v-model="email" required />
      </div>
      <div>
        <label for="address">Adresse</label>
        <input type="text" id="address" v-model="address" required />
      </div>
      <div>
        <label for="zip_code">Code Postal</label>
        <input type="text" id="zip_code" v-model="zip_code" required />
      </div>
      <div>
        <label for="password">Mot de passe</label>
        <input type="password" id="password" v-model="password" required />
      </div>
      <div>
        <label for="confirmPassword">Confirmer le mot de passe</label>
        <input type="password" id="confirmPassword" v-model="confirmPassword" required />
      </div>
      <button type="submit">S'inscrire</button>
      <div v-if="errorMessage" class="error-message">
        {{ errorMessage }}
      </div>
    </form>
  </div>
</template>

<script>
import userModel from '../models/userModel'; 

export default {
  name: 'register',
  data() {
    return {
      firstname: '',
      name: '',
      email: '',
      password: '',
      address: '',
      zip_code: '',
      confirmPassword: '',
      errorMessage: ''
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
        zip_code: this.zip_code
      };

      const createdUser = await userModel.createUser(userData);

      if (createdUser) {
        this.firstname = '';
        this.name = '';
        this.email = '';
        this.address = '';
        this.zip_code = '';
        this.password = '';
        this.confirmPassword = '';
        this.errorMessage = '';
        console.log("Formulaire soumis avec succès !");
      } else {
        this.errorMessage = "Une erreur est survenue lors de la création de l'utilisateur.";
      }
    }
  }
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
