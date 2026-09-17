<template>
  <div class="max-w-[1050px]">
    <div class="mb-8">
      <h1 class="text-[32px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">
        {{ texts.headerTitle }}
      </h1>
      <p class="mt-3 text-[14px] text-[#9e9e9e] tracking-[-0.7px] max-w-[760px] leading-snug">
        {{ texts.intro }}
      </p>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-16">
      <svg class="w-12 h-12 text-red animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
    </div>

    <div v-else-if="error" class="bg-[#eaeaea] rounded-[20px] p-8">
      <p class="text-red">{{ error }}</p>
    </div>

    <div v-else class="space-y-5">
      <section class="bg-[#d7d7d7] rounded-[24px] px-8 py-7">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
          <h2 class="text-[24px] md:text-[32px] font-bold tracking-[-1.6px] text-black leading-none">
            {{ texts.libraryTitle }}
          </h2>
          <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-black">
            {{ documents.length }} {{ texts.documentsTotal }}
          </p>
        </div>
        <div class="flex h-[10px] rounded-[6px] overflow-hidden">
          <div
            v-for="item in statusBar"
            :key="item.id"
            class="h-full first:rounded-l-[6px] last:rounded-r-[6px]"
            :class="item.barClass"
            :style="{ width: `${item.percent}%` }"
          />
        </div>
        <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2">
          <div
            v-for="item in statusBar"
            :key="`legend-${item.id}`"
            class="flex items-center gap-2"
          >
            <span class="size-3 rounded-[4px] shrink-0" :class="item.dotClass" />
            <span class="text-[20px] font-bold tracking-[-1px] text-black leading-none">{{ item.count }}</span>
            <span class="text-[14px] font-medium tracking-[-0.7px] text-black">{{ item.label }}</span>
          </div>
        </div>
      </section>

      <section v-if="refreshItems.length" class="bg-[#eaeaea] rounded-[20px] px-4 py-7 md:px-8">
        <div class="flex items-start gap-3">
          <span class="w-[26px] h-[26px] rounded-[6px] bg-[#d7d7d7] flex items-center justify-center shrink-0">
            <img :src="iconRefresh" alt="" class="w-[18px] h-[18px]">
          </span>
          <div class="min-w-0">
            <h2 class="text-[24px] md:text-[32px] font-bold tracking-[-1.6px] text-black leading-none">
              {{ texts.refreshTitle }}
            </h2>
            <p class="mt-2 text-[14px] text-[#9e9e9e] tracking-[-0.7px] max-w-[720px]">
              {{ texts.refreshIntro }}
            </p>
          </div>
        </div>
        <div class="mt-5 space-y-3">
          <article
            v-for="item in refreshItems"
            :key="`refresh-${item.kind}`"
            class="flex flex-wrap items-center justify-between gap-3 bg-[#f0f0f0] rounded-[16px] px-4 min-h-[85px] py-4"
          >
            <div class="flex items-center gap-3 min-w-0">
              <span class="w-[26px] h-[26px] rounded-[6px] bg-[#d7d7d7] flex items-center justify-center shrink-0">
                <img :src="iconFor(item)" alt="" class="w-[18px] h-[18px]">
              </span>
              <div class="min-w-0">
                <h3 class="text-[20px] md:text-[24px] font-bold tracking-[-1.2px] text-black leading-none">
                  {{ item.title }}
                </h3>
                <p class="mt-2 text-[12px] font-bold uppercase tracking-[-0.6px] text-[#737373]">
                  {{ groupTitle(item.group) }}
                  <span class="mx-1.5 inline-block w-1.5 h-1.5 rounded-full bg-red align-middle" />
                  {{ updatedLabel(item, 'short') }}
                </p>
              </div>
            </div>
            <router-link
              :to="item.to"
              class="shrink-0 h-[39px] px-4 rounded-[6px] bg-[#d7d7d7] text-[16px] md:text-[20px] tracking-[-1px] text-black hover:bg-rose flex items-center"
            >
              {{ texts.reviewUpdate }}
            </router-link>
          </article>
        </div>
      </section>

      <div class="flex flex-wrap items-center gap-3">
        <label class="flex items-center gap-3 h-[50px] w-full max-w-[311px] bg-[#f0f0f0] rounded-[16px] px-[19px]">
          <img :src="iconSearch" alt="" class="w-5 h-5 shrink-0">
          <input
            v-model="search"
            type="search"
            :placeholder="texts.search"
            class="w-full bg-transparent text-[20px] tracking-[-1px] text-black outline-none placeholder:text-black/20"
          >
        </label>
        <div class="flex flex-wrap gap-2 md:ml-auto">
          <button
            v-for="filter in filters"
            :key="filter.id"
            type="button"
            class="h-[38px] px-4 rounded-[19px] text-[14px] font-bold uppercase tracking-[-0.7px] text-black"
            :class="statusFilter === filter.id
              ? 'bg-[#979797]'
              : 'border-2 border-[#979797]'"
            @click="statusFilter = filter.id"
          >
            {{ filter.label }}
          </button>
        </div>
      </div>

      <section
        v-for="group in visibleGroups"
        :key="group.id"
        class="bg-[#eaeaea] rounded-[20px] p-1.5"
      >
        <button
          type="button"
          class="w-full flex items-center justify-between gap-3 text-left px-6 py-6"
          @click="toggleGroup(group.id)"
        >
          <h2 class="text-[32px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">
            {{ group.title }}
          </h2>
          <span class="flex items-center gap-2 shrink-0">
            <span class="text-[14px] text-[#9e9e9e] tracking-[-0.7px]">
              {{ group.upToDateCount }} {{ texts.of }} {{ group.items.length }} {{ texts.upToDate.toLowerCase() }}
            </span>
            <img
              :src="iconChevron"
              alt=""
              class="w-[18px] h-[18px] transition-transform"
              :class="collapsed[group.id] ? '-rotate-90' : ''"
            >
          </span>
        </button>

        <div v-if="!collapsed[group.id]" class="bg-white rounded-[20px] p-3 space-y-3">
          <article
            v-for="item in group.items"
            :key="item.kind"
            class="flex flex-wrap items-center justify-between gap-4 bg-[#f0f0f0] rounded-[16px] px-4 py-4 min-h-[108px]"
          >
            <div class="flex items-start gap-[15px] min-w-0 flex-1">
              <span class="w-[26px] h-[26px] rounded-[6px] bg-white border-2 border-[#d7d7d7] flex items-center justify-center shrink-0 mt-0.5">
                <img :src="iconFor(item)" alt="" class="w-[18px] h-[18px]">
              </span>
              <div class="min-w-0">
                <h3 class="text-[20px] md:text-[24px] font-medium tracking-[-1.2px] text-black leading-none">
                  {{ item.title }}
                </h3>
                <p class="mt-2 text-[14px] text-[#9e9e9e] tracking-[-0.7px]">
                  {{ item.description }}
                </p>
                <p class="mt-1 text-[14px] text-[#9e9e9e] tracking-[-0.7px]">
                  {{ updatedLabel(item) }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
              <span class="h-5 px-2 rounded-[19px] border-2 border-[#979797] text-[12px] font-medium tracking-[-0.6px] text-[#737373] flex items-center whitespace-nowrap">
                {{ statusLabel(item.status) }}
              </span>
              <router-link
                :to="item.to"
                class="h-[39px] px-4 rounded-[6px] bg-[#d7d7d7] text-[16px] md:text-[20px] tracking-[-1px] text-black hover:bg-rose flex items-center whitespace-nowrap"
              >
                {{ actionLabel(item.status) }}
              </router-link>
            </div>
          </article>
        </div>
      </section>

      <div v-if="!visibleGroups.length" class="bg-[#eaeaea] rounded-[20px] p-8">
        <p class="text-[#9e9e9e]">{{ texts.noMatches }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import iconDocuments from '@/assets/images/icons/dashboard/v2/documents.svg'
import iconPlanning from '@/assets/images/icons/dashboard/v2/document-planning.svg'
import iconRefresh from '@/assets/images/icons/dashboard/v2/refresh.svg'
import iconSearch from '@/assets/images/icons/dashboard/v2/search.svg'
import iconChevron from '@/assets/images/icons/dashboard/v2/chevron.svg'
import {
  documentStatus,
  getDocumentCatalog,
  getDocumentGroups,
  mergeDocumentCatalog,
} from '@/data/worksheets'

const languageStore = useLanguageStore()

const loading = ref(true)
const error = ref(null)
const search = ref('')
const statusFilter = ref('all')
const statuses = ref([])
const catalogExtra = ref([])
const collapsed = reactive({})

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      headerTitle: 'Dokumente',
      intro: 'Jeder Fragebogen, den Peerie dich ausfüllen lässt — hier gespeichert, damit du ihn wiederfindest und aktualisierst, wenn sich dein Business ändert, statt von vorn zu beginnen.',
      libraryTitle: 'Deine Dokumentenbibliothek',
      documentsTotal: 'Dokumente insgesamt',
      refreshTitle: 'Zeit für ein Update',
      refreshIntro: 'Diese wurden seit Monaten nicht angefasst — wenn sich etwas geändert hat, aktualisiere sie, damit der Rest von Peerie stimmt.',
      search: 'Dokumente suchen…',
      allStatuses: 'Alle Status',
      notStarted: 'Nicht begonnen',
      draft: 'Entwurf',
      upToDate: 'Aktuell',
      needsReview: 'Prüfen',
      reviewUpdate: 'Prüfen & aktualisieren',
      view: 'Ansehen',
      continue: 'Weiter',
      start: 'Starten',
      of: 'von',
      neverFilled: 'Noch nicht ausgefüllt',
      updatedDays: (n) => `Aktualisiert vor ${n} Tagen`,
      updatedMonths: (n) => `Aktualisiert vor ${n} Monaten`,
      lastUpdatedMonths: (n) => `Zuletzt aktualisiert vor ${n} Monaten`,
      noMatches: 'Keine Dokumente zu dieser Suche.',
    }
  }

  return {
    headerTitle: 'Documents',
    intro: 'Every questionnaire Peerie asks you to fill out along the way — saved here so you can find it again and update it whenever your business changes, instead of starting over.',
    libraryTitle: 'Your document library',
    documentsTotal: 'documents total',
    refreshTitle: 'Worth a refresh',
    refreshIntro: 'These have not been touched in months — if anything changed, update them so the rest of Peerie stays accurate.',
    search: 'Search documents...',
    allStatuses: 'All statuses',
    notStarted: 'Not started',
    draft: 'Draft',
    upToDate: 'Up to date',
    needsReview: 'Needs review',
    reviewUpdate: 'Review & update',
    view: 'View',
    continue: 'Continue',
    start: 'Start',
    of: 'of',
    neverFilled: 'Never filled out',
    updatedDays: (n) => `Updated ${n} day${n === 1 ? '' : 's'} ago`,
    updatedMonths: (n) => `Updated ${n} month${n === 1 ? '' : 's'} ago`,
    lastUpdatedMonths: (n) => `Last updated ${n} month${n === 1 ? '' : 's'} ago`,
    noMatches: 'No documents match this search.',
  }
})

