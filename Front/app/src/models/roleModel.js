const BASE_URL = `http://${window.location.hostname}:9999`

export default {
    async getRoleById(id){
        try {
          const response = await fetch(`${BASE_URL}/role/${id}`);
          if (!response.ok) {
            throw new Error('Réponse du serveur non valide');
          }
        const data = await response.json();
        return data;
        } catch (error) {
          console.error("Erreur lors de la récupération des utilisateurs", error);
        return [];
        }
    }
}