
<template>
  <li :class="{ completed: todo.completed }">
    <label>
      <input type="checkbox" v-model="todo.completed" @change="toggleTodo">
      {{ todo.title }}
    </label>
  </li>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  todo: {
    type: Object,
    required: true
  }
});

const toggleTodo = () => {
  emit('toggle', props.todo);
};
</script>

<style scoped>
.completed {
  opacity: 0.5;
  text-decoration: line-through;
}
</style>
