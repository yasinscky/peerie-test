<template>
  <div class="max-w-[1440px] mx-auto">
    <div class="mb-6 md:mb-8">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
          <h1 class="text-[32px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">
            {{ texts.headerTitle }}
          </h1>
          <div class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-[14px] text-purple/70 tracking-[-0.7px]">
            <span>{{ texts.subtitle }}</span>
            <button
              v-if="isDevelopment"
              type="button"
              class="text-purple hover:text-red underline underline-offset-2"
              @click="showGenerateMonthModal = true"
            >
              Generate Month (Test)
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="isDevelopment && showGenerateMonthModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
      @click.self="showGenerateMonthModal = false"
    >
      <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-black mb-4">Generate Tasks for Month</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-black mb-2">Year</label>
            <input
              v-model.number="generateMonthYear"
              type="number"
              min="2020"
              max="2100"
              class="w-full px-3 py-2 border border-grey rounded-lg focus:outline-none focus:border-red"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-black mb-2">Month</label>
            <select
              v-model.number="generateMonthMonth"
              class="w-full px-3 py-2 border border-grey rounded-lg focus:outline-none focus:border-red"
            >
              <option :value="1">January</option>
              <option :value="2">February</option>
              <option :value="3">March</option>
              <option :value="4">April</option>
              <option :value="5">May</option>
              <option :value="6">June</option>
              <option :value="7">July</option>
              <option :value="8">August</option>
              <option :value="9">September</option>
              <option :value="10">October</option>
              <option :value="11">November</option>
              <option :value="12">December</option>
            </select>
          </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
          <button
            type="button"
            class="px-4 py-2 text-purple border border-grey rounded-[20px] hover:bg-rose"
            @click="showGenerateMonthModal = false"
          >
            Cancel
          </button>
          <button
            type="button"
            class="px-4 py-2 bg-red text-white rounded-[16px] hover:bg-red-dark disabled:opacity-50"
            :disabled="isGeneratingMonth"
            @click="generateMonthTasks"
          >
            {{ isGeneratingMonth ? 'Generating...' : 'Generate' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-16">
      <svg class="w-12 h-12 text-red mx-auto mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
      <p class="text-purple/70">Loading plans...</p>
    </div>

    <div v-else-if="!plan" class="bg-white rounded-[30px] shadow-card p-10 text-center">
      <h3 class="text-xl font-bold text-black mb-2">{{ texts.emptyTitle }}</h3>
      <p class="text-purple/70">{{ texts.emptyText }}</p>
    </div>

    <div v-else class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_320px] gap-5">
      <div class="space-y-5 min-w-0">
        <section class="bg-white rounded-[30px] shadow-card p-5 md:p-8 relative overflow-hidden">
          <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-2 text-[12px] font-bold uppercase tracking-[-0.6px] text-purple">
                <span>{{ texts.stageLabel }}</span>
                <span class="w-1.5 h-1.5 rounded-full bg-red" />
                <span>{{ planFocusLabel }}</span>
              </div>
              <h2 class="mt-3 text-[24px] md:text-[32px] font-bold tracking-[-1.6px] text-black leading-none">
                {{ texts.planTitle }} {{ monthName }}
              </h2>
              <p class="mt-3 text-[16px] text-purple/70 tracking-[-0.8px] max-w-[490px] leading-snug">
                {{ planSubtitle }}
              </p>
            </div>
            <div class="relative w-24 h-24 shrink-0">
              <svg class="w-full h-full -rotate-90" viewBox="0 0 96 96">
                <circle cx="48" cy="48" r="39" fill="none" stroke="rgb(var(--color-cream))" stroke-width="10" />
                <circle
                  cx="48"
                  cy="48"
                  r="39"
                  fill="none"
                  stroke="rgb(var(--color-red))"
                  stroke-width="10"
                  stroke-linecap="round"
                  :stroke-dasharray="circumference"
                  :stroke-dashoffset="progressOffset"
                />
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-[24px] font-bold tracking-[-1.2px] text-black">{{ progressPercent }}%</span>
              </div>
            </div>
          </div>

          <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
              <div class="h-2.5 w-[100px] rounded-[10px] bg-yellow mb-3" />
              <p class="text-[36px] font-bold tracking-[-1.8px] text-black leading-none">
                {{ stats.completedTasks }}/{{ stats.totalTasks }}
              </p>
              <p class="mt-2 text-[14px] font-medium tracking-[-0.7px] text-purple">{{ texts.tasksCompleted }}</p>
            </div>
            <div>
              <div class="h-2.5 w-[100px] rounded-[10px] bg-yellow mb-3" />
              <p class="text-[36px] font-bold tracking-[-1.8px] text-black leading-none">{{ weeksLeftLabel }}</p>
              <p class="mt-2 text-[14px] font-medium tracking-[-0.7px] text-purple">{{ texts.leftInStage }}</p>
            </div>
            <div>
              <div class="h-2.5 w-[100px] rounded-[10px] bg-grey mb-3 overflow-hidden">
                <div class="h-full bg-red rounded-[10px]" :style="{ width: `${Math.min(progressPercent, 100)}%` }" />
              </div>
              <p class="text-[36px] font-bold tracking-[-1.8px] text-black leading-none">{{ pendingTasksCount }}</p>
              <p class="mt-2 text-[14px] font-medium tracking-[-0.7px] text-purple">{{ texts.pendingTasks }}</p>
            </div>
          </div>
        </section>

        <section class="bg-white rounded-[30px] shadow-card p-4 md:p-5">
          <div class="flex flex-wrap items-center justify-between gap-2 mb-4 px-1">
            <div class="flex flex-wrap items-center gap-3">
              <h3 class="text-[24px] font-bold tracking-[-1.2px] text-black">{{ texts.actionSteps }}</h3>
              <span class="text-[14px] text-purple/70 tracking-[-0.7px]">{{ currentMonthRange }}</span>
            </div>
            <button
              type="button"
              class="inline-flex items-center gap-1 text-[16px] text-purple tracking-[-0.8px] hover:text-red"
              @click="showAllTasks = !showAllTasks"
            >
              {{ showAllTasks ? texts.hideTasks : texts.allTasks }}
              <img :src="iconArrowLink" alt="" class="w-4 h-4">
            </button>
          </div>

          <div class="space-y-3">
            <div
              v-for="item in visibleActionTasks"
              :key="item.task.pivot?.id || item.task.id"
              class="bg-light-grey rounded-[20px] px-4 py-3 flex items-center gap-3 md:gap-4"
            >
              <button
                type="button"
                class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors"
                :class="item.task.pivot?.completed
                  ? 'bg-green border-green'
                  : 'border-grey hover:border-red'"
                @click="toggleTask(item.task.pivot.id, !item.task.pivot.completed)"
              >
                <svg v-if="item.task.pivot?.completed" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </button>

              <div class="w-8 h-8 rounded-[12px] bg-yellow flex items-center justify-center shrink-0">
                <img :src="iconActionSteps" alt="" class="w-4 h-4">
              </div>

              <router-link
                v-if="item.task.pivot?.id"
                :to="taskDetailRoute(item.task)"
                class="min-w-0 flex-1"
              >
                <div class="flex flex-wrap items-center gap-2">
                  <h4
                    class="text-[18px] md:text-[20px] font-bold tracking-[-1px] text-black truncate"
                    :class="item.task.pivot?.completed ? 'line-through opacity-60' : ''"
                  >
                    {{ item.task.title }}
                  </h4>
                  <span
                    class="inline-flex items-center px-[5px] py-1 rounded-[7px] text-[14px] md:text-[16px] tracking-[-0.8px] shrink-0"
                    :class="taskStatusClass(item.task)"
                  >
                    {{ taskStatusLabel(item.task) }}
                  </span>
                </div>
                <p class="mt-1 text-[12px] font-bold uppercase tracking-[-0.6px] text-purple/70">
                  {{ item.categoryName }} · {{ getTaskMinutes(item.task) }} MIN
                </p>
              </router-link>
              <div v-else class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                  <h4
                    class="text-[18px] md:text-[20px] font-bold tracking-[-1px] text-black truncate"
                    :class="item.task.pivot?.completed ? 'line-through opacity-60' : ''"
                  >
                    {{ item.task.title }}
                  </h4>
                  <span
                    class="inline-flex items-center px-[5px] py-1 rounded-[7px] text-[14px] md:text-[16px] tracking-[-0.8px] shrink-0"
                    :class="taskStatusClass(item.task)"
                  >
                    {{ taskStatusLabel(item.task) }}
                  </span>
                </div>
                <p class="mt-1 text-[12px] font-bold uppercase tracking-[-0.6px] text-purple/70">
                  {{ item.categoryName }} · {{ getTaskMinutes(item.task) }} MIN
                </p>
              </div>

              <router-link
                v-if="item.task.pivot?.id"
                :to="taskDetailRoute(item.task)"
                class="inline-flex items-center justify-center h-[42px] px-5 rounded-[20px] bg-red shadow-red text-[16px] font-bold tracking-[-0.8px] text-white hover:bg-red-dark shrink-0"
              >
                {{ texts.open }}
              </router-link>
            </div>

            <div v-if="flatTasks.length === 0" class="py-10 text-center text-purple/70">
              {{ texts.noTasks }}
            </div>
          </div>
        </section>

        <section v-if="showAllTasks && categorisedTasks.length > 0" class="space-y-4">
          <div
            v-for="category in categorisedTasks"
            :key="category.name"
            class="bg-white rounded-[30px] shadow-card overflow-hidden"
          >
            <div class="px-5 py-4 flex items-center justify-between">
              <div>
                <h3 class="text-[20px] font-bold tracking-[-1px] text-black">{{ category.name }}</h3>
                <p class="text-[14px] text-purple/70">
                  {{ category.tasks.length }} tasks · {{ formatHoursAndMinutes(category.totalMinutes) }}
                </p>
              </div>
              <p class="text-[20px] font-bold text-black">{{ category.progress }}%</p>
            </div>
            <div class="px-4 pb-4 space-y-2">
              <router-link
                v-for="task in category.tasks"
                :key="task.id"
                :to="task.pivot?.id ? taskDetailRoute(task) : ''"
                class="w-full text-left bg-light-grey rounded-[20px] border-4 border-rose px-4 py-3 flex items-center justify-between gap-3 hover:bg-rose"
              >
                <span class="font-medium text-black" :class="task.pivot?.completed ? 'line-through opacity-60' : ''">
                  {{ task.title }}
                </span>
                <span
                  class="inline-flex items-center px-[5px] py-1 rounded-[7px] text-[14px] shrink-0"
                  :class="taskStatusClass(task)"
                >
                  {{ taskStatusLabel(task) }}
                </span>
              </router-link>
            </div>
          </div>
        </section>
      </div>

      <aside class="space-y-5">
        <section class="bg-white rounded-[30px] shadow-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-[20px] font-bold tracking-[-1px] text-black">{{ texts.progress }} {{ monthName }}</h3>
          </div>
          <div class="relative w-[180px] h-[180px] mx-auto">
            <svg class="w-full h-full -rotate-90" viewBox="0 0 180 180">
              <circle cx="90" cy="90" r="72" fill="none" stroke="rgb(var(--color-cream))" stroke-width="16" />
              <circle
                cx="90"
                cy="90"
                r="72"
                fill="none"
                stroke="rgb(var(--color-red))"
                stroke-width="16"
                stroke-linecap="round"
                :stroke-dasharray="largeCircumference"
                :stroke-dashoffset="largeProgressOffset"
              />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4">
              <p class="text-[36px] font-bold tracking-[-1.8px] text-black leading-none">{{ progressPercent }}%</p>
              <p class="mt-2 text-[14px] text-purple/70 tracking-[-0.7px]">
                {{ stats.completedTasks }} {{ texts.of }} {{ stats.totalTasks }} {{ texts.tasks }}
              </p>
            </div>
          </div>
          <div class="mt-5 space-y-2 text-[14px] tracking-[-0.7px]">
            <div class="flex items-center justify-between text-black">
              <span>{{ texts.completed }}</span>
              <span class="font-bold">{{ stats.completedTasks }}</span>
            </div>
            <div class="flex items-center justify-between text-black">
              <span>{{ texts.pending }}</span>
              <span class="font-bold">{{ stats.inProgressTasks }}</span>
            </div>
            <div class="flex items-center justify-between text-black">
              <span>{{ texts.total }}</span>
              <span class="font-bold">{{ stats.totalTasks }}</span>
            </div>
          </div>
        </section>

        <section class="bg-white rounded-[30px] shadow-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-[20px] font-bold tracking-[-1px] text-black">{{ texts.quickLinks }}</h3>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <router-link
              to="/dashboard/content-ideas"
              class="bg-light-grey rounded-[20px] border-4 border-rose p-3 hover:bg-rose transition-colors"
            >
              <img :src="iconContentCalendar" alt="" class="w-4 h-4 mb-2">
              <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-purple">{{ texts.contentIdeas }}</p>
            </router-link>
            <router-link
              to="/dashboard/image-generator"
              class="bg-light-grey rounded-[20px] border-4 border-rose p-3 hover:bg-rose transition-colors"
            >
              <img :src="iconResourceLibrary" alt="" class="w-4 h-4 mb-2">
              <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-purple">{{ texts.imageLibrary }}</p>
            </router-link>
            <router-link
              to="/dashboard/hashtags"
              class="bg-light-grey rounded-[20px] border-4 border-rose p-3 hover:bg-rose transition-colors"
            >
              <img :src="iconActionSteps" alt="" class="w-4 h-4 mb-2">
              <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-purple">{{ texts.hashtags }}</p>
            </router-link>
            <router-link
              to="/dashboard/documents"
              class="bg-light-grey rounded-[20px] border-4 border-rose p-3 hover:bg-rose transition-colors"
            >
              <img :src="iconDocuments" alt="" class="w-[14px] h-[14px] mb-2">
              <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-purple">{{ texts.documents }}</p>
            </router-link>
          </div>
        </section>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import iconActionSteps from '@/assets/images/icons/dashboard/v2/action-steps.svg'
import iconArrowLink from '@/assets/images/icons/dashboard/v2/arrow-link.svg'
import iconContentCalendar from '@/assets/images/icons/dashboard/v2/content-calendar.svg'
import iconResourceLibrary from '@/assets/images/icons/dashboard/v2/resource-library.svg'
import iconDocuments from '@/assets/images/icons/dashboard/v2/documents.svg'

const router = useRouter()
const route = useRoute()
const languageStore = useLanguageStore()

const plans = ref([])
const isLoading = ref(true)
const availableMonths = ref([])
const selectedYear = ref(parseInt(route.query.year) || new Date().getFullYear())
const selectedMonth = ref(parseInt(route.query.month) || new Date().getMonth() + 1)
const stats = ref({
  completedTasks: 0,
  inProgressTasks: 0,
  totalTasks: 0
})
const showGenerateMonthModal = ref(false)
const generateMonthYear = ref(new Date().getFullYear())
const generateMonthMonth = ref(new Date().getMonth() + 1)
const isGeneratingMonth = ref(false)
const showAllTasks = ref(false)
const userName = ref('')

const taskDetailRoute = (task) => ({
  name: 'TaskDetail',
  params: { planTaskId: String(task.pivot.id) },
  query: {
    from: 'marketing-plans',
    year: selectedYear.value,
    month: selectedMonth.value
  }
})

const isDevelopment = computed(() => {
  if (import.meta.env.MODE === 'development') {
    return true
  }
  
  if (import.meta.env.DEV) {
    return true
  }
  
  const hostname = window.location.hostname
  return hostname === 'localhost' || hostname === '127.0.0.1' || hostname.startsWith('192.168.') || hostname.startsWith('10.')
})

const plan = computed(() => plans.value[0] || null)

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      headerTitle: 'Dein Marketingplan',
      subtitle: 'Verfolge deine Marketingaufgaben nach Kategorie',
      welcome: 'Willkommen zurück',
      planTitle: 'Dein Marketingplan für',
      stageLabel: 'Monatlicher Plan',
      tasksCompleted: 'Aufgaben erledigt',
      leftInStage: 'noch in diesem Monat',
      pendingTasks: 'offen',
      actionSteps: 'Action Steps',
      allTasks: 'Alle Aufgaben',
      hideTasks: 'Weniger',
      open: 'Öffnen',
      noTasks: 'Keine Aufgaben für diesen Monat',
      progress: 'Fortschritt',
      of: 'von',
      tasks: 'Aufgaben',
      completed: 'Erledigt',
      pending: 'Offen',
      total: 'Gesamt',
      quickLinks: 'Schnellzugriff',
      contentIdeas: 'Content Ideen',
      imageLibrary: 'Bildbibliothek',
      hashtags: 'Hashtags',
      documents: 'Dokumente',
      emptyTitle: 'Du hast noch keine Pläne',
      emptyText: 'Dein Marketingplan wird nach dem Ausfüllen des Fragebogens automatisch erstellt',
      done: 'Done',
      notStarted: 'Not started'
    }
  }

  return {
    headerTitle: 'Marketing Plan',
    subtitle: 'Track your marketing tasks by category',
    welcome: 'Welcome back',
    planTitle: 'Your marketing plan for',
    stageLabel: 'Monthly plan',
    tasksCompleted: 'tasks completed',
    leftInStage: 'left in this stage',
    pendingTasks: 'pending tasks',
    actionSteps: "This week's action steps",
    allTasks: 'All tasks',
    hideTasks: 'Hide tasks',
    open: 'Open',
    noTasks: 'No tasks for this month',
    progress: 'Progress',
    of: 'of',
    tasks: 'tasks',
    completed: 'Completed',
    pending: 'Pending',
    total: 'Total',
    quickLinks: 'Quick links',
    contentIdeas: 'Content Ideas',
    imageLibrary: 'Image Library',
    hashtags: 'Hashtags',
    documents: 'Documents',
    emptyTitle: "You don't have any plans yet",
    emptyText: 'Your marketing plan will be created after completing the questionnaire',
    done: 'Done',
    notStarted: 'Not started'
  }
})

