<template>
    <navBar />
  <div class="min-h-screen">


    <main class="flex-grow container mx-auto px-4 py-12">
      <h1 class="text-5xl sm:text-6xl font-extrabold tracking-wide text-pink-500 drop-shadow-[0_0_10px_rgba(236,72,153,0.5)] flex items-center justify-center mt-12">
          Contactez-nous
      </h1>
      <Contact />
    </main>
  </div>
</template>

<script>
import Contact from '../components/ContactComponent.vue';
import navBar from '../components/NavBar.vue';

export default {
  components: {
    Contact,
    navBar
  }
};
</script>
