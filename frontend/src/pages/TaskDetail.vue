<template>
  <div class="max-w-[1440px] mx-auto pb-10">
    <button
      type="button"
      class="inline-flex items-center gap-2 text-[14px] tracking-[-0.7px] text-purple hover:text-red mb-5"
      @click="goBack"
    >
      <span class="text-[18px] leading-none">←</span>
      {{ texts.back }}
    </button>

    <div v-if="isLoading" class="text-center py-16">
      <svg class="w-12 h-12 text-red mx-auto mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
    </div>

    <div v-else-if="error" class="bg-white rounded-[30px] shadow-card p-8 text-center">
      <p class="text-purple">{{ error }}</p>
    </div>

    <div v-else-if="task">
      <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div class="min-w-0">
          <h1 class="text-[32px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">
            {{ task.title }}
          </h1>
          <div class="mt-4 flex flex-wrap gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-grey text-purple">
              {{ getTaskMinutes(task) }} min
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green/10 text-green">
              {{ formatFrequency(task.frequency) }}
            </span>
            <span
              v-if="task.category"
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-rose text-red"
            >
              {{ task.category }}
            </span>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
          <router-link
            v-if="worksheetKind"
            :to="`/dashboard/documents/${worksheetSlug}`"
            class="h-[42px] px-5 rounded-[20px] bg-light-grey border-4 border-rose text-[16px] font-bold tracking-[-0.8px] text-black hover:bg-rose flex items-center"
          >
            {{ texts.openTemplate }}
          </router-link>
          <span class="text-[13px] tracking-[-0.6px]" :class="saveStatusClass">
            {{ saveStatusLabel }}
          </span>
          <button
            type="button"
            class="h-[42px] px-5 rounded-[20px] text-[16px] font-bold tracking-[-0.8px] transition-colors"
            :class="completed
              ? 'bg-green text-white'
              : 'bg-red text-white shadow-red hover:bg-red-dark'"
            :disabled="isSaving"
            @click="toggleCompleted"
          >
            {{ completed ? texts.markOpen : texts.markDone }}
          </button>
        </div>
      </div>

      <section
        v-if="parsed.introHtml.trim()"
        class="bg-white rounded-[30px] shadow-card p-5 md:p-8 mb-5"
      >
        <div class="prose max-w-none text-purple leading-relaxed instruction-content" v-html="introContent" />
      </section>

      <template v-if="worksheetKind && worksheetSchema.layout === 'fields'">
        <section
          v-for="stepNumber in fieldSteps"
          :key="`ws-${stepNumber}`"
          class="bg-white rounded-[30px] shadow-card p-5 md:p-8 mb-5"
        >
          <div
            v-if="stepInstruction(stepNumber)"
            class="prose max-w-none text-purple leading-relaxed instruction-content mb-6"
            v-html="stepInstruction(stepNumber)"
          />
          <WorksheetFields
            :schema="worksheetSchema"
            :document="document"
            :only-step="stepNumber"
            plain
            :show-step-titles="!stepInstruction(stepNumber)"
            :add-label="texts.addRow"
            :remove-label="texts.removeRow"
          />
        </section>
      </template>

      <template v-else-if="worksheetKind">
        <section
          v-for="(step, index) in parsed.steps"
          :key="`step-${index}`"
          class="bg-white rounded-[30px] shadow-card p-5 md:p-8 mb-5"
        >
          <div
            class="prose max-w-none text-purple leading-relaxed instruction-content"
            v-html="step.html"
          />
        </section>
        <WorksheetFields
          class="mb-5"
          :schema="worksheetSchema"
          :document="document"
          :add-label="texts.addRow"
          :remove-label="texts.removeRow"
        />
      </template>

      <template v-else>
        <section
          v-for="(step, index) in parsed.steps"
          :key="`step-${index}`"
          class="bg-white rounded-[30px] shadow-card p-5 md:p-8 mb-5"
        >
          <div
            class="prose max-w-none text-purple leading-relaxed instruction-content mb-6"
            v-html="step.html"
          />
          <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-2">
            {{ texts.yourNotes }}
          </label>
          <textarea
            v-model="stepNotes[index]"
            rows="5"
            :placeholder="texts.notesPlaceholder"
            class="w-full rounded-[16px] border border-grey bg-light-grey px-4 py-3 text-[16px] text-black tracking-[-0.8px] outline-none focus:border-red resize-y"
          />
        </section>

        <section
          v-if="parsed.steps.length === 0"
          class="bg-white rounded-[30px] shadow-card p-5 md:p-8 mb-5"
        >
          <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-2">
            {{ texts.yourNotes }}
          </label>
          <textarea
            v-model="generalNotes"
            rows="8"
            :placeholder="texts.notesPlaceholder"
            class="w-full rounded-[16px] border border-grey bg-light-grey px-4 py-3 text-[16px] text-black tracking-[-0.8px] outline-none focus:border-red resize-y"
          />
        </section>
      </template>

      <section
        v-if="parsed.outroHtml.trim()"
        class="bg-white rounded-[30px] shadow-card p-5 md:p-8"
      >
        <div class="prose max-w-none text-purple leading-relaxed instruction-content" v-html="parsed.outroHtml" />
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import { getTaskWorksheetKind, parseInstruction } from '@/composables/useInstruction'
import WorksheetFields from '@/components/WorksheetFields.vue'
import {
  applyDocument,
  emptyWorksheetDocument,
  getWorksheetSchema,
  kindToSlug,
  schemaFromTask,
} from '@/data/worksheets'

