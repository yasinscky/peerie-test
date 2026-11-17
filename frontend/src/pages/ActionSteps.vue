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
      <div class="space-y-4 min-w-0">
        <section class="bg-white rounded-[30px] shadow-card px-5 py-6 md:px-8 md:py-7">
          <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
              <div class="flex flex-wrap items-center gap-2 text-[12px] font-bold uppercase tracking-[-0.6px] text-purple">
                <span>{{ texts.thisMonth }}</span>
                <span class="w-1.5 h-1.5 rounded-full bg-red" />
                <span>{{ monthName }} {{ selectedYear }}</span>
              </div>
              <h2 class="mt-3 text-[28px] md:text-[32px] font-bold tracking-[-1.6px] text-black leading-none">
                {{ stageTitle }}
              </h2>
            </div>
            <div class="relative w-[96px] h-[96px] shrink-0">
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
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-[24px] font-bold tracking-[-1.2px] text-black leading-none">{{ progressPercent }}%</span>
                <span class="mt-1 text-[12px] font-bold uppercase tracking-[-0.6px] text-purple">{{ texts.complete }}</span>
              </div>
            </div>
          </div>

          <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
              <p class="text-[36px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">{{ stats.completedTasks }}</p>
              <p class="mt-2 text-[14px] font-medium tracking-[-0.7px] text-purple max-w-[110px] leading-tight">{{ texts.tasksCompleted }}</p>
            </div>
            <div>
              <p class="text-[36px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">{{ pendingTasksCount }}</p>
              <p class="mt-2 text-[14px] font-medium tracking-[-0.7px] text-purple max-w-[110px] leading-tight">{{ texts.tasksRemaining }}</p>
            </div>
            <div>
              <p class="text-[36px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">{{ weeksLeftLabel }}</p>
              <p class="mt-2 text-[14px] font-medium tracking-[-0.7px] text-purple max-w-[120px] leading-tight">{{ texts.leftInStage }}</p>
            </div>
            <div>
              <p class="text-[36px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">{{ daysLeft }}</p>
              <p class="mt-2 text-[14px] font-medium tracking-[-0.7px] text-purple max-w-[90px] leading-tight">{{ texts.daysLeft }}</p>
            </div>
          </div>
        </section>

        <div class="flex flex-wrap items-center gap-2">
          <label class="flex items-center gap-2 h-[42px] min-w-[180px] flex-1 max-w-[280px] bg-white border border-rose rounded-[16px] px-4 shadow-card">
            <svg class="w-4 h-4 shrink-0 text-red" viewBox="0 0 20 20" fill="none" aria-hidden="true">
              <circle cx="9" cy="9" r="5.5" stroke="currentColor" stroke-width="1.6" />
              <path d="M13.2 13.2L16.5 16.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
            <input
              v-model="searchQuery"
              type="search"
              :placeholder="texts.searchTasks"
              class="w-full bg-transparent text-[16px] tracking-[-0.8px] text-black outline-none placeholder:text-purple/40"
            >
          </label>
          <button
            v-for="filter in statusFilters"
            :key="filter.id"
            type="button"
            class="h-[38px] px-4 rounded-[19px] text-[14px] font-bold uppercase tracking-[-0.7px] transition-colors"
            :class="statusFilter === filter.id ? 'bg-red text-white shadow-red-sm' : 'bg-white border border-grey text-purple hover:bg-rose'"
            @click="statusFilter = filter.id"
          >
            {{ filter.label }}
          </button>
          <button
            type="button"
            class="ml-auto inline-flex items-center gap-2 text-[12px] font-medium tracking-[-0.6px] text-purple"
            @click="hideCompleted = !hideCompleted"
          >
            <span
              class="relative w-[35px] h-[20px] rounded-[19px] border-2 transition-colors"
              :class="hideCompleted ? 'bg-red border-red' : 'bg-white border-grey'"
            >
              <span
                class="absolute top-[1px] h-[14px] w-[14px] rounded-full transition-all"
                :class="hideCompleted ? 'left-[15px] bg-white' : 'left-[1px] bg-purple'"
              />
            </span>
            {{ texts.hideCompleted }}
          </button>
        </div>

        <section
          v-for="category in filteredCategories"
          :key="category.name"
          class="bg-white rounded-[24px] shadow-card p-4 md:p-5"
        >
            <button
              type="button"
              class="w-full flex items-center gap-3 text-left"
              @click="toggleCategory(category.name)"
            >
              <span
                class="w-9 h-9 rounded-[12px] flex items-center justify-center shrink-0"
                :class="categoryAccent(category.name).tile"
              >
                <img :src="iconActionSteps" alt="" class="w-4 h-4">
              </span>
              <h3 class="flex-1 text-[28px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">
                {{ category.name }}
              </h3>
              <svg
                class="w-5 h-5 shrink-0 text-purple transition-transform"
                :class="collapsedCategories[category.name] ? '-rotate-90' : ''"
                viewBox="0 0 20 20"
                fill="none"
                aria-hidden="true"
              >
                <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>

            <div v-if="!collapsedCategories[category.name]" class="mt-4">
              <p class="text-[12px] font-bold tracking-[-0.6px] text-purple/70">
                {{ category.completed }} {{ texts.of }} {{ category.tasks.length }} {{ texts.tasksCompleted }}
              </p>
              <div class="mt-2 h-[6px] rounded-[6px] bg-light-grey overflow-hidden">
                <div class="h-full rounded-[6px]" :class="categoryAccent(category.name).bar" :style="{ width: `${category.progress}%` }" />
              </div>

              <div v-if="openTasks(category).length" class="mt-5">
                <p class="text-[12px] font-bold tracking-[-0.6px] text-purple/70">
                  {{ texts.allTasks }} ({{ openTasks(category).length }})
                </p>
                <div class="mt-2 space-y-2">
                  <article
                    v-for="task in openTasks(category)"
                    :key="task.pivot?.id || task.id"
                    class="bg-light-grey rounded-[16px] px-4 py-4 flex items-center gap-3"
                  >
                    <button
                      type="button"
                      class="w-5 h-5 rounded-full border-2 border-grey bg-white flex items-center justify-center shrink-0 hover:border-red"
                      @click="task.pivot?.id && toggleTask(task.pivot.id, !task.pivot.completed)"
                    />
                    <span
                      class="w-8 h-8 rounded-[12px] flex items-center justify-center shrink-0"
                      :class="categoryAccent(category.name).tile"
                    >
                      <img :src="iconActionSteps" alt="" class="w-4 h-4">
                    </span>
                    <div class="min-w-0 flex-1">
                      <h4 class="text-[18px] md:text-[20px] font-bold tracking-[-1px] text-black leading-none">
                        {{ task.title }}
                      </h4>
                      <p class="mt-2 flex flex-wrap items-center gap-2 text-[12px] font-bold uppercase tracking-[-0.6px] text-purple/70">
                        <span>{{ category.name }}</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-red" />
                        <span>{{ getTaskMinutes(task) }} {{ texts.min }}</span>
                      </p>
                    </div>
                    <span class="hidden sm:inline-flex items-center px-2 py-1 rounded-[7px] bg-yellow text-[14px] tracking-[-0.7px] text-black shrink-0">
                      {{ texts.inProgress }}
                    </span>
                    <router-link
                      v-if="task.pivot?.id"
                      :to="taskDetailRoute(task)"
                      class="inline-flex items-center justify-center h-[42px] px-5 rounded-[20px] bg-red shadow-red text-[16px] font-bold tracking-[-0.8px] text-white hover:bg-red-dark shrink-0"
                    >
                      {{ texts.open }}
                    </router-link>
                  </article>
                </div>
              </div>

              <div v-if="!hideCompleted && doneTasks(category).length" class="mt-5">
                <p class="text-[12px] font-bold tracking-[-0.6px] text-purple/70">
                  {{ texts.completed }} ({{ doneTasks(category).length }})
                </p>
                <div class="mt-2 space-y-2">
                  <article
                    v-for="task in doneTasks(category)"
                    :key="task.pivot?.id || task.id"
                    class="bg-light-grey rounded-[16px] px-4 py-4 flex items-center gap-3"
                  >
                    <button
                      type="button"
                      class="w-5 h-5 rounded-full border-2 border-green bg-green flex items-center justify-center shrink-0"
                      @click="task.pivot?.id && toggleTask(task.pivot.id, false)"
                    >
                      <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <span
                      class="w-8 h-8 rounded-[12px] flex items-center justify-center shrink-0"
                      :class="categoryAccent(category.name).tile"
                    >
                      <img :src="iconActionSteps" alt="" class="w-4 h-4">
                    </span>
                    <div class="min-w-0 flex-1">
                      <h4 class="text-[18px] md:text-[20px] font-bold tracking-[-1px] text-black leading-none line-through opacity-60">
                        {{ task.title }}
                      </h4>
                      <p class="mt-2 flex flex-wrap items-center gap-2 text-[12px] font-bold uppercase tracking-[-0.6px] text-purple/70">
                        <span>{{ category.name }}</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-green" />
                        <span>{{ getTaskMinutes(task) }} {{ texts.min }}</span>
                      </p>
                    </div>
                    <span class="hidden sm:inline-flex items-center px-2 py-1 rounded-[7px] bg-green/20 text-[14px] tracking-[-0.7px] text-green shrink-0">
                      {{ texts.done }}
                    </span>
                    <router-link
                      v-if="task.pivot?.id"
                      :to="taskDetailRoute(task)"
                      class="inline-flex items-center justify-center h-[42px] px-5 rounded-[20px] bg-red shadow-red text-[16px] font-bold tracking-[-0.8px] text-white hover:bg-red-dark shrink-0"
                    >
                      {{ texts.open }}
                    </router-link>
                  </article>
                </div>
              </div>
            </div>
        </section>

        <div v-if="filteredCategories.length === 0" class="py-10 text-center text-purple/70">
          {{ texts.noTasks }}
        </div>
      </div>

      <aside>
        <section class="bg-white rounded-[30px] shadow-card p-5">
          <div class="flex items-center gap-3">
            <h3 class="text-[32px] font-bold tracking-[-1.6px] text-black leading-none">{{ texts.progress }}</h3>
            <div ref="progressMonthRoot" class="relative">
              <button
                type="button"
                class="inline-flex items-center gap-2"
                @click="progressMonthOpen = !progressMonthOpen"
              >
                <span class="text-[16px] font-bold tracking-[-0.8px] text-black">
                  {{ monthName }} {{ selectedYear }}
                </span>
                <span class="w-5 h-5 rounded-[12px] bg-yellow flex items-center justify-center shrink-0">
                  <img
                    :src="iconChevronDown"
                    alt=""
                    class="w-4 h-4 transition-transform"
                    :class="progressMonthOpen ? 'rotate-180' : ''"
                  >
                </span>
              </button>
              <div
                v-if="progressMonthOpen"
                class="absolute left-0 top-full z-20 mt-2 min-w-[180px] max-h-[240px] overflow-y-auto bg-white rounded-[16px] border border-grey shadow-card p-1.5"
              >
                <button
                  v-for="monthOption in availableMonths"
                  :key="`${monthOption.year}-${monthOption.month}`"
                  type="button"
                  class="w-full text-left px-3 py-2 rounded-[12px] text-[14px] font-bold tracking-[-0.7px] transition-colors"
                  :class="isProgressMonthSelected(monthOption.year, monthOption.month)
                    ? 'bg-red text-white'
                    : 'text-black hover:bg-rose'"
                  @click="selectProgressMonth(monthOption.year, monthOption.month)"
                >
                  {{ formatMonthOption(monthOption.year, monthOption.month) }}
                </button>
              </div>
            </div>
          </div>
          <div class="relative w-[220px] h-[220px] mx-auto mt-6">
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
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6">
              <p class="text-[48px] md:text-[56px] font-bold tracking-[-2.8px] text-black leading-none">{{ progressPercent }}%</p>
              <p class="mt-2 text-[14px] tracking-[-0.7px] text-purple/70">
                {{ stats.completedTasks }} {{ texts.of }} {{ stats.totalTasks }} {{ texts.tasks }}
              </p>
            </div>
          </div>
          <div class="mt-6 space-y-3 text-[16px] tracking-[-0.8px]">
            <div class="flex items-center justify-between text-black">
              <span class="inline-flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-green" />
                {{ texts.completed }}
              </span>
              <span class="font-bold">{{ stats.completedTasks }}</span>
            </div>
            <div class="flex items-center justify-between text-black">
              <span class="inline-flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-yellow" />
                {{ texts.tasksRemaining }}
              </span>
              <span class="font-bold">{{ pendingTasksCount }}</span>
            </div>
            <div class="flex items-center justify-between text-black">
              <span class="inline-flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-red" />
                {{ texts.total }}
              </span>
              <span class="font-bold">{{ stats.totalTasks }}</span>
            </div>
          </div>
        </section>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import iconActionSteps from '@/assets/images/icons/dashboard/v2/action-steps.svg'
