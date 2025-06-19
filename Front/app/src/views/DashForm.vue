<template>
  <div class="flex h-screen">
    <div >
      <SideBar />
    </div>
    <div class="flex-1  p-4 overflow-y-auto">
      <FormComponents />
    </div>
  </div>
</template>

<script>
import SideBar from "../components/SideBarAdmin.vue"
import FormComponents from "../components/FormComponents.vue";

export default {
  components: {
    SideBar,
    FormComponents
  }
}
</script>
