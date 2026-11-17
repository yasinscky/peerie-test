<template>
  <div class="max-w-[1440px] mx-auto">
    <div class="mb-6 md:mb-8">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
          <h1 class="text-[32px] md:text-[40px] font-bold tracking-[-2px] text-black leading-none">
            {{ texts.welcome }}, {{ welcomeName }}
          </h1>
          <div class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-[14px] text-purple/70 tracking-[-0.7px]">
            <span>{{ todayLabel }}</span>
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
                {{ texts.planTitle }}
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
                {{ weekCompletedCount }}/{{ weekTaskCount }}
              </p>
              <p class="mt-2 text-[14px] font-medium tracking-[-0.7px] text-purple">{{ texts.tasksCompleted }}</p>
            </div>
            <div>
              <div class="h-2.5 w-[100px] rounded-[10px] bg-yellow mb-3" />
              <p class="text-[36px] font-bold tracking-[-1.8px] text-black leading-none">{{ daysLeftLabel }}</p>
              <p class="mt-2 text-[14px] font-medium tracking-[-0.7px] text-purple">{{ texts.leftInStage }}</p>
            </div>
            <div>
              <div class="h-2.5 w-[100px] rounded-[10px] bg-grey mb-3 overflow-hidden">
                <div class="h-full bg-red rounded-[10px]" :style="{ width: `${Math.min(progressPercent, 100)}%` }" />
              </div>
              <p class="text-[36px] font-bold tracking-[-1.8px] text-black leading-none">{{ weekPendingCount }}</p>
              <p class="mt-2 text-[14px] font-medium tracking-[-0.7px] text-purple">{{ texts.pendingTasks }}</p>
            </div>
          </div>
        </section>

        <section class="bg-white rounded-[30px] shadow-card p-4 md:p-5">
          <div class="flex flex-wrap items-center justify-between gap-2 mb-4 px-1">
            <div class="flex flex-wrap items-center gap-3">
              <h3 class="text-[24px] font-bold tracking-[-1.2px] text-black">{{ texts.actionSteps }}</h3>
              <span class="text-[14px] text-purple/70 tracking-[-0.7px]">{{ weekRangeLabel }}</span>
            </div>
            <router-link
              :to="{ path: '/dashboard/action-steps', query: { year: selectedYear, month: selectedMonth } }"
              class="inline-flex items-center gap-1 text-[16px] text-purple tracking-[-0.8px] hover:text-red"
            >
              {{ texts.allTasks }}
              <img :src="iconArrowLink" alt="" class="w-4 h-4">
            </router-link>
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

            <div v-if="visibleActionTasks.length === 0" class="py-10 text-center text-purple/70">
              {{ texts.noTasks }}
            </div>
          </div>
        </section>
      </div>

      <aside class="space-y-5">
        <section class="bg-white rounded-[30px] shadow-card p-5">
          <div class="flex items-center gap-3 mb-4">
            <h3 class="text-[32px] font-bold tracking-[-1.6px] text-black leading-none">{{ texts.progress }}</h3>
            <div ref="weekMenuRoot" class="relative">
              <button
                type="button"
                class="inline-flex items-center gap-2"
                @click="weekMenuOpen = !weekMenuOpen"
              >
                <span class="text-[16px] font-bold tracking-[-0.8px] text-black">
                  {{ texts.week }} {{ selectedWeek }}
                </span>
                <span class="w-5 h-5 rounded-[12px] bg-yellow flex items-center justify-center shrink-0">
                  <img
                    :src="iconChevronDown"
                    alt=""
                    class="w-4 h-4 transition-transform"
                    :class="weekMenuOpen ? 'rotate-180' : ''"
                  >
                </span>
              </button>
              <div
                v-if="weekMenuOpen"
                class="absolute left-0 top-full z-20 mt-2 min-w-[190px] bg-white rounded-[16px] border border-grey shadow-card p-1.5"
              >
                <button
                  v-for="option in weekOptions"
                  :key="option.week"
                  type="button"
                  class="w-full text-left px-3 py-2 rounded-[12px] transition-colors"
                  :class="selectedWeek === option.week
                    ? 'bg-red text-white'
                    : 'text-black hover:bg-rose'"
                  @click="selectWeek(option.week)"
                >
                  <span class="block text-[14px] font-bold tracking-[-0.7px]">{{ option.label }}</span>
                  <span
                    class="block text-[12px] tracking-[-0.6px]"
                    :class="selectedWeek === option.week ? 'text-white/80' : 'text-purple/70'"
                  >
                    {{ option.range }}
                  </span>
                </button>
              </div>
            </div>
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
                {{ weekCompletedCount }} {{ texts.of }} {{ weekTaskCount }} {{ texts.tasks }}
              </p>
            </div>
          </div>
          <div class="mt-5 space-y-2 text-[14px] tracking-[-0.7px]">
            <div class="flex items-center justify-between text-black">
              <span>{{ texts.completed }}</span>
              <span class="font-bold">{{ weekCompletedCount }}</span>
            </div>
            <div class="flex items-center justify-between text-black">
              <span>{{ texts.pending }}</span>
              <span class="font-bold">{{ weekPendingCount }}</span>
            </div>
            <div class="flex items-center justify-between text-black">
              <span>{{ texts.total }}</span>
              <span class="font-bold">{{ weekTaskCount }}</span>
            </div>
          </div>
        </section>

        <section class="bg-white rounded-[30px] shadow-card p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-[20px] font-bold tracking-[-1px] text-black">{{ texts.quickLinks }}</h3>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <router-link
              to="/dashboard/content-calendar"
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
              to="/dashboard/documents"
              class="bg-light-grey rounded-[20px] border-4 border-rose p-3 hover:bg-rose transition-colors"
            >
              <img :src="iconDocuments" alt="" class="w-[14px] h-[14px] mb-2">
              <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-purple">{{ texts.documents }}</p>
            </router-link>
            <router-link
              to="/dashboard/hashtags"
              class="bg-light-grey rounded-[20px] border-4 border-rose p-3 hover:bg-rose transition-colors"
            >
              <img :src="iconHashtags" alt="" class="w-4 h-4 mb-2">
              <p class="text-[12px] font-bold uppercase tracking-[-0.6px] text-purple">{{ texts.hashtags }}</p>
            </router-link>
          </div>
        </section>

        <section class="bg-white rounded-[30px] shadow-card p-5">
          <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0" />
            </svg>
            <h3 class="text-[24px] font-bold tracking-[-1.2px] text-black leading-none">{{ texts.notifications }}</h3>
            <button
              type="button"
              class="ml-auto text-[14px] tracking-[-0.7px] text-purple/70 hover:text-black disabled:opacity-40"
              :disabled="!hasUnreadAnnouncements || markingRead"
              @click="markAllRead"
            >
              {{ texts.markAllRead }}
            </button>
          </div>
          <p v-if="!notifications.length" class="text-[15px] text-purple/70">{{ texts.noNotifications }}</p>
          <ul v-else class="space-y-4">
            <li
              v-for="(item, index) in notifications"
              :key="item.id"
              class="list-disc ml-5"
              :class="index > 0 ? 'border-t border-grey pt-4' : ''"
            >
              <router-link v-if="item.to" :to="item.to" class="block">
                <p class="text-[16px] font-bold tracking-[-0.8px] text-black leading-tight">{{ item.title }}</p>
                <p class="mt-1 text-[14px] text-purple/70 leading-snug">{{ item.body }}</p>
                <p v-if="item.published_at" class="mt-1 text-[12px] uppercase tracking-[-0.4px] text-purple/50">{{ formatNotificationDate(item.published_at) }}</p>
              </router-link>
              <template v-else>
                <p class="text-[16px] font-bold tracking-[-0.8px] text-black leading-tight">{{ item.title }}</p>
                <p class="mt-1 text-[14px] text-purple/70 leading-snug">{{ item.body }}</p>
                <p v-if="item.published_at" class="mt-1 text-[12px] uppercase tracking-[-0.4px] text-purple/50">{{ formatNotificationDate(item.published_at) }}</p>
              </template>
            </li>
          </ul>
        </section>
      </aside>
    </div>

    <section class="mt-5 bg-white rounded-[30px] shadow-card p-4 md:p-5">
      <div class="flex flex-wrap items-center justify-between gap-2 mb-4 px-1">
        <div class="flex flex-wrap items-center gap-3">
          <h3 class="text-[24px] font-bold tracking-[-1.2px] text-black">{{ texts.contentCalendar }}</h3>
          <span class="text-[14px] text-purple/70 tracking-[-0.7px]">{{ texts.thisWeek }}</span>
        </div>
        <router-link
          to="/dashboard/content-calendar"
          class="inline-flex items-center gap-1 text-[16px] text-purple tracking-[-0.8px] hover:text-red"
        >
          {{ texts.openCalendar }}
          <img :src="iconArrowLink" alt="" class="w-4 h-4">
        </router-link>
      </div>

      <div class="overflow-x-auto -mx-1 px-1">
        <div class="grid grid-cols-7 gap-2 md:gap-3 min-w-[720px]">
          <div
            v-for="day in weekDays"
            :key="day.dateKey"
            class="rounded-[16px] overflow-hidden min-h-[160px] flex flex-col"
            :class="day.isToday ? 'bg-rose' : 'bg-light-grey'"
          >
            <div
              class="px-2.5 py-2 text-[13px] md:text-[14px] font-bold tracking-[-0.7px]"
              :class="day.isToday
                ? 'bg-red text-white'
                : 'text-purple'"
            >
              {{ day.label }}
            </div>
            <div class="flex-1 p-2 space-y-2">
              <router-link
                v-if="day.idea"
                to="/dashboard/content-calendar"
                class="block rounded-[12px] bg-white px-2.5 py-2 shadow-[0_2px_8px_rgba(28,26,27,0.06)] hover:shadow-[0_4px_12px_rgba(28,26,27,0.1)] transition-shadow"
              >
                <span
                  class="inline-block text-[10px] font-bold uppercase tracking-[-0.4px] px-1.5 py-0.5 rounded-[6px] mb-1.5"
                  :class="ideaTypeClass(day.idea.title)"
                >
                  {{ ideaTypeLabel(day.idea.title) }}
                </span>
                <p class="text-[12px] md:text-[13px] font-medium tracking-[-0.5px] text-black leading-snug line-clamp-3">
                  {{ ideaBodyTitle(day.idea.title) }}
                </p>
              </router-link>
              <p
                v-else
                class="text-[12px] text-purple/40 tracking-[-0.5px] px-1 py-2"
              >
                {{ texts.noIdea }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section v-if="continueDocuments.length" class="mt-5 bg-white rounded-[30px] shadow-card p-4 md:p-5">
      <div class="flex flex-wrap items-center justify-between gap-2 mb-4 px-1">
        <div class="flex items-center gap-3">
          <span class="w-5 h-5 rounded-[6px] bg-grey flex items-center justify-center shrink-0">
            <img :src="iconContinue" alt="" class="w-[14px] h-[14px]">
          </span>
          <h3 class="text-[24px] md:text-[32px] font-bold tracking-[-1.6px] text-black leading-none">
            {{ texts.continueTitle }}
          </h3>
        </div>
        <router-link
          to="/dashboard/documents"
          class="inline-flex items-center gap-1 text-[16px] text-purple tracking-[-0.8px] hover:text-red"
        >
          {{ texts.allDrafts }}
          <img :src="iconArrowLink" alt="" class="w-4 h-4">
        </router-link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <router-link
          v-for="item in continueDocuments"
          :key="item.kind"
          :to="item.to"
          class="bg-light-grey rounded-[16px] p-3 hover:bg-rose transition-colors"
        >
          <div class="aspect-[3/2] w-full rounded-[16px] border border-[#cacaca] bg-white overflow-hidden flex items-center justify-center">
            <img
              v-if="item.image"
              :src="item.image"
              alt=""
              class="w-full h-full object-cover"
            >
            <img v-else :src="iconDocumentCard" alt="" class="w-5 h-5">
          </div>
          <h4 class="mt-4 text-[24px] font-bold tracking-[-1.2px] text-black leading-none">
            {{ item.title }}
          </h4>
          <p class="mt-2 text-[12px] font-bold uppercase tracking-[-0.6px] text-[#737373]">
            {{ item.shortLabel }}
          </p>
          <div class="mt-4 h-2.5 rounded-[6px] bg-white overflow-hidden">
            <div class="h-full rounded-[6px] bg-red" :style="{ width: `${item.percent}%` }" />
          </div>
          <div class="mt-3 h-[38px] rounded-[6px] bg-grey text-[14px] font-bold uppercase tracking-[-0.7px] text-black flex items-center justify-center">
            {{ item.percent > 0 ? `${texts.continueAction} · ${item.percent}%` : texts.start }}
          </div>
        </router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useLanguageStore } from '@/stores/language'
