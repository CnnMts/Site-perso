
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
  },
   async  getMessages(token) {
    try {
      const headers = { 'Content-Type': 'application/json' };
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }
      const response = await fetch(`${BASE_URL}/contact`, {
        method: 'GET',
        credentials: 'include',
        headers,
      });
      if (!response.ok) throw new Error(`Erreur HTTP ${response.status}`);
      return await response.json();
    } catch (error) {
      console.error('Erreur getMessages:', error);
      return null;
    }
  }
};

export default contactMessagesModel;
