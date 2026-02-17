<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="card p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <img :src="logoImage" alt="Peerie Logo" class="w-40 h-40 mx-auto">
          <h1 class="text-3xl font-bold text-gray-900 mb-4">Onboarding questionnaire</h1>
          <p class="text-gray-600 text-lg">
            Tell us a bit about your business so we can build your customised marketing plan.
          </p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
          <div class="flex justify-between text-sm text-gray-600 mb-2">
            <span>Step {{ displayStep }} of {{ totalSteps }}</span>
            <span>{{ Math.round(progress) }}%</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" :style="{ width: progress + '%' }"></div>
          </div>
          <div class="mt-2 text-center">
            <span class="text-sm font-medium text-gray-700">{{ currentStepTitle }}</span>
          </div>
        </div>

        <!-- Form Steps -->
        <form @submit.prevent="nextStep" v-if="!isSubmitted" class="space-y-6">
          
          <!-- Step 1: Business Profile -->
          <div v-if="currentStep === 1" class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">About your business</h2>
            
            <!-- Country -->
            <div>
              <label class="form-label">What country are you operating in?</label>
              <div class="grid grid-cols-3 gap-3">
                <label v-for="country in countries" :key="country.value" class="relative flex flex-col items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.country === country.value }">
                  <input v-model="form.country" type="radio" :value="country.value" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mb-2">{{ country.flag }}</span>
                  <div class="font-medium text-center">{{ country.label }}</div>
                </label>
              </div>
            </div>

            <!-- Industry -->
            <div>
              <label class="form-label">What industry are you in?</label>
              <div class="grid grid-cols-3 gap-3">
                <label v-for="industry in industries" :key="industry.value" class="relative flex flex-col items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.industry === industry.value }">
                  <input v-model="form.industry" type="radio" :value="industry.value" class="sr-only" @change="updateProgress">
                  <span class="text-2xl mb-2">{{ industry.icon }}</span>
                  <div class="font-medium">{{ industry.label }}</div>
                </label>
              </div>
            </div>

            <!-- Primary Language -->
            <div>
              <label class="form-label">What language do you want to use for your marketing plan?</label>
              <div class="grid grid-cols-2 gap-4">
                <label
                  class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
                  :class="[
                    form.language === 'de' ? 'border-primary-500 bg-primary-50' : '',
                    form.country === 'uk' || form.country === 'ie' ? 'opacity-40 pointer-events-none' : ''
                  ]"
                >
                  <input
                    v-model="form.language"
                    type="radio"
                    value="de"
                    class="sr-only"
                    :disabled="form.country === 'uk' || form.country === 'ie'"
                    @change="updateProgress"
                  >
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">🇩🇪</span>
                    <div>
                      <div class="font-medium">Deutsch</div>
                    </div>
                  </div>
                </label>
                <label
                  class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
                  :class="[
                    form.language === 'en' ? 'border-primary-500 bg-primary-50' : '',
                    form.country === 'de' ? 'opacity-40 pointer-events-none' : ''
                  ]"
                >
                  <input
                    v-model="form.language"
                    type="radio"
                    value="en"
                    class="sr-only"
                    :disabled="form.country === 'de'"
                    @change="updateProgress"
                  >
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">🇬🇧</span>
                    <div>
                      <div class="font-medium">English</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Local Presence -->
            <div>
              <label class="form-label">Do you serve customers in person?</label>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.is_local_business === true }">
                  <input v-model="form.is_local_business" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div>
                      <div class="font-medium">Yes</div>
                    </div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.is_local_business === false }">
                  <input v-model="form.is_local_business" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div>
                      <div class="font-medium">No, online only</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Weekly Capacity -->
            <div>
              <label class="form-label">How much time can you realistically spend on marketing each week?</label>
              <p class="text-sm text-gray-500 mb-3">We’ll tailor your plan to match your capacity.</p>
              <div class="grid grid-cols-2 gap-3">
                <label v-for="option in timeOptions" :key="option.value" class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.marketing_time_per_week === option.value }">
                  <input v-model="form.marketing_time_per_week" type="radio" :value="option.value" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">{{ option.icon }}</span>
                    <div>
                      <div class="font-medium">{{ option.label }}</div>
                      <div v-if="option.description" class="text-sm text-gray-500">{{ option.description }}</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Step 2: Goals & Tactics -->
          <div v-if="currentStep === 2" class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Goals</h2>
            
            <!-- Business Goals -->
            <div>
              <label class="form-label">Do you have your main business goals defined?</label>
              <p class="text-sm text-gray-500 mb-3">Choose “No” if you haven’t defined them yet or want to refine them.</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.business_goals_defined === true }">
                  <input v-model="form.business_goals_defined" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div>
                      <div class="font-medium">Yes</div>
                    </div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.business_goals_defined === false }">
                  <input v-model="form.business_goals_defined" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div>
                      <div class="font-medium">No</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Marketing Goals -->
            <div>
              <label class="form-label">Do you have your marketing goals defined?</label>
              <p class="text-sm text-gray-500 mb-3">Choose “No” if you haven’t defined them yet or want to refine them.</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.marketing_goals_defined === true }">
                  <input v-model="form.marketing_goals_defined" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div>
                      <div class="font-medium">Yes</div>
                    </div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.marketing_goals_defined === false }">
                  <input v-model="form.marketing_goals_defined" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div>
                      <div class="font-medium">No</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Step 3: Local Presence Details -->
          <div v-if="currentStep === 3 && form.is_local_business" class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Local SEO & directories</h2>
            
            <!-- Google Business Profile -->
            <div>
              <label class="form-label">Have you claimed and verified your Google Business Profile?</label>
              <p class="text-sm text-gray-500 mb-3">If it’s claimed/verified but not fully filled out, choose “Yes”.</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.google_business_claimed === true }">
                  <input v-model="form.google_business_claimed" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div class="font-medium">Yes</div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.google_business_claimed === false }">
                  <input v-model="form.google_business_claimed" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div class="font-medium">No</div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Core Directories -->
            <div>
              <label class="form-label">Have you claimed your business on Apple Business Connect and Bing Places for Business?</label>
              <p class="text-sm text-gray-500 mb-3">If you only have one of them, choose “No”.</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.core_directories_claimed === true }">
                  <input v-model="form.core_directories_claimed" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div class="font-medium">Yes</div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.core_directories_claimed === false }">
                  <input v-model="form.core_directories_claimed" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div class="font-medium">No</div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Industry Directories -->
            <div>
              <label class="form-label">Are you listed on industry-specific directories?</label>
              <p class="text-sm text-gray-500 mb-3">If you’re listed on 3–4 directories specific to your industry, choose “Yes”.</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.industry_directories_claimed === true }">
                  <input v-model="form.industry_directories_claimed" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div class="font-medium">Yes</div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.industry_directories_claimed === false }">
                  <input v-model="form.industry_directories_claimed" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div class="font-medium">No</div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Business Directories -->
            <div>
              <label class="form-label">Are you listed on general business directories?</label>
              <p class="text-sm text-gray-500 mb-3">If you’re listed on 3–4 general directories, choose “Yes” (e.g., Yelp, Yellow Pages, Gelbe Seiten).</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.business_directories_claimed === true }">
                  <input v-model="form.business_directories_claimed" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div class="font-medium">Yes</div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.business_directories_claimed === false }">
                  <input v-model="form.business_directories_claimed" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div class="font-medium">No</div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Step 4: Tools -->
          <div v-if="currentStep === 4" class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Tools & channels</h2>
            
            <!-- Website -->
            <div>
              <label class="form-label">Do you have at least a basic website?</label>
              <p class="text-sm text-gray-500 mb-3">If you have at least a simple one-page website, choose “Yes”.</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.has_website === true }">
                  <input v-model="form.has_website" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div class="font-medium">Yes</div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.has_website === false }">
                  <input v-model="form.has_website" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div class="font-medium">No</div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Email Marketing Tool -->
            <div>
              <label class="form-label">Do you use an email marketing platform to email customers?</label>
              <p class="text-sm text-gray-500 mb-3">If you don’t email customers or only email manually from your regular inbox (Gmail/Outlook), choose “No”.</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.email_marketing_tool === true }">
                  <input v-model="form.email_marketing_tool" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div class="font-medium">Yes</div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.email_marketing_tool === false }">
                  <input v-model="form.email_marketing_tool" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div class="font-medium">No</div>
                  </div>
                </label>
              </div>
            </div>

            <!-- CRM Pipeline -->
            <div>
              <label class="form-label">Do you use any CRM (or other system) to track leads?</label>
              <p class="text-sm text-gray-500 mb-3">If you use any method to track and manage leads/customers (including a spreadsheet), choose “Yes”.</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.crm_pipeline === true }">
                  <input v-model="form.crm_pipeline" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div class="font-medium">Yes</div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.crm_pipeline === false }">
                  <input v-model="form.crm_pipeline" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div class="font-medium">No</div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Running Ads -->
            <div>
              <label class="form-label">Are you currently running paid ads? <span class="font-normal">(choose all that apply)</span></label>
              <div class="grid grid-cols-2 gap-3">
                <label
                  v-for="option in adsChannelOptions"
                  :key="option.value"
                  class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
                  :class="{
                    'border-primary-500 bg-primary-50': Array.isArray(form.running_ads) && form.running_ads.includes(option.value)
                  }"
                >
                  <input
                    v-model="form.running_ads"
                    type="checkbox"
                    :value="option.value"
                    class="sr-only"
                    @change="() => { onRunningAdsChange(); updateProgress() }"
                  >
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">{{ option.icon }}</span>
                    <div>
                      <div class="font-medium">{{ option.label }}</div>
                      <div class="text-sm text-gray-500">{{ option.description }}</div>
                    </div>
                  </div>
                </label>

                <label
                  v-if="noAdsOption"
                  class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
                  :class="{
                    'border-primary-500 bg-primary-50': form.running_ads_none === true
                  }"
                >
                  <input
                    v-model="form.running_ads_none"
                    type="checkbox"
                    class="sr-only"
                    @change="() => { onRunningAdsNoneChange(); updateProgress() }"
                  >
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">{{ noAdsOption.icon }}</span>
                    <div>
                      <div class="font-medium">{{ noAdsOption.label }}</div>
                      <div class="text-sm text-gray-500">{{ noAdsOption.description }}</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Step 5: Social Media Channels -->
          <div v-if="currentStep === 5" class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Social Media</h2>
            
            <!-- Primary Social Channel -->
            <div>
              <label class="form-label">Do you have a social media account for your business?</label>
              <div class="grid grid-cols-2 gap-4 mb-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.has_primary_social_channel === true }">
                  <input v-model="form.has_primary_social_channel" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div class="font-medium">Yes</div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.has_primary_social_channel === false }">
                  <input v-model="form.has_primary_social_channel" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div class="font-medium">No</div>
                  </div>
                </label>
              </div>

              <!-- Primary Social Channel Type -->
              <div v-if="form.has_primary_social_channel === true">
                <label class="form-label">What is your primary social media platform? <span class="font-normal">(choose one)</span></label>
                <div class="grid grid-cols-3 gap-3">
                  <label v-for="channel in availablePrimaryChannels" :key="channel.value" class="relative flex flex-col items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.primary_social_channel === channel.value }">
                    <input v-model="form.primary_social_channel" type="radio" :value="channel.value" class="sr-only" @change="updateProgress">
                    <span class="text-2xl mb-2">{{ channel.icon }}</span>
                    <div class="font-medium text-sm text-center">{{ channel.label }}</div>
                  </label>
                </div>
              </div>
            </div>

            <!-- Secondary Social Channel -->
            <div>
              <label class="form-label">Do you use more than one social media platform?</label>
              <div class="grid grid-cols-2 gap-4 mb-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.has_secondary_social_channel === true }">
                  <input v-model="form.has_secondary_social_channel" type="radio" :value="true" class="sr-only" @change="updateProgress" :disabled="form.has_primary_social_channel === false">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div class="font-medium">Yes</div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.has_secondary_social_channel === false }">
                  <input v-model="form.has_secondary_social_channel" type="radio" :value="false" class="sr-only" @change="updateProgress" :disabled="form.has_primary_social_channel === false">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div class="font-medium">No</div>
                  </div>
                </label>
              </div>

              <!-- Secondary Social Channel Type -->
              <div v-if="form.has_secondary_social_channel === true">
                <label class="form-label">Which other platform do you use? <span class="font-normal">(choose one)</span></label>
                <div class="grid grid-cols-3 gap-3">
                  <label v-for="channel in availableSecondaryChannels" :key="channel.value" class="relative flex flex-col items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.secondary_social_channel === channel.value }">
                    <input v-model="form.secondary_social_channel" type="radio" :value="channel.value" class="sr-only" @change="updateProgress">
                    <span class="text-2xl mb-2">{{ channel.icon }}</span>
                    <div class="font-medium text-sm text-center">{{ channel.label }}</div>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 6: Review -->
          <div v-if="currentStep === 6" class="space-y-6">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Your answers</h2>
                <p class="text-gray-600">
                  Here’s a quick summary of what you told us. If anything looks off, you can edit it now — it only takes a second.
                </p>
                <p class="text-gray-600 mt-2">
                  When you’re happy, continue — we’ll generate your marketing plan using these answers.
                </p>
              </div>
            </div>

            <div class="space-y-6">
              <div class="border rounded-lg p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                  <h3 class="text-lg font-semibold text-gray-900">About your business</h3>
                  <button type="button" class="btn btn-outline px-4 py-2" @click="goToStep(1)">Edit</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <div class="text-sm text-gray-500">Country</div>
                    <div class="font-medium text-gray-900">{{ countryLabel }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Industry</div>
                    <div class="font-medium text-gray-900">{{ industryLabel }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Language</div>
                    <div class="font-medium text-gray-900">{{ languageLabel }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Do you serve customers in person?</div>
                    <div class="font-medium text-gray-900">{{ yesNo(form.is_local_business) }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Weekly marketing capacity</div>
                    <div class="font-medium text-gray-900">{{ marketingTimeLabel }}</div>
                  </div>
                </div>
              </div>

              <div class="border rounded-lg p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                  <h3 class="text-lg font-semibold text-gray-900">Goals</h3>
                  <button type="button" class="btn btn-outline px-4 py-2" @click="goToStep(2)">Edit</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <div class="text-sm text-gray-500">Main business goals defined</div>
                    <div class="font-medium text-gray-900">{{ yesNo(form.business_goals_defined) }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Marketing goals defined</div>
                    <div class="font-medium text-gray-900">{{ yesNo(form.marketing_goals_defined) }}</div>
                  </div>
                </div>
              </div>

              <div v-if="form.is_local_business" class="border rounded-lg p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                  <h3 class="text-lg font-semibold text-gray-900">Local SEO & directories</h3>
                  <button type="button" class="btn btn-outline px-4 py-2" @click="goToStep(3)">Edit</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <div class="text-sm text-gray-500">Google Business Profile claimed & verified</div>
                    <div class="font-medium text-gray-900">{{ yesNo(form.google_business_claimed) }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Apple Business Connect & Bing Places claimed</div>
                    <div class="font-medium text-gray-900">{{ yesNo(form.core_directories_claimed) }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Listed on industry-specific directories</div>
                    <div class="font-medium text-gray-900">{{ yesNo(form.industry_directories_claimed) }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Listed on general business directories</div>
                    <div class="font-medium text-gray-900">{{ yesNo(form.business_directories_claimed) }}</div>
                  </div>
                </div>
              </div>

              <div class="border rounded-lg p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                  <h3 class="text-lg font-semibold text-gray-900">Tools & channels</h3>
                  <button type="button" class="btn btn-outline px-4 py-2" @click="goToStep(4)">Edit</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <div class="text-sm text-gray-500">Basic website</div>
                    <div class="font-medium text-gray-900">{{ yesNo(form.has_website) }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Email marketing platform</div>
                    <div class="font-medium text-gray-900">{{ yesNo(form.email_marketing_tool) }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">CRM / lead tracking</div>
                    <div class="font-medium text-gray-900">{{ yesNo(form.crm_pipeline) }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Paid ads</div>
                    <div class="font-medium text-gray-900">{{ runningAdsLabel }}</div>
                  </div>
                </div>
              </div>

              <div class="border rounded-lg p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                  <h3 class="text-lg font-semibold text-gray-900">Social Media</h3>
                  <button type="button" class="btn btn-outline px-4 py-2" @click="goToStep(5)">Edit</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <div class="text-sm text-gray-500">Primary platform</div>
                    <div class="font-medium text-gray-900">{{ primarySocialLabel }}</div>
                  </div>
                  <div>
                    <div class="text-sm text-gray-500">Second platform</div>
                    <div class="font-medium text-gray-900">{{ secondarySocialLabel }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="flex justify-between pt-6">
            <button 
              type="button" 
              @click="prevStep" 
              v-if="currentStep > 1"
              class="btn btn-outline px-6 py-3"
            >
              ← Back
            </button>
            <div v-else></div>

            <button 
              type="submit" 
              class="btn px-6 py-3" 
              :disabled="isLoading || !canProceed"
            >
              <span v-if="isLoading" class="spinner mr-2"></span>
              {{ primaryCtaLabel }}
            </button>
          </div>
        </form>

        <!-- Success Message -->
        <div v-if="isSubmitted && planId" class="text-center">
          <div class="alert alert-success">
            <div class="text-6xl mb-4">🎉</div>
            <h3 class="text-2xl font-bold text-green-800 mb-2">Your marketing plan is ready 🎉</h3>
            <p class="text-green-700 mb-6">
              Head to your dashboard to see your personalised plan and this month’s tasks.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              <router-link to="/dashboard" class="btn btn-success px-8 py-3">
                Go to dashboard
              </router-link>
            </div>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="alert alert-error">
          <div class="flex items-center">
            <span class="text-xl mr-2">⚠️</span>
            <div>
              <div class="font-medium">Error</div>
              <div>{{ error }}</div>
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
      { value: 'none', label: 'None', description: 'You’re not currently running paid ads.', icon: '❌' }
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

<style scoped>
.progress-bar {
  @apply w-full bg-gray-200 rounded-full h-2;
}

.progress-fill {
  @apply bg-gradient-to-r from-primary-500 to-secondary-500 h-2 rounded-full transition-all duration-300;
}
</style>