import iconActionSteps from '@/assets/images/icons/dashboard/v2/action-steps.svg'
import iconHashtags from '@/assets/images/icons/dashboard/v2/hash.svg'
import iconArrowLink from '@/assets/images/icons/dashboard/v2/arrow-link.svg'
import iconContentCalendar from '@/assets/images/icons/dashboard/v2/content-calendar.svg'
import iconResourceLibrary from '@/assets/images/icons/dashboard/v2/resource-library.svg'
import iconDocuments from '@/assets/images/icons/dashboard/v2/documents.svg'
import iconContinue from '@/assets/images/icons/dashboard/v2/continue.svg'
import iconDocumentCard from '@/assets/images/icons/dashboard/v2/document-card.svg'
import iconChevronDown from '@/assets/images/icons/dashboard/v2/chevron-down.svg'
import { documentStatus, getDocumentCatalog, mergeDocumentCatalog } from '@/data/worksheets'
import { comparePlanTasks, currentPlanWeek, daysUntilWeekEnd, formatWeekRange } from '@/utils/planWeeks'
import { useNotifications } from '@/composables/useNotifications'

const route = useRoute()
const languageStore = useLanguageStore()
const { notifications, hasUnreadAnnouncements, markingRead, formatNotificationDate, fetchNotifications, markAllRead } = useNotifications()

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
const userName = ref('')
const weekIdeas = ref([])
const worksheetStatuses = ref([])
const catalogExtra = ref([])

