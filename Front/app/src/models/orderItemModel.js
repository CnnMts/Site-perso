const orderItemModel = {
  async addOrderItem(orderItemData) {
    try {
      const response = await fetch('http://127.0.0.1:9999/AddorderItems', {
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
