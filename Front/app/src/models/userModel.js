const BASE_URL = `http://${window.location.hostname}:9999`
import Swal  from 'sweetalert2';
export default {
  async getUsers() {
    try {
      const response = await fetch(`${BASE_URL}/user`);
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

  async logUser(userData) {
    try {
      const response = await fetch(`${BASE_URL}/auth/login`, {
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
      const response = await fetch(`${BASE_URL}/auth/register`, {
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
      const response = await fetch(`${BASE_URL}/user/${id}`);
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
      const response = await fetch(`${BASE_URL}/user/${id}`, {
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
  },

  async updateUserById(id, payload) {
    try {
      const response = await fetch(`${BASE_URL}/user/${id}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload), 
      });

      if (!response.ok) {
        throw new Error('Réponse du serveur non valide');
      }

      return await response.json();
    } catch (error) {
      console.error("Erreur lors de la mise à jour de l'utilisateur :", error);
      return null;
    }
  },

  async updateUserDelivery(id, payload) {
    try {
      const response = await fetch(`${BASE_URL}/userDelivery/${id}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload), 
      });

      if (!response.ok) {
        throw new Error('Réponse du serveur non valide');
      }

      return await response.json();
    } catch (error) {
      console.error("Erreur lors de la mise à jour de l'utilisateur :", error);
      return null;
    }
  },

  async logOut(){
    try {
      const response = await fetch(`${BASE_URL}/auth/logout`, {
        method: "POST",
        credentials: "include"
     });
      if (!response.ok) {
        throw new Error(`Erreur serveur: ${response.status}`);
      }
      await Swal.fire({
      icon: 'success',
      title: 'Déconnexion',
      confirmButtonText: 'OK'
    });
      window.location.href = "/login";
    } catch (error) {
      console.error("Erreur de déconnexion :", error);
      alert("Erreur lors de la déconnexion, veuillez réessayer.");
    }
  }

  };