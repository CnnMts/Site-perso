<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4 text-purple-700">Messages du formulaire de contact</h1>

    <button
      @click="fetchMessages"
      class="bg-gradient-to-r from-violet-600 via-purple-700 to-pink-500 text-white px-4 py-2 rounded mb-4 hover:from-pink-500 hover:to-violet-600 transition-colors"
    >
      Rafraîchir les messages
    </button>

    <ul v-if="messages && messages.length" class="space-y-4">
      <li
        v-for="message in messages"
        :key="message.id"
        class="border border-violet-300 p-4 rounded bg-white shadow-sm hover:shadow-md transition-shadow"
      >
        <p><strong>Nom :</strong> {{ message.name }}</p>
        <p><strong>Email :</strong> {{ message.email }}</p>
        <p><strong>Sujet :</strong> {{ message.subject }}</p>
        <p><strong>Message :</strong> {{ message.message }}</p>
        <p class="text-sm text-gray-500 mt-2">Envoyé le : {{ formatDate(message.created_at) }}</p>
      </li>
    </ul>

    <p v-else class="text-gray-600 italic">Aucun message à afficher.</p>
  </div>
</template>

<script>
import contactMessagesModel from '@/models/contactMessagesModel'

export default {
  data() {
    return {
      messages: null,
    }
  },

  methods: {
    getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
      return null;
    },

    async fetchMessages() {
      const token = this.getCookie('pmaUser');
      const msgs = await contactMessagesModel.getMessages(token);
      this.messages = msgs || [];
    },

    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    }
  },

  mounted() {
    this.fetchMessages();
  }
}
</script>
