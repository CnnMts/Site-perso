<template>
  <div class="flex h-screen">
    <div >
      <SideBar />
    </div>
    <div class="flex-1  p-4 overflow-y-auto">
      <product />
    </div>
  </div>
</template>

<script>
import SideBar from "../components/SideBarAdmin.vue"
import product from "../components/ProductComponent.vue"

export default {
  components: {
    SideBar,
    product
  }
}
</script>
