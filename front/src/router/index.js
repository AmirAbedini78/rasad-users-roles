import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import UsersRolesView from '../views/UsersRolesView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/users-roles',
      name: 'users-roles',
      component: UsersRolesView,
    },
  ],
})

export default router