import iconChevronDown from '@/assets/images/icons/dashboard/v2/chevron-down.svg'

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
const searchQuery = ref('')
const statusFilter = ref('all')
const hideCompleted = ref(false)
const collapsedCategories = ref({})
const progressMonthOpen = ref(false)
const progressMonthRoot = ref(null)

const taskDetailRoute = (task) => ({
  name: 'TaskDetail',
  params: { planTaskId: String(task.pivot.id) },
  query: {
    from: 'action-steps',
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
      headerTitle: 'Action Steps',
      subtitle: 'Alle Aufgaben deines Plans für diesen Monat, nach Bereich gruppiert.',
      thisMonth: 'Dieser Monat',
      tasksCompleted: 'Aufgaben erledigt',
      tasksRemaining: 'Aufgaben offen',
      leftInStage: 'noch in diesem Monat',
      daysLeft: 'Tage übrig',
      searchTasks: 'Aufgaben suchen...',
      allStatuses: 'Alle Status',
      notStarted: 'Nicht gestartet',
      inProgress: 'In Arbeit',
      hideCompleted: 'Erledigte ausblenden',
      allTasks: 'Alle Aufgaben',
      open: 'Öffnen',
      noTasks: 'Keine Aufgaben für diesen Monat',
      progress: 'Fortschritt',
      of: 'von',
      tasks: 'Aufgaben',
      completed: 'Erledigt',
      total: 'Gesamt',
      complete: 'erledigt',
      min: 'Min',
      emptyTitle: 'Du hast noch keine Action Steps',
      emptyText: 'Deine Action Steps werden nach dem Ausfüllen des Fragebogens automatisch erstellt',
      done: 'Erledigt'
    }
  }

  return {
    headerTitle: 'Action Steps',
    subtitle: 'Every task behind this month’s plan, grouped by area.',
    thisMonth: 'This month',
    tasksCompleted: 'tasks completed',
    tasksRemaining: 'tasks remaining',
    leftInStage: 'left in this stage',
    daysLeft: 'days left',
    searchTasks: 'Search tasks...',
    allStatuses: 'All statuses',
    notStarted: 'Not started',
    inProgress: 'In progress',
    hideCompleted: 'Hide completed',
    allTasks: 'All tasks',
    open: 'Open',
    noTasks: 'No tasks for this month',
    progress: 'Progress',
    of: 'of',
    tasks: 'tasks',
    completed: 'Completed',
    total: 'Total',
    complete: 'complete',
    min: 'min',
    emptyTitle: "You don't have action steps yet",
    emptyText: 'Your action steps will be created after completing the questionnaire',
    done: 'Done'
  }
})

