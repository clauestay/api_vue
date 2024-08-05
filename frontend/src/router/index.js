import { createRouter, createWebHistory } from 'vue-router'
import Conectar from '@/views/Conectar.vue'
import { useAuthStore } from '../stores/auth';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'conectar-app',
      component: Conectar
    },
    {
      path: '/error',
      name: 'Error',
      component: () => import('../views/Error.vue')
    },
    {
      path: '/inicio',
      name: 'Inicio',
      component: () => import('../views/EntregaTurno/Inicio.vue')
    },
    {
      path: '/crear',
      name: 'Crear',
      component: () => import('../views/EntregaTurno/Crear.vue')
    },
    {
      path: '/editar/:id',
      name: 'Editar',
      component: () => import('../views/EntregaTurno/Crear.vue')
    },
    {
      path: '/misTurnos',
      name: 'MisTurnos',
      component: () => import('../views/EntregaTurno/Crear.vue')
    },
    {
      path: '/listadoTurnos',
      name: 'Listadoturnos',
      component: () => import('../views/EntregaTurno/Crear.vue')
    }
  ]
})

router.beforeEach( async (to) => {
  const publicPages = ['/', '/error', '/inicio', '/crear', '/misTurnos', '/listadoTurnos'];
  const authRequired = !publicPages.includes(to.path);
  const auth = useAuthStore();
  if (authRequired && !auth.user) {
    auth.redirectTo = to.fullPath;
    return '/error';
  }
});

export default router