const route = useRoute()
const router = useRouter()
const languageStore = useLanguageStore()

const isLoading = ref(true)
const error = ref('')
const task = ref(null)
const planId = ref(null)
const completed = ref(false)
const document = reactive(emptyWorksheetDocument('buyer_persona'))
const stepNotes = reactive({})
const generalNotes = ref('')
const isHydrated = ref(false)
const isSaving = ref(false)
const saveState = ref('idle')
let saveTimer = null

const worksheetKind = computed(() => getTaskWorksheetKind(task.value))
const worksheetSlug = computed(() => (worksheetKind.value ? kindToSlug(worksheetKind.value) : ''))
const worksheetSchema = computed(() => {
  if (task.value?.document_fields?.length) return schemaFromTask(task.value)
  return getWorksheetSchema(worksheetKind.value, languageStore.language)
})
const parsed = computed(() => parseInstruction(task.value?.description || ''))
const fieldSteps = computed(() => {
  const steps = new Set((worksheetSchema.value.fields || []).map((field) => field.step).filter(Boolean))
  return [...steps].sort((a, b) => a - b)
})

const introContent = computed(() => {
  if (parsed.value.titleHtml && !String(task.value?.description || '').includes('<h1')) {
    return `${parsed.value.titleHtml}${parsed.value.introHtml}`
  }
  return parsed.value.introHtml
})

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      back: 'Zurück',
      markDone: 'Als erledigt markieren',
      markOpen: 'Wieder öffnen',
      openTemplate: 'Vorlage öffnen',
      yourNotes: 'Deine Notizen',
      notesPlaceholder: 'Schreib deine Antworten hier…',
      addRow: 'Zeile hinzufügen',
      removeRow: 'Löschen',
      saving: 'Speichert…',
      saved: 'Gespeichert',
      saveError: 'Speichern fehlgeschlagen',
      notFound: 'Aufgabe nicht gefunden',
    }
  }

  return {
    back: 'Back',
    markDone: 'Mark as done',
    markOpen: 'Reopen',
    openTemplate: 'Open template',
    yourNotes: 'Your notes',
    notesPlaceholder: 'Write your answers here…',
    addRow: 'Add row',
    removeRow: 'Remove',
    saving: 'Saving…',
    saved: 'Saved',
    saveError: 'Could not save',
    notFound: 'Task not found',
  }
})

