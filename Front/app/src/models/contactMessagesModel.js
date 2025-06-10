
const BASE_URL = `http://${window.location.hostname}:9999`

const contactMessagesModel = {
  async addContactMessage(contactMessagesData) {
    try {
      const response = await fetch(`${BASE_URL}/contact`, {
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
