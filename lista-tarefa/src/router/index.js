import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: Login,
      meta: { publica: true }
    },
    {
      path: '/cadastro',
      name: 'cadastro',
      component: () => import("@/views/Cadastro.vue"),
      meta: { publica: true }
    },
    {
      path: '/tarefa',
      name: 'tarefa',
      component: () => import("@/views/Tarefas.vue")
    },
    {
      path: '/categoria',
      name: 'categoria',
      component: () => import("@/views/Categorias.vue")
    }
  ],
})

//Sem token só acessa login/cadastro; logado não volta para eles
router.beforeEach((to) => {
  const logado = !!localStorage.getItem('token');
  if(!to.meta.publica && !logado){
    return { name: 'login' };
  }
  if(to.meta.publica && logado){
    return { name: 'tarefa' };
  }
});

export default router
