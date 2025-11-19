<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="card p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <img :src="logoImage" alt="Peerie Logo" class="w-40 h-40 mx-auto">
          <h1 class="text-3xl font-bold text-gray-900 mb-4">Create Marketing Plan</h1>
          <p class="text-gray-600 text-lg">
            Answer questions in several steps to create a personalized plan
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
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Business Profile</h2>
            
            <!-- Country -->
            <div>
              <label class="form-label">Country of Operation</label>
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
              <label class="form-label">Industry</label>
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
              <label class="form-label">Primary language for marketing</label>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.language === 'de' }">
                  <input v-model="form.language" type="radio" value="de" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">🇩🇪</span>
                    <div>
                      <div class="font-medium">DE</div>
                      <div class="text-sm text-gray-500">German</div>
                    </div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.language === 'en' }">
                  <input v-model="form.language" type="radio" value="en" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">🇬🇧</span>
                    <div>
                      <div class="font-medium">EN</div>
                      <div class="text-sm text-gray-500">English</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Local Presence -->
            <div>
              <label class="form-label">Local Presence</label>
              <p class="text-sm text-gray-500 mb-3">Do you have a physical presence or operate locally?</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.is_local_business === true }">
                  <input v-model="form.is_local_business" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div>
                      <div class="font-medium">Yes</div>
                      <div class="text-sm text-gray-500">We operate locally</div>
                    </div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.is_local_business === false }">
                  <input v-model="form.is_local_business" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div>
                      <div class="font-medium">No</div>
                      <div class="text-sm text-gray-500">Online only</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Weekly Capacity -->
            <div>
              <label class="form-label">Weekly marketing tasks load</label>
              <div class="grid grid-cols-3 gap-3">
                <label v-for="option in timeOptions" :key="option.value" class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.marketing_time_per_week === option.value }">
                  <input v-model="form.marketing_time_per_week" type="radio" :value="option.value" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">{{ option.icon }}</span>
                    <div>
                      <div class="font-medium">{{ option.label }}</div>
                      <div class="text-sm text-gray-500">{{ option.description }}</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Step 2: Goals & Tactics -->
          <div v-if="currentStep === 2" class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Goals & Tactics</h2>
            
            <!-- Business Goals -->
            <div>
              <label class="form-label">Business goals defined</label>
              <p class="text-sm text-gray-500 mb-3">Do you have clearly defined business goals and KPIs?</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.business_goals_defined === true }">
                  <input v-model="form.business_goals_defined" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div>
                      <div class="font-medium">Yes</div>
                      <div class="text-sm text-gray-500">Goals are set</div>
                    </div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.business_goals_defined === false }">
                  <input v-model="form.business_goals_defined" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div>
                      <div class="font-medium">No</div>
                      <div class="text-sm text-gray-500">Not yet</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Marketing Goals -->
            <div>
              <label class="form-label">Marketing goals defined</label>
              <p class="text-sm text-gray-500 mb-3">Do you have clearly defined marketing goals and strategy?</p>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.marketing_goals_defined === true }">
                  <input v-model="form.marketing_goals_defined" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div>
                      <div class="font-medium">Yes</div>
                      <div class="text-sm text-gray-500">Strategy ready</div>
                    </div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.marketing_goals_defined === false }">
                  <input v-model="form.marketing_goals_defined" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div>
                      <div class="font-medium">No</div>
                      <div class="text-sm text-gray-500">Not yet</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Step 3: Local Presence Details -->
          <div v-if="currentStep === 3 && form.is_local_business" class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Local Presence Details</h2>
            
            <!-- Google Business Profile -->
            <div>
              <label class="form-label">Google Business Profile claimed & filled out</label>
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
              <label class="form-label">Core directories claimed</label>
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
              <label class="form-label">Industry directories claimed</label>
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
              <label class="form-label">Business directories claimed</label>
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
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Tools</h2>
            
            <!-- Website -->
            <div>
              <label class="form-label">Website in place</label>
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
              <label class="form-label">Email marketing tool in place</label>
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
              <label class="form-label">CRM or simple pipeline to track leads</label>
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
              <label class="form-label">Running ads</label>
              <div class="grid grid-cols-2 gap-3">
                <label v-for="option in adsOptions" :key="option.value" class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.running_ads === option.value }">
                  <input v-model="form.running_ads" type="radio" :value="option.value" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">{{ option.icon }}</span>
                    <div>
                      <div class="font-medium">{{ option.label }}</div>
                      <div class="text-sm text-gray-500">{{ option.description }}</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Step 5: Social Media Channels -->
          <div v-if="currentStep === 5" class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Social Media Channels</h2>
            
            <!-- Primary Social Channel -->
            <div>
              <label class="form-label">Primary social media channel</label>
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
                <label class="form-label">Select primary channel</label>
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
              <label class="form-label">Secondary social media channel</label>
              <div class="grid grid-cols-2 gap-4 mb-4">
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.has_secondary_social_channel === true }">
                  <input v-model="form.has_secondary_social_channel" type="radio" :value="true" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div class="font-medium">Yes</div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-primary-500 bg-primary-50': form.has_secondary_social_channel === false }">
                  <input v-model="form.has_secondary_social_channel" type="radio" :value="false" class="sr-only" @change="updateProgress">
                  <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div class="font-medium">No</div>
                  </div>
                </label>
              </div>

              <!-- Secondary Social Channel Type -->
              <div v-if="form.has_secondary_social_channel === true">
                <label class="form-label">Select secondary channel</label>
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
              {{ isLoading ? 'Creating plan...' : isLastStep ? 'Create Plan' : 'Next →' }}
            </button>
          </div>
        </form>

        <!-- Success Message -->
        <div v-if="isSubmitted && planId" class="text-center">
          <div class="alert alert-success">
            <div class="text-6xl mb-4">🎉</div>
            <h3 class="text-2xl font-bold text-green-800 mb-2">Plan successfully created!</h3>
            <p class="text-green-700 mb-6">
              Your personalized marketing plan is ready. Welcome to your dashboard!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              <router-link to="/dashboard" class="btn btn-success px-8 py-3">
                Go to Dashboard
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
import logoImage from '@/assets/images/logos/logo.png'

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
      return form.value.is_local_business === false ? 4 : 5
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
      running_ads: '',
      
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
      { value: 2, label: '2h', description: 'Minimal', icon: '⏰' },
      { value: 4, label: '4h', description: 'Standard', icon: '⏱️' },
      { value: 6, label: '6h+', description: 'Active', icon: '🚀' }
    ]

    const adsOptions = [
      { value: 'retargeting', label: 'Retargeting', description: 'Re-engage visitors', icon: '🎯' },
      { value: 'paid_search', label: 'Paid Search', description: 'Google/Bing ads', icon: '🔍' },
      { value: 'prospecting_social', label: 'Prospecting social', description: 'Social prospecting', icon: '📱' },
      { value: 'none', label: 'None', description: 'No ads yet', icon: '❌' }
    ]

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
      1: 'Business Profile',
      2: 'Goals & tactics',
      3: 'Local presence',
      4: 'Tools',
      5: 'Social Media Channels'
    }

    const currentStepTitle = computed(() => stepTitles[currentStep.value])
    
    const displayStep = computed(() => {
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
      if (form.value.running_ads) completed++

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

    const isLastStep = computed(() => {
      return currentStep.value === 5
    })

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
                 form.value.running_ads !== ''
        case 5:
          return form.value.has_primary_social_channel !== null &&
                 form.value.has_secondary_social_channel !== null &&
                 (form.value.has_primary_social_channel === false || form.value.primary_social_channel) &&
                 (form.value.has_secondary_social_channel === false || form.value.secondary_social_channel)
        default:
          return false
      }
    })

    const updateProgress = () => {
    }

    const nextStep = async () => {
      if (!canProceed.value) return

      if (isLastStep.value) {
        await submitQuestionnaire()
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

    const submitQuestionnaire = async () => {
      isLoading.value = true
      error.value = ''

      try {
        const submitData = {
          ...form.value,
          business_niche: form.value.industry
        }

        const response = await axios.post('/api/questionnaire', submitData)
        
        if (response.data.success) {
          planId.value = response.data.plan.id
          isSubmitted.value = true
        } else {
          error.value = response.data.message || 'Произошла ошибка при создании плана'
        }
      } catch (err) {
        if (err.response?.data?.errors) {
          const errors = Object.values(err.response.data.errors).flat()
          error.value = errors.join(', ')
        } else {
          error.value = err.response?.data?.message || 'Произошла ошибка при создании плана'
        }
      } finally {
        isLoading.value = false
      }
    }

    watch(() => form.value.has_primary_social_channel, (newValue) => {
      if (newValue === false) {
        form.value.primary_social_channel = ''
      }
    })
    
    watch(() => form.value.has_secondary_social_channel, (newValue) => {
      if (newValue === false) {
        form.value.secondary_social_channel = ''
      }
    })

    onMounted(async () => {
      const savedUser = localStorage.getItem('user')
      if (!savedUser) {
        router.push('/login')
        return
      }

      try {
        const response = await axios.get('/api/plans')
        if (response.data && response.data.length > 0) {
          router.push('/dashboard')
        }
      } catch (error) {
        console.log('Не удалось проверить существующие планы')
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
      isLastStep,
      canProceed,
      countries,
      industries,
      timeOptions,
      adsOptions,
      socialChannels,
      availablePrimaryChannels,
      availableSecondaryChannels,
      updateProgress,
      nextStep,
      prevStep,
      submitQuestionnaire,
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
