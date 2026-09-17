<template>
  <div class="max-w-[1440px] mx-auto">
    <div class="mb-6 md:mb-8 flex flex-wrap items-start justify-between gap-4">
      <div>
        <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-purple/70 mb-2">{{ texts.headerSection }}</p>
        <h1 class="text-[32px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">{{ texts.headerTitle }}</h1>
        <p class="mt-3 text-[16px] text-purple/70 tracking-[-0.8px] max-w-[720px]">{{ texts.subtitle }}</p>
      </div>

      <div class="flex items-center gap-1 bg-white rounded-[20px] shadow-card p-1">
        <button
          type="button"
          class="w-10 h-10 rounded-[16px] flex items-center justify-center transition-colors"
          :class="viewMode === 'calendar' ? 'bg-red text-white' : 'text-purple hover:bg-rose'"
          :title="texts.calendarView"
          @click="viewMode = 'calendar'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </button>
        <button
          type="button"
          class="w-10 h-10 rounded-[16px] flex items-center justify-center transition-colors"
          :class="viewMode === 'list' ? 'bg-red text-white' : 'text-purple hover:bg-rose'"
          :title="texts.listView"
          @click="viewMode = 'list'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>

    <div v-if="selectedYear && selectedMonth" class="bg-white rounded-[30px] shadow-card p-5 md:p-8">
      <div v-if="availableMonths.length > 0" class="flex flex-wrap gap-2 mb-6">
        <button
          v-for="monthOption in availableMonths"
          :key="`${monthOption.year}-${monthOption.month}`"
          type="button"
          class="h-[34px] px-3 rounded-full text-[13px] font-medium tracking-[-0.5px] transition-colors"
          :class="isMonthSelected(monthOption.year, monthOption.month)
            ? 'bg-red text-white shadow-[0_8px_18px_rgba(243,71,103,0.28)]'
            : 'bg-light-grey border-2 border-rose text-purple hover:bg-rose'"
          @click="selectMonth(monthOption.year, monthOption.month)"
        >
          {{ formatMonth(monthOption.year, monthOption.month) }}
        </button>
      </div>

      <div class="flex items-center justify-between mb-6">
        <h2 class="text-[24px] md:text-[32px] font-bold tracking-[-1.6px] text-black leading-none">{{ formatMonthFull(selectedYear, selectedMonth) }}</h2>
        <div class="flex items-center gap-2">
          <button
            type="button"
            class="w-10 h-10 rounded-[16px] bg-light-grey border-4 border-rose flex items-center justify-center hover:bg-rose disabled:opacity-40"
            :disabled="!canGoPrevious"
            @click="previousMonth"
          >
            <svg class="w-5 h-5 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button
            type="button"
            class="w-10 h-10 rounded-[16px] bg-light-grey border-4 border-rose flex items-center justify-center hover:bg-rose disabled:opacity-40"
            :disabled="!canGoNext"
            @click="nextMonth"
          >
            <svg class="w-5 h-5 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>

      <div v-if="viewMode === 'calendar'" class="calendar overflow-x-auto">
        <div class="grid grid-cols-7 gap-2 md:gap-3 mb-3 min-w-[720px]">
          <div
            v-for="day in dayNames"
            :key="day"
            class="text-center text-[13px] font-bold tracking-[-0.6px] text-purple/70 py-2"
          >
            {{ day }}
          </div>
        </div>

        <div class="grid grid-cols-7 gap-2 md:gap-3 min-w-[720px]">
          <div
            v-for="(day, index) in calendarDays"
            :key="index"
            class="min-h-[140px] flex flex-col rounded-[16px] overflow-hidden transition-colors"
            :class="getDayClasses(day)"
            @click="selectDate(day)"
          >
            <div v-if="day" class="flex flex-col h-full">
              <div
                class="px-2.5 py-2 text-right text-[13px] font-bold tracking-[-0.6px]"
                :class="day.isToday ? 'bg-red text-white' : 'text-purple'"
              >
                {{ day.date }}
              </div>
              <div class="flex-1 p-2">
                <div v-if="getIdeaForDate(day.fullDate)" class="rounded-[12px] bg-white px-2.5 py-2 shadow-[0_2px_8px_rgba(28,26,27,0.06)]">
                  <span
                    class="inline-block text-[10px] font-bold uppercase tracking-[-0.4px] px-1.5 py-0.5 rounded-[6px] mb-1.5"
                    :class="ideaTypeClass(getIdeaForDate(day.fullDate).title)"
                  >
                    {{ ideaTypeLabel(getIdeaForDate(day.fullDate).title) }}
                  </span>
                  <p class="text-[12px] md:text-[13px] font-medium tracking-[-0.5px] text-black leading-snug line-clamp-3">
                    {{ ideaBodyTitle(getIdeaForDate(day.fullDate).title) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="viewMode === 'list'" class="space-y-2">
        <div
          v-for="day in calendarDaysWithIdeas"
          :key="day.date"
          class="flex items-center px-4 py-3 bg-light-grey rounded-[20px] border-4 border-rose hover:bg-rose cursor-pointer"
          @click="selectDate(day)"
        >
          <div class="flex items-center gap-4 flex-1">
            <div class="text-center min-w-[60px]">
              <div class="text-[18px] font-bold tracking-[-0.9px] text-black">{{ day.date }}</div>
              <div class="text-[12px] text-purple/70 tracking-[-0.5px]">{{ formatDayName(day.fullDate) }}</div>
            </div>
            <div class="flex-1 min-w-0">
              <template v-if="getIdeaForDate(day.fullDate)">
                <span
                  class="inline-block text-[10px] font-bold uppercase tracking-[-0.4px] px-1.5 py-0.5 rounded-[6px] mb-1"
                  :class="ideaTypeClass(getIdeaForDate(day.fullDate).title)"
                >
                  {{ ideaTypeLabel(getIdeaForDate(day.fullDate).title) }}
                </span>
                <p class="font-medium tracking-[-0.7px] text-black">
                  {{ ideaBodyTitle(getIdeaForDate(day.fullDate).title) }}
                </p>
              </template>
              <p v-else class="text-purple/40 italic">{{ texts.noIdea }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-12">
      <svg class="w-12 h-12 text-red mx-auto mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
      <p class="text-purple/70">{{ texts.loading }}</p>
    </div>

    <transition name="fade">
      <div
        v-if="showModal && contentIdea"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-8"
        @click.self="closeModal"
      >
        <div class="relative max-w-2xl w-full bg-white rounded-[30px] shadow-[0_16px_40px_rgba(28,26,27,0.16)] max-h-[90vh] overflow-hidden">
          <button
            type="button"
            class="absolute top-4 right-4 flex items-center justify-center w-9 h-9 rounded-full bg-rose text-red hover:bg-red hover:text-white z-10"
            @click="closeModal"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <div class="p-6 sm:p-8 overflow-y-auto no-scrollbar max-h-[90vh]">
            <h2 class="text-[24px] md:text-[28px] font-bold tracking-[-1.4px] text-black mb-2 pr-10">
              {{ formatDateTitle(contentIdea.date) }}: {{ contentIdea.title }}
            </h2>

            <div class="space-y-5 mt-6">
              <div>
                <div class="flex items-center justify-between mb-2">
                  <label class="text-[14px] font-bold tracking-[-0.7px] text-black">Caption</label>
                  <button
                    type="button"
                    class="h-[32px] px-3 rounded-[12px] bg-red text-white text-[12px] font-bold tracking-[-0.5px] hover:bg-red-dark flex items-center gap-1"
                    @click="copyToClipboard(contentIdea.caption)"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>{{ copyCaptionText }}</span>
                  </button>
                </div>
                <div class="bg-light-grey rounded-[20px] border-4 border-rose p-4">
                  <p class="text-black whitespace-pre-wrap tracking-[-0.6px]">{{ contentIdea.caption }}</p>
                </div>
              </div>

              <div v-if="contentIdea.hashtags">
                <div class="flex items-center justify-between mb-2">
                  <label class="text-[14px] font-bold tracking-[-0.7px] text-black">Hashtags</label>
                  <button
                    type="button"
                    class="h-[32px] px-3 rounded-[12px] bg-red text-white text-[12px] font-bold tracking-[-0.5px] hover:bg-red-dark flex items-center gap-1"
                    @click="copyToClipboard(contentIdea.hashtags)"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>{{ copyHashtagsText }}</span>
                  </button>
                </div>
                <div class="bg-light-grey rounded-[20px] border-4 border-rose p-4">
                  <p class="text-black whitespace-pre-wrap tracking-[-0.6px]">{{ contentIdea.hashtags }}</p>
                </div>
              </div>

              <div v-if="contentIdea.tips">
                <label class="text-[14px] font-bold tracking-[-0.7px] text-black mb-2 block">Tips</label>
                <div class="bg-light-grey rounded-[20px] border-4 border-rose p-4">
                  <p class="text-black whitespace-pre-wrap tracking-[-0.6px]">{{ contentIdea.tips }}</p>
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
  if (!day) return 'bg-transparent'
  
  const hasIdea = getIdeaForDate(day.fullDate)
  const classes = ['cursor-pointer']
  
  if (day.isToday) {
    classes.push('bg-rose')
  } else if (hasIdea) {
    classes.push('bg-light-grey')
  } else {
    classes.push('bg-light-grey opacity-40 cursor-default')
  }
  
  return classes.join(' ')
}

const ideaTypeLabel = (title) => {
  const raw = String(title || '').trim()
  if (!raw) return 'POST'
  const match = raw.match(/^([A-Za-zÄÖÜäöüß\s/]+):/)
  if (match) return match[1].trim().toUpperCase()
  const first = raw.split(/\s+/)[0]
  return first.length <= 12 ? first.toUpperCase() : 'POST'
}

const ideaBodyTitle = (title) => {
  const raw = String(title || '').trim()
  const match = raw.match(/^[^:]+:\s*(.+)$/)
  return match ? match[1].trim() : raw
}

const ideaTypeClass = (title) => {
  const type = ideaTypeLabel(title)
  if (/REEL|VIDEO|CLIP/i.test(type)) return 'bg-rose text-red'
  if (/PROMO|ADS|OFFER/i.test(type)) return 'bg-green/15 text-green'
  if (/STORY|STORIES/i.test(type)) return 'bg-yellow/40 text-black/70'
  if (/CAROUSEL|POST/i.test(type)) return 'bg-mint/20 text-purple'
  return 'bg-muted text-purple'
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