const taskDetailRoute = (task) => ({
  name: 'TaskDetail',
  params: { planTaskId: String(task.pivot.id) },
  query: {
    from: 'dashboard',
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
      welcome: 'Willkommen zurück',
      planTitle: 'Deine Action Steps diese Woche',
      stageLabel: 'Monatlicher Plan',
      tasksCompleted: 'Aufgaben erledigt',
      leftInStage: 'noch diese Woche',
      pendingTasks: 'offen',
      actionSteps: 'Action Steps',
      allTasks: 'Alle Aufgaben',
      hideTasks: 'Weniger',
      open: 'Öffnen',
      noTasks: 'Keine Aufgaben in dieser Woche',
      progress: 'Fortschritt',
      of: 'von',
      tasks: 'Aufgaben',
      completed: 'Erledigt',
      pending: 'Offen',
      total: 'Gesamt',
      week: 'Woche',
      quickLinks: 'Schnellzugriff',
      notifications: 'Benachrichtigungen',
      markAllRead: 'Alle gelesen',
      noNotifications: 'Keine Benachrichtigungen',
      contentIdeas: 'Content Calendar',
      contentCalendar: 'Content Calendar',
      thisWeek: 'diese Woche',
      openCalendar: 'Kalender öffnen',
      noIdea: 'Keine Idee',
      imageLibrary: 'Bildbibliothek',
      hashtags: 'Hashtags',
      documents: 'Dokumente',
      continueTitle: 'Weitermachen, wo du aufgehört hast',
      allDrafts: 'Alle Entwürfe',
      continueAction: 'Weiter',
      start: 'Starten',
      emptyTitle: 'Du hast noch keine Action Steps',
      emptyText: 'Deine Action Steps werden nach dem Ausfüllen des Fragebogens automatisch erstellt',
      done: 'Done',
      notStarted: 'Not started'
    }
  }

  return {
    welcome: 'Welcome back',
    planTitle: 'Your action steps this week',
    stageLabel: 'Monthly plan',
    tasksCompleted: 'tasks completed',
    leftInStage: 'left this week',
    pendingTasks: 'pending tasks',
    actionSteps: 'Action Steps',
    allTasks: 'All tasks',
    hideTasks: 'Hide tasks',
    open: 'Open',
    noTasks: 'No tasks this week',
    progress: 'Progress',
    of: 'of',
    tasks: 'tasks',
    completed: 'Completed',
    pending: 'Pending',
    total: 'Total',
    week: 'Week',
    quickLinks: 'Quick links',
    notifications: 'Notifications',
    markAllRead: 'Mark all read',
    noNotifications: 'No notifications',
    contentIdeas: 'Content Calendar',
    contentCalendar: 'Content Calendar',
    thisWeek: 'this week',
    openCalendar: 'Open calendar',
    noIdea: 'No idea',
    imageLibrary: 'Image Library',
    hashtags: 'Hashtags',
    documents: 'Documents',
    continueTitle: 'Continue where you left off',
    allDrafts: 'All drafts',
    continueAction: 'Continue',
    start: 'Start',
    emptyTitle: "You don't have action steps yet",
    emptyText: 'Your action steps will be created after completing the questionnaire',
    done: 'Done',
    notStarted: 'Not started'
  }
})

