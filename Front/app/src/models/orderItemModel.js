const BASE_URL = `http://${window.location.hostname}:9999`


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
      throw error; 
    }
  }
};

export default orderItemModel;
