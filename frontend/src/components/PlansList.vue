<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#3F4369]">{{ texts.title }}</h1>
        <p class="text-[#3F4369] opacity-70 mt-2">{{ texts.subtitle }}</p>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
        <div class="spinner mb-4"></div>
        <p class="text-gray-600">{{ texts.loading }}</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="alert alert-error max-w-2xl mx-auto">
        <div class="flex items-center">
          <span class="text-xl mr-3">⚠️</span>
          <div>
            <div class="font-medium">Loading Error</div>
            <div>{{ error }}</div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="plans.length === 0" class="text-center py-20">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg border border-[#DCDCDC] p-8">
          <div class="w-20 h-20 bg-gradient-to-br from-[#F34767] to-[#1C8E9E] rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-[#3F4369] mb-4">{{ texts.emptyTitle }}</h3>
          <p class="text-[#3F4369] opacity-70 mb-8">
            {{ texts.emptyText }}
          </p>
          <router-link 
            to="/questionnaire" 
            class="inline-flex items-center px-8 py-4 bg-[#F34767] text-white rounded-2xl hover:bg-[#1C8E9E] transition-colors shadow-lg hover:shadow-xl font-medium"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ texts.completeQuestionnaire }}
          </router-link>
        </div>
      </div>

      <!-- Plans Display -->
      <div v-else class="space-y-6">
        <div 
          v-for="plan in plans" 
          :key="plan.id" 
          class="bg-white rounded-2xl shadow-lg border border-[#DCDCDC] overflow-hidden"
        >
          <!-- Plan Header -->
          <div class="bg-gradient-to-r from-[#FFEBD0] to-white p-6 border-b border-[#DCDCDC]">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-2xl font-bold text-[#3F4369] mb-2">
                  {{ plan.title }}
                </h3>
                <div class="flex items-center text-sm text-[#3F4369] opacity-70">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  {{ texts.created }} {{ formatDate(plan.created_at) }}
                </div>
              </div>
              <div class="text-right">
                <div class="text-3xl font-bold text-[#F34767]">
                  {{ Math.round(((plan.completed_tasks || 0) / (plan.total_tasks || 1)) * 100) }}%
                </div>
                <p class="text-sm text-[#3F4369] opacity-70">{{ texts.completed }}</p>
              </div>
            </div>
          </div>
            
          <!-- Plan Stats -->
          <div class="p-6">
            <!-- Progress Bar -->
            <div class="mb-6">
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-[#3F4369]">{{ texts.taskProgress }}</span>
                <span class="text-sm text-[#3F4369] opacity-70">
                  {{ plan.completed_tasks || 0 }} / {{ plan.total_tasks || 0 }} {{ texts.tasks }}
                </span>
              </div>
              <div class="w-full bg-[#DCDCDC] rounded-full h-3">
                <div 
                  class="bg-[#F34767] h-3 rounded-full transition-all duration-300"
                  :style="{ width: `${Math.round(((plan.completed_tasks || 0) / (plan.total_tasks || 1)) * 100)}%` }"
                ></div>
              </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-4">
              <div class="text-center bg-white border border-[#DCDCDC] rounded-xl p-4">
                <div class="text-2xl font-bold text-[#1C8E9E]">4</div>
                <div class="text-xs text-[#3F4369] opacity-70">{{ texts.weeks }}</div>
              </div>
              <div class="text-center bg-white border border-[#DCDCDC] rounded-xl p-4">
                <div class="text-2xl font-bold text-[#F34767]">{{ plan.total_tasks || 0 }}</div>
                <div class="text-xs text-[#3F4369] opacity-70">{{ texts.totalTasks }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'

export default {
  name: 'PlansList',
  setup() {
    const languageStore = useLanguageStore()
    const plans = ref([])
    const isLoading = ref(true)
    const error = ref('')

    const texts = computed(() => {
      if (languageStore.language === 'de') {
        return {
          title: 'Dein Marketingplan',
          subtitle: 'Sieh dir deinen personalisierten Marketingplan aus deinem Fragebogen an',
          loading: 'Pläne werden geladen...',
          emptyTitle: 'Du hast noch keinen Plan',
          emptyText: 'Fülle den Fragebogen aus, um automatisch deinen personalisierten Marketingplan zu erhalten',
          completeQuestionnaire: 'Fragebogen ausfüllen',
          created: 'Erstellt:',
          completed: 'abgeschlossen',
          taskProgress: 'Aufgabenfortschritt',
          tasks: 'Aufgaben',
          weeks: 'Wochen',
          totalTasks: 'Gesamtaufgaben',
        }
      }

      return {
        title: 'Your Marketing Plan',
        subtitle: 'View your personalized marketing plan generated from your questionnaire',
        loading: 'Loading plans...',
        emptyTitle: 'No Plan Yet',
        emptyText: 'Complete the questionnaire to get your personalized marketing plan generated automatically',
        completeQuestionnaire: 'Complete Questionnaire',
        created: 'Created:',
        completed: 'completed',
        taskProgress: 'Task Progress',
        tasks: 'tasks',
        weeks: 'Weeks',
        totalTasks: 'Total Tasks',
      }
    })

    const fetchPlans = async () => {
      try {
        const response = await axios.get('/api/plans')
        
        if (response.data.success) {
          plans.value = response.data.plans
        } else {
          error.value = response.data.message || 'Error loading plans'
        }
      } catch (err) {
        error.value = err.response?.data?.message || 'Error loading plans'
      } finally {
        isLoading.value = false
      }
    }

    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    onMounted(() => {
      fetchPlans()
    })

    return {
      plans,
      isLoading,
      error,
      formatDate,
      texts,
    }
  }
}
</script>

<style scoped>
.card {
  @apply transition-all duration-300;
}

.card:hover {
  @apply transform -translate-y-1;
}

.btn-sm {
  @apply px-3 py-1.5 text-sm;
}
</style>

