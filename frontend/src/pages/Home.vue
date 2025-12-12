<template>
  <div class="min-h-screen bg-white dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Navigation -->
    <nav class="relative px-4 md:px-6 py-3 md:py-4 bg-white">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center gap-2">
          <img src="@/assets/images/logos/logo.svg" alt="Peerie" class="h-8 md:h-12 w-auto">
          <span class="text-[#1c1a1b] text-lg md:text-xl font-bold md:hidden">Peerie</span>
        </div>

        <!-- Mobile Icons -->
        <div class="flex items-center gap-3 md:hidden">
          <button class="p-2">
            <svg class="w-5 h-5 text-[#1c1a1b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </button>
          <button class="p-2">
            <svg class="w-5 h-5 text-[#1c1a1b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </button>
          <button class="p-2">
            <svg class="w-5 h-5 text-[#1c1a1b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>
        </div>

        <!-- Desktop Action Buttons -->
        <div class="hidden md:flex items-center space-x-4">
          <router-link 
            to="/login"
            class="px-8 py-3 text-[#3F4369] border-2 border-[#3F4369] rounded-[15px] hover:bg-[#3F4369] hover:text-white transition-all duration-200 font-bold uppercase text-lg"
          >
            Log in
          </router-link>
          <router-link 
            to="/register"
            class="px-8 py-3 bg-[#F34767] text-white rounded-[15px] hover:bg-[#d93d5a] transition-all duration-200 font-bold uppercase text-lg shadow-lg"
          >
            Get started
          </router-link>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative px-4 md:px-6 py-12 md:py-20 bg-white overflow-hidden min-h-[400px] md:min-h-[600px]">
      <!-- Decorative Background Elements - rotated rectangles -->
      <div class="absolute top-[150px] md:top-[296px] left-[calc(50%+10px)] md:left-[calc(50%+30px)] w-[300px] md:w-[848px] h-[260px] md:h-[737px] bg-[#FFEB88] rounded-[40px] md:rounded-[108px] transform rotate-[347.08deg]"></div>
      <div class="absolute top-[130px] md:top-[278px] left-[calc(75%+-3px)] w-[300px] md:w-[848px] h-[260px] md:h-[737px] bg-[#3F4369] rounded-[40px] md:rounded-[108px] transform rotate-[347.08deg] opacity-50 md:opacity-100"></div>
      <div class="absolute top-[250px] md:top-[533px] left-[calc(87.5%+40px)] md:left-[calc(87.5%+119px)] w-[200px] md:w-[848px] h-[200px] md:h-[737px] bg-[#1C8E9E] rounded-[40px] md:rounded-[108px] transform rotate-[5.202deg] opacity-50 md:opacity-100"></div>
      
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 items-center">
          <!-- Left Column - Text -->
          <div>
          <!-- Main Headline -->
            <h1 class="text-4xl md:text-7xl lg:text-[160px] font-bold text-[#1C1A1B] mb-4 md:mb-8 leading-[0.95]">
              Digital marketing made
            </h1>
            <div class="relative inline-block mb-6 md:mb-8">
              <h1 class="text-4xl md:text-7xl lg:text-[160px] font-bold text-[#F34767] leading-[0.95] transform rotate-[-9.59deg] relative">
                Simple
          </h1>
              <div class="absolute bottom-0 left-0 w-full h-[8px] md:h-[20px] bg-[#F34767] transform rotate-[-9.99deg]"></div>
            </div>
          
          <!-- Subtitle -->
            <p class="text-base md:text-2xl lg:text-3xl text-[#1C1A1B] mb-8 md:mb-12 max-w-2xl leading-relaxed">
            Grow online without the stress — with a plan, templates, and tools tailored to your business.
          </p>
          
          <!-- CTA Buttons -->
            <div class="flex flex-col gap-4 md:gap-6 items-start">
            <router-link 
              to="/register"
                class="px-6 md:px-8 py-3 md:py-4 bg-[#F34767] text-white rounded-[30px] md:rounded-[40px] hover:bg-[#d93d5a] transition-all duration-200 font-bold text-base md:text-xl uppercase shadow-xl"
            >
              Get started
            </router-link>
              <p class="text-sm md:text-lg text-[#3F4369] font-bold">
                Start your 7 day free trial — cancel anytime!
              </p>
            </div>
          </div>
          
          <!-- Right Column - Decorative Elements -->
          <div class="relative hidden lg:block">
            <div class="absolute top-0 right-0 w-[100px] h-[100px] bg-white rounded-[15px] flex items-center justify-center">
              <svg class="w-[60px] h-[60px] text-[#1C1A1B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 4 Colorful Blocks Section + Save Time Block -->
    <div class="px-4 md:px-6 py-16 md:py-32 bg-white relative overflow-hidden">
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
          <!-- Block 1: Focus on What Works - Red/Pink -->
          <div class="bg-[#F34767] rounded-[30px] md:rounded-[70px] p-6 md:p-12 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[200px] md:w-[497px] h-[240px] md:h-[598px] bg-white/10 rounded-[25px] md:rounded-[50px] transform rotate-[353.893deg]"></div>
            <h3 class="text-2xl md:text-[64px] font-bold mb-4 md:mb-6 leading-tight md:leading-none relative z-10">Focus on What Works for Your Business</h3>
            <p class="text-base md:text-[32px] leading-relaxed md:leading-[1.2] relative z-10">
              Receive a step‑by‑step marketing plan tailored to your business—no cookie‑cutter playbooks. Know where to start and what to do each week to see real growth.
            </p>
          </div>

          <!-- Block 2: Generate Revenue - Yellow -->
          <div class="bg-[#FFEB88] rounded-[30px] md:rounded-[70px] p-6 md:p-12 text-[#3F4369] relative overflow-hidden">
            <div class="absolute top-0 left-0 w-[180px] md:w-[467px] h-[150px] md:h-[378px] bg-white/50 rounded-[25px] md:rounded-[50px] transform rotate-[5.861deg]"></div>
            <h3 class="text-3xl md:text-[96px] font-bold mb-4 md:mb-6 leading-tight md:leading-none relative z-10">Generate Revenue</h3>
            <p class="text-base md:text-[32px] leading-relaxed md:leading-[1.2] relative z-10">
              Reach clients where they already are and guide them to purchase.
            </p>
          </div>

          <!-- Block 3: Stay Consistent - Light Gray -->
          <div class="bg-[#DCDCDC] rounded-[30px] md:rounded-[70px] p-6 md:p-12 text-[#1C1A1B] relative overflow-hidden hidden md:block">
            <div class="absolute top-0 left-0 w-[220px] md:w-[545px] h-[280px] md:h-[710px] bg-white/50 rounded-[35px] md:rounded-[70px]"></div>
            <h3 class="text-2xl md:text-[64px] font-bold mb-4 md:mb-6 leading-tight md:leading-none relative z-10">Stay Consistent</h3>
            <p class="text-base md:text-[32px] leading-relaxed md:leading-[1.2] relative z-10">
              Join a community of fellow small‑business owners solving the same challenges. Share feedback, swap tips and get accountability—because marketing works best when you're not doing it alone.
            </p>
          </div>

          <!-- Block 4: Stop Losing Nights - Teal -->
          <div class="bg-[#1C8E9E] rounded-[30px] md:rounded-[70px] p-6 md:p-12 text-white relative overflow-hidden hidden md:block">
            <div class="absolute top-0 left-0 w-[170px] md:w-[430px] h-[160px] md:h-[394px] bg-white/42 rounded-[25px] md:rounded-[50px] transform rotate-[356.689deg]"></div>
            <h3 class="text-2xl md:text-[64px] font-bold mb-4 md:mb-6 leading-tight md:leading-none relative z-10">Stop Losing Nights to Marketing</h3>
            <p class="text-base md:text-[32px] leading-relaxed md:leading-[1.2] relative z-10">
              Need more hands on deck? Our expert team can handle website development or provide individual consulting, so you stay focused on running your business.
            </p>
          </div>
        </div>

        <!-- Save Time & Increase Productivity Block -->
        <div class="mt-8 md:mt-16 bg-[#3F4369] rounded-[30px] md:rounded-[70px] p-6 md:p-12 text-white relative overflow-hidden hidden md:block">
          <div class="absolute -bottom-10 md:-bottom-20 right-0 w-[200px] md:w-[480px] h-[150px] md:h-[360px] bg-white/10 rounded-[25px] md:rounded-[50px] transform rotate-[4.9deg]"></div>
          <h3 class="text-3xl md:text-[64px] lg:text-[96px] font-bold mb-4 md:mb-6 leading-tight md:leading-[0.95] relative z-10">
            Save Time & Increase Productivity
          </h3>
          <p class="text-base md:text-[32px] leading-relaxed md:leading-[1.2] max-w-3xl relative z-10">
            Create content in minutes using industry-specific ideas and templates and stay productive without a burnout.
          </p>
        </div>
      </div>
    </div>

    <!-- No Generic Templates Section - Pink Background -->
    <div class="px-4 md:px-6 py-16 md:py-32 bg-[#F34767] relative overflow-hidden">
      <div class="absolute top-0 right-0 w-[300px] md:w-[839px] h-[280px] md:h-[787px] bg-[#3F4369] rounded-[30px] md:rounded-[70px] transform rotate-[7.38deg] opacity-50 md:opacity-100"></div>
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 items-center">
          <div class="text-white">
            <h2 class="text-2xl md:text-[64px] font-bold mb-6 md:mb-8 leading-tight md:leading-none">
              No generic or one-size-fits-all templates
            </h2>
            <p class="text-lg md:text-[36px] leading-relaxed md:leading-[1.2] mb-4 md:mb-6">
              We optimise strategy and content specifically for
            </p>
            <p class="text-3xl md:text-[64px] font-bold mb-4 md:mb-6 leading-tight md:leading-none">
              YOUR BUSINESS,
            </p>
            <p class="text-lg md:text-[36px] leading-relaxed md:leading-[1.2] mb-6 md:mb-8">
              removing the guesswork and hours of customisation!
            </p>
            <p class="text-xl md:text-[64px] leading-relaxed md:leading-[1.2] mb-6 md:mb-8">
              All at the cost of a <span class="underline">monthly subscription</span> instead of an agency retainer.
            </p>
            <router-link 
              to="/register"
              class="inline-block px-6 md:px-8 py-3 md:py-4 bg-white text-[#F34767] rounded-[30px] md:rounded-[40px] hover:bg-[#FFEBD0] transition-all duration-200 font-bold text-base md:text-[40px] uppercase shadow-xl"
            >
              Get started
            </router-link>
          </div>
            </div>
            </div>
          </div>

    <!-- FAQ Section with Accordion -->
    <div class="px-4 md:px-6 py-16 md:py-32 bg-[#FFEB88] relative overflow-hidden">
      <div class="absolute top-0 left-0 w-[150px] md:w-[370px] h-[155px] md:h-[383px] bg-[#7FB5B5] rounded-[50px] md:rounded-[100px]"></div>
      <div class="absolute top-0 left-[calc(25%+60px)] md:left-[calc(25%+167px)] w-[150px] md:w-[370px] h-[155px] md:h-[383px] bg-[#7FB5B5] rounded-[50px] md:rounded-[100px]"></div>
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12">
          <div>
            <h2 class="text-3xl md:text-7xl font-bold text-[#1C1A1B] mb-6 md:mb-8">
              Still wondering if <span class="text-[#F34767]">Peerie</span> <span class="underline decoration-[#FFEB88]">right for you?</span>
            </h2>
          </div>
          <div class="space-y-3 md:space-y-4">
            <div 
              v-for="(item, index) in faqItems" 
              :key="index"
              class="bg-[#FFEB88] rounded-[15px] md:rounded-[20px] p-4 md:p-6 cursor-pointer transition-all"
              @click="toggleFaq(index)"
            >
              <div class="flex items-start justify-between gap-3">
                <p class="text-[#1C1A1B] text-sm md:text-lg font-medium flex-1">
                  <span v-html="item.question"></span>
                </p>
                <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#1C1A1B] flex items-center justify-center flex-shrink-0">
                  <svg 
                    class="w-4 h-4 md:w-5 md:h-5 text-[#FFEB88] transform transition-transform"
                    :class="{ 'rotate-45': item.isOpen }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                </div>
            </div>
              <div 
                v-if="item.isOpen" 
                class="mt-3 md:mt-4 text-[#1C1A1B] text-sm md:text-lg"
              >
                {{ item.answer }}
              </div>
            </div>
          </div>
            </div>
          </div>
        </div>

    <!-- Try Peerie for Free Section -->
    <div class="px-4 md:px-6 py-16 md:py-32 bg-white">
      <div class="max-w-7xl mx-auto text-center">
        <h2 class="text-4xl md:text-7xl lg:text-[250px] font-bold text-[#1C1A1B] mb-2 md:mb-4 leading-[0.95] tracking-[-2px] md:tracking-[-10px]">
          Try Peerie
        </h2>
        <h2 class="text-4xl md:text-7xl lg:text-[250px] font-bold text-[#F34767] mb-6 md:mb-8 leading-[0.95] tracking-[-2px] md:tracking-[-10px]">
          <span class="text-[#F34767]">for free!</span>
        </h2>
        <p class="text-lg md:text-[40px] font-bold text-[#3F4369] mb-8 md:mb-12">
          Start your 7‑day FREE trial — cancel anytime!
        </p>
        <router-link 
          to="/register"
          class="inline-block px-8 md:px-12 py-4 md:py-6 bg-[#F34767] text-white rounded-[30px] md:rounded-[40px] hover:bg-[#d93d5a] transition-all duration-200 font-bold text-base md:text-[40px] uppercase shadow-xl"
        >
          Get started
        </router-link>
      </div>
    </div>

    <!-- What's Inside Section -->
    <div class="px-4 md:px-6 py-16 md:py-32 bg-white relative overflow-hidden">
      <div class="absolute top-0 left-0 w-[300px] md:w-[612px] h-[230px] md:h-[472px] bg-[#DCDCDC] opacity-50 rounded-[25px] md:rounded-[50px] transform rotate-[3.523deg]"></div>
      <div class="max-w-7xl mx-auto relative z-10">
        <h2 class="text-4xl md:text-7xl lg:text-[144px] font-bold text-[#1C1A1B] mb-10 md:mb-20 leading-[0.95]">
            What's Inside?
          </h2>
        
        <div class="space-y-6 md:space-y-12">
          <div class="flex items-start gap-4">
            <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#1C8E9E] flex items-center justify-center flex-shrink-0 mt-1">
              <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg md:text-[96px] font-bold text-[#1C1A1B] mb-1 md:mb-2 leading-tight md:leading-[0.95]">Digital marketing strategy & execution plan</h3>
            </div>
          </div>
          
          <div class="flex items-start gap-4">
            <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#1C8E9E] flex items-center justify-center flex-shrink-0 mt-1">
              <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg md:text-[96px] font-bold text-[#1C1A1B] mb-1 md:mb-2 leading-tight md:leading-[0.95]">Content calendar</h3>
            </div>
          </div>
          
          <div class="flex items-start gap-4">
            <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#1C8E9E] flex items-center justify-center flex-shrink-0 mt-1">
              <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg md:text-[96px] font-bold text-[#1C1A1B] mb-1 md:mb-2 leading-tight md:leading-[0.95]">Image library</h3>
            </div>
          </div>
          
          <div class="flex items-start gap-4">
            <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#1C8E9E] flex items-center justify-center flex-shrink-0 mt-1">
              <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg md:text-[96px] font-bold text-[#1C1A1B] mb-1 md:mb-2 leading-tight md:leading-[0.95]">Content creator</h3>
            </div>
          </div>
          
          <div class="flex items-start gap-4">
            <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#1C8E9E] flex items-center justify-center flex-shrink-0 mt-1">
              <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg md:text-[96px] font-bold text-[#1C1A1B] mb-1 md:mb-2 leading-tight md:leading-[0.95]">Hashtag library</h3>
            </div>
          </div>
          
          <div class="flex items-start gap-4">
            <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#1C8E9E] flex items-center justify-center flex-shrink-0 mt-1">
              <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg md:text-[96px] font-bold text-[#1C1A1B] mb-1 md:mb-2 leading-tight md:leading-[0.95]">Post opportunity</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Why Peerie Section -->
    <div id="features" class="px-4 md:px-6 py-16 md:py-32 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12 md:mb-20">
          <h2 class="text-4xl md:text-7xl font-bold text-[#1C1A1B] mb-4">
            Why <span class="text-[#F34767]">Peerie?</span>
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
          <!-- Card 1: Easy & Affordable -->
          <div class="bg-white border border-[#DCDCDC] rounded-xl md:rounded-2xl p-6 md:p-8 shadow-lg">
            <div class="w-12 h-12 md:w-16 md:h-16 bg-[#1C8E9E] rounded-xl md:rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            <h3 class="text-xl md:text-2xl font-bold text-[#3F4369] mb-3 md:mb-4">Easy & Affordable</h3>
            <p class="text-base md:text-lg text-[#3F4369]">
              Subscription-based pricing keeps costs predictable, with add-on services when you need to get it done for you.
            </p>
            </div>

          <!-- Card 2: Tailored Solution -->
          <div class="bg-white border border-[#DCDCDC] rounded-xl md:rounded-2xl p-6 md:p-8 shadow-lg">
            <div class="w-12 h-12 md:w-16 md:h-16 bg-[#1C8E9E] rounded-xl md:rounded-2xl flex items-center justify-center mb-4 md:mb-6">
              <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              </div>
            <h3 class="text-xl md:text-2xl font-bold text-[#3F4369] mb-3 md:mb-4">Tailored Solution</h3>
            <p class="text-base md:text-lg text-[#3F4369]">
              Our resources aren't generic—they're crafted with your industry in mind, so you get relevant, ready-to-use content without spending hours tweaking it to fit.
            </p>
          </div>

          <!-- Card 3: Constantly Evolving (Desktop only) -->
          <div class="hidden lg:block bg-white border border-[#DCDCDC] rounded-2xl p-8 shadow-lg">
            <div class="w-16 h-16 bg-[#7FB5B5] rounded-2xl flex items-center justify-center mb-6">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
              </div>
            <h3 class="text-2xl font-bold text-[#3F4369] mb-4">Constantly Evolving</h3>
            <p class="text-lg text-[#3F4369]">
              Templates and strategies are updated in real time to match the latest trends and best practices, so you never fall behind.
            </p>
          </div>

          <!-- Card 4: More Than Just Templates (Desktop only) -->
          <div class="hidden lg:block bg-white border border-[#DCDCDC] rounded-2xl p-8 shadow-lg">
            <div class="w-16 h-16 bg-[#7FB5B5] rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-[#3F4369] mb-4">More Than Just Templates</h3>
            <p class="text-lg text-[#3F4369]">
              Peerie combines plug-and-play content with strategic guidance tailored to your industry, so every action supports your business goals.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- How It Works Section -->
    <div class="px-4 md:px-6 py-16 md:py-32 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12 md:mb-20">
          <h2 class="text-4xl md:text-7xl lg:text-[128px] font-bold text-[#1C1A1B] mb-4 md:mb-8 leading-none">
            How It Works
          </h2>
          <div class="w-[200px] md:w-[386px] h-[10px] md:h-[20px] bg-[#F34767] mx-auto"></div>
        </div>
        
        <div class="space-y-8 md:space-y-12">
          <!-- Step 1 -->
          <div class="flex items-start space-x-4 md:space-x-8">
            <div class="w-[60px] h-[60px] md:w-[140px] md:h-[140px] bg-[#3F4369] rounded-[25px] md:rounded-[55px] flex items-center justify-center flex-shrink-0">
              <span class="text-white font-bold text-2xl md:text-[64px]">1</span>
            </div>
            <div class="flex-1">
              <h3 class="text-xl md:text-[48px] font-bold text-[#1C1A1B] mb-2 md:mb-4 leading-tight md:leading-[0.95]">Tell us about your business</h3>
              <p class="text-sm md:text-[24px] text-[#1C1A1B] leading-relaxed md:leading-[1.2]">
                Sign up and answer a few quick questions—industry, goals, audience, location, content preferences. The more you share, the sharper your plan.
              </p>
            </div>
          </div>
          
          <!-- Step 2 -->
          <div class="flex items-start space-x-4 md:space-x-8">
            <div class="w-[60px] h-[60px] md:w-[140px] md:h-[140px] bg-[#3F4369] rounded-[25px] md:rounded-[55px] flex items-center justify-center flex-shrink-0">
              <span class="text-white font-bold text-2xl md:text-[64px]">2</span>
            </div>
            <div class="flex-1">
              <h3 class="text-xl md:text-[48px] font-bold text-[#1C1A1B] mb-2 md:mb-4 leading-tight md:leading-[0.95]">Receive a custom plan. Peerie will generate a personalized marketing plan and resources for your business.</h3>
              <p class="text-sm md:text-[24px] text-[#1C1A1B] leading-relaxed md:leading-[1.2]">
                We turn your answers into a clear 30-day roadmap: what to focus on, where to show up, and the exact order to do it.
              </p>
            </div>
          </div>
          
          <!-- Step 3-5 (Desktop only) -->
          <div class="hidden md:block space-y-12">
            <!-- Step 3 -->
            <div class="flex items-start space-x-8">
              <div class="w-[140px] h-[140px] bg-[#3F4369] rounded-[55px] flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold text-[64px]">3</span>
              </div>
              <div class="flex-1">
                <h3 class="text-[48px] font-bold text-[#1C1A1B] mb-4 leading-[0.95]">Open your workspace</h3>
                <p class="text-[24px] text-[#1C1A1B] leading-[1.2]">
                  Your account is preloaded with your plan, content ideas, templates, and workbooks — tuned to your industry and country.
                </p>
              </div>
            </div>
            
            <!-- Step 4 -->
            <div class="flex items-start space-x-8">
              <div class="w-[140px] h-[140px] bg-[#3F4369] rounded-[55px] flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold text-[64px]">4</span>
              </div>
              <div class="flex-1">
                <h3 class="text-[48px] font-bold text-[#1C1A1B] mb-4 leading-[0.95]">Follow the plan</h3>
                <p class="text-[24px] text-[#1C1A1B] leading-[1.2]">
                  Each week, open the checklist and deliver: post content, send email, tick the SEO task. Simple. No starting from scratch.
                </p>
              </div>
            </div>
            
            <!-- Step 5 -->
            <div class="flex items-start space-x-8">
              <div class="w-[140px] h-[140px] bg-[#3F4369] rounded-[55px] flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold text-[64px]">5</span>
              </div>
              <div class="flex-1">
                <h3 class="text-[48px] font-bold text-[#1C1A1B] mb-4 leading-[0.95]">Keep momentum</h3>
                <p class="text-[24px] text-[#1C1A1B] leading-[1.2]">
                  Your plan refreshes monthly with new ideas and templates—so staying consistent stays easy.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Trusted by Small Businesses Section -->
    <div class="px-4 md:px-6 py-16 md:py-32 bg-[#7FB5B5]">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12 md:mb-20">
          <h2 class="text-3xl md:text-7xl lg:text-[128px] font-bold text-[#1C1A1B] mb-6 md:mb-8 leading-none">
            Trusted by <span class="text-[#F34767]">Small Businesses</span>
          </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
          <!-- Testimonial 1 -->
          <div class="bg-white rounded-[50px] md:rounded-[100px] p-6 md:p-8 shadow-xl">
            <div class="w-full h-[200px] md:h-[383px] bg-[#7FB5B5] rounded-[50px] md:rounded-[100px] mb-4 md:mb-6 flex items-center justify-center relative overflow-hidden">
              <div class="absolute w-[120px] md:w-[292px] h-[110px] md:h-[261px] bg-white/50 rounded-[25px] md:rounded-[50px] transform rotate-[9.924deg]"></div>
              <div class="relative z-10 w-16 h-16 md:w-24 md:h-24 bg-[#3F4369] rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-lg md:text-2xl">A</span>
              </div>
            </div>
            <p class="text-sm md:text-lg text-[#1C1A1B] text-center">
              Lorem ipsum dolor sit amet consectetur.
            </p>
          </div>
          
          <!-- Testimonial 2 -->
          <div class="bg-white rounded-[50px] md:rounded-[100px] p-6 md:p-8 shadow-xl">
            <div class="w-full h-[200px] md:h-[383px] bg-[#7FB5B5] rounded-[50px] md:rounded-[100px] mb-4 md:mb-6 flex items-center justify-center relative overflow-hidden">
              <div class="absolute w-[120px] md:w-[292px] h-[110px] md:h-[261px] bg-white/50 rounded-[25px] md:rounded-[50px] transform rotate-[351.683deg]"></div>
              <div class="relative z-10 w-16 h-16 md:w-24 md:h-24 bg-[#3F4369] rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-lg md:text-2xl">B</span>
              </div>
            </div>
            <p class="text-sm md:text-lg text-[#1C1A1B] text-center">
              Lorem ipsum dolor sit amet consectetur.
            </p>
          </div>
          
          <!-- Testimonial 3 -->
          <div class="bg-white rounded-[50px] md:rounded-[100px] p-6 md:p-8 shadow-xl hidden md:block">
            <div class="w-full h-[383px] bg-[#7FB5B5] rounded-[100px] mb-6 flex items-center justify-center relative overflow-hidden">
              <div class="absolute w-[292px] h-[261px] bg-white/50 rounded-[50px] transform rotate-[4.74deg]"></div>
              <div class="relative z-10 w-24 h-24 bg-[#3F4369] rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-2xl">C</span>
              </div>
            </div>
            <p class="text-lg text-[#1C1A1B] text-center">
              Lorem ipsum dolor sit amet consectetur.
            </p>
          </div>
          
          <!-- Testimonial 4 -->
          <div class="bg-white rounded-[50px] md:rounded-[100px] p-6 md:p-8 shadow-xl hidden lg:block">
            <div class="w-full h-[383px] bg-[#7FB5B5] rounded-[100px] mb-6 flex items-center justify-center relative overflow-hidden">
              <div class="absolute w-[292px] h-[261px] bg-white/50 rounded-[50px] transform rotate-[353.303deg]"></div>
              <div class="relative z-10 w-24 h-24 bg-[#3F4369] rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-2xl">D</span>
              </div>
            </div>
            <p class="text-lg text-[#1C1A1B] text-center">
              Lorem ipsum dolor sit amet consectetur.
            </p>
          </div>
        </div>
      </div>
    </div>

        <!-- Final CTA Section -->
        <div class="px-4 md:px-6 py-16 md:py-32 bg-[#F34767] relative overflow-hidden">
      <div class="max-w-7xl mx-auto text-center relative z-10">
        <h2 class="text-4xl md:text-7xl lg:text-[128px] font-bold text-white mb-6 md:mb-8 leading-none">
          Get Started
        </h2>
        <p class="text-lg md:text-[36px] text-white mb-8 md:mb-12 leading-relaxed md:leading-[1.2]">
          Sign up for your tailored digital marketing strategy and resources now
        </p>
        <router-link 
          to="/register"
          class="inline-block px-8 md:px-12 py-4 md:py-6 bg-white text-[#F34767] rounded-[30px] md:rounded-[40px] hover:bg-[#FFEBD0] transition-all duration-200 font-bold text-base md:text-[40px] uppercase shadow-xl"
        >
          Get started
        </router-link>
      </div>
    </div>


  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import logoImage from '@/assets/images/logos/logo.svg'

const faqItems = ref([
  {
    question: "You realise that digital marketing can generate more revenue, but don't know <strong>where to start?</strong>",
    answer: "We provide you with a strategy tailored to your specific business, all you need to do is to follow the steps and see your business grow.",
    isOpen: false
  },
  {
    question: "You love what you do and want to <strong>spend more time with your customers</strong> instead of spending hours on creating content?",
    answer: "We have you covered with plug-and-play templates and content ideas for your industry - saving you hours every week.",
    isOpen: false
  },
  {
    question: "You would love to post regularly, but have <strong>no idea what to share?</strong>",
    answer: "We deliver fresh content ideas for your specific industry—on social media, email marketing, and your website.",
    isOpen: false
  },
  {
    question: "You have tried digital marketing before, but had <strong>given up before you saw any results?</strong>",
    answer: "We make digital marketing easy to run and help you stay on track.",
    isOpen: true
  },
  {
    question: "You're already posting like a pro, but want to <strong>explore other</strong> aspects of digital marketing?",
    answer: "We help you start your email marketing campaigns, optimise for local SEO, and build your website.",
    isOpen: false
  }
])

const toggleFaq = (index) => {
  faqItems.value[index].isOpen = !faqItems.value[index].isOpen
}
</script>