const filters = computed(() => [
  { id: 'all', label: texts.value.allStatuses },
  { id: 'not_started', label: texts.value.notStarted },
  { id: 'draft', label: texts.value.draft },
  { id: 'up_to_date', label: texts.value.upToDate },
  { id: 'needs_review', label: texts.value.needsReview },
])

const statusMeta = {
  up_to_date: { barClass: 'bg-[#468335]', dotClass: 'bg-[#468335]' },
  draft: { barClass: 'bg-[#ea8f45]', dotClass: 'bg-[#ea8f45]' },
  needs_review: { barClass: 'bg-[#f32d2d]', dotClass: 'bg-[#f32d2d]' },
  not_started: { barClass: 'bg-[#a2a2a2]', dotClass: 'bg-[#a2a2a2]' },
}

const documents = computed(() => {
  const catalog = mergeDocumentCatalog(getDocumentCatalog(languageStore.language), catalogExtra.value)
  const byKind = Object.fromEntries(statuses.value.map((item) => [item.kind, item]))
  return catalog.map((meta) => {
    const live = byKind[meta.kind] || {}
    const status = documentStatus(live)
    return {
      ...meta,
      ...live,
      status,
      to: `/dashboard/documents/${meta.kind.replaceAll('_', '-')}`,
    }
  })
})

