<template>
  <NavBar />
  <div class="min-h-screen text-white p-6 mt-24">
    <section
      class="max-w-5xl mx-auto text-center py-16 px-6 rounded-3xl border border-pink-500/20 shadow-[0_0_30px_rgba(236,72,153,0.15)] animate-fade-in-up"
    >
      <h1 class="text-5xl font-extrabold mb-4 text-pink-400 drop-shadow-md">Crée ta tasse personnalisée</h1>
      <p class="text-lg text-gray-300 mb-10">
        Ajoute ton design ou une photo – le cadeau parfait à créer toi-même !
      </p>

      <img
        src="../../public/Assets/Accessoire/Mug_Landingpage.jpg"
        alt="Mug personnalisé"
        class="mx-auto w-72 rounded-xl shadow-lg mb-10 transition-transform hover:scale-105 duration-500"
      />

      <router-link
        to="/customizer/1"
        class="bg-pink-600 text-white text-lg font-semibold px-6 py-3 rounded-full shadow-md hover:bg-pink-700 transition"
      >
        Personnaliser ma tasse
      </router-link>
    </section>
    <section
      class="max-w-4xl mx-auto mt-20 py-12 px-6 grid sm:grid-cols-2 gap-8 text-left bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl border border-pink-500/10 shadow-[0_0_20px_rgba(236,72,153,0.05)] animate-fade-in-up"
    >
      <div>
        <h2 class="text-xl font-semibold text-pink-400 mb-2"> Personnalisation facile</h2>
        <p class="text-gray-300">Utilise notre éditeur pour ajouter une image, du texte ou un logo.</p>
      </div>
      <div>
        <h2 class="text-xl font-semibold text-pink-400 mb-2">Livraison rapide</h2>
        <p class="text-gray-300">Reçois ta tasse sous 3 à 5 jours ouvrés directement chez toi.</p>
      </div>
      <div>
        <h2 class="text-xl font-semibold text-pink-400 mb-2"> Idée cadeau originale</h2>
        <p class="text-gray-300">Parfaite pour un anniversaire, la fête des mères ou un collègue.</p>
      </div>
      <div>
        <h2 class="text-xl font-semibold text-pink-400 mb-2"> Avis clients</h2>
        <p class="text-gray-300">"J’ai adoré la qualité ! Ma photo rend super bien." – Julie</p>
      </div>
    </section>
  </div>
</template>

<script>
import NavBar from '@/components/NavBar.vue';

export default {
  name: "LandingPage",
  components: {
    NavBar,
  },
};
</script>