const welcomeName = computed(() => {
  if (!userName.value) return 'there'
  return userName.value.split(' ')[0]
})

const todayLabel = computed(() => {
  const now = new Date()
  return now.toLocaleDateString(languageStore.language === 'de' ? 'de-DE' : 'en-US', {
    weekday: 'long',
    month: 'long',
    day: 'numeric'
  })
})

const selectedWeek = ref(currentPlanWeek(selectedYear.value, selectedMonth.value))
const weekMenuOpen = ref(false)
const weekMenuRoot = ref(null)

watch([selectedYear, selectedMonth], () => {
  selectedWeek.value = currentPlanWeek(selectedYear.value, selectedMonth.value)
})

const progressPercent = computed(() => {
  if (!weekTaskCount.value) return 0
  return Math.round((weekCompletedCount.value / weekTaskCount.value) * 100)
})

const circumference = 2 * Math.PI * 39
const largeCircumference = 2 * Math.PI * 72

const progressOffset = computed(() => {
  return circumference - (progressPercent.value / 100) * circumference
})

const largeProgressOffset = computed(() => {
  return largeCircumference - (progressPercent.value / 100) * largeCircumference
})

const daysLeftLabel = computed(() => {
  const days = daysUntilWeekEnd(selectedYear.value, selectedMonth.value, selectedWeek.value)
  if (languageStore.language === 'de') {
    return days === 1 ? '1 Tag' : `${days} Tage`
  }
  return days === 1 ? '1 day' : `${days} days`
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
  return flatTasks.value
    .filter(item => Number(item.task.pivot?.week) === selectedWeek.value)
    .sort(comparePlanTasks)
})

