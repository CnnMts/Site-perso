
<template>
  <div>
    <form @submit.prevent="addTodo">
      <fieldset>
        <input type="text" placeholder="Tâche à faire" v-model="newTodo">
        <button :disabled="newTodo.length === 0">Ajouter</button>
      </fieldset>
    </form>
    <div v-if="todos.length === 0">Vous n'avez pas de tâche à faire</div>
    <div v-else>
      <ul>
        <TodoItem
          v-for="todo in todos"
          :key="todo.id"
          :todo="todo"
          @toggle="toggleTodo"
        />
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import TodoItem from './TodoItem.vue';

const todos = ref([
  { id: 1, title: 'Tâche de test', completed: false }
]);
const newTodo = ref('');

const addTodo = () => {
  const todo = {
    id: Date.now(),
    title: newTodo.value,
    completed: false
  };
  todos.value.push(todo);
  newTodo.value = '';
};

const toggleTodo = (todo) => {
  todo.completed = !todo.completed;
};
</script>

<style scoped>

</style>
