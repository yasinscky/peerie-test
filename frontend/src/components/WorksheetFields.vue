<template>
  <div class="space-y-5">
    <template v-if="schema.layout === 'categories' && onlyStep == null">
    <section
      v-for="key in schema.categoryKeys"
      :key="`cat-${key}`"
      :class="plain ? '' : 'bg-white rounded-[30px] shadow-card p-5 md:p-8'"
    >
      <h2 class="text-[20px] md:text-[24px] font-bold tracking-[-1.2px] text-black mb-4">
        {{ schema.labels[key] }}
      </h2>
      <div class="space-y-3">
        <div
          v-for="(_, index) in (document.categories?.[key] || [])"
          :key="`${key}-${index}`"
          class="flex items-center gap-2"
        >
          <input
            v-model="document.categories[key][index]"
            type="text"
            :placeholder="schema.placeholders?.idea"
            class="w-full rounded-[16px] border border-grey bg-light-grey px-4 py-3 text-[16px] text-black tracking-[-0.8px] outline-none focus:border-red"
          >
          <button
            type="button"
            class="shrink-0 h-[48px] px-4 rounded-[16px] border border-grey bg-white text-[14px] font-bold tracking-[-0.7px] text-red hover:bg-rose"
            @click="removeCategoryItem(key, index)"
          >
            {{ removeLabel }}
          </button>
        </div>
      </div>
      <button
        type="button"
        class="mt-4 h-[42px] px-5 rounded-[20px] bg-light-grey border-4 border-rose text-[16px] font-bold tracking-[-0.8px] text-black hover:bg-rose"
        @click="document.categories[key].push('')"
      >
        {{ addLabel }}
      </button>
    </section>
    </template>

    <template v-if="taskSections.length && onlyStep == null">
      <section
        v-for="section in taskSections"
        :key="`task-${section.plan_task_id}`"
        :class="plain ? '' : 'bg-white rounded-[30px] shadow-card p-5 md:p-8'"
      >
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
          <h2 class="text-[20px] md:text-[24px] font-bold tracking-[-1.2px] text-black">
            {{ section.title }}
          </h2>
          <router-link
            v-if="section.plan_task_id"
            :to="{ name: 'TaskDetail', params: { planTaskId: section.plan_task_id }, query: { from: 'documents', worksheet: worksheetSlug } }"
            class="shrink-0 text-[14px] font-bold tracking-[-0.7px] text-red hover:text-red-dark"
          >
            {{ openTaskLabel }}
          </router-link>
        </div>
        <div class="overflow-x-auto">
          <table class="worksheet-table">
            <thead>
              <tr>
                <th v-for="(header, headerIndex) in sectionHeaders(section)" :key="`head-${section.plan_task_id}-${headerIndex}`">
                  {{ header }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, index) in section.rows" :key="`${section.plan_task_id}-${index}`">
                <td v-for="key in schema.rowKeys" :key="`${section.plan_task_id}-${index}-${key}`">
                  <span v-if="key === labelColumnKey" class="worksheet-table-label">{{ row[key] }}</span>
                  <input
                    v-else
                    v-model="row[key]"
                    type="text"
                    :placeholder="schema.placeholders?.[key] || schema.labels[key]"
                    class="worksheet-table-input"
                  >
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </template>

    <section
      v-else-if="(schema.rowKeys || []).length && onlyStep == null"
      :class="plain ? '' : 'bg-white rounded-[30px] shadow-card p-5 md:p-8'"
    >
      <h2
        v-if="schema.rowTitle"
        class="text-[20px] md:text-[24px] font-bold tracking-[-1.2px] text-black mb-4"
      >
        {{ schema.rowTitle }}
      </h2>
      <div class="overflow-x-auto">
        <table class="worksheet-table">
          <thead>
            <tr>
              <th v-for="key in schema.rowKeys" :key="`head-${key}`">
                {{ schema.labels[key] }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in (document.rows || [])" :key="`row-${index}`">
              <td v-for="key in schema.rowKeys" :key="`${index}-${key}`">
                <span v-if="key === labelColumnKey" class="worksheet-table-label">{{ row[key] }}</span>
                <input
                  v-else
                  v-model="row[key]"
                  type="text"
                  :placeholder="schema.placeholders?.[key] || schema.labels[key]"
                  class="worksheet-table-input"
                >
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <template v-if="(schema.fields || []).length && showFields">
      <component
        :is="plain ? 'div' : 'section'"
        v-for="stepNumber in fieldSteps"
        :key="`ws-step-${stepNumber}`"
        :class="plain ? '' : 'bg-white rounded-[30px] shadow-card p-5 md:p-8'"
      >
        <h2
          v-if="showStepTitles && schema.stepTitles?.[stepNumber]"
          class="text-[20px] md:text-[24px] font-bold tracking-[-1.2px] text-black mb-5"
        >
          {{ schema.stepTitles[stepNumber] }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="field in fieldsForStep(stepNumber)"
            :key="field.key"
            :class="field.type === 'textarea' ? 'md:col-span-2' : ''"
          >
            <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-2">
              {{ schema.labels[field.key] }}
            </label>
            <textarea
              v-if="field.type === 'textarea'"
              v-model="document.fields[field.key]"
              :rows="field.rows || 3"
              :placeholder="schema.placeholders?.[field.key]"
              class="w-full rounded-[16px] border border-grey bg-light-grey px-4 py-3 text-[16px] text-black tracking-[-0.8px] outline-none focus:border-red resize-y"
            />
            <input
              v-else
              v-model="document.fields[field.key]"
              type="text"
              :placeholder="schema.placeholders?.[field.key]"
              class="w-full rounded-[16px] border border-grey bg-light-grey px-4 py-3 text-[16px] text-black tracking-[-0.8px] outline-none focus:border-red"
            >
          </div>
        </div>
      </component>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  schema: { type: Object, required: true },
  document: { type: Object, required: true },
  addLabel: { type: String, default: '+' },
  removeLabel: { type: String, default: 'Remove' },
  onlyStep: { type: Number, default: null },
  plain: { type: Boolean, default: false },
  showStepTitles: { type: Boolean, default: true },
  openTaskLabel: { type: String, default: 'Open task' },
  worksheetSlug: { type: String, default: '' },
})

