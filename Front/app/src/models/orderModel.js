const BASE_URL = `http://${window.location.hostname}:9999`


export default {
  async addOrder(orderData) {
    try {
      const response = await fetch(`${BASE_URL}/orders`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderData),
      });
      return await response.json();
    } catch (error) {
      console.error('Erreur addOrder:', error);
    }
  },

      async getOrders() {
    try {
      const response = await fetch(`${BASE_URL}/orders`);
      if (!response.ok) {
        throw new Error('Réponse du serveur non valide');
      }
      const data = await response.json(); 
      return data;
    } catch (error) {
      console.error("Erreur lors de la récupération des utilisateurs", error);
      return []; 
    }
  },

    async getOrdersById(id) {
    try {
      const response = await fetch(`${BASE_URL}/orders/${id}`);
      if (!response.ok) {
        throw new Error('Réponse du serveur non valide');
      }
      const data = await response.json(); 
      return data;
    } catch (error) {
      console.error("Erreur lors de la récupération des utilisateurs", error);
      return []; 
    }
  },

  async getCartClient(id) {
    try {
      const response = await fetch(`${BASE_URL}/order/cart/${id}`);
      if (response.status === 404) {
        return null; 
      }
      if (!response.ok) {
        throw new Error('Réponse du serveur non valide');
      }
      const data = await response.json();
      return data;
      } catch (error) {
        console.error("Erreur lors de la récupération des utilisateurs", error);
        return null; 
    }
  },

  async getOrdersByUserId(userId) {
  try {
    const response = await fetch(`${BASE_URL}/ordersUserId/${userId}`);
    if (!response.ok) throw new Error("Erreur lors de la récupération des commandes");
    return await response.json();
  } catch (error) {
    console.error("Erreur fetch commandes :", error);
    throw error;
  }
},
async updateOrderStatus(orderId, statusId) {
  try {
    const response = await fetch(`${BASE_URL}/ordersUpStatus/${orderId}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ status_id: statusId })
    });

    if (!response.ok) throw new Error('Erreur mise à jour statut');

    return await response.json();
  } catch (error) {
    console.error('Erreur updateOrderStatus:', error);
    throw error;
  }
}


};
