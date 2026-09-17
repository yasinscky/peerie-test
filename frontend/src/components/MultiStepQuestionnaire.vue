<template>
  <div class="min-h-screen bg-light-grey">
    <div class="max-w-[800px] mx-auto px-3 sm:px-4 py-6 sm:py-12">
      <div class="bg-white rounded-[20px] sm:rounded-[30px] shadow-card p-4 sm:p-6 md:p-10">
        <div class="text-center mb-6 sm:mb-8">
          <img :src="logoImage" alt="Peerie Logo" class="w-20 h-20 sm:w-32 sm:h-32 mx-auto">
          <h1 class="text-[24px] sm:text-[32px] md:text-[40px] font-bold tracking-[-2px] text-black mb-2 sm:mb-3">Onboarding questionnaire</h1>
          <p class="text-[16px] text-purple/70 tracking-[-0.8px] max-w-[560px] mx-auto">
            Tell us a bit about your business so we can build your customised marketing plan.
          </p>
        </div>

        <div class="mb-8">
          <div class="flex justify-between text-[13px] tracking-[-0.6px] text-purple/70 mb-2">
            <span>Step {{ displayStep }} of {{ totalSteps }}</span>
            <span>{{ Math.round(progress) }}%</span>
          </div>
          <div class="w-full bg-muted rounded-full h-2">
            <div
              class="h-2 rounded-full transition-all duration-300"
              :style="{ width: progress + '%', background: 'linear-gradient(90deg, rgb(var(--color-red)), rgb(var(--color-red-lighter)))' }"
            ></div>
          </div>
          <div class="mt-2 text-center">
            <span class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ currentStepTitle }}</span>
          </div>
        </div>

        <form @submit.prevent="nextStep" v-if="!isSubmitted" class="space-y-6">

          <div v-if="currentStep === 1" class="space-y-6">
            <h2 class="text-[20px] sm:text-[24px] font-bold tracking-[-1.2px] text-black mb-4">About your business</h2>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-3">What country are you operating in?</label>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label
                  v-for="country in countries"
                  :key="country.value"
                  class="relative flex flex-col items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.country === country.value ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.country" type="radio" :value="country.value" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mb-2">{{ country.flag }}</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black text-center">{{ country.label }}</div>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-3">What industry are you in?</label>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label
                  v-for="industry in industries"
                  :key="industry.value"
                  class="relative flex flex-col items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.industry === industry.value ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.industry" type="radio" :value="industry.value" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mb-2">{{ industry.icon }}</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">{{ industry.label }}</div>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-3">What language do you want to use for your marketing plan?</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="[
                    form.language === 'de' ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted',
                    form.country === 'uk' || form.country === 'ie' ? 'opacity-40 pointer-events-none' : ''
                  ]"
                >
                  <input v-model="form.language" type="radio" value="de" class="sr-only" :disabled="form.country === 'uk' || form.country === 'ie'" @change="updateProgress">
                  <span class="text-2xl mr-3">🇩🇪</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Deutsch</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="[
                    form.language === 'en' ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted',
                    form.country === 'de' ? 'opacity-40 pointer-events-none' : ''
                  ]"
                >
                  <input v-model="form.language" type="radio" value="en" class="sr-only" :disabled="form.country === 'de'" @change="updateProgress">
                  <span class="text-2xl mr-3">🇬🇧</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">English</div>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-3">Do you serve customers in person?</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.is_local_business === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.is_local_business" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.is_local_business === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.is_local_business" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No, online only</div>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-1">How much time can you realistically spend on marketing each week?</label>
              <p class="text-[13px] text-purple/60 tracking-[-0.6px] mb-3">We'll tailor your plan to match your capacity.</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  v-for="option in timeOptions"
                  :key="option.value"
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.marketing_time_per_week === option.value ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.marketing_time_per_week" type="radio" :value="option.value" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">{{ option.icon }}</span>
                  <div>
                    <div class="font-bold text-[14px] tracking-[-0.7px] text-black">{{ option.label }}</div>
                    <div v-if="option.description" class="text-[13px] text-purple/60 tracking-[-0.6px]">{{ option.description }}</div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <div v-if="currentStep === 2" class="space-y-6">
            <h2 class="text-[20px] sm:text-[24px] font-bold tracking-[-1.2px] text-black mb-4">Goals</h2>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-1">Do you have your main business goals defined?</label>
              <p class="text-[13px] text-purple/60 tracking-[-0.6px] mb-3">Choose "No" if you haven't defined them yet or want to refine them.</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.business_goals_defined === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.business_goals_defined" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.business_goals_defined === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.business_goals_defined" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No</div>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-1">Do you have your marketing goals defined?</label>
              <p class="text-[13px] text-purple/60 tracking-[-0.6px] mb-3">Choose "No" if you haven't defined them yet or want to refine them.</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.marketing_goals_defined === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.marketing_goals_defined" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.marketing_goals_defined === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.marketing_goals_defined" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No</div>
                </label>
              </div>
            </div>
          </div>

          <div v-if="currentStep === 3 && form.is_local_business" class="space-y-6">
            <h2 class="text-[20px] sm:text-[24px] font-bold tracking-[-1.2px] text-black mb-4">Local SEO & directories</h2>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-1">Have you claimed and verified your Google Business Profile?</label>
              <p class="text-[13px] text-purple/60 tracking-[-0.6px] mb-3">If it's claimed/verified but not fully filled out, choose "Yes".</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.google_business_claimed === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.google_business_claimed" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.google_business_claimed === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.google_business_claimed" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No</div>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-1">Have you claimed your business on Apple Business Connect and Bing Places for Business?</label>
              <p class="text-[13px] text-purple/60 tracking-[-0.6px] mb-3">If you only have one of them, choose "No".</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.core_directories_claimed === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.core_directories_claimed" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.core_directories_claimed === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.core_directories_claimed" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No</div>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-1">Are you listed on industry-specific directories?</label>
              <p class="text-[13px] text-purple/60 tracking-[-0.6px] mb-3">If you're listed on 3–4 directories specific to your industry, choose "Yes".</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.industry_directories_claimed === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.industry_directories_claimed" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.industry_directories_claimed === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.industry_directories_claimed" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No</div>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-1">Are you listed on general business directories?</label>
              <p class="text-[13px] text-purple/60 tracking-[-0.6px] mb-3">If you're listed on 3–4 general directories, choose "Yes" (e.g., Yelp, Yellow Pages, Gelbe Seiten).</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.business_directories_claimed === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.business_directories_claimed" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.business_directories_claimed === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.business_directories_claimed" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No</div>
                </label>
              </div>
            </div>
          </div>

          <div v-if="currentStep === 4" class="space-y-6">
            <h2 class="text-[20px] sm:text-[24px] font-bold tracking-[-1.2px] text-black mb-4">Tools & channels</h2>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-1">Do you have at least a basic website?</label>
              <p class="text-[13px] text-purple/60 tracking-[-0.6px] mb-3">If you have at least a simple one-page website, choose "Yes".</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.has_website === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.has_website" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.has_website === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.has_website" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No</div>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-1">Do you use an email marketing platform to email customers?</label>
              <p class="text-[13px] text-purple/60 tracking-[-0.6px] mb-3">If you don't email customers or only email manually from your regular inbox (Gmail/Outlook), choose "No".</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.email_marketing_tool === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.email_marketing_tool" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.email_marketing_tool === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.email_marketing_tool" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No</div>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-1">Do you use any CRM (or other system) to track leads?</label>
              <p class="text-[13px] text-purple/60 tracking-[-0.6px] mb-3">If you use any method to track and manage leads/customers (including a spreadsheet), choose "Yes".</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.crm_pipeline === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.crm_pipeline" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.crm_pipeline === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.crm_pipeline" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No</div>
                </label>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-3">Are you currently running paid ads? <span class="font-normal text-purple/60">(choose all that apply)</span></label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  v-for="option in adsChannelOptions"
                  :key="option.value"
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="Array.isArray(form.running_ads) && form.running_ads.includes(option.value) ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.running_ads" type="checkbox" :value="option.value" class="sr-only" @change="() => { onRunningAdsChange(); updateProgress() }">
                  <span class="text-2xl mr-3">{{ option.icon }}</span>
                  <div>
                    <div class="font-bold text-[14px] tracking-[-0.7px] text-black">{{ option.label }}</div>
                    <div class="text-[13px] text-purple/60 tracking-[-0.6px]">{{ option.description }}</div>
                  </div>
                </label>

                <label
                  v-if="noAdsOption"
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.running_ads_none === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.running_ads_none" type="checkbox" class="sr-only" @change="() => { onRunningAdsNoneChange(); updateProgress() }">
                  <span class="text-2xl mr-3">{{ noAdsOption.icon }}</span>
                  <div>
                    <div class="font-bold text-[14px] tracking-[-0.7px] text-black">{{ noAdsOption.label }}</div>
                    <div class="text-[13px] text-purple/60 tracking-[-0.6px]">{{ noAdsOption.description }}</div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <div v-if="currentStep === 5" class="space-y-6">
            <h2 class="text-[20px] sm:text-[24px] font-bold tracking-[-1.2px] text-black mb-4">Social Media</h2>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-3">Do you have a social media account for your business?</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.has_primary_social_channel === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.has_primary_social_channel" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.has_primary_social_channel === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.has_primary_social_channel" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No</div>
                </label>
              </div>

              <div v-if="form.has_primary_social_channel === true">
                <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-3">What is your primary social media platform? <span class="font-normal text-purple/60">(choose one)</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                  <label
                    v-for="channel in availablePrimaryChannels"
                    :key="channel.value"
                    class="relative flex flex-col items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                    :class="form.primary_social_channel === channel.value ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                  >
                    <input v-model="form.primary_social_channel" type="radio" :value="channel.value" class="sr-only" @change="updateProgress">
                    <span class="text-2xl mb-2">{{ channel.icon }}</span>
                    <div class="font-bold text-[13px] tracking-[-0.6px] text-black text-center">{{ channel.label }}</div>
                  </label>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-3">Do you use more than one social media platform?</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.has_secondary_social_channel === true ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.has_secondary_social_channel" type="radio" :value="true" class="sr-only" @change="updateProgress" :disabled="form.has_primary_social_channel === false">
                  <span class="text-2xl mr-3">✅</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">Yes</div>
                </label>
                <label
                  class="relative flex items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                  :class="form.has_secondary_social_channel === false ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                >
                  <input v-model="form.has_secondary_social_channel" type="radio" :value="false" class="sr-only" @change="updateProgress" :disabled="form.has_primary_social_channel === false">
                  <span class="text-2xl mr-3">❌</span>
                  <div class="font-bold text-[14px] tracking-[-0.7px] text-black">No</div>
                </label>
              </div>

              <div v-if="form.has_secondary_social_channel === true">
                <label class="block text-[14px] font-bold tracking-[-0.7px] text-black mb-3">Which other platform do you use? <span class="font-normal text-purple/60">(choose one)</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                  <label
                    v-for="channel in availableSecondaryChannels"
                    :key="channel.value"
                    class="relative flex flex-col items-center p-4 rounded-[16px] border cursor-pointer transition-all"
                    :class="form.secondary_social_channel === channel.value ? 'border-red bg-rose' : 'border-grey bg-light-grey hover:bg-muted'"
                  >
                    <input v-model="form.secondary_social_channel" type="radio" :value="channel.value" class="sr-only" @change="updateProgress">
                    <span class="text-2xl mb-2">{{ channel.icon }}</span>
                    <div class="font-bold text-[13px] tracking-[-0.6px] text-black text-center">{{ channel.label }}</div>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <div v-if="currentStep === 6" class="space-y-5">
            <div>
              <h2 class="text-[24px] font-bold tracking-[-1.2px] text-black mb-2">Your answers</h2>
              <p class="text-[14px] text-purple/70 tracking-[-0.7px]">
                Here's a quick summary of what you told us. If anything looks off, you can edit it now — it only takes a second.
              </p>
              <p class="text-[14px] text-purple/70 tracking-[-0.7px] mt-1">
                When you're happy, continue — we'll generate your marketing plan using these answers.
              </p>
            </div>

            <div class="space-y-4">
              <div class="rounded-[16px] border border-grey bg-light-grey p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                  <h3 class="text-[16px] font-bold tracking-[-0.8px] text-black">About your business</h3>
                  <button type="button" class="h-[36px] px-4 rounded-[16px] border border-grey bg-white text-[13px] font-bold tracking-[-0.6px] text-black hover:bg-muted transition-colors" @click="goToStep(1)">Edit</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Country</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ countryLabel }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Industry</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ industryLabel }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Language</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ languageLabel }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Serve customers in person?</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ yesNo(form.is_local_business) }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Weekly marketing capacity</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ marketingTimeLabel }}</div>
                  </div>
                </div>
              </div>

              <div class="rounded-[16px] border border-grey bg-light-grey p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                  <h3 class="text-[16px] font-bold tracking-[-0.8px] text-black">Goals</h3>
                  <button type="button" class="h-[36px] px-4 rounded-[16px] border border-grey bg-white text-[13px] font-bold tracking-[-0.6px] text-black hover:bg-muted transition-colors" @click="goToStep(2)">Edit</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Main business goals defined</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ yesNo(form.business_goals_defined) }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Marketing goals defined</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ yesNo(form.marketing_goals_defined) }}</div>
                  </div>
                </div>
              </div>

              <div v-if="form.is_local_business" class="rounded-[16px] border border-grey bg-light-grey p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                  <h3 class="text-[16px] font-bold tracking-[-0.8px] text-black">Local SEO & directories</h3>
                  <button type="button" class="h-[36px] px-4 rounded-[16px] border border-grey bg-white text-[13px] font-bold tracking-[-0.6px] text-black hover:bg-muted transition-colors" @click="goToStep(3)">Edit</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Google Business Profile</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ yesNo(form.google_business_claimed) }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Apple & Bing Places</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ yesNo(form.core_directories_claimed) }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Industry directories</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ yesNo(form.industry_directories_claimed) }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">General business directories</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ yesNo(form.business_directories_claimed) }}</div>
                  </div>
                </div>
              </div>

              <div class="rounded-[16px] border border-grey bg-light-grey p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                  <h3 class="text-[16px] font-bold tracking-[-0.8px] text-black">Tools & channels</h3>
                  <button type="button" class="h-[36px] px-4 rounded-[16px] border border-grey bg-white text-[13px] font-bold tracking-[-0.6px] text-black hover:bg-muted transition-colors" @click="goToStep(4)">Edit</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Basic website</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ yesNo(form.has_website) }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Email marketing platform</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ yesNo(form.email_marketing_tool) }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">CRM / lead tracking</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ yesNo(form.crm_pipeline) }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Paid ads</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ runningAdsLabel }}</div>
                  </div>
                </div>
              </div>

              <div class="rounded-[16px] border border-grey bg-light-grey p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                  <h3 class="text-[16px] font-bold tracking-[-0.8px] text-black">Social Media</h3>
                  <button type="button" class="h-[36px] px-4 rounded-[16px] border border-grey bg-white text-[13px] font-bold tracking-[-0.6px] text-black hover:bg-muted transition-colors" @click="goToStep(5)">Edit</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Primary platform</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ primarySocialLabel }}</div>
                  </div>
                  <div>
                    <div class="text-[12px] text-purple/60 tracking-[-0.6px]">Second platform</div>
                    <div class="text-[14px] font-bold tracking-[-0.7px] text-black">{{ secondarySocialLabel }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="flex justify-between pt-6">
            <button
              type="button"
              @click="prevStep"
              v-if="currentStep > 1"
              class="h-[42px] px-5 rounded-[20px] bg-light-grey border-4 border-rose text-[14px] font-bold tracking-[-0.7px] text-black hover:bg-rose transition-colors"
            >
              ← Back
            </button>
            <div v-else></div>

            <button
              type="submit"
              class="h-[42px] px-6 rounded-[20px] bg-red shadow-red-sm text-[14px] font-bold tracking-[-0.7px] text-white hover:bg-red-dark transition-colors disabled:opacity-50"
              :disabled="isLoading || !canProceed"
            >
              <span v-if="isLoading" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2 align-middle"></span>
              {{ primaryCtaLabel }}
            </button>
          </div>
        </form>

        <div v-if="isSubmitted && planId" class="text-center py-8">
          <div class="text-6xl mb-4">🎉</div>
          <h3 class="text-[24px] font-bold tracking-[-1.2px] text-black mb-2">Your marketing plan is ready 🎉</h3>
          <p class="text-[16px] text-purple/70 tracking-[-0.8px] mb-6">
            Head to your dashboard to see your personalised plan and this month's tasks.
          </p>
          <router-link
            to="/dashboard"
            class="inline-flex h-[42px] px-8 items-center rounded-[20px] bg-red shadow-red text-[14px] font-bold tracking-[-0.7px] text-white hover:bg-red-dark transition-colors"
          >
            Go to dashboard
          </router-link>
        </div>

        <div v-if="error" class="mt-6 rounded-[16px] bg-rose border border-red/20 px-5 py-4">
          <div class="flex items-center">
            <span class="text-xl mr-3">⚠️</span>
            <div>
              <div class="text-[14px] font-bold tracking-[-0.7px] text-red">Error</div>
              <div class="text-[14px] text-black tracking-[-0.7px]">{{ error }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import logoImage from '@/assets/images/logos/logo.svg'

export default {
  name: 'MultiStepQuestionnaire',
  setup() {
    const router = useRouter()
    const isLoading = ref(false)
    const isSubmitted = ref(false)
    const error = ref('')
    const planId = ref(null)
    const currentStep = ref(1)
    
    const totalSteps = computed(() => {
      const base = form.value.is_local_business === false ? 4 : 5
      return base + 1
    })

    const form = ref({
      country: '',
      industry: '',
      language: '',
      is_local_business: null,
      marketing_time_per_week: null,
      
      business_goals_defined: null,
      marketing_goals_defined: null,
      
      google_business_claimed: null,
      core_directories_claimed: null,
      industry_directories_claimed: null,
      business_directories_claimed: null,
      
      has_website: null,
      email_marketing_tool: null,
      crm_pipeline: null,
      running_ads: [],
      running_ads_none: false,
      
      has_primary_social_channel: null,
      primary_social_channel: '',
      has_secondary_social_channel: null,
      secondary_social_channel: '',
    })

    const countries = [
      { value: 'de', label: 'Deutschland', flag: '🇩🇪' },
      { value: 'uk', label: 'United Kingdom', flag: '🇬🇧' },
      { value: 'ie', label: 'Ireland', flag: '🇮🇪' }
    ]

    const industries = [
      { value: 'beauty', label: 'Beauty', icon: '💅' },
      { value: 'physio', label: 'Physio', icon: '🏥' },
      { value: 'coaching', label: 'Coaching', icon: '💼' }
    ]

    const timeOptions = [
      { value: 2, label: '2h Standard', description: '', icon: '⏰' },
      { value: 4, label: '4h Turbo', description: '', icon: '⏱️' }
    ]

    const adsOptions = [
      { value: 'retargeting', label: 'Retargeting', description: 'Ads shown to people who already visited your website or engaged with you (to bring them back and convert).', icon: '🎯' },
      { value: 'paid_search', label: 'Paid Search', description: 'Ads in search results when someone searches for relevant keywords.', icon: '🔍' },
      { value: 'prospecting_social', label: 'Prospecting (Social)', description: 'Social ads shown to new audiences based on targeting (to find new customers).', icon: '📱' },
      { value: 'none', label: 'None', description: 'You\u2019re not currently running paid ads.', icon: '❌' }
    ]

    const adsChannelOptions = computed(() => {
      return adsOptions.filter(option => option.value !== 'none')
    })

    const noAdsOption = computed(() => {
      return adsOptions.find(option => option.value === 'none')
    })

    const socialChannels = [
      { value: 'instagram', label: 'Instagram', icon: '📸' },
      { value: 'facebook', label: 'Facebook', icon: '👥' },
      { value: 'linkedin', label: 'LinkedIn', icon: '💼' },
      { value: 'tiktok', label: 'TikTok', icon: '🎵' },
      { value: 'youtube', label: 'YouTube', icon: '📺' },
      { value: 'twitter', label: 'X (Twitter)', icon: '🐦' }
    ]
    
    const availablePrimaryChannels = computed(() => {
      return socialChannels.filter(channel => channel.value !== form.value.secondary_social_channel)
    })
    
    const availableSecondaryChannels = computed(() => {
      return socialChannels.filter(channel => channel.value !== form.value.primary_social_channel)
    })

    const stepTitles = {
      1: 'About your business',
      2: 'Goals',
      3: 'Local SEO & directories',
      4: 'Tools & channels',
      5: 'Social Media',
      6: 'Your answers'
    }

    const currentStepTitle = computed(() => stepTitles[currentStep.value])
    
    const displayStep = computed(() => {
      if (currentStep.value === 6 && !form.value.is_local_business) {
        return 5
      }
      if (currentStep.value > 3 && !form.value.is_local_business) {
        return currentStep.value - 1
      }
      return currentStep.value
    })

    const progress = computed(() => {
      let completed = 0
      let total = 0

      total += 5
      if (form.value.country) completed++
      if (form.value.industry) completed++
      if (form.value.language) completed++
      if (form.value.is_local_business !== null) completed++
      if (form.value.marketing_time_per_week !== null) completed++

      total += 2
      if (form.value.business_goals_defined !== null) completed++
      if (form.value.marketing_goals_defined !== null) completed++
      
      if (form.value.is_local_business) {
        total += 4
        if (form.value.google_business_claimed !== null) completed++
        if (form.value.core_directories_claimed !== null) completed++
        if (form.value.industry_directories_claimed !== null) completed++
        if (form.value.business_directories_claimed !== null) completed++
      }

      total += 4
      if (form.value.has_website !== null) completed++
      if (form.value.email_marketing_tool !== null) completed++
      if (form.value.crm_pipeline !== null) completed++
      if ((Array.isArray(form.value.running_ads) && form.value.running_ads.length > 0) || form.value.running_ads_none === true) completed++

      total += 2
      if (form.value.has_primary_social_channel !== null) completed++
      if (form.value.has_secondary_social_channel !== null) completed++
      
      if (form.value.has_primary_social_channel === true) {
        total += 1
        if (form.value.primary_social_channel) completed++
      }
      
      if (form.value.has_secondary_social_channel === true) {
        total += 1
        if (form.value.secondary_social_channel) completed++
      }

      return total > 0 ? (completed / total) * 100 : 0
    })

    const isQuestionnaireLastStep = computed(() => currentStep.value === 5)
    const isReviewStep = computed(() => currentStep.value === 6)

    const canProceed = computed(() => {
      switch (currentStep.value) {
        case 1:
          return form.value.country && 
                 form.value.industry && 
                 form.value.language && 
                 form.value.is_local_business !== null &&
                 form.value.marketing_time_per_week !== null
        case 2:
          return form.value.business_goals_defined !== null && 
                 form.value.marketing_goals_defined !== null
        case 3:
          if (!form.value.is_local_business) return true
          return form.value.google_business_claimed !== null &&
                 form.value.core_directories_claimed !== null &&
                 form.value.industry_directories_claimed !== null &&
                 form.value.business_directories_claimed !== null
        case 4:
          return form.value.has_website !== null &&
                 form.value.email_marketing_tool !== null &&
                 form.value.crm_pipeline !== null &&
                 ((Array.isArray(form.value.running_ads) && form.value.running_ads.length > 0) || form.value.running_ads_none === true)
        case 5:
          return form.value.has_primary_social_channel !== null &&
                 form.value.has_secondary_social_channel !== null &&
                 (form.value.has_primary_social_channel === false || form.value.primary_social_channel) &&
                 (form.value.has_secondary_social_channel === false || form.value.secondary_social_channel)
        case 6:
          return true
        default:
          return false
      }
    })

    const updateProgress = () => {
    }

    const onRunningAdsChange = () => {
      if (Array.isArray(form.value.running_ads) && form.value.running_ads.length > 0) {
        form.value.running_ads_none = false
      }
    }

    const onRunningAdsNoneChange = () => {
      if (form.value.running_ads_none) {
        form.value.running_ads = []
      }
    }

    const nextStep = async () => {
      if (!canProceed.value) return

      if (isReviewStep.value) {
        await submitQuestionnaire()
        return
      }

      if (isQuestionnaireLastStep.value) {
        currentStep.value = 6
      } else {
        currentStep.value++
        if (currentStep.value === 3 && !form.value.is_local_business) {
          currentStep.value = 4
        }
      }
    }

    const prevStep = () => {
      if (currentStep.value > 1) {
        currentStep.value--
        if (currentStep.value === 3 && !form.value.is_local_business) {
          currentStep.value = 2
        }
      }
    }

    const goToStep = (step) => {
      const target = Number(step)
      if (!Number.isFinite(target)) return
      if (target === 3 && !form.value.is_local_business) {
        currentStep.value = 4
        return
      }
      if (target >= 1 && target <= 5) {
        currentStep.value = target
      }
    }

    const yesNo = (value) => {
      if (value === true) return 'Yes'
      if (value === false) return 'No'
      return '—'
    }

    const countryLabel = computed(() => countries.find(c => c.value === form.value.country)?.label || '—')
    const industryLabel = computed(() => industries.find(i => i.value === form.value.industry)?.label || '—')
    const languageLabel = computed(() => {
      if (form.value.language === 'de') return 'Deutsch'
      if (form.value.language === 'en') return 'English'
      return '—'
    })
    const marketingTimeLabel = computed(() => timeOptions.find(o => o.value === form.value.marketing_time_per_week)?.label || '—')
    const runningAdsLabel = computed(() => {
      if (form.value.running_ads_none === true) return 'None'
      const list = Array.isArray(form.value.running_ads) ? form.value.running_ads : []
      if (list.length === 0) return '—'
      return list
        .map(v => adsChannelOptions.value.find(o => o.value === v)?.label || v)
        .join(', ')
    })
    const primarySocialLabel = computed(() => {
      if (form.value.has_primary_social_channel === false) return 'None'
      if (!form.value.primary_social_channel) return '—'
      return socialChannels.find(c => c.value === form.value.primary_social_channel)?.label || form.value.primary_social_channel
    })
    const secondarySocialLabel = computed(() => {
      if (form.value.has_secondary_social_channel === false) return 'None'
      if (!form.value.secondary_social_channel) return '—'
      return socialChannels.find(c => c.value === form.value.secondary_social_channel)?.label || form.value.secondary_social_channel
    })

    const primaryCtaLabel = computed(() => {
      if (isLoading.value) return 'Creating your plan...'
      if (isReviewStep.value) return 'Continue'
      if (isQuestionnaireLastStep.value) return 'Your answers →'
      return 'Continue →'
    })

    const submitQuestionnaire = async () => {
      isLoading.value = true
      error.value = ''

      try {
        const submitData = {
          ...form.value
        }

        const response = await axios.post('/api/questionnaire', submitData)
        
        if (response.data.success) {
          planId.value = response.data.plan.id
          isSubmitted.value = true
        } else {
          error.value = response.data.message || 'An error occurred while creating the plan'
        }
      } catch (err) {
        if (err.response?.data?.errors) {
          const errors = Object.values(err.response.data.errors).flat()
          error.value = errors.join(', ')
        } else {
          error.value = err.response?.data?.message || 'An error occurred while creating the plan'
        }
      } finally {
        isLoading.value = false
      }
    }

    watch(() => form.value.has_primary_social_channel, (newValue) => {
      if (newValue === false) {
        form.value.primary_social_channel = ''
        form.value.has_secondary_social_channel = false
        form.value.secondary_social_channel = ''
      }
    })
    
    watch(() => form.value.has_secondary_social_channel, (newValue) => {
      if (newValue === false) {
        form.value.secondary_social_channel = ''
      }
    })

    watch(() => form.value.country, (newCountry) => {
      if (newCountry === 'de' && form.value.language !== 'de') {
        form.value.language = 'de'
      }
      if ((newCountry === 'uk' || newCountry === 'ie') && form.value.language !== 'en') {
        form.value.language = 'en'
      }
    })

    onMounted(async () => {
      try {
        const response = await axios.get('/api/user')
        if (!response.data?.success) {
          router.push('/login')
          return
        }

        const hasCompletedQuestionnaire = Boolean(
          (response.data?.user?.has_completed_questionnaire ?? response.data?.has_completed_questionnaire) === true
        )
        if (hasCompletedQuestionnaire) {
          router.push('/dashboard')
          return
        }
      } catch (error) {
        router.push('/login')
      }
    })

    return {
      form,
      isLoading,
      isSubmitted,
      error,
      planId,
      currentStep,
      totalSteps,
      displayStep,
      currentStepTitle,
      progress,
      isQuestionnaireLastStep,
      isReviewStep,
      canProceed,
      countries,
      industries,
      timeOptions,
      adsOptions,
      adsChannelOptions,
      noAdsOption,
      socialChannels,
      availablePrimaryChannels,
      availableSecondaryChannels,
      updateProgress,
      onRunningAdsChange,
      onRunningAdsNoneChange,
      nextStep,
      prevStep,
      goToStep,
      submitQuestionnaire,
      yesNo,
      countryLabel,
      industryLabel,
      languageLabel,
      marketingTimeLabel,
      runningAdsLabel,
      primarySocialLabel,
      secondarySocialLabel,
      primaryCtaLabel,
      logoImage
    }
  }
}
</script>
