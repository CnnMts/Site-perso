export default {
  async getUsers() {
    try {
      const response = await fetch('http://127.0.0.1:9999/user');
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

  async createUser(userData) {
    try {
      const response = await fetch('http://127.0.0.1:9999/auth/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData),
      });

      if (!response.ok) {
        throw new Error('Réponse du serveur non valide');
      }

      const data = await response.json();
      return data;
    } catch (error) {
      console.error("Erreur lors de la création de l'utilisateur", error);
      return null;
    }
  },

  async getNameByEmail(email) {
    try {
      const response = await fetch(`http://127.0.0.1:9999/user/search/${email}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error('Erreur lors de la récupération du nom de l’utilisateur.');
      }

      const data = await response.json();
      return data.name;
    } catch (error) {
      console.error(error.message);
      return null;
    }
  },

  async getUserByName(name) {
    try {
      const response = await fetch(`http://127.0.0.1:9999/caca/${name}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error("Erreur lors de la récupération de l'utilisateur par nom.");
      }

      const data = await response.json();
      return data; 
    } catch (error) {
      console.error(error.message);
      return null;
    }
  }
};