const statusBar = computed(() => {
  const order = ['up_to_date', 'draft', 'needs_review', 'not_started']
  const total = documents.value.length || 1
  const labels = {
    up_to_date: texts.value.upToDate,
    draft: texts.value.draft,
    needs_review: texts.value.needsReview,
    not_started: texts.value.notStarted,
  }
  return order.map((id) => {
    const count = documents.value.filter((item) => item.status === id).length
    return {
      id,
      count,
      percent: (count / total) * 100,
      label: labels[id],
      ...statusMeta[id],
    }
  })
})

const refreshItems = computed(() => documents.value.filter((item) => item.status === 'needs_review'))

const filteredDocuments = computed(() => {
  const query = search.value.trim().toLowerCase()
  return documents.value.filter((item) => {
    if (statusFilter.value !== 'all' && item.status !== statusFilter.value) return false
    if (!query) return true
    return `${item.title} ${item.description}`.toLowerCase().includes(query)
  })
})

const visibleGroups = computed(() => getDocumentGroups(languageStore.language)
  .map((group) => {
    const items = filteredDocuments.value.filter((item) => item.group === group.id)
    return {
      ...group,
      items,
      upToDateCount: items.filter((item) => item.status === 'up_to_date').length,
    }
  })
  .filter((group) => group.items.length > 0))

