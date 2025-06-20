
# Mug & Co Customizer

Une application web développée avec Vue.js permettant aux utilisateurs de personnaliser des mugs et t-shirts via un canevas interactif (Fabric.js) et un rendu 3D en temps réel (Three.js).

## Fonctionnalités

- Interface intuitive pour personnaliser visuellement des produits (mugs, t-shirts)
- Intégration de **Fabric.js** pour la gestion du canevas
- Rendu 3D dynamique avec **Three.js**
- Importation d’images personnelles
- Affichage des zones de sécurité et des marges sur le canevas
- Système de panier et de commande via une **API REST** PHP

## Tech Stack

- **Frontend** : Vue.js, TailwindCSS, Fabric.js, Three.js, Vite
- **Backend** : PHP (architecture MVC custom avec routing et middlewares)
- **Base de données** : MySQL

## Installation

Suivez ces étapes pour configurer et lancer le projet localement :

### 1. Cloner le dépôt

```bash
git clone https://github.com/CnnMts/site-perso.git
cd site-perso
```

### 2. Installer les dépendances

#### Frontend (Vue.js)

```bash
cd Front
npm install
# ou
yarn install
```

#### Backend (PHP)

```bash
cd ../Back
composer install
```

### 3. Configurer l’environnement

- Créez un fichier `.env` à partir de `.env.example` dans le dossier `Back/`
- Renseignez vos identifiants de base de données MySQL
- Importez la base de données via un fichier `database.sql` si fourni

### 4. Lancer l'application

#### Frontend (Vite - Vue.js)

```bash
cd Front/app/src
npm run dev
# ou
yarn dev
```

Accès : [http://localhost:5173](http://localhost:5173)

#### Backend (PHP)

Utilisez un serveur web local (Apache/Nginx) pointant vers `Back/public`, ou bien le serveur de développement intégré :

```bash
cd Back/public
php -S localhost:9999
```

Accès API : [http://localhost:9999](http://localhost:9999)
 Assurez-vous que la réécriture d’URL (URL rewriting) est activée si vous utilisez Apache pour faire fonctionner le routing proprement.


1. Forkez le projet
2. Créez une branche (`git checkout -b feature/ma-fonction`)
3. Commitez vos changements (`git commit -m "Ajout de ma fonction"`)
4. Pushez la branche (`git push origin feature/ma-fonction`)
5. Ouvrez une **Pull Request**

Merci de respecter les conventions du projet et d’inclure des tests pertinents si besoin.
