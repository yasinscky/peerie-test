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

    <div class="mb-8">
      <p class="text-[#3F4369] opacity-70 mt-2">{{ texts.subtitle }}</p>
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

      <div class="calendar">
        <div class="grid grid-cols-7 gap-1 mb-2">
          <div
            v-for="day in dayNames"
            :key="day"
            class="text-center text-sm font-medium text-[#3F4369] opacity-70 py-2"
          >
            {{ day }}
          </div>
        </div>

        <div class="grid grid-cols-7 gap-1">
          <div
            v-for="(day, index) in calendarDays"
            :key="index"
            class="aspect-square flex items-center justify-center text-sm transition-colors cursor-pointer rounded-lg"
            :class="getDayClasses(day)"
            @click="selectDate(day)"
          >
            <span v-if="day">{{ day.date }}</span>
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
      errorLoading: 'Fehler beim Laden der Idee'
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
    errorLoading: 'Error loading idea'
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

const selectMonth = (year, month) => {
  selectedYear.value = year
  selectedMonth.value = month
  selectedDate.value = null
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
  if (!day) return 'bg-transparent'
  
  const classes = []
  
  if (day.isSelected) {
    classes.push('bg-[#f34767] text-white font-bold')
  } else if (day.isToday) {
    classes.push('bg-blue-500 text-white font-bold')
  } else {
    classes.push('text-[#3F4369] hover:bg-[#FFEBD0]')
  }
  
  return classes.join(' ')
}

const selectDate = async (day) => {
  if (!day) return
  selectedDate.value = day.fullDate
  
  await fetchContentIdea(day.fullDate)
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
})
</script>

<style scoped>
.calendar {
  min-height: 400px;
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
