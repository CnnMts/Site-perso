<template>
  <div class="main-container">
    <SideBarAdmin />
    <DashBoardUsers />
  </div>
</template>

<script>
import DashBoardUsers from '@/components/DashBoardUsers.vue';
import SideBarAdmin from '@/components/SideBarAdmin.vue';

export default {
  components: {
    DashBoardUsers,
    SideBarAdmin,
  },
};
</script>

<style scoped>
.main-container {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

.main-container > *:first-child {
  width: 250px;
  flex-shrink: 0;
}

.main-container > *:last-child {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
}
</style>
