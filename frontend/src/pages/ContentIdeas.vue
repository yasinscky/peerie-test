<template>
  <div class="max-w-7xl mx-auto">
    <div class="bg-[#f34767] pt-[19px] pb-[19px] lg:h-28 lg:pt-0 lg:pb-0 px-4 lg:px-8 flex items-center justify-between rounded-[20px] lg:rounded-40 mb-8">
      <div class="flex items-center">
        <div class="w-10 h-10 rounded-lg bg-opacity-20 hidden md:flex items-center justify-center">
          <img :src="logoWhite" alt="Peerie Logo" class="w-10 h-10">
        </div>
        <h1 class="text-white text-2xl lg:text-3xl font-bold">{{ texts.headerTitle }}</h1>
      </div>
      <div class="flex items-center space-x-2 text-white text-sm lg:text-xl font-medium">
        <span>{{ texts.headerSection }}</span>
        <span class="opacity-40">|</span>
        <span class="opacity-40">{{ texts.headerCurrent }}</span>
      </div>
    </div>

    <div class="flex items-center justify-between mb-8">
      <p class="text-[#3F4369] opacity-70 mt-2">{{ texts.subtitle }}</p>
      
      <div class="flex items-center gap-2 bg-white rounded-lg border-2 border-[#DCDCDC] p-1">
        <button
          @click="viewMode = 'calendar'"
          :class="viewMode === 'calendar' 
            ? 'bg-[#3F4369] text-white' 
            : 'bg-white text-[#3F4369] hover:bg-[#FFEBD0]'"
          class="p-2 rounded transition-colors"
          :title="texts.calendarView"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
        </button>
        <button
          @click="viewMode = 'list'"
          :class="viewMode === 'list' 
            ? 'bg-[#3F4369] text-white' 
            : 'bg-white text-[#3F4369] hover:bg-[#FFEBD0]'"
          class="p-2 rounded transition-colors"
          :title="texts.listView"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
          </svg>
        </button>
      </div>
    </div>

    <div v-if="availableMonths.length > 0" class="mb-8">
      <div class="bg-white rounded-2xl shadow-lg border border-[#DCDCDC] p-6">
        <h3 class="text-xl font-bold text-[#3F4369] mb-4">{{ texts.monthSelector }}</h3>
        <div class="space-y-2">
          <button
            v-for="monthOption in availableMonths"
            :key="`${monthOption.year}-${monthOption.month}`"
            type="button"
            class="block w-full text-left px-4 py-2 rounded-lg border transition-colors"
            :class="isMonthSelected(monthOption.year, monthOption.month)
              ? 'bg-[#f34767] text-white border-[#f34767]'
              : 'border-[#DCDCDC] bg-white text-[#3F4369] hover:border-[#f34767] hover:bg-[#FFEBD0]'"
            @click="selectMonth(monthOption.year, monthOption.month)"
          >
            {{ formatMonth(monthOption.year, monthOption.month) }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="selectedYear && selectedMonth" class="bg-white rounded-2xl shadow-lg border border-[#DCDCDC] p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-[#3F4369]">{{ formatMonthFull(selectedYear, selectedMonth) }}</h2>
        <div class="flex items-center space-x-2">
          <button
            @click="previousMonth"
            class="p-2 rounded-lg border border-[#DCDCDC] hover:bg-[#FFEBD0] transition-colors"
            :disabled="!canGoPrevious"
          >
            <svg class="w-5 h-5 text-[#3F4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </button>
          <button
            @click="nextMonth"
            class="p-2 rounded-lg border border-[#DCDCDC] hover:bg-[#FFEBD0] transition-colors"
            :disabled="!canGoNext"
          >
            <svg class="w-5 h-5 text-[#3F4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>

      <div v-if="viewMode === 'calendar'" class="calendar">
        <div class="grid grid-cols-7 gap-3 mb-3">
          <div
            v-for="day in dayNames"
            :key="day"
            class="text-center text-sm font-medium text-[#3F4369] opacity-70 py-2"
          >
            {{ day }}
          </div>
        </div>

        <div class="grid grid-cols-7 gap-3">
          <div
            v-for="(day, index) in calendarDays"
            :key="index"
            class="min-h-[140px] p-3 flex flex-col transition-colors cursor-pointer rounded-lg border"
            :class="getDayClasses(day)"
            @click="selectDate(day)"
          >
            <div v-if="day" class="flex flex-col h-full">
              <div class="text-right text-sm font-medium mb-2" :class="day.isToday ? 'text-white' : 'text-[#3F4369]'">
                {{ day.date }}
              </div>
              <div v-if="getIdeaForDate(day.fullDate)" class="flex-1 flex items-start">
                <p class="text-sm leading-tight line-clamp-4" :class="day.isToday ? 'text-white' : 'text-[#3F4369]'">
                  {{ getIdeaForDate(day.fullDate).title }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="viewMode === 'list'" class="space-y-2">
        <div
          v-for="day in calendarDaysWithIdeas"
          :key="day.date"
          class="flex items-center px-4 py-3 border border-[#DCDCDC] rounded-lg hover:bg-[#FFEBD0] hover:border-[#f34767] transition-colors cursor-pointer"
          @click="selectDate(day)"
        >
          <div class="flex items-center gap-4 flex-1">
            <div class="text-center min-w-[60px]">
              <div class="text-lg font-bold text-[#3F4369]">{{ day.date }}</div>
              <div class="text-xs text-[#3F4369] opacity-70">{{ formatDayName(day.fullDate) }}</div>
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2 text-sm text-[#3F4369] opacity-50 mb-1">
                <span>12:00 AM</span>
                <span v-if="getIdeaForDate(day.fullDate)" class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                  </svg>
                </span>
              </div>
              <p v-if="getIdeaForDate(day.fullDate)" class="font-medium text-[#3F4369]">
                {{ getIdeaForDate(day.fullDate).title }}
              </p>
              <p v-else class="text-[#3F4369] opacity-50 italic">{{ texts.noIdea }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-12">
      <svg class="w-16 h-16 text-blue-600 mx-auto mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-gray-600 dark:text-gray-400">{{ texts.loading }}</p>
    </div>

    <transition name="fade">
      <div
        v-if="showModal && contentIdea"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-8"
        @click.self="closeModal"
      >
        <div class="relative max-w-2xl w-full bg-white rounded-2xl shadow-2xl border border-[#DCDCDC] max-h-[90vh] overflow-hidden">
          <button
            class="absolute top-4 right-4 flex items-center justify-center w-8 h-8 rounded-full bg-[#FFEBD0] text-[#F34767] hover:bg-[#F34767] hover:text-white transition-colors z-10"
            @click="closeModal"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>

          <div class="p-6 sm:p-8 overflow-y-auto no-scrollbar max-h-[90vh]">
            <h2 class="text-2xl font-bold text-[#3F4369] mb-2">
              {{ formatDateTitle(contentIdea.date) }}: {{ contentIdea.title }}
            </h2>

            <div class="space-y-6 mt-6">
              <div>
                <div class="flex items-center justify-between mb-2">
                  <label class="text-sm font-medium text-[#3F4369]">Caption</label>
                  <button
                    @click="copyToClipboard(contentIdea.caption)"
                    class="px-3 py-1 text-xs font-medium rounded-lg border border-[#F34767] text-[#F34767] hover:bg-[#F34767] hover:text-white transition-colors flex items-center space-x-1"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ copyCaptionText }}</span>
                  </button>
                </div>
                <div class="bg-[#F9FAFB] border border-[#DCDCDC] rounded-lg p-4">
                  <p class="text-[#3F4369] whitespace-pre-wrap">{{ contentIdea.caption }}</p>
                </div>
              </div>

              <div v-if="contentIdea.hashtags">
                <div class="flex items-center justify-between mb-2">
                  <label class="text-sm font-medium text-[#3F4369]">Hashtags</label>
                  <button
                    @click="copyToClipboard(contentIdea.hashtags)"
                    class="px-3 py-1 text-xs font-medium rounded-lg border border-[#F34767] text-[#F34767] hover:bg-[#F34767] hover:text-white transition-colors flex items-center space-x-1"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ copyHashtagsText }}</span>
                  </button>
                </div>
                <div class="bg-[#F9FAFB] border border-[#DCDCDC] rounded-lg p-4">
                  <p class="text-[#3F4369] whitespace-pre-wrap">{{ contentIdea.hashtags }}</p>
                </div>
              </div>

              <div v-if="contentIdea.tips">
                <label class="text-sm font-medium text-[#3F4369] mb-2 block">Tips</label>
                <div class="bg-[#F9FAFB] border border-[#DCDCDC] rounded-lg p-4">
                  <p class="text-[#3F4369] whitespace-pre-wrap">{{ contentIdea.tips }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import logoWhite from '@/assets/images/logos/logo-white.svg'

const router = useRouter()
const route = useRoute()
const languageStore = useLanguageStore()

const isLoading = ref(true)
const availableMonths = ref([])
const selectedYear = ref(null)
const selectedMonth = ref(null)
const selectedDate = ref(null)
const showModal = ref(false)
const contentIdea = ref(null)
const isLoadingIdea = ref(false)
const copyCaptionText = ref('Copy to clipboard')
const copyHashtagsText = ref('Copy to clipboard')
const viewMode = ref('calendar')
const monthIdeas = ref([])

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      headerTitle: 'Content Ideen',
      headerSection: 'Erstellen',
      headerCurrent: 'Content Ideen',
      subtitle: 'Wähle einen Monat und erstelle Content-Ideen für dein Marketing',
      monthSelector: 'Monat auswählen',
      loading: 'Lädt...',
      noIdeaFound: 'Keine Idee für dieses Datum gefunden',
      errorLoading: 'Fehler beim Laden der Idee',
      calendarView: 'Kalenderansicht',
      listView: 'Listenansicht',
      noIdea: 'Keine Idee'
    }
  }

  return {
    headerTitle: 'Content Ideas',
    headerSection: 'Create',
    headerCurrent: 'Content Ideas',
    subtitle: 'Select a month and create content ideas for your marketing',
    monthSelector: 'Select Month',
    loading: 'Loading...',
    noIdeaFound: 'No idea found for this date',
    errorLoading: 'Error loading idea',
    calendarView: 'Calendar View',
    listView: 'List View',
    noIdea: 'No idea'
  }
})

