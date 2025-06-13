const BASE_URL = `http://${window.location.hostname}:9999`;
let token = null;

const orderItemModel = {
  async addOrderItem(orderItemData) {
    try {
      const response = await fetch(`${BASE_URL}/AddorderItems`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderItemData),
      });
      
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Erreur serveur: ${errorData.error || response.statusText}`);
      }
      
      return await response.json();
    } catch (error) {
      console.error('Erreur addOrderItem:', error);
      console.error('Données envoyées:', orderItemData);
      throw error; 
    }
  },

   setToken(newToken) {
      token = newToken;
   },

  async getOrderByOrderId(orderId) {
    try {
     const response = await fetch(`${BASE_URL}/orderItems/byOrder/${orderId}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      }
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Erreur serveur: ${errorData.error || response.statusText}`);
      }

      return await response.json();
    } catch (error) {
      console.error('Erreur getOrderByOrderId:', error);
      throw error;
    }
  }
};

export default orderItemModel;
