import { createRouter, createWebHistory } from 'vue-router';
import { jwtDecode } from 'jwt-decode';

import Home from '../views/Home.vue';
import UserView from '../views/ViewModelUser.vue';
import RegisterView from '../views/RegisterPage.vue';
import LoginView from '../views/LoginPage.vue';
import ProductDetail from '../views/ProductDetail.vue';
import Customizer from '../views/Customizer.vue';
import Render3d from '../views/Render3d.vue';
import Contact from '../views/Contact.vue';
import Dash from '../views/DashBoard.vue';
import DashBoardUsers from '../views/Users.vue';

const admin = 1;

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/user', name: 'UserView', component: UserView },
  { path: '/register', name: 'Register', component: RegisterView },
  { path: '/login', name: 'Login', component: LoginView },
  { path: '/product/:id', name: 'product-detail', component: ProductDetail },
  { path: '/customizer/:id', name: 'Customizer', component: Customizer },
  { path: '/render', name: 'render', component: Render3d },
  { path: '/contact', name: 'contact', component: Contact },
  { path: '/dash', name: 'dash', component: Dash, meta: { requiresAdmin: true } },
  { path: '/dash/users', name: 'dashUsers', component: DashBoardUsers, meta: { requiresAdmin: true } },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
  return null;
}
router.beforeEach((to, from, next) => {
  const token = getCookie('pmaUser');

  if (to.meta.requiresAdmin) {
    if (!token) {
      alert("Accès refusé. Veuillez vous connecter.");
      return next('/login');
    }

    try {
      const decoded = jwtDecode(token);
      const userRole = decoded.role;

      if (userRole === 1 || userRole === 'admin') {
        return next();
      } else {
        alert("Accès réservé aux administrateurs.");
        return next('/');
      }
    } catch (err) {
      console.error("Token invalide :", err);
      document.cookie = "pmaUser=; Max-Age=0; path=/;";
      return next('/login');
    }
  }

  return next();
});


export default router;
