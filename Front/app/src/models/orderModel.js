export default {
  async addOrder(orderData) {
    try {
      const response = await fetch('http://127.0.0.1:9999/orders', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderData),
      });
      return await response.json();
    } catch (error) {
      console.error('Erreur addOrder:', error);
    }
  },

};
