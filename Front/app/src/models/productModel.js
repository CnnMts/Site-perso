const BASE_URL = `http://${window.location.hostname}:9999`

export default {

    async getProduct() {
      try {
        const response = await fetch(`${BASE_URL}/product`);
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
        const response = await fetch(`${BASE_URL}/product/${id}`);
        if (!response.ok) {
          throw new Error('Réponse du serveur non valide');
        }
        const data = await response.json(); 
        return data;
      } catch (error) {
        console.error("Erreur lors de la récupération du produit par ID", error);
        return {};
      }
    },
    async addProduct(payload) {
      const res = await fetch(`${BASE_URL}/product`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })

      if (!res.ok) {
        const message = await res.text()
        throw new Error(`Erreur API : ${res.status} - ${message}`)
      }

     return await res.json()
    },
    async  updateProduct(id, payload) {
      const res = await fetch(`${BASE_URL}/product/${id}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
       body: JSON.stringify(payload)
      })
      if (!res.ok) {
        const message = await res.text()
        throw new Error(`Erreur API : ${res.status} - ${message}`)
      }
        return await res.json()
    },
    async deleteProduct(id) {
      const res = await fetch(`http://127.0.0.1:9999/product/${id}`, {
        method: 'DELETE'
      })
      if (!res.ok) {
        const message = await res.text()
        throw new Error(`Erreur API : ${res.status} - ${message}`)
      }
    return true
    }

}