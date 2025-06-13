<template>
  <div class="bg-gray-900 text-white py-6">
    <footer class="max-w-screen-xl mx-auto px-4 flex flex-col sm:flex-row justify-between items-center gap-4">
      <div class="text-center sm:text-left">
        <h2 class="text-lg font-semibold">Mug & Co</h2>
        <p class="text-sm text-gray-400">© {{ new Date().getFullYear() }} Tous droits réservés</p>
      </div>

      <div class="flex gap-4">
        <a href="tel:+33646848928" class="text-sm hover:text-gray-300">
           06.46.84.89.28
        </a>
        <a href="mailto:contact@mymentalworld.com" class="text-sm hover:text-gray-300">
          contact@mugandco.com
        </a>
      </div>

      <div class="flex gap-4">
        <a href="#" class="hover:text-blue-400">Mentions légales</a>
        <a href="#" class="hover:text-blue-400">CGU</a>
      </div>
    </footer>
  </div>
</template>

<script>
export default {}
</script>
