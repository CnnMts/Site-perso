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

      async getOrders() {
    try {
      const response = await fetch('http://127.0.0.1:9999/orders');
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
      const response = await fetch(`http://127.0.0.1:9999/order/cart/${id}`);
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
    const response = await fetch(`http://127.0.0.1:9999/ordersUserId/${userId}`);
    if (!response.ok) throw new Error("Erreur lors de la récupération des commandes");
    return await response.json();
  } catch (error) {
    console.error("Erreur fetch commandes :", error);
    throw error;
  }
}

};
