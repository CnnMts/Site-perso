import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import TodoApp from '../views/TodoApp.vue'; 
import UserView from '../views/ViewModelUser.vue'; 
import RegisterView from '../views/RegisterPage.vue';
import LoginView from '../views/LoginPage.vue'; 
import ProductDetail from '../views/ProductDetail.vue'; 
import Customizer from '../views/Customizer.vue';
import Render3d from '../views/Render3d.vue';

const routes = [
  {
    path: '/app',
    name: 'TodoApp',
    component: TodoApp,
  },
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
    component:  Render3d
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

export default router;