const weekTaskCount = computed(() => visibleActionTasks.value.length)

const weekCompletedCount = computed(() => {
  return visibleActionTasks.value.filter(item => item.task.pivot?.completed).length
})

const weekPendingCount = computed(() => weekTaskCount.value - weekCompletedCount.value)

const weekRangeLabel = computed(() => {
  return formatWeekRange(selectedYear.value, selectedMonth.value, selectedWeek.value, languageStore.language)
})

const weekOptions = computed(() => {
  return [1, 2, 3, 4].map((week) => ({
    week,
    label: `${texts.value.week} ${week}`,
    range: formatWeekRange(selectedYear.value, selectedMonth.value, week, languageStore.language),
  }))
})

const selectWeek = (week) => {
  selectedWeek.value = week
  weekMenuOpen.value = false
}

const closeWeekMenuOnOutsideClick = (event) => {
  if (!weekMenuOpen.value) return
  if (weekMenuRoot.value?.contains(event.target)) return
  weekMenuOpen.value = false
}

const formatDateKey = (date) => {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const getWeekMonday = (date = new Date()) => {
  const d = new Date(date)
  d.setHours(0, 0, 0, 0)
  const day = d.getDay()
  const diff = day === 0 ? -6 : 1 - day
  d.setDate(d.getDate() + diff)
  return d
}

const weekDays = computed(() => {
  const monday = getWeekMonday()
  const todayKey = formatDateKey(new Date())
  const dayNames = languageStore.language === 'de'
    ? ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']
    : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

  return Array.from({ length: 7 }, (_, index) => {
    const date = new Date(monday)
    date.setDate(monday.getDate() + index)
    const dateKey = formatDateKey(date)
    return {
      date,
      dateKey,
      label: `${dayNames[index]} ${date.getDate()}`,
      isToday: dateKey === todayKey,
      idea: weekIdeas.value.find(idea => idea.date === dateKey) || null
    }
  })
})

const continueDocuments = computed(() => {
  const byKind = Object.fromEntries(worksheetStatuses.value.map((item) => [item.kind, item]))
  const monthOpen = (item) => (item.schedule || []).some((entry) => (
    Number(entry.year) === selectedYear.value
    && Number(entry.month) === selectedMonth.value
    && !entry.completed
  ))
  const ranked = mergeDocumentCatalog(getDocumentCatalog(languageStore.language), catalogExtra.value).map((meta) => {
    const live = byKind[meta.kind] || {}
    const status = documentStatus(live)
    const openThisMonth = monthOpen(live)
    const percent = live.completed && !openThisMonth ? 100 : Number(live.fill_percent || 0)
    return {
      ...meta,
      ...live,
      status,
      openThisMonth,
      percent,
      to: `/dashboard/documents/${meta.kind.replaceAll('_', '-')}`,
    }
  }).filter((item) => item.plan_task_id)

  const needsUpdate = []
  const drafts = []
  const thisMonth = []
  const rest = []
  ranked.forEach((item) => {
    if (item.status === 'needs_review' && item.filled) {
      needsUpdate.push(item)
      return
    }
    if (Number(item.fill_percent) >= 100 || item.status === 'up_to_date' || item.completed) return
    if (item.status === 'draft') {
      drafts.push(item)
      return
    }
    if (item.openThisMonth) {
      thisMonth.push(item)
      return
    }
    if (!item.filled) rest.push(item)
  })

  return [...needsUpdate, ...drafts, ...thisMonth, ...rest].slice(0, 3)
})

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

const fetchWorksheets = async () => {
  try {
    const response = await axios.get('/api/worksheets')
    worksheetStatuses.value = response.data?.worksheets || []
    catalogExtra.value = response.data?.catalog || []
  } catch (error) {
    console.error('Failed to load documents:', error)
    worksheetStatuses.value = []
  }
}

const fetchWeekIdeas = async () => {
  try {
    const monday = getWeekMonday()
    const sunday = new Date(monday)
    sunday.setDate(monday.getDate() + 6)

    const months = [
      { year: monday.getFullYear(), month: monday.getMonth() + 1 },
      { year: sunday.getFullYear(), month: sunday.getMonth() + 1 }
    ].filter((item, index, arr) =>
      arr.findIndex(m => m.year === item.year && m.month === item.month) === index
    )

    const responses = await Promise.all(
      months.map(({ year, month }) =>
        axios.get('/api/content-ideas/by-month', { params: { year, month } })
      )
    )

    weekIdeas.value = responses.flatMap(response =>
      response.data?.success ? (response.data.data || []) : []
    )
  } catch (error) {
    console.error('Failed to load week content ideas:', error)
    weekIdeas.value = []
  }
}

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

      if (availableMonths.value.length > 0) {
        const latest = availableMonths.value[0]
        selectedYear.value = latest.year
        selectedMonth.value = latest.month
      }
    } else {
      const now = new Date()
      availableMonths.value = [{
        year: now.getFullYear(),
        month: now.getMonth() + 1
      }]
      selectedYear.value = now.getFullYear()
      selectedMonth.value = now.getMonth() + 1
    }
  } catch (error) {
    console.error('Failed to load available months:', error)
    const now = new Date()
    availableMonths.value = [{
      year: now.getFullYear(),
      month: now.getMonth() + 1
    }]
    selectedYear.value = now.getFullYear()
    selectedMonth.value = now.getMonth() + 1
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
      await fetchPlans()
    }
  } catch (error) {
    console.error('Failed to generate month tasks:', error)
    alert('Failed to generate tasks: ' + (error.response?.data?.message || error.message))
  } finally {
    isGeneratingMonth.value = false
  }
}

const updateStats = () => {
  stats.value = {
    completedTasks: plans.value.reduce((sum, plan) => sum + (plan.completed_tasks || 0), 0),
    inProgressTasks: plans.value.reduce((sum, plan) => sum + ((plan.total_tasks || 0) - (plan.completed_tasks || 0)), 0),
    totalTasks: plans.value.reduce((sum, plan) => sum + (plan.total_tasks || 0), 0)
  }
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
  document.addEventListener('click', closeWeekMenuOnOutsideClick)
  try {
    const savedUser = localStorage.getItem('user')
    if (savedUser) {
      const parsed = JSON.parse(savedUser)
      userName.value = parsed?.name || ''
    }
  } catch (error) {
    console.error('Failed to parse saved user:', error)
  }

  await fetchAvailableMonths()
  await Promise.all([fetchPlans(), fetchWeekIdeas(), fetchWorksheets(), fetchNotifications()])
})

onUnmounted(() => {
  document.removeEventListener('click', closeWeekMenuOnOutsideClick)
})
</script>

