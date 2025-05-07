const orderItemModel = {
  async addOrderItem(orderItemData) {
    try {
      const response = await fetch('http://127.0.0.1:9999/orderItems', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderItemData),
      });
      return await response.json();
    } catch (error) {
      console.error('Erreur addOrderItem:', error);
    }
  }
};

export default orderItemModel;