const saveStatusLabel = computed(() => {
  if (saveState.value === 'saving') return texts.value.saving
  if (saveState.value === 'saved') return texts.value.saved
  if (saveState.value === 'error') return texts.value.saveError
  return ''
})

const saveStatusClass = computed(() => {
  if (saveState.value === 'error') return 'text-red'
  if (saveState.value === 'saved') return 'text-green'
  return 'text-purple/70'
})

const stepInstruction = (stepNumber) => {
  const step = parsed.value.steps[stepNumber - 1]
  return step?.html || ''
}

const getTaskMinutes = (currentTask) => {
  if (!currentTask) return 0
  if (currentTask.duration_minutes !== undefined && currentTask.duration_minutes !== null) {
    return currentTask.duration_minutes
  }
  return 60
}

const formatFrequency = (frequency) => {
  const map = languageStore.language === 'de'
    ? {
        once: 'Einmalig',
        weekly: 'Wöchentlich',
        bi_weekly: 'Alle 2 Wochen',
        monthly: 'Monatlich',
        quarterly: 'Vierteljährlich',
        half_yearly: 'Halbjährlich',
        yearly: 'Jährlich',
      }
    : {
        once: 'Once',
        weekly: 'Weekly',
        bi_weekly: 'Bi-weekly',
        monthly: 'Monthly',
        quarterly: 'Quarterly',
        half_yearly: 'Half-yearly',
        yearly: 'Yearly',
      }

  if (!frequency) return map.once
  const key = frequency.toLowerCase()
  return map[key] || key
}

const resetDocument = (kind) => {
  const empty = emptyWorksheetDocument(kind, worksheetSchema.value)
  Object.keys(document).forEach((key) => {
    delete document[key]
  })
  Object.assign(document, empty)
  if (empty.fields) document.fields = reactive({ ...empty.fields })
  if (empty.rows) document.rows = reactive(empty.rows.map((row) => ({ ...row })))
  if (empty.categories) {
    document.categories = reactive(
      Object.fromEntries(Object.entries(empty.categories).map(([key, items]) => [key, [...items]]))
    )
  }
}

const buildNotesPayload = () => {
  if (worksheetKind.value) {
    return JSON.stringify({
      type: worksheetKind.value,
      fields: { ...(document.fields || {}) },
      rows: [...(document.rows || [])].map((row) => ({ ...row })),
      categories: Object.fromEntries(
        Object.entries(document.categories || {}).map(([key, items]) => [key, [...items]])
      ),
    })
  }

  if (parsed.value.steps.length > 0) {
    return JSON.stringify({
      type: 'steps',
      steps: { ...stepNotes },
    })
  }

  return JSON.stringify({
    type: 'notes',
    text: generalNotes.value,
  })
}

const hydrateNotes = (notes) => {
  Object.keys(stepNotes).forEach((key) => {
    delete stepNotes[key]
  })
  generalNotes.value = ''

  if (worksheetKind.value) {
    resetDocument(worksheetKind.value)
    if (!notes) return
    try {
      const parsedNotes = JSON.parse(notes)
      if (parsedNotes && typeof parsedNotes === 'object') {
        applyDocument(document, parsedNotes, worksheetKind.value, worksheetSchema.value)
      }
    } catch {
      generalNotes.value = notes
    }
    return
  }

  if (!notes) return

  try {
    const parsedNotes = JSON.parse(notes)
    if (parsedNotes && typeof parsedNotes === 'object') {
      if (parsedNotes.steps && typeof parsedNotes.steps === 'object') {
        Object.entries(parsedNotes.steps).forEach(([key, value]) => {
          stepNotes[key] = value || ''
        })
      }
      if (typeof parsedNotes.text === 'string') {
        generalNotes.value = parsedNotes.text
      }
      return
    }
  } catch {
    generalNotes.value = notes
  }
}

