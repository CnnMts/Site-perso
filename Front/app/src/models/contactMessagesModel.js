const contactMessagesModel = {
  async addContactMessage(contactMessagesData) {
    try {
      const response = await fetch('http://127.0.0.1:9999/contact', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(contactMessagesData),
      });
      return await response.json();
    } catch (error) {
      console.error('Erreur addOrderItem:', error);
    }
  }
};

export default contactMessagesModel;
