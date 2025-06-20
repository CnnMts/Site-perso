<template>
  <div class="max-w-md mx-auto mt-10 px-4">
    <form @submit.prevent="submitForm" class="space-y-6 bg-gray-900 p-6 rounded-2xl shadow-[0_0_30px_rgba(236,72,153,0.15)] border border-pink-500/20">
      <div v-for="(field, index) in fields" :key="index">
        <label :for="field.id" class="block text-sm font-semibold text-pink-300 mb-1">
          {{ field.label }}
        </label>
        <input
          :type="field.type"
          :id="field.id"
          v-model="field.value"
          class="w-full px-4 py-2 border border-pink-500 bg-gray-800 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition"
        />
      </div>
      <button
        type="submit"
        class="w-full text-white font-semibold py-2 rounded-md bg-gradient-to-r from-pink-500 to-purple-500 hover:from-pink-600 hover:to-purple-600 transition-all duration-300 shadow-md"
      >
        Envoyer
      </button>
    </form>
  </div>
</template>



<script>
import ContactMessagesModel from "../models/contactMessagesModel";
import Swal  from 'sweetalert2';

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
        await Swal.fire({
        icon: 'success',
        title: 'Message Envoyé',
        text: 'Votre message a bien été pris en compte.',
        confirmButtonText: 'OK'
      });
      this.$router.push('/');
      } catch (error) {
        console.error(error);
      }
    },
  },
};
</script>