const welcomeName = computed(() => {
  if (!userName.value) return 'there'
  return userName.value.split(' ')[0]
})

const monthName = computed(() => {
  const names = languageStore.language === 'de'
    ? ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
    : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
  return names[selectedMonth.value - 1] || ''
})

const todayLabel = computed(() => {
  const now = new Date()
  return now.toLocaleDateString(languageStore.language === 'de' ? 'de-DE' : 'en-US', {
    weekday: 'long',
    month: 'long',
    day: 'numeric'
  })
})

const progressPercent = computed(() => {
  if (!plan.value) return 0
  return getProgressPercentage(plan.value)
})

const circumference = 2 * Math.PI * 39
const largeCircumference = 2 * Math.PI * 72

const progressOffset = computed(() => {
  return circumference - (progressPercent.value / 100) * circumference
})

const largeProgressOffset = computed(() => {
  return largeCircumference - (progressPercent.value / 100) * largeCircumference
})

const pendingTasksCount = computed(() => stats.value.inProgressTasks)

const weeksLeftLabel = computed(() => {
  const now = new Date()
  const end = new Date(selectedYear.value, selectedMonth.value, 0)
  const daysLeft = Math.max(0, Math.ceil((end - now) / (1000 * 60 * 60 * 24)))
  const weeks = Math.max(0, Math.ceil(daysLeft / 7))
  return `${weeks} wks`
})

