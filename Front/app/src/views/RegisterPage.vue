<template>
  <div class="register-page">
    <Register />
  </div>
</template>

<script>
import Register from '../components/Register.vue'; // Importer le composant Register

export default {
  components: {
    Register
  }
};
</script>

<style scoped>
.register-page {
  padding: 20px;
}
</style>
