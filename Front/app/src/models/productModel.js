export default {

    async getProduct() {
      try {
        const response = await fetch('http://127.0.0.1:9999/product');
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

    async getProductById(id) {
      try {
        const response = await fetch(`http://127.0.0.1:9999/product/${id}`);
        if (!response.ok) {
          throw new Error('Réponse du serveur non valide');
        }
        const data = await response.json(); 
        return data;
      } catch (error) {
        console.error("Erreur lors de la récupération du produit par ID", error);
        return {};
      }
    }
    




}