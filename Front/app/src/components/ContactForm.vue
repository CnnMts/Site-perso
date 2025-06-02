<template>
  <div class="max-w-md mx-auto mt-10 px-4">
    <form @submit.prevent="submitForm" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
      <div v-for="(field, index) in fields" :key="index">
        <label :for="field.id" class="block text-sm font-medium text-gray-700 mb-1">
          {{ field.label }}
        </label>
        <input
          :type="field.type"
          :id="field.id"
          v-model="field.value"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>
      <button
        type="submit"
        class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200"
      >
        Envoyer
      </button>
    </form>
  </div>
</template>

<script>
import ContactMessagesModel from "../models/contactMessagesModel";

export default {
  data() {
    return {
      fields: [
        { id: "name", label: "Nom", type: "text", value: "" },
        { id: "email", label: "Email", type: "email", value: "" },
        { id: "subject", label: "Sujet", type: "text", value: "" },
        { id: "message", label: "Message", type: "text", value: "" },
      ],
    };
  },
  methods: {
    async submitForm() {
      const payload = {};
      this.fields.forEach((field) => {
        payload[field.id] = field.value;
      });

      try {
        await ContactMessagesModel.addContactMessage(payload);
        this.fields.forEach((field) => (field.value = ""));
      } catch (error) {
        console.error(error);
      }
    },
  },
};
</script>