const monthName = computed(() => {
  const names = languageStore.language === 'de'
    ? ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
    : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
  return names[selectedMonth.value - 1] || ''
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

const daysLeft = computed(() => {
  const now = new Date()
  const end = new Date(selectedYear.value, selectedMonth.value, 0)
  return Math.max(0, Math.ceil((end - now) / (1000 * 60 * 60 * 24)))
})

const weeksLeftLabel = computed(() => {
  const weeks = Math.max(0, Math.ceil(daysLeft.value / 7))
  return languageStore.language === 'de' ? `${weeks} Wo.` : `${weeks} wks`
})

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

const uniqueTasks = (plan) => {
  const seen = new Set()
  const tasks = []
  for (const category of plan?.categories || []) {
    for (const task of category.tasks || []) {
      if (seen.has(task.id)) continue
      seen.add(task.id)
      tasks.push(task)
    }
  }
  return tasks
}

const updateStats = () => {
  const tasks = plans.value.flatMap((plan) => uniqueTasks(plan))
  const completedTasks = tasks.filter((task) => task.pivot?.completed).length
  stats.value = {
    completedTasks,
    inProgressTasks: tasks.length - completedTasks,
    totalTasks: tasks.length,
  }
}

const getProgressPercentage = (plan) => {
  const tasks = uniqueTasks(plan)
  if (!tasks.length) return 0
  const completed = tasks.filter((task) => task.pivot?.completed).length
  return Math.round((completed / tasks.length) * 100)
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
      const seen = new Set()
      const tasks = []
      for (const task of category.tasks || []) {
        if (seen.has(task.id)) continue
        seen.add(task.id)
        tasks.push(task)
      }
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

const categoryAccents = {
  Goals: { tile: 'bg-yellow', bar: 'bg-red' },
  'Marketing Foundations': { tile: 'bg-cream', bar: 'bg-purple' },
  'Local SEO': { tile: 'bg-mint', bar: 'bg-green' },
  Content: { tile: 'bg-yellow', bar: 'bg-red' },
  'Social Media': { tile: 'bg-[#ffd6de]', bar: 'bg-red' },
  Website: { tile: 'bg-mint', bar: 'bg-green' },
  'Email Marketing': { tile: 'bg-cream', bar: 'bg-red' },
  'Paid Advertising': { tile: 'bg-[#ffd6de]', bar: 'bg-red' },
  'Leads & Clients': { tile: 'bg-yellow', bar: 'bg-green' },
}

const fallbackAccents = [
  { tile: 'bg-yellow', bar: 'bg-red' },
  { tile: 'bg-mint', bar: 'bg-green' },
  { tile: 'bg-cream', bar: 'bg-purple' },
  { tile: 'bg-[#ffd6de]', bar: 'bg-red' },
]

const categoryAccent = (name) => {
  if (categoryAccents[name]) return categoryAccents[name]
  const index = Math.abs([...String(name)].reduce((sum, char) => sum + char.charCodeAt(0), 0)) % fallbackAccents.length
  return fallbackAccents[index]
}

const stageTitle = computed(() => {
  const names = categorisedTasks.value.map((category) => category.name).filter(Boolean)
  if (names.length >= 2) return `${names[0]} & ${names[1]}`
  if (names.length === 1) return names[0]
  return texts.value.headerTitle
})

const statusFilters = computed(() => [
  { id: 'all', label: texts.value.allStatuses },
  { id: 'not_started', label: texts.value.notStarted },
  { id: 'in_progress', label: texts.value.inProgress },
  { id: 'done', label: texts.value.done },
])

const matchesTask = (task) => {
  const query = searchQuery.value.trim().toLowerCase()
  if (query && !String(task.title || '').toLowerCase().includes(query)) return false
  const done = Boolean(task.pivot?.completed)
  if (statusFilter.value === 'done') return done
  if (statusFilter.value === 'not_started' || statusFilter.value === 'in_progress') return !done
  return true
}

const openTasks = (category) => (category.tasks || []).filter((task) => !task.pivot?.completed && matchesTask(task))

const doneTasks = (category) => {
  if (hideCompleted.value || statusFilter.value === 'not_started' || statusFilter.value === 'in_progress') return []
  return (category.tasks || []).filter((task) => task.pivot?.completed && matchesTask(task))
}

const filteredCategories = computed(() => {
  return categorisedTasks.value.filter((category) => openTasks(category).length || doneTasks(category).length)
})

const toggleCategory = (name) => {
  collapsedCategories.value = {
    ...collapsedCategories.value,
    [name]: !collapsedCategories.value[name],
  }
}

const formatMonthOption = (year, month) => {
  const names = languageStore.language === 'de'
    ? ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
    : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
  return `${names[month - 1] || ''} ${year}`
}

const isProgressMonthSelected = (year, month) => {
  return selectedYear.value === year && selectedMonth.value === month
}

const selectProgressMonth = (year, month) => {
  progressMonthOpen.value = false
  handleMonthSelect({ year, month })
}

const closeProgressMonthOnOutsideClick = (event) => {
  if (!progressMonthOpen.value) return
  if (progressMonthRoot.value?.contains(event.target)) return
  progressMonthOpen.value = false
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
  document.addEventListener('click', closeProgressMonthOnOutsideClick)
  await fetchAvailableMonths()
  await fetchPlans()
})

onUnmounted(() => {
  document.removeEventListener('click', closeProgressMonthOnOutsideClick)
})
</script>

