<template>
  <div class="main-container">
    <SideBar />
    <MainView />
  </div>
</template>

<script scoped>
import SideBar from "../components/SideBarAdmin.vue"
import MainView from "../components/MainBoardView.vue"

export default {
  components: {
    SideBar,
    MainView
  }
}
</script>

<style>
.main-container {
  display: flex;
  height: 100vh;
  overflow: hidden;
}
</style>