const iconFor = (item) => (item.group === 'planning' ? iconPlanning : iconDocuments)

const groupTitle = (groupId) => getDocumentGroups(languageStore.language).find((group) => group.id === groupId)?.title || ''

const statusLabel = (status) => ({
  not_started: texts.value.notStarted,
  draft: texts.value.draft,
  up_to_date: texts.value.upToDate,
  needs_review: texts.value.needsReview,
}[status] || status)

const actionLabel = (status) => ({
  not_started: texts.value.start,
  draft: texts.value.continue,
  up_to_date: texts.value.view,
  needs_review: texts.value.reviewUpdate,
}[status] || texts.value.view)

const updatedLabel = (item, variant = 'default') => {
  if (!item.filled || !item.updated_at) return texts.value.neverFilled
  const diff = Date.now() - new Date(item.updated_at).getTime()
  const days = Math.max(1, Math.round(diff / (24 * 60 * 60 * 1000)))
  if (days < 30) return texts.value.updatedDays(days)
  const months = Math.max(1, Math.round(days / 30))
  return variant === 'short' ? texts.value.lastUpdatedMonths(months) : texts.value.updatedMonths(months)
}

const toggleGroup = (id) => {
  collapsed[id] = !collapsed[id]
}

const fetchDocuments = async () => {
  try {
    loading.value = true
    error.value = null
    const response = await axios.get('/api/worksheets')
    statuses.value = response.data?.worksheets || []
    catalogExtra.value = response.data?.catalog || []
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load documents'
  } finally {
    loading.value = false
  }
}

onMounted(fetchDocuments)
</script>
