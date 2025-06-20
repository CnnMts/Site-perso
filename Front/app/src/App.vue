<template>
  <div id="app" class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black p-6 sm:p-10">
    <router-view></router-view>
  </div>
  <Footer v-if="!$route.meta.hideFooter" />
</template>

<script scoped>
import { ref } from 'vue';
import Footer from './components/Footer.vue';
export default{
  components:{
    Footer
  }
};
</script>