const dayNames = computed(() => {
  if (languageStore.language === 'de') {
    return ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa']
  }
  return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
})

const fetchAvailableMonths = async () => {
  try {
    isLoading.value = true
    const response = await axios.get('/api/content-ideas/available-months')
    if (response.data.success && response.data.months) {
      availableMonths.value = response.data.months
      
      if (availableMonths.value.length > 0) {
        const firstMonth = availableMonths.value[0]
        selectedYear.value = firstMonth.year
        selectedMonth.value = firstMonth.month
      }
    }
  } catch (error) {
    console.error('Failed to load available months:', error)
  } finally {
    isLoading.value = false
  }
}

const isMonthSelected = (year, month) => {
  return selectedYear.value === year && selectedMonth.value === month
}

const formatMonth = (year, month) => {
  const date = new Date(year, month - 1, 1)
  const monthNames = languageStore.language === 'de'
    ? ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez']
    : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  
  return `${monthNames[month - 1]} ${year}`
}

const formatMonthFull = (year, month) => {
  const date = new Date(year, month - 1, 1)
  const monthNames = languageStore.language === 'de'
    ? ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
    : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
  
  return `${monthNames[month - 1]} ${year}`
}

const selectMonth = async (year, month) => {
  selectedYear.value = year
  selectedMonth.value = month
  selectedDate.value = null
  await fetchMonthIdeas(year, month)
}

