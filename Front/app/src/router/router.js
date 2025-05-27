import { createRouter, createWebHistory } from 'vue-router';
import { jwtDecode } from 'jwt-decode';


import Home from '../views/Home.vue';
import TodoApp from '../views/TodoApp.vue'; 
import UserView from '../views/ViewModelUser.vue'; 
import RegisterView from '../views/RegisterPage.vue';
import LoginView from '../views/LoginPage.vue'; 
import ProductDetail from '../views/ProductDetail.vue'; 
import Customizer from '../views/Customizer.vue';
import Render3d from '../views/Render3d.vue';
import Contact from '../views/Contact.vue';
import Dash from '../views/DashBoard.vue';

const admin = 1;

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/user',
    name: 'UserView',
    component: UserView,
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterView,
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
  },
  {
    path: '/product/:id',
    name: 'product-detail',
    component: ProductDetail
  },
  {
    path: '/customizer/:id',
    name: 'Customizer',
    component: Customizer
  },
  {
    path: '/render',
    name: 'render',
    component: Render3d
  },
  {
    path: '/contact',
    name: 'contact',
    component: Contact
  },
  {
    path: '/dash',
    name: 'dash',
    component: Dash
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');

  if (to.path === '/dash') {
    if (!token) {
      alert("Accès refusé. Veuillez vous connecter.");
      return next('/login');
    }

    try {
      const decoded = jwtDecode(token);
      if (decoded.role === admin) {
        return next(); 
      } else {
        alert("Accès réservé aux administrateurs.");
        return next('/');
      }
    } catch (err) {
      console.error("Token invalide :", err);
      localStorage.removeItem('token');
      return next('/login');
    }
  }

  return next(); 
});

export default router;