const planFocusLabel = computed(() => {
  if (plan.value?.industries?.length) {
    return plan.value.industries.slice(0, 2).join(' & ')
  }
  return plan.value?.country || 'Marketing'
})

const planSubtitle = computed(() => {
  if (languageStore.language === 'de') {
    return `Fokus diesen Monat: Sichtbarkeit und Content. Dein Plan aktualisiert sich automatisch am 1. des nächsten Monats.`
  }
  return `This month's focus: growing visibility and content. Your plan refreshes automatically next month.`
})

const currentMonthRange = computed(() => {
  const start = new Date(selectedYear.value, selectedMonth.value - 1, 1)
  const end = new Date(selectedYear.value, selectedMonth.value, 0)
  const opts = { month: 'short', day: 'numeric' }
  const locale = languageStore.language === 'de' ? 'de-DE' : 'en-US'
  return `${start.toLocaleDateString(locale, opts)} – ${end.toLocaleDateString(locale, opts)}`
})

const flatTasks = computed(() => {
  if (!categorisedTasks.value.length) return []
  return categorisedTasks.value.flatMap(category =>
    (category.tasks || []).map(task => ({
      task,
      categoryName: category.name
    }))
  )
})

const visibleActionTasks = computed(() => {
  if (showAllTasks.value) return flatTasks.value
  const pending = flatTasks.value.filter(item => !item.task.pivot?.completed)
  const source = pending.length > 0 ? pending : flatTasks.value
  return source.slice(0, 6)
})

