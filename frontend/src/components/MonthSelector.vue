<template>
  <div class="bg-white rounded-[17px] border border-[#DCDCDC] p-3">
    <div class="space-y-1.5">
      <button
        v-for="monthOption in availableMonths"
        :key="`${monthOption.year}-${monthOption.month}`"
        type="button"
        class="w-full flex items-center space-x-2 p-2 rounded-lg border transition-all"
        :class="isSelected(monthOption.year, monthOption.month)
          ? 'border-[#f34767] bg-[#f34767] text-white'
          : 'border-[#DCDCDC] bg-white text-[#3F4369] hover:bg-[#FFEBD0]'"
        @click="selectMonth(monthOption.year, monthOption.month)"
      >
        <div class="w-3 h-3 rounded border-2 flex items-center justify-center flex-shrink-0"
          :class="isSelected(monthOption.year, monthOption.month)
            ? 'bg-white border-white'
            : 'border-[#3F4369]'">
          <svg v-if="isSelected(monthOption.year, monthOption.month)" class="w-2 h-2 text-[#f34767]" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
        </div>
        <span class="text-sm font-medium">
          {{ formatMonth(monthOption.year, monthOption.month) }}
        </span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useLanguageStore } from '@/stores/language'

const props = defineProps({
  availableMonths: {
    type: Array,
    default: () => []
  },
  selectedYear: {
    type: Number,
    default: null
  },
  selectedMonth: {
    type: Number,
    default: null
  }
})

const emit = defineEmits(['select'])

const languageStore = useLanguageStore()

const texts = computed(() => {
  if (languageStore.language === 'de') {
    return {
      title: 'Monat auswählen'
    }
  }
  return {
    title: 'Select Month'
  }
})

const isSelected = (year, month) => {
  return props.selectedYear === year && props.selectedMonth === month
}

const selectMonth = (year, month) => {
  emit('select', { year, month })
}

const formatMonth = (year, month) => {
  const date = new Date(year, month - 1, 1)
  const monthNames = languageStore.language === 'de'
    ? ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
    : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
  
  return `${monthNames[month - 1]} ${year}`
}
</script>
