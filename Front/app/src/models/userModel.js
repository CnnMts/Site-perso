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

  async logUser(userData){
      try {
        const response = await fetch('http://127.0.0.1:9999/auth/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          credentials: 'include',
          body: JSON.stringify(userData),
        });

        if (!response.ok) {
          throw new Error('Erreur lors de la connexion.');
        }

        const data = await response.json();
        return data; 
      } catch (error) {
        console.error(error.message);
        this.errorMessage = error.message;
        return null;
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
        credentials: "include",
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

  async getUserById(id) {
  try {
    const response = await fetch(`http://127.0.0.1:9999/user/${id}`);
    if (!response.ok) {
      throw new Error('Réponse du serveur non valide');
    }
    const data = await response.json();
    return data;
  } catch (error) {
    console.error("Erreur lors de la récupération de l'utilisateur :", error);
    throw error;
  }
},

async deleteUserById(id) {
  try {
    const response = await fetch(`http://127.0.0.1:9999/user/${id}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error('Réponse du serveur non valide');
    }

    return await response.json();
  } catch (error) {
    console.error("Erreur lors de la suppression de l'utilisateur", error);
    return null;
  }
}


};
