<template>
  <div class="container-contact">
  <navBar />
  <h1>Contact Us</h1>
  <Contact />
   <Footer />
  </div>
</template>

<script>
import Contact from '../components/ContactComponent.vue';
import navBar from '../components/NavBar.vue';
import Footer from '../components/Footer.vue';


export default {
  components: {
    Contact,
    navBar,
    Footer,
  }
};
</script>

<style>

</style>