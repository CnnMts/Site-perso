export default {
    async getRoleById(id){
        try {
          const response = await fetch('http://127.0.0.1:9999/role/:id');
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