const showFields = computed(() => props.schema.layout === 'fields' || (props.schema.fields || []).length > 0)

const fieldSteps = computed(() => {
  if (props.onlyStep != null) return [props.onlyStep]
  const steps = new Set((props.schema.fields || []).map((field) => field.step).filter(Boolean))
  return [...steps].sort((a, b) => a - b)
})

const fieldsForStep = (stepNumber) => (props.schema.fields || []).filter((field) => field.step === stepNumber)

const ANSWER_LABEL = /^(your (?:answer|answers|input|plan|notes|hashtags|campaign)|this month|deine (?:antwort|antworten|angaben|notizen|hashtags|kampagne)|dein plan|dieser monat)$/i

const taskSections = computed(() => (Array.isArray(props.document.sections) ? props.document.sections : []))

const sectionHeaders = (section) => {
  const headers = Array.isArray(section?.headers) ? section.headers.filter(Boolean) : []
  if (headers.length >= (props.schema.rowKeys || []).length) return headers
  return (props.schema.rowKeys || []).map((key) => props.schema.labels?.[key] || key)
}

const labelColumnKey = computed(() => {
  const keys = props.schema.rowKeys || []
  if (keys.length !== 2) return null
  const answerLabel = String(props.schema.labels?.[keys[1]] || '').trim()
  return ANSWER_LABEL.test(answerLabel) ? keys[0] : null
})

const removeCategoryItem = (key, index) => {
  const items = props.document.categories?.[key]
  if (!Array.isArray(items)) return
  items.splice(index, 1)
  if (!items.length) items.push('')
}
</script>

<style scoped>
.worksheet-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

.worksheet-table th,
.worksheet-table td {
  border: 1px solid rgb(var(--color-grey));
  padding: 0.625rem 0.75rem;
  text-align: left;
  vertical-align: middle;
}

.worksheet-table th {
  background: rgb(var(--color-light-grey));
  font-weight: 700;
  color: rgb(var(--color-black));
}

.worksheet-table-label {
  display: block;
  color: rgb(var(--color-purple));
}

.worksheet-table-input {
  width: 100%;
  min-height: 2.25rem;
  border: 0;
  border-radius: 10px;
  background: transparent;
  padding: 0.25rem 0.625rem;
  outline: none;
  font: inherit;
  color: rgb(var(--color-black));
}

.worksheet-table-input::placeholder {
  color: rgb(var(--color-purple) / 0.45);
}

.worksheet-table-input:placeholder-shown,
.worksheet-table-input:focus {
  background: rgb(var(--color-light-grey));
  box-shadow: inset 0 0 0 1px rgb(var(--color-grey));
}
</style>
