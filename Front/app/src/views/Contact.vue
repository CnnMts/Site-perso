<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-white via-slate-50 to-blue-50">
    <navBar />

    <main class="flex-grow container mx-auto px-4 py-12">
      <h1 class="text-3xl sm:text-4xl font-bold text-center text-gray-800 mb-10">Contactez-nous</h1>
      <Contact />
    </main>

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
