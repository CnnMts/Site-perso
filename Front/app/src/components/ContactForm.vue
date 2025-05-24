<template>
  <div class="form-container">
    <form @submit.prevent="submitForm">
      <div v-for="(field, index) in fields" :key="index">
        <label :for="field.id">{{ field.label }}</label>
        <input :type="field.type" :id="field.id" v-model="field.value" />
      </div>
      <button type="submit">Envoyer</button>
    </form>
  </div>
</template>

<script>
  import ContactMessagesModel from "../models/contactMessagesModel"


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
        this.fields.forEach(field => {
        payload[field.id] = field.value;
        });

        try {
          const response = await ContactMessagesModel.addContactMessage(payload);
          this.fields.forEach(field => (field.value = ""));
        } catch (error) {
      }
    }
  }
};
</script>

<style>
.form-container {
  max-width: 400px;
  margin: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
label {
  display: block;
  font-weight: bold;
}
input {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
}
button {
  padding: 10px;
  margin-top: 10px;
  cursor: pointer;
}
</style>
