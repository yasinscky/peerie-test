<template>
  <div class="max-w-7xl mx-auto">
    <div class="mb-8">
      <div>
        <h1 class="text-3xl font-bold text-[#3F4369]">{{ texts.title }}</h1>
        <p class="text-[#3F4369] opacity-70 mt-2">{{ texts.subtitle }}</p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#DCDCDC]">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-[#1C8E9E]">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-[#3F4369] opacity-70">Completed Tasks</p>
            <p class="text-2xl font-bold text-[#3F4369]">{{ stats.completedTasks }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#DCDCDC]">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-[#FFEBD0] border-2 border-[#F34767]">
            <svg class="w-6 h-6 text-[#3F4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-[#3F4369] opacity-70">In Progress</p>
            <p class="text-2xl font-bold text-[#3F4369]">{{ stats.inProgressTasks }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#DCDCDC]">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-gradient-to-br from-[#F34767] to-[#1C8E9E]">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-[#3F4369] opacity-70">Total Tasks</p>
            <p class="text-2xl font-bold text-[#3F4369]">{{ stats.totalTasks }}</p>
          </div>
        </div>
      </div>
    </div>


    <!-- Plan Overview -->
    <div v-if="plan" class="bg-white rounded-2xl shadow-lg border border-[#DCDCDC] mb-8">
      <div class="bg-gradient-to-r to-white p-6 border-b border-[#DCDCDC]">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-2xl font-bold text-[#3F4369] mb-2">{{ plan.title }}</h3>
            <div class="flex items-center space-x-4 text-sm text-[#3F4369] opacity-70">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ plan.country }}
              </span>
              <span class="flex items-center" v-if="plan.industries && plan.industries.length">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                {{ plan.industries.join(', ') }}
              </span>
            </div>
          </div>
          <div class="text-right">
            <div class="text-3xl font-bold text-[#F34767]">
              {{ getProgressPercentage(plan) }}%
            </div>
            <p class="text-sm text-[#3F4369] opacity-70">completed</p>
          </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="mt-4">
          <div class="w-full bg-[#DCDCDC] rounded-full h-3">
            <div 
              class="bg-[#F34767] h-3 rounded-full transition-all duration-300"
              :style="{ width: getProgressPercentage(plan) + '%' }"
            ></div>
          </div>
          <div class="flex justify-between text-sm text-[#3F4369] opacity-70 mt-2">
            <span>{{ plan.completed_tasks || 0 }} completed</span>
            <span>{{ plan.total_tasks || 0 }} total tasks</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Categorised Tasks -->
    <div v-if="plan && categorisedTasks.length > 0" class="space-y-6">
      <div 
        v-for="category in categorisedTasks" 
        :key="category.name"
        class="bg-white rounded-2xl shadow-lg border border-[#DCDCDC] overflow-hidden"
      >
        <div class="bg-gradient-to-r to-white p-6 border-b border-[#DCDCDC] flex items-center justify-between">
          <div>
            <h3 class="text-2xl font-bold text-[#3F4369]">{{ category.name }}</h3>
            <p class="text-[#3F4369] opacity-70">{{ category.tasks.length }} tasks • {{ category.totalHours }}h estimated</p>
          </div>
          <div class="text-right">
            <div class="text-2xl font-bold text-[#F34767]">
              {{ category.progress }}%
            </div>
            <p class="text-sm text-[#3F4369] opacity-70">completed</p>
          </div>
        </div>

        <div class="p-6">
          <div v-if="category.tasks.length === 0" class="text-center py-8">
            <div class="w-16 h-16 bg-[#FFEBD0] rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-[#3F4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"/>
              </svg>
            </div>
            <p class="text-[#3F4369] opacity-70">No tasks in this category</p>
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="task in category.tasks"
              :key="task.id"
              class="p-4 rounded-lg border transition-all cursor-pointer"
              :class="task.pivot.completed 
                ? 'bg-[#1C8E9E] bg-opacity-10 border-[#1C8E9E]' 
                : 'bg-white border-[#DCDCDC] hover:border-[#F34767] hover:bg-[#FFEBD0]'"
              @click="toggleTask(task.pivot.id, !task.pivot.completed)"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-start space-x-3">
                    <div 
                      class="w-5 h-5 rounded-full border-2 flex items-center justify-center mt-0.5 transition-colors"
                      :class="task.pivot.completed 
                        ? 'bg-[#1C8E9E] border-[#1C8E9E]' 
                        : 'border-[#DCDCDC] hover:border-[#F34767]'"
                    >
                      <svg v-if="task.pivot.completed" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <h4 class="font-medium text-[#3F4369] mb-1" :class="task.pivot.completed ? 'line-through opacity-70' : ''">
                        {{ task.title }}
                      </h4>
                      <p class="text-sm text-[#3F4369] opacity-70" :class="task.pivot.completed ? 'line-through' : ''">
                        {{ getInstructionPreview(task) }}
                      </p>
                  
                      
                      <!-- Task Meta -->
                      <div class="flex flex-wrap gap-2 mt-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#FFEBD0] text-[#3F4369]">
                          ⏱️ {{ getTaskMinutes(task) }} min
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#1C8E9E]/10 text-[#1C8E9E]">
                          📅 {{ formatFrequency(task.frequency) }}
                        </span>
                      </div>

                      <div class="mt-4 flex flex-wrap items-center gap-3">
                        <button
                          class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg border border-[#F34767] text-[#F34767] hover:bg-[#F34767] hover:text-white transition-colors"
                          @click.stop="openTaskModal(task)"
                        >
                          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12h.01M12 12h.01M9 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4.255-.932L3 20l1.084-3.252C3.379 15.59 3 13.846 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                          </svg>
                          View instructions
                        </button>
                      </div>
                      
                      <!-- Task Notes -->
                      <div v-if="task.pivot.notes" class="bg-[#FFEBD0] border border-[#F34767] rounded-lg p-3 mt-3">
                        <div class="text-sm">
                          <span class="font-medium text-[#3F4369]">Notes:</span>
                          <span class="text-[#3F4369] ml-1">{{ task.pivot.notes }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Task Status Badge -->
                <div class="flex items-center space-x-2">
                  <span 
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                    :class="task.pivot.completed 
                      ? 'bg-[#1C8E9E] text-white' 
                      : 'bg-[#FFEBD0] text-[#3F4369]'"
                  >
                    {{ task.pivot.completed ? 'Completed' : 'Pending' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!isLoading && categorisedTasks.length === 0" class="text-center py-12">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
      </svg>
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ texts.emptyTitle }}</h3>
      <p class="text-gray-600 dark:text-gray-400 mb-6">{{ texts.emptyText }}</p>
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto">
        <p class="text-blue-800 text-sm">
          <strong>Information:</strong> Marketing plan is created automatically after completing the questionnaire during registration.
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-12">
      <svg class="w-16 h-16 text-blue-600 mx-auto mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-gray-600 dark:text-gray-400">Loading plans...</p>
    </div>
  </div>

  <!-- Task Instruction Modal -->
  <transition name="fade">
    <div
      v-if="instructionModalOpen && selectedTask"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-8"
    >
      <div class="relative max-w-3xl w-full bg-white rounded-2xl shadow-2xl border border-[#DCDCDC] max-h-[90vh] overflow-hidden">
        <button
          class="absolute top-4 right-4 flex items-center justify-center w-8 h-8 rounded-full bg-[#FFEBD0] text-[#F34767] hover:bg-[#F34767] hover:text-white transition-colors"
          @click="closeTaskModal"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>

        <div class="p-6 sm:p-8 overflow-y-auto max-h-[90vh]">
          <div class="flex flex-wrap gap-3 items-start mb-6">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#DCDCDC] text-[#3F4369]">
              ⏱️ {{ getTaskMinutes(selectedTask) }} min
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#1C8E9E]/10 text-[#1C8E9E]">
              📅 {{ formatFrequency(selectedTask.frequency) }}
            </span>
          </div>

          <h2 class="text-2xl font-semibold text-[#3F4369] mb-4">{{ selectedTask.title }}</h2>

          <div
            v-if="selectedTaskIsHtml"
            class="prose max-w-none text-[#3F4369] leading-relaxed"
          >
            <div v-html="instructionHtml" />
          </div>

          <div
            v-else
            class="space-y-4 text-[#3F4369] leading-relaxed"
          >
            <p
              v-for="(section, idx) in instructionContent.sections"
              :key="idx"
              class="whitespace-pre-line"
            >
              {{ section }}
            </p>
          </div>

          <div v-if="!selectedTaskIsHtml && instructionContent.prerequisites.length" class="mt-6">
            <h3 class="text-sm font-semibold text-[#3F4369] uppercase tracking-wide mb-2">Prerequisites</h3>
            <ul class="space-y-2 text-sm text-[#3F4369]">
              <li v-for="(pr, idx) in instructionContent.prerequisites" :key="idx" class="flex items-start gap-2">
                <span class="text-[#F34767] mt-1">•</span>
                <span>{{ pr }}</span>
              </li>
            </ul>
          </div>

          <div v-if="!selectedTaskIsHtml && instructionContent.resources.length" class="mt-6">
            <h3 class="text-sm font-semibold text-[#3F4369] uppercase tracking-wide mb-2">Resources</h3>
            <div class="flex flex-wrap gap-3">
              <a
                v-for="(resource, idx) in instructionContent.resources"
                :key="idx"
                :href="resource.href"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center px-3 py-2 rounded-lg border border-[#1C8E9E] text-sm text-[#1C8E9E] hover:bg-[#1C8E9E] hover:text-white transition-colors"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.121 14.121a3 3 0 010 4.243l-1.757 1.757a3 3 0 01-4.243-4.243l1.757-1.757m4.242-4.242l1.758-1.757a3 3 0 10-4.243-4.243l-1.757 1.757a3 3 0 004.242 4.243z"></path>
                </svg>
                {{ resource.label }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'

const router = useRouter()
const languageStore = useLanguageStore()

const plans = ref([])
const isLoading = ref(true)
const stats = ref({
  completedTasks: 0,
  inProgressTasks: 0,
  totalTasks: 0
})
const instructionModalOpen = ref(false)
const selectedTask = ref(null)

const plan = computed(() => plans.value[0] || null)

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      title: 'Dein Marketingplan',
      subtitle: 'Verfolge deine personalisierten Marketingaufgaben nach Kategorie',
      emptyTitle: 'Du hast noch keine Pläne',
      emptyText: 'Dein Marketingplan wird nach dem Ausfüllen des Fragebogens automatisch erstellt'
    }
  }

  return {
    title: 'Your Marketing Plan',
    subtitle: 'Track your personalized marketing tasks by category',
    emptyTitle: "You don't have any plans yet",
    emptyText: 'Your marketing plan will be created after completing the questionnaire'
  }
})

const fetchPlans = async () => {
  try {
    const response = await axios.get('/api/plans')
    console.log('API Response:', response.data)
    
    let apiPlans = []
    if (response.data.plans) {
      apiPlans = response.data.plans
    } else if (Array.isArray(response.data)) {
      apiPlans = response.data
    }
    
    console.log('API Plans:', apiPlans)
    
    if (apiPlans.length > 0 && (!apiPlans[0].weeks || apiPlans[0].weeks.length === 0)) {
      console.log('API plan without weeks, using as is')
      plans.value = apiPlans
      updateStats()
    } else if (apiPlans.length > 0) {
      console.log('Using API plan with weeks:', apiPlans[0].weeks)
      plans.value = apiPlans
      updateStats()
    } else {
      plans.value = []
      updateStats()
    }
  } catch (error) {
    console.error('Failed to load plans:', error)
    
    plans.value = []
    updateStats()
  } finally {
    isLoading.value = false
  }
}

const updateStats = () => {
  console.log('Updating stats for plans:', plans.value)
  stats.value = {
    completedTasks: plans.value.reduce((sum, plan) => sum + (plan.completed_tasks || 0), 0),
    inProgressTasks: plans.value.reduce((sum, plan) => sum + ((plan.total_tasks || 0) - (plan.completed_tasks || 0)), 0),
    totalTasks: plans.value.reduce((sum, plan) => sum + (plan.total_tasks || 0), 0)
  }
  console.log('Stats updated:', stats.value)
}

const getProgressPercentage = (plan) => {
  if (!plan.total_tasks || plan.total_tasks === 0) return 0
  return Math.round((plan.completed_tasks || 0) / plan.total_tasks * 100)
}

function getTaskMinutes(task) {
  if (!task) {
    return 0
  }

  if (task.duration_minutes !== undefined && task.duration_minutes !== null) {
    return task.duration_minutes
  }

  if (task.duration_hours !== undefined && task.duration_hours !== null) {
    return task.duration_hours * 60
  }

  return 60
}

function getTaskHours(task) {
  const minutes = getTaskMinutes(task)
  return Number((minutes / 60).toFixed(1))
}

const categorisedTasks = computed(() => {
  if (!plan.value) return []

  if (Array.isArray(plan.value.categories) && plan.value.categories.length > 0) {
    return plan.value.categories.map(category => {
      const tasks = category.tasks || []
      const totalMinutes = tasks.reduce((sum, task) => sum + getTaskMinutes(task), 0)
      const totalHours = Number((totalMinutes / 60).toFixed(1))
      const completed = tasks.filter(task => task.pivot?.completed).length
      const progress = tasks.length > 0
        ? Math.round((completed / tasks.length) * 100)
        : 0

      return {
        ...category,
        tasks,
        totalMinutes,
        totalHours,
        completed,
        progress,
      }
    })
  }

  if (!plan.value.weeks) return []

  const categoryMapping = {
    Goals: 'Goals',
    'Digital Marketing Foundations': 'Digital Marketing Foundations',
    'Local SEO': 'Local SEO',
    Content: 'Content',
    'Social Media': 'Social Media',
    Website: 'Website',
    'Email Marketing': 'Email Marketing',
    'Paid Ads': 'Paid Advertising',
    'Paid Advertising': 'Paid Advertising',
    CRM: 'CRM',
    Buffer: null
  }

  const desiredOrder = [
    'Goals',
    'Digital Marketing Foundations',
    'Local SEO',
    'Content',
    'Social Media',
    'Website',
    'Email Marketing',
    'Paid Advertising',
    'CRM'
  ]

  const categories = new Map()

  plan.value.weeks.forEach(week => {
    week.tasks?.forEach(task => {
      const rawCategory = task.category || 'General'
      const mappedCategory = Object.prototype.hasOwnProperty.call(categoryMapping, rawCategory)
        ? categoryMapping[rawCategory]
        : rawCategory

      if (!mappedCategory || mappedCategory === 'Buffer') {
        return
      }

      if (!categories.has(mappedCategory)) {
        categories.set(mappedCategory, {
          name: mappedCategory,
          tasks: [],
          totalMinutes: 0,
          completed: 0
        })
      }

      const category = categories.get(mappedCategory)
      category.tasks.push(task)
      category.totalMinutes += getTaskMinutes(task)
      if (task.pivot?.completed) {
        category.completed += 1
      }
    })
  })

  const result = Array.from(categories.values()).map(category => {
    const totalHours = Number((category.totalMinutes / 60).toFixed(1))

    return {
      ...category,
      totalHours,
      progress: category.tasks.length > 0
        ? Math.round((category.completed / category.tasks.length) * 100)
        : 0
    }
  })

  result.sort((a, b) => {
    const indexA = desiredOrder.indexOf(a.name)
    const indexB = desiredOrder.indexOf(b.name)

    if (indexA === -1 && indexB === -1) {
      return a.name.localeCompare(b.name)
    }
    if (indexA === -1) return 1
    if (indexB === -1) return -1
    return indexA - indexB
  })

  return result
})

const formatDate = (dateString) => {
  if (!dateString) return 'Not specified'
  const date = new Date(dateString)
  return date.toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const toggleTask = async (planTaskId, completed) => {
  try {
    const plan = plans.value.find(p => p.weeks?.some(w => w.tasks?.some(t => t.pivot?.id === planTaskId)))
    if (!plan) return

    await axios.put(`/api/plan/${plan.id}/plan-task/${planTaskId}`, {
      completed: completed
    })

    plan.weeks.forEach(week => {
      const task = week.tasks.find(t => t.pivot?.id === planTaskId)
      if (task) {
        task.pivot.completed = completed
      }
    })

    plan.completed_tasks = plan.weeks.reduce((sum, week) => {
      return sum + week.tasks.filter(task => task.pivot?.completed).length
    }, 0)

    plan.total_tasks = plan.weeks.reduce((sum, week) => sum + week.tasks.length, 0)

    if (Array.isArray(plan.categories)) {
      plan.categories.forEach(category => {
        category.tasks?.forEach(task => {
          if (task.pivot?.id === planTaskId) {
            task.pivot.completed = completed
          }
        })

        const completedCount = category.tasks
          ? category.tasks.filter(task => task.pivot?.completed).length
          : 0

        category.completed = completedCount
        category.progress = category.tasks && category.tasks.length > 0
          ? Math.round((completedCount / category.tasks.length) * 100)
          : 0
      })
    }

    updateStats()
  } catch (error) {
    console.error('Failed to update task:', error)
  }
}

const formatFrequency = (frequency) => {
  const map = {
    once: 'Once',
    weekly: 'Weekly',
    bi_weekly: 'Bi-weekly',
    monthly: 'Monthly',
    quarterly: 'Quarterly',
    half_yearly: 'Half-yearly',
    yearly: 'Yearly',
  }

  if (!frequency) {
    return 'Once'
  }

  const key = frequency.toLowerCase()
  return map[key] || key.charAt(0).toUpperCase() + key.slice(1)
}

const parseInstruction = (description = '') => {
  const blocks = description
    .split(/\n{2,}/)
    .map(block => block.trim())
    .filter(Boolean)

  const sections = []
  const prerequisites = []
  const resources = []

  blocks.forEach(block => {
    if (block.startsWith('Prerequisites:')) {
      const raw = block.replace('Prerequisites:', '').trim()
      raw.split(';').forEach(item => {
        const cleaned = item.trim()
        if (cleaned) {
          prerequisites.push(cleaned)
        }
      })
    } else if (block.startsWith('Resources:')) {
      const raw = block.replace('Resources:', '').trim()
      raw.split(',').forEach(item => {
        const url = item.trim()
        if (url) {
          resources.push({
            label: url.replace(/https?:\/\//, '').replace(/\/$/, ''),
            href: url.startsWith('http') ? url : `https://${url}`
          })
        }
      })
    } else {
      sections.push(block)
    }
  })

  return { sections, prerequisites, resources }
}

const getInstructionPreview = (task) => {
  if (!task?.description) return ''
  const text = task.description
    .replace(/<[^>]+>/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()

  if (!text) return ''

  if (text.length > 220) {
    return text.slice(0, 220).trimEnd() + '…'
  }
  return text
}

const openTaskModal = (task) => {
  selectedTask.value = task
  instructionModalOpen.value = true
}

const closeTaskModal = () => {
  instructionModalOpen.value = false
  selectedTask.value = null
}

const selectedTaskIsHtml = computed(() => {
  const description = selectedTask.value?.description
  if (!description || typeof description !== 'string') {
    return false
  }
  return /<\/?[a-z][\s\S]*>/i.test(description)
})

const instructionHtml = computed(() => {
  if (!selectedTask.value?.description) {
    return ''
  }

  if (selectedTaskIsHtml.value) {
    return selectedTask.value.description
  }

  const { sections } = parseInstruction(selectedTask.value.description)

  return sections
    .map(section => `<p>${section.replace(/\n/g, '<br>')}</p>`)
    .join('')
})

const instructionContent = computed(() => {
  if (!selectedTask.value?.description) {
    return { sections: [], prerequisites: [], resources: [] }
  }

  return parseInstruction(selectedTask.value.description)
})

const closeOnEscape = (event) => {
  if (event.key === 'Escape') {
    closeTaskModal()
  }
}

onMounted(() => {
  fetchPlans()
  window.addEventListener('keydown', closeOnEscape)
})

onUnmounted(() => {
  window.removeEventListener('keydown', closeOnEscape)
})
</script>

<style scoped>
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 150ms ease;
}
</style>
