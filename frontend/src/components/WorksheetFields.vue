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

    <section
      v-if="schema.layout === 'rows' && onlyStep == null"
      :class="plain ? '' : 'bg-white rounded-[30px] shadow-card p-5 md:p-8'"
    >
      <div class="space-y-4">
        <div
          v-for="(row, index) in (document.rows || [])"
          :key="`row-${index}`"
          class="bg-light-grey rounded-[20px] border-4 border-rose p-4"
        >
          <div class="flex items-start justify-end mb-3">
            <button
              type="button"
              class="h-[36px] px-4 rounded-[16px] border border-grey bg-white text-[13px] font-bold tracking-[-0.6px] text-red hover:bg-rose"
              @click="removeRow(index)"
            >
              {{ removeLabel }}
            </button>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div
              v-for="key in schema.rowKeys"
              :key="`${index}-${key}`"
              :class="key === 'notes' || key === 'topic' ? 'md:col-span-2' : ''"
            >
              <label class="block text-[13px] font-bold tracking-[-0.6px] text-black mb-1">
                {{ schema.labels[key] }}
              </label>
              <input
                v-model="row[key]"
                type="text"
                :placeholder="schema.placeholders?.[key]"
                class="w-full rounded-[16px] border border-grey bg-white px-3 py-2 text-[15px] text-black tracking-[-0.7px] outline-none focus:border-red"
              >
            </div>
          </div>
        </div>
      </div>
      <button
        type="button"
        class="mt-4 h-[42px] px-5 rounded-[20px] bg-light-grey border-4 border-rose text-[16px] font-bold tracking-[-0.8px] text-black hover:bg-rose"
        @click="addRow"
      >
        {{ addLabel }}
      </button>
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
})

const showFields = computed(() => props.schema.layout === 'fields' || (props.schema.fields || []).length > 0)

const fieldSteps = computed(() => {
  if (props.onlyStep != null) return [props.onlyStep]
  const steps = new Set((props.schema.fields || []).map((field) => field.step).filter(Boolean))
  return [...steps].sort((a, b) => a - b)
})

const fieldsForStep = (stepNumber) => (props.schema.fields || []).filter((field) => field.step === stepNumber)

const addRow = () => {
  if (!Array.isArray(props.document.rows)) props.document.rows = []
  const row = {}
  ;(props.schema.rowKeys || []).forEach((key) => {
    row[key] = ''
  })
  props.document.rows.push(row)
}

const removeRow = (index) => {
  if (!Array.isArray(props.document.rows)) return
  props.document.rows.splice(index, 1)
}

const removeCategoryItem = (key, index) => {
  const items = props.document.categories?.[key]
  if (!Array.isArray(items)) return
  items.splice(index, 1)
  if (!items.length) items.push('')
}
</script>
