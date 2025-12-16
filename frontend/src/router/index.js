import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'
import { usePostHog } from '@/composables/usePostHog'
import Home from '../pages/Home.vue'
import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import ForgotPassword from '../pages/ForgotPassword.vue'
import Dashboard from '../pages/Dashboard.vue'
import MarketingPlans from '../pages/MarketingPlans.vue'
import ImageGenerator from '../pages/ImageGenerator.vue'
import Settings from '../pages/Settings.vue'
import Hashtags from '../pages/Hashtags.vue'
import Community from '../pages/Community.vue'
import Learn from '../pages/Learn.vue'
import MultiStepQuestionnaire from '../components/MultiStepQuestionnaire.vue'
import PlansList from '../components/PlansList.vue'

const { posthog } = usePostHog()

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
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: ForgotPassword
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
        path: 'community',
        name: 'Community',
        component: Community
      },
      {
        path: 'settings',
        name: 'Settings',
        component: Settings
      },
      {
        path: 'learn',
        name: 'Learn',
        component: Learn
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
    component: MultiStepQuestionnaire,
    meta: { requiresAuth: true }
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
      
      if (!response.data.success) {
        posthog?.reset()
        next('/login')
        return
      }
      
      const user = response.data.user || null
      if (user) {
        posthog?.identify(user.id.toString(), {
          email: user.email,
          name: user.name
        })
      }

      const isQuestionnaireRoute = to.name === 'Questionnaire' || to.path === '/questionnaire'
      const hasCompletedQuestionnaire = Boolean(user?.has_completed_questionnaire)

      if (!hasCompletedQuestionnaire && !isQuestionnaireRoute) {
        next('/questionnaire')
        return
      }
    } catch (error) {
      console.log('Not authorized, redirecting to login')
      posthog?.reset()
      next('/login')
      return
    }
  }
  
  next()
})

router.afterEach((to, from) => {
  if (posthog) {
    posthog.capture('$pageview', {
      $current_url: window.location.href,
      path: to.path,
      route_name: to.name,
      requires_auth: to.matched.some(record => record.meta.requiresAuth)
    })
  }
})

export default router
