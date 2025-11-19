import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'
import Home from '../pages/Home.vue'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import Dashboard from '../pages/Dashboard.vue'
import MarketingPlans from '../pages/MarketingPlans.vue'
import ImageGenerator from '../pages/ImageGenerator.vue'
import Settings from '../pages/Settings.vue'
import Hashtags from '../pages/Hashtags.vue'
import MultiStepQuestionnaire from '../components/MultiStepQuestionnaire.vue'
import PlansList from '../components/PlansList.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
    redirect: '/dashboard/marketing-plans',
    children: [
      {
        path: 'marketing-plans',
        name: 'MarketingPlans',
        component: MarketingPlans
      },
      {
        path: 'image-generator',
        name: 'ImageGenerator',
        component: ImageGenerator
      },
      {
        path: 'hashtags',
        name: 'Hashtags',
        component: Hashtags
      },
      {
        path: 'settings',
        name: 'Settings',
        component: Settings
      }
    ]
  },
  {
    path: '/marketing-plans',
    name: 'MarketingPlansRedirect',
    redirect: '/dashboard/marketing-plans'
  },
  {
    path: '/image-generator',
    name: 'ImageGeneratorRedirect',
    redirect: '/dashboard/image-generator'
  },
  {
    path: '/settings',
    name: 'SettingsRedirect',
    redirect: '/dashboard/settings'
  },
  {
    path: '/questionnaire',
    name: 'Questionnaire',
    component: MultiStepQuestionnaire
  },
  {
    path: '/plans',
    name: 'PlansList',
    component: PlansList,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  
  if (requiresAuth) {
    try {
      const response = await axios.get('/api/user')
      
      if (!response.data) {
        next('/login')
        return
      }
    } catch (error) {
      console.log('Not authorized, redirecting to login')
      next('/login')
      return
    }
  }
  
  next()
})

export default router
