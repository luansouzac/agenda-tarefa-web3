import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: Login,
    },
    {
      path: '/tarefa',
      name: 'tarefa',
      component: import("@/views/Tarefas.vue")
    },
    {
      path: '/categoria',
      name: 'categoria',
      component: import("@/views/Categorias.vue")
    }
  ],
})

export default router