const fetchMonthIdeas = async (year, month) => {
  try {
    const response = await axios.get('/api/content-ideas/by-month', {
      params: { year, month }
    })
    if (response.data.success) {
      monthIdeas.value = response.data.data || []
    }
  } catch (error) {
    console.error('Failed to load month ideas:', error)
    monthIdeas.value = []
  }
}

const getIdeaForDate = (date) => {
  if (!date || !monthIdeas.value.length) return null
  const dateStr = formatDateForApi(date)
  return monthIdeas.value.find(idea => idea.date === dateStr) || null
}

const calendarDaysWithIdeas = computed(() => {
  return calendarDays.value.filter(day => day && getIdeaForDate(day.fullDate))
})

const formatDayName = (date) => {
  const dayNames = languageStore.language === 'de'
    ? ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa']
    : ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
  return dayNames[date.getDay()]
}

const canGoPrevious = computed(() => {
  if (!selectedYear.value || !selectedMonth.value || availableMonths.value.length === 0) return false
  const currentIndex = availableMonths.value.findIndex(
    m => m.year === selectedYear.value && m.month === selectedMonth.value
  )
  return currentIndex > 0
})

const canGoNext = computed(() => {
  if (!selectedYear.value || !selectedMonth.value || availableMonths.value.length === 0) return false
  const currentIndex = availableMonths.value.findIndex(
    m => m.year === selectedYear.value && m.month === selectedMonth.value
  )
  return currentIndex < availableMonths.value.length - 1
})

const previousMonth = () => {
  if (!canGoPrevious.value) return
  const currentIndex = availableMonths.value.findIndex(
    m => m.year === selectedYear.value && m.month === selectedMonth.value
  )
  if (currentIndex > 0) {
    const prevMonth = availableMonths.value[currentIndex - 1]
    selectMonth(prevMonth.year, prevMonth.month)
  }
}

const nextMonth = () => {
  if (!canGoNext.value) return
  const currentIndex = availableMonths.value.findIndex(
    m => m.year === selectedYear.value && m.month === selectedMonth.value
  )
  if (currentIndex < availableMonths.value.length - 1) {
    const nextMonth = availableMonths.value[currentIndex + 1]
    selectMonth(nextMonth.year, nextMonth.month)
  }
}