const taskStatusLabel = (task) => {
  return task.pivot?.completed ? texts.value.done : texts.value.notStarted
}

const taskStatusClass = (task) => {
  return task.pivot?.completed
    ? 'bg-green/20 text-green'
    : 'bg-grey text-purple'
}

const fetchAvailableMonths = async () => {
  try {
    const response = await axios.get('/api/plans/available-months')
    if (response.data.success && response.data.months) {
      availableMonths.value = response.data.months
      
      if (availableMonths.value.length > 0 && !route.query.year && !route.query.month) {
        const latest = availableMonths.value[0]
        selectedYear.value = latest.year
        selectedMonth.value = latest.month
        router.replace({
          query: {
            ...route.query,
            year: latest.year,
            month: latest.month
          }
        })
      }
    }
  } catch (error) {
    console.error('Failed to load available months:', error)
    const now = new Date()
    availableMonths.value = [{
      year: now.getFullYear(),
      month: now.getMonth() + 1
    }]
  }
}

const fetchPlans = async () => {
  try {
    isLoading.value = true
    console.log('Fetching plans for:', { year: selectedYear.value, month: selectedMonth.value })
    const response = await axios.get('/api/plans', {
      params: {
        year: selectedYear.value,
        month: selectedMonth.value
      }
    })
    console.log('API Response:', response.data)
    console.log('Requested year/month:', response.data.year, response.data.month)
    
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

const handleMonthSelect = ({ year, month }) => {
  console.log('Month selected:', { year, month })
  selectedYear.value = year
  selectedMonth.value = month
  router.push({
    query: {
      ...route.query,
      year,
      month
    }
  })
}

const generateMonthTasks = async () => {
  try {
    isGeneratingMonth.value = true
    const response = await axios.post('/api/plans/generate-month', {
      year: generateMonthYear.value,
      month: generateMonthMonth.value
    })
    
    if (response.data.success) {
      alert(`Tasks generated successfully for ${generateMonthYear.value}-${generateMonthMonth.value}`)
      showGenerateMonthModal.value = false
      await fetchAvailableMonths()
      selectedYear.value = generateMonthYear.value
      selectedMonth.value = generateMonthMonth.value
      router.push({
        query: {
          ...route.query,
          year: generateMonthYear.value,
          month: generateMonthMonth.value
        }
      })
    }
  } catch (error) {
    console.error('Failed to generate month tasks:', error)
    alert('Failed to generate tasks: ' + (error.response?.data?.message || error.message))
  } finally {
    isGeneratingMonth.value = false
  }
}

watch(() => route.query, (newQuery) => {
  if (newQuery.year && newQuery.month) {
    selectedYear.value = parseInt(newQuery.year)
    selectedMonth.value = parseInt(newQuery.month)
    fetchPlans()
  }
}, { immediate: false })

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

const categorisedTasks = computed(() => {
  if (!plan.value) return []

  if (Array.isArray(plan.value.categories) && plan.value.categories.length > 0) {
    return plan.value.categories.map(category => {
      const tasks = category.tasks || []
      const completed = tasks.filter(task => task.pivot?.completed).length
      const progress = tasks.length > 0
        ? Math.round((completed / tasks.length) * 100)
        : 0

      return {
        ...category,
        tasks,
        totalMinutes: category.totalMinutes || 0,
        completed,
        progress,
      }
    })
  }

  return []
})

const formatHoursAndMinutes = (minutes) => {
  const value = Number.isFinite(Number(minutes)) ? Math.max(0, Math.round(Number(minutes))) : 0
  const hours = Math.floor(value / 60)
  const mins = value % 60
  if (hours <= 0) {
    return `${mins}m`
  }
  if (mins === 0) {
    return `${hours}h`
  }
  return `${hours}h ${mins}m`
}

const toggleTask = async (planTaskId, completed) => {
  try {
    const plan = plans.value.find(p => {
      if (Array.isArray(p.categories)) {
        return p.categories.some(category =>
          (category.tasks || []).some(task => task.pivot?.id === planTaskId)
        )
      }

      if (Array.isArray(p.tasks)) {
        return p.tasks.some(task => task.pivot?.id === planTaskId)
      }

      return false
    })
    if (!plan) return

    await axios.put(`/api/plan/${plan.id}/plan-task/${planTaskId}`, {
      completed: completed
    })

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

    if (Array.isArray(plan.categories)) {
      plan.completed_tasks = plan.categories.reduce((sum, category) => {
        const tasks = category.tasks || []
        return sum + tasks.filter(task => task.pivot?.completed).length
      }, 0)

      plan.total_tasks = plan.categories.reduce((sum, category) => {
        const tasks = category.tasks || []
        return sum + tasks.length
      }, 0)
    }

    updateStats()
  } catch (error) {
    console.error('Failed to update task:', error)
  }
}

onMounted(async () => {
  try {
    const savedUser = localStorage.getItem('user')
    if (savedUser) {
      const parsed = JSON.parse(savedUser)
      userName.value = parsed?.name || ''
    }
  } catch (error) {
    console.error('Failed to parse saved user:', error)
  }

  await fetchPlans()
})
</script>