const saveNotes = async () => {
  if (!planId.value || !route.params.planTaskId || !isHydrated.value) return

  isSaving.value = true
  saveState.value = 'saving'
  try {
    await axios.put(`/api/plan/${planId.value}/plan-task/${route.params.planTaskId}`, {
      notes: buildNotesPayload(),
    })
    saveState.value = 'saved'
  } catch (saveError) {
    console.error('Failed to save task notes:', saveError)
    saveState.value = 'error'
  } finally {
    isSaving.value = false
  }
}

const scheduleSave = () => {
  if (!isHydrated.value) return
  saveState.value = 'saving'
  if (saveTimer) {
    clearTimeout(saveTimer)
  }
  saveTimer = setTimeout(() => {
    saveNotes()
  }, 700)
}

const toggleCompleted = async () => {
  if (!planId.value || !route.params.planTaskId) return
  const nextValue = !completed.value
  completed.value = nextValue
  isSaving.value = true
  try {
    await axios.put(`/api/plan/${planId.value}/plan-task/${route.params.planTaskId}`, {
      completed: nextValue,
      notes: buildNotesPayload(),
    })
    saveState.value = 'saved'
  } catch (saveError) {
    completed.value = !nextValue
    console.error('Failed to update task status:', saveError)
    saveState.value = 'error'
  } finally {
    isSaving.value = false
  }
}

const goBack = () => {
  const from = route.query.from
  if (from === 'dashboard') {
    router.push({ path: '/dashboard' })
    return
  }
  if (from === 'learn' || from === 'documents') {
    const slug = String(route.query.worksheet || worksheetSlug.value || '')
    router.push({
      path: slug ? `/dashboard/documents/${slug}` : '/dashboard/documents',
    })
    return
  }

  const query = {}
  if (route.query.year) query.year = route.query.year
  if (route.query.month) query.month = route.query.month
  router.push({ path: '/dashboard/marketing-plans', query })
}

const fetchTask = async () => {
  isLoading.value = true
  error.value = ''
  isHydrated.value = false
  try {
    const response = await axios.get(`/api/plan-tasks/${route.params.planTaskId}`)
    if (!response.data?.success) {
      error.value = texts.value.notFound
      return
    }

    task.value = response.data.task
    planId.value = response.data.plan_id
    completed.value = Boolean(response.data.plan_task?.completed)
    hydrateNotes(response.data.plan_task?.notes)
    await nextTick()
    isHydrated.value = true
    saveState.value = 'idle'
  } catch (fetchError) {
    console.error('Failed to load task:', fetchError)
    error.value = texts.value.notFound
  } finally {
    isLoading.value = false
  }
}

watch(document, () => {
  if (worksheetKind.value) scheduleSave()
}, { deep: true })

watch(stepNotes, () => {
  if (!worksheetKind.value) scheduleSave()
}, { deep: true })

watch(generalNotes, () => {
  if (!worksheetKind.value) scheduleSave()
})

watch(() => route.params.planTaskId, () => {
  fetchTask()
})

onMounted(fetchTask)

onUnmounted(() => {
  if (saveTimer) {
    clearTimeout(saveTimer)
  }
})
</script>

<style scoped>
.instruction-content {
  font-family: 'Manrope', system-ui, sans-serif;
}

.instruction-content :deep(ul) {
  list-style-type: disc;
  padding-left: 1.5rem;
  margin-left: 0;
}

.instruction-content :deep(ol) {
  list-style-type: decimal;
  padding-left: 1.5rem;
  margin-left: 0;
}

.instruction-content :deep(p),
.instruction-content :deep(li) {
  font-family: 'Manrope', system-ui, sans-serif;
}

.instruction-content :deep(p) {
  margin-bottom: 0.75rem;
}

.instruction-content :deep(h1),
.instruction-content :deep(h2),
.instruction-content :deep(h3) {
  font-family: 'Switzer', system-ui, sans-serif;
  color: rgb(var(--color-black));
}
</style>