const calendarDays = computed(() => {
  if (!selectedYear.value || !selectedMonth.value) return []

  const year = selectedYear.value
  const month = selectedMonth.value
  const firstDay = new Date(year, month - 1, 1)
  const lastDay = new Date(year, month, 0)
  const daysInMonth = lastDay.getDate()
  const startingDayOfWeek = firstDay.getDay()

  const days = []
  
  for (let i = 0; i < startingDayOfWeek; i++) {
    days.push(null)
  }

  for (let day = 1; day <= daysInMonth; day++) {
    const date = new Date(year, month - 1, day)
    const isToday = isDateToday(date)
    const isSelected = selectedDate.value && 
      selectedDate.value.getDate() === day &&
      selectedDate.value.getMonth() === month - 1 &&
      selectedDate.value.getFullYear() === year

    days.push({
      date: day,
      fullDate: date,
      isToday,
      isSelected
    })
  }

  const remainingDays = 42 - days.length
  for (let i = 1; i <= remainingDays; i++) {
    days.push(null)
  }

  return days
})

const isDateToday = (date) => {
  const today = new Date()
  return date.getDate() === today.getDate() &&
    date.getMonth() === today.getMonth() &&
    date.getFullYear() === today.getFullYear()
}

const getDayClasses = (day) => {
  if (!day) return 'bg-transparent border-transparent'
  
  const hasIdea = getIdeaForDate(day.fullDate)
  const classes = []
  
  if (day.isToday) {
    classes.push('bg-[#1C8E9E] text-white border-[#1C8E9E]')
  } else if (hasIdea) {
    classes.push('bg-white border-[#DCDCDC] hover:bg-[#FFEBD0] hover:border-[#f34767]')
  } else {
    classes.push('bg-gray-50 border-gray-200 text-[#3F4369] opacity-40 cursor-default')
  }
  
  return classes.join(' ')
}

const selectDate = async (day) => {
  if (!day) return
  
  const idea = getIdeaForDate(day.fullDate)
  if (!idea) return
  
  selectedDate.value = day.fullDate
  contentIdea.value = idea
  showModal.value = true
}

const fetchContentIdea = async (date) => {
  try {
    isLoadingIdea.value = true
    showModal.value = false
    contentIdea.value = null
    
    const dateStr = formatDateForApi(date)
    const response = await axios.get('/api/content-ideas/by-date', {
      params: { date: dateStr }
    })
    
    if (response.data.success && response.data.data) {
      contentIdea.value = {
        ...response.data.data,
        date: parseApiDate(response.data.data.date)
      }
      showModal.value = true
    } else {
      alert(texts.value.noIdeaFound)
    }
  } catch (error) {
    console.error('Failed to load content idea:', error)
    if (error.response?.status === 404) {
      alert(texts.value.noIdeaFound)
    } else {
      alert(texts.value.errorLoading)
    }
  } finally {
    isLoadingIdea.value = false
  }
}

const closeModal = () => {
  showModal.value = false
  contentIdea.value = null
}

const formatDateTitle = (date) => {
  if (!date) return ''
  const d = new Date(date)
  const day = d.getDate()
  const monthNames = languageStore.language === 'de'
    ? ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
    : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
  
  return `${day} ${monthNames[d.getMonth()]}`
}

const copyToClipboard = async (text) => {
  try {
    await navigator.clipboard.writeText(text)
    if (text === contentIdea.value?.caption) {
      copyCaptionText.value = 'Copied!'
      setTimeout(() => {
        copyCaptionText.value = 'Copy to clipboard'
      }, 2000)
    } else if (text === contentIdea.value?.hashtags) {
      copyHashtagsText.value = 'Copied!'
      setTimeout(() => {
        copyHashtagsText.value = 'Copy to clipboard'
      }, 2000)
    }
  } catch (error) {
    console.error('Failed to copy:', error)
  }
}

const formatDateForApi = (date) => {
  const d = new Date(date)
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const parseApiDate = (value) => {
  if (!value) return null
  if (value instanceof Date) return value
  if (typeof value !== 'string') return new Date(value)

  const parts = value.split('-').map((v) => parseInt(v, 10))
  if (parts.length !== 3 || parts.some((v) => Number.isNaN(v))) {
    return new Date(value)
  }

  const [year, month, day] = parts
  return new Date(year, month - 1, day)
}

onMounted(async () => {
  await fetchAvailableMonths()
  if (selectedYear.value && selectedMonth.value) {
    await fetchMonthIdeas(selectedYear.value, selectedMonth.value)
  }
})
</script>

<style scoped>
.calendar {
  min-height: 400px;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-4 {
  display: -webkit-box;
  -webkit-line-clamp: 4;
  line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 150ms ease;
}
</style>
