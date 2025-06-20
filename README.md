# Mug & Co Customizer

Une application web Vue.js permettant aux utilisateurs de personnaliser des mugs et t-shirts via un canevas interactif avec Fabric.js et d’afficher un rendu 3D en temps réel avec Three.js.

## Aperçu

![Aperçu de Mug & Co Customizer](assets/preview.png)

## Fonctionnalités principales

*   Interface intuitive pour personnaliser visuellement des produits (mugs, t-shirts)
*   Intégration de Fabric.js pour la gestion du canevas
*   Rendu 3D dynamique avec Three.js
*   Import d’images personnelles
*   Zones de sécurité et marges affichées sur le canevas
*   Système de panier et de commandes (back-end en PHP avec API REST)

## Tech Stack

*   **Frontend**: Vue.js, TailwindCSS, Fabric.js, Three.js
*   **Backend**: PHP MVC custom avec routes et middlewares
*   **Base de données**: MySQL

## Installation

Suivez ces étapes pour configurer et lancer le projet localement :

1.  **Cloner le dépôt :**
    ```bash
    git clone https://github.com/votre-utilisateur/mug-co-customizer.git
    cd mug-co-customizer
    ```

2.  **Installer les dépendances frontend :**
    ```bash
    # Naviguer vers le dossier du frontend (ajustez si nécessaire)
    cd frontend
    # Installer les dépendances avec npm ou yarn
    npm install
    # ou
    yarn install
    ```

3.  **Installer les dépendances backend :**
    ```bash
    # Naviguer vers le dossier du backend (ajustez si nécessaire)
    cd ../backend
    # Installer les dépendances avec Composer
    composer install
    ```

4.  **Configuration de l'environnement :**
    *   Créez un fichier `.env` à partir de `.env.example` dans le dossier backend et configurez vos identifiants de base de données MySQL.
    *   Importez la structure de la base de données (par exemple, à partir d'un fichier `database.sql` si fourni).

5.  **Lancer l'application :**
    *   **Frontend (Vue.js) :**
        ```bash
        # Depuis le dossier frontend
        npm run serve
        # ou
        yarn serve
        ```
    *   **Backend (PHP) :** Configurez votre serveur web local (Apache, Nginx) pour pointer vers le dossier `public` du backend. Assurez-vous que les réécritures d'URL sont activées pour que le routage PHP fonctionne correctement. Alternativement, vous pouvez utiliser le serveur de développement intégré de PHP (moins recommandé pour un développement complet) :
        ```bash
        # Depuis le dossier backend/public
        php -S localhost:8000
        ```
    L'application frontend devrait être accessible sur `http://localhost:8080` (ou un port similaire) et l'API backend sur `http://localhost:8000` (ou le port que vous avez configuré).

## Auteur

*   **Matis Conan**

## Contribuer

Les contributions sont les bienvenues ! Si vous souhaitez contribuer à ce projet, veuillez suivre ces étapes :

1.  Forkez le projet.
2.  Créez une nouvelle branche pour votre fonctionnalité (`git checkout -b feature/nouvelle-fonctionnalite`).
3.  Commitez vos changements (`git commit -m 'Ajout de nouvelle-fonctionnalite'`).
4.  Pushez vers la branche (`git push origin feature/nouvelle-fonctionnalite`).
5.  Ouvrez une Pull Request.

Veuillez vous assurer que votre code respecte les conventions de style du projet et inclut des tests pertinents si applicable.
