<template>
  <div class="min-h-screen bg-white dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Navigation -->
    <nav class="relative px-6 py-4 bg-white">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
          <img src="@/assets/images/logos/logo.svg" alt="Peerie" class="h-12 w-auto">
        </div>

        <!-- Navigation Links -->
        <div class="hidden md:flex items-center space-x-8">
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Home</a>
          <div class="relative group">
            <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors flex items-center">
              Industries
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </a>
          </div>
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Services</a>
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Blog</a>
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Pricing</a>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-4">
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
    <div class="relative px-6 py-20 bg-white overflow-hidden min-h-[600px]">
      <!-- Decorative Background Elements - rotated rectangles -->
      <div class="absolute top-[296px] left-[calc(50%+30px)] w-[848px] h-[737px] bg-[#FFEB88] rounded-[108px] transform rotate-[347.08deg]"></div>
      <div class="absolute top-[278px] left-[calc(75%+-3px)] w-[848px] h-[737px] bg-[#3F4369] rounded-[108px] transform rotate-[347.08deg]"></div>
      <div class="absolute top-[533px] left-[calc(87.5%+119px)] w-[848px] h-[737px] bg-[#1C8E9E] rounded-[108px] transform rotate-[5.202deg]"></div>
      
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <!-- Left Column - Text -->
          <div>
          <!-- Main Headline -->
            <h1 class="text-7xl md:text-8xl lg:text-[160px] font-bold text-[#1C1A1B] mb-8 leading-[0.95]">
              Digital marketing made
            </h1>
            <div class="relative inline-block mb-8">
              <h1 class="text-7xl md:text-8xl lg:text-[160px] font-bold text-[#F34767] leading-[0.95] transform rotate-[-9.59deg] relative">
                Simple
          </h1>
              <div class="absolute bottom-0 left-0 w-full h-[20px] bg-[#F34767] transform rotate-[-9.99deg]"></div>
            </div>
          
          <!-- Subtitle -->
            <p class="text-2xl md:text-3xl text-[#1C1A1B] mb-12 max-w-2xl leading-relaxed">
            Grow online without the stress — with a plan, templates, and tools tailored to your business.
          </p>
          
          <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-6 items-start">
            <router-link 
              to="/register"
                class="px-8 py-4 bg-[#F34767] text-white rounded-[40px] hover:bg-[#d93d5a] transition-all duration-200 font-bold text-xl uppercase shadow-xl"
            >
              Get started
            </router-link>
              <p class="text-lg text-[#3F4369] font-bold self-center">
                Sign up for early access
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

    <!-- Features Grid -->
    <div class="px-6 py-20 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
          <!-- Feature 1 -->
          <div class="text-center">
            <div class="w-[130px] h-[130px] bg-[#F34767] rounded-[30px] mx-auto mb-6 flex items-center justify-center shadow-lg"></div>
            <h3 class="text-[32px] font-medium text-[#1C1A1B] mb-2 leading-none">Tailored marketing strategy</h3>
          </div>

          <!-- Feature 2 -->
          <div class="text-center">
            <div class="w-[130px] h-[130px] bg-[#F34767] rounded-[30px] mx-auto mb-6 flex items-center justify-center shadow-lg"></div>
            <h3 class="text-[32px] font-medium text-[#1C1A1B] mb-2 leading-none">Ready-to-use templates</h3>
          </div>

          <!-- Feature 3 -->
          <div class="text-center">
            <div class="w-[130px] h-[130px] bg-[#F34767] rounded-[30px] mx-auto mb-6 flex items-center justify-center shadow-lg"></div>
            <h3 class="text-[32px] font-medium text-[#1C1A1B] mb-2 leading-none">Industry-specific content ideas</h3>
          </div>

          <!-- Feature 4 -->
          <div class="text-center">
            <div class="w-[130px] h-[130px] bg-[#F34767] rounded-[30px] mx-auto mb-6 flex items-center justify-center shadow-lg"></div>
            <h3 class="text-[32px] font-medium text-[#1C1A1B] mb-2 leading-none">Collaborative peer community</h3>
          </div>

          <!-- Feature 5 -->
          <div class="text-center">
            <div class="w-[130px] h-[130px] bg-[#F34767] rounded-[30px] mx-auto mb-6 flex items-center justify-center shadow-lg"></div>
            <h3 class="text-[32px] font-medium text-[#1C1A1B] mb-2 leading-none">Done for your services</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- No More Guesswork Section -->
    <div class="px-6 py-32 bg-white">
      <div class="max-w-7xl mx-auto text-center">
        <h2 class="text-7xl md:text-8xl lg:text-[128px] font-bold text-[#1C1A1B] mb-8 leading-tight">
          No more guesswork
        </h2>
        <h2 class="text-7xl md:text-8xl lg:text-[128px] font-bold text-[#1C1A1B] mb-8 leading-tight">
          No more wasted time
        </h2>
        <h2 class="text-7xl md:text-8xl lg:text-[128px] font-bold text-[#1C1A1B] mb-8 leading-tight">
          Just your <span class="text-[#F34767]">digital marketing</span>
        </h2>
        <div class="relative inline-block mb-16">
          <div class="absolute top-[50%] left-[50%] transform translate-x-[-50%] translate-y-[-50%] rotate-[345deg]">
            <div class="w-[88px] h-[88px] bg-[#7FB5B5] rounded-[26px]"></div>
          </div>
          <div class="absolute top-[50%] left-[50%] transform translate-x-[-50%] translate-y-[-50%] rotate-[345deg]">
            <svg class="w-[54px] h-[54px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <h2 class="text-7xl md:text-8xl lg:text-[160px] font-bold text-[#F34767] relative transform rotate-[-15deg]">
            Done
          </h2>
          <div class="absolute bottom-0 left-0 w-full h-[20px] bg-[#F34767] transform rotate-[-15deg]"></div>
        </div>
      </div>
    </div>

    <!-- 4 Colorful Blocks Section -->
    <div class="px-6 py-32 bg-white relative overflow-hidden">
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Block 1: Focus on What Works - Red/Pink -->
          <div class="bg-[#F34767] rounded-[70px] p-12 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[497px] h-[598px] bg-white/10 rounded-[50px] transform rotate-[353.893deg]"></div>
            <h3 class="text-[64px] font-bold mb-6 leading-none relative z-10">Focus on What Works for Your Business</h3>
            <p class="text-[32px] leading-[1.2] relative z-10">
              Receive a step‑by‑step plan tailored to your business—no cookie‑cutter playbooks. Know where to start and what to do each week to see real growth.
              </p>
            </div>

          <!-- Block 2: Generate Revenue - Yellow -->
          <div class="bg-[#FFEB88] rounded-[70px] p-12 text-[#3F4369] relative overflow-hidden">
            <div class="absolute top-0 left-0 w-[467px] h-[378px] bg-white/50 rounded-[50px] transform rotate-[5.861deg]"></div>
            <h3 class="text-[96px] font-bold mb-6 leading-none relative z-10">Generate Revenue</h3>
            <p class="text-[32px] leading-[1.2] relative z-10">
              Reach clients where they already are and guide them to purchase with proven conversion tactics and ready-to-use templates.
            </p>
          </div>

          <!-- Block 3: Stay Consistent - Light Gray -->
          <div class="bg-[#DCDCDC] rounded-[70px] p-12 text-[#1C1A1B] relative overflow-hidden">
            <div class="absolute top-0 left-0 w-[545px] h-[710px] bg-white/50 rounded-[70px]"></div>
            <h3 class="text-[64px] font-bold mb-6 leading-none relative z-10">Stay Consistent</h3>
            <p class="text-[32px] leading-[1.2] relative z-10">
              Join a community of fellow small‑business owners solving the same challenges. Share feedback, swap tips and get accountability—because marketing works best when you're not doing it alone.
            </p>
          </div>

          <!-- Block 4: Stop Losing Nights - Teal -->
          <div class="bg-[#1C8E9E] rounded-[70px] p-12 text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-[430px] h-[394px] bg-white/42 rounded-[50px] transform rotate-[356.689deg]"></div>
            <h3 class="text-[64px] font-bold mb-6 leading-none relative z-10">Stop Losing Nights to Marketing</h3>
            <p class="text-[32px] leading-[1.2] relative z-10">
              Need more hands on deck? Our expert team can handle website development or provide individual consulting, so you stay focused on running your business.
              </p>
            </div>
          </div>
      </div>
            </div>

    <!-- No Generic Templates Section - Pink Background -->
    <div class="px-6 py-32 bg-[#F34767] relative overflow-hidden">
      <div class="absolute top-0 right-0 w-[839px] h-[787px] bg-[#3F4369] rounded-[70px] transform rotate-[7.38deg]"></div>
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div class="text-white">
            <h2 class="text-[64px] font-bold mb-8 leading-none">
              No generic or one-size-fits-all templates
            </h2>
            <p class="text-[36px] leading-[1.2] mb-6">
              We optimise strategy and content specifically for
            </p>
            <p class="text-[64px] font-bold mb-6 leading-none">
              YOUR BUSINESS,
            </p>
            <p class="text-[36px] leading-[1.2] mb-8">
              removing the guesswork and hours of customisation!
            </p>
            <p class="text-[64px] leading-[1.2] mb-8">
              All at the cost of a <span class="underline">monthly subscription</span> instead of an agency retainer.
            </p>
            <router-link 
              to="/register"
              class="inline-block px-8 py-4 bg-white text-[#F34767] rounded-[40px] hover:bg-[#FFEBD0] transition-all duration-200 font-bold text-[40px] uppercase shadow-xl"
            >
              Get started
            </router-link>
          </div>
            </div>
            </div>
          </div>

    <!-- FAQ Section with Accordion -->
    <div class="px-6 py-32 bg-[#FFEB88] relative overflow-hidden">
      <div class="absolute top-0 left-0 w-[370px] h-[383px] bg-[#7FB5B5] rounded-[100px]"></div>
      <div class="absolute top-0 left-[calc(25%+167px)] w-[370px] h-[383px] bg-[#7FB5B5] rounded-[100px]"></div>
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
          <div>
            <h2 class="text-7xl md:text-8xl font-bold text-[#1C1A1B] mb-8">
              Still wondering if <span class="text-[#F34767]">Peerie</span> <span class="underline decoration-[#FFEB88]">right for you?</span>
            </h2>
          </div>
          <div class="space-y-4">
            <div 
              v-for="(item, index) in faqItems" 
              :key="index"
              class="bg-[#FFEB88] rounded-[20px] p-6 cursor-pointer transition-all"
              @click="toggleFaq(index)"
            >
              <div class="flex items-center justify-between">
                <p class="text-[#1C1A1B] text-lg font-medium pr-4">
                  <span v-html="item.question"></span>
                </p>
                <svg 
                  class="w-6 h-6 text-[#1C1A1B] transform transition-transform flex-shrink-0"
                  :class="{ 'rotate-180': item.isOpen }"
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
              <div 
                v-if="item.isOpen" 
                class="mt-4 text-[#1C1A1B] text-lg"
              >
                {{ item.answer }}
              </div>
            </div>
          </div>
            </div>
          </div>
        </div>

    <!-- Try Peerie for Free Section -->
    <div class="px-6 py-32 bg-white">
      <div class="max-w-7xl mx-auto text-center">
        <h2 class="text-7xl md:text-8xl lg:text-[250px] font-bold text-[#1C1A1B] mb-4 leading-[0.95] tracking-[-10px]">
          Try Peerie
        </h2>
        <h2 class="text-7xl md:text-8xl lg:text-[250px] font-bold text-[#F34767] mb-8 leading-[0.95] tracking-[-10px]">
          for free!
        </h2>
        <p class="text-[40px] font-bold text-[#3F4369] mb-12">
          Start your 7‑day FREE trial — cancel anytime!
        </p>
        <router-link 
          to="/register"
          class="inline-block px-12 py-6 bg-[#F34767] text-white rounded-[40px] hover:bg-[#d93d5a] transition-all duration-200 font-bold text-[40px] uppercase shadow-xl"
        >
          Get started
        </router-link>
      </div>
    </div>

    <!-- What's Inside Section -->
    <div class="px-6 py-32 bg-white relative overflow-hidden">
      <div class="absolute top-0 left-0 w-[612px] h-[472px] bg-[#DCDCDC] opacity-50 rounded-[50px] transform rotate-[3.523deg]"></div>
      <div class="max-w-7xl mx-auto relative z-10">
        <h2 class="text-7xl md:text-8xl lg:text-[144px] font-bold text-[#1C1A1B] mb-20 leading-[0.95]">
            What's Inside?
          </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
          <!-- Left Column -->
          <div class="space-y-12">
              <div>
              <h3 class="text-[96px] font-bold text-[#F34767] mb-2 leading-[0.95] opacity-40">Digital marketing strategy & execution plan</h3>
              <p class="text-[48px] font-bold text-[#1C1A1B] opacity-40">tailored to your business</p>
            </div>
            
              <div>
              <h3 class="text-[96px] font-bold text-[#F34767] mb-2 leading-[0.95] opacity-40">Content templates</h3>
              <p class="text-[48px] font-bold text-[#1C1A1B] opacity-40">including ready-to-use posts</p>
            </div>
            
              <div>
              <h3 class="text-[96px] font-bold text-[#F34767] mb-2 leading-[0.95] opacity-40">Fresh content ideas</h3>
              <p class="text-[48px] font-bold text-[#1C1A1B] opacity-40">updated regularly for your industry</p>
            </div>
            
              <div>
              <h3 class="text-[96px] font-bold text-[#F34767] mb-2 leading-[0.95]">Image library</h3>
              <p class="text-[48px] font-bold text-[#1C1A1B]">of 1000+ free stock images</p>
            </div>
            
              <div>
              <h3 class="text-[96px] font-bold text-[#F34767] mb-2 leading-[0.95] opacity-40">Content calendar</h3>
              <p class="text-[48px] font-bold text-[#1C1A1B] opacity-40">to plan, schedule, and stay organised</p>
            </div>
            
              <div>
              <h3 class="text-[96px] font-bold text-[#F34767] mb-2 leading-[0.95] opacity-40">Local SEO handbook</h3>
              <p class="text-[48px] font-bold text-[#1C1A1B] opacity-40">with country- and industry-specific directories to list on</p>
            </div>
          </div>
          
          <!-- Right Column -->
          <div class="space-y-12">
              <div>
              <h3 class="text-[96px] font-bold text-[#F34767] mb-2 leading-[0.95] opacity-40">Hashtag library</h3>
              <p class="text-[48px] font-bold text-[#1C1A1B] opacity-40">handpicked and proved for your niche</p>
            </div>
            
              <div>
              <h3 class="text-[96px] font-bold text-[#F34767] mb-2 leading-[0.95] opacity-40">Peer community</h3>
              <p class="text-[48px] font-bold text-[#1C1A1B] opacity-40">for experience exchange and business opportunities</p>
            </div>
            
              <div>
              <h3 class="text-[96px] font-bold text-[#F34767] mb-2 leading-[0.95] opacity-40">Guides</h3>
              <p class="text-[48px] font-bold text-[#1C1A1B] opacity-40">covering everything you need to know about digital marketing</p>
            </div>
            
              <div>
              <h3 class="text-[96px] font-bold text-[#F34767] mb-2 leading-[0.95] opacity-40">Checklists</h3>
              <p class="text-[48px] font-bold text-[#1C1A1B] opacity-40">to keep you on track</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Why Peerie Section -->
    <div id="features" class="px-6 py-32 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-20">
          <h2 class="text-7xl md:text-8xl font-bold text-[#1C1A1B] mb-4">
            Why <span class="text-[#F34767]">Peerie?</span>
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Card 1: Easy & Affordable -->
          <div class="bg-white border border-[#DCDCDC] rounded-2xl p-8 shadow-lg">
            <div class="w-16 h-16 bg-[#7FB5B5] rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            <h3 class="text-2xl font-bold text-[#3F4369] mb-4">Easy & Affordable</h3>
            <p class="text-lg text-[#3F4369]">
              Subscription-based pricing keeps costs predictable, with add-on services when you need to get it done for you.
            </p>
            </div>

          <!-- Card 2: Constantly Evolving -->
          <div class="bg-white border border-[#DCDCDC] rounded-2xl p-8 shadow-lg">
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

          <!-- Card 3: Tailored by Industry -->
          <div class="bg-white border border-[#DCDCDC] rounded-2xl p-8 shadow-lg">
            <div class="w-16 h-16 bg-[#7FB5B5] rounded-2xl flex items-center justify-center mb-6">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
            <h3 class="text-2xl font-bold text-[#3F4369] mb-4">Tailored by Industry</h3>
            <p class="text-lg text-[#3F4369]">
              Our resources aren't generic—they're crafted with your industry in mind, so you get relevant, ready-to-use content without spending hours tweaking it to fit.
            </p>
            </div>

          <!-- Card 4: More Than Just Templates -->
          <div class="bg-white border border-[#DCDCDC] rounded-2xl p-8 shadow-lg">
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
    <div class="px-6 py-32 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-20">
          <h2 class="text-7xl md:text-8xl lg:text-[128px] font-bold text-[#1C1A1B] mb-8 leading-none">
            How It Works
          </h2>
          <div class="w-[386px] h-[20px] bg-[#F34767] mx-auto"></div>
        </div>
        
        <div class="space-y-12">
          <!-- Step 1 -->
          <div class="flex items-start space-x-8">
            <div class="w-[140px] h-[140px] bg-[#3F4369] rounded-[55px] flex items-center justify-center flex-shrink-0">
              <span class="text-white font-bold text-[64px]">1</span>
            </div>
            <div class="flex-1">
              <h3 class="text-[48px] font-bold text-[#1C1A1B] mb-4 leading-[0.95]">Tell us about your business</h3>
              <p class="text-[24px] text-[#1C1A1B] leading-[1.2]">
                Sign up and answer a few quick questions—industry, goals, audience, location, content preferences. The more you share, the sharper your plan.
              </p>
            </div>
          </div>
          
          <!-- Step 2 -->
          <div class="flex items-start space-x-8">
            <div class="w-[140px] h-[140px] bg-[#3F4369] rounded-[55px] flex items-center justify-center flex-shrink-0">
              <span class="text-white font-bold text-[64px]">2</span>
            </div>
            <div class="flex-1">
              <h3 class="text-[48px] font-bold text-[#1C1A1B] mb-4 leading-[0.95]">Get your tailored strategy</h3>
              <p class="text-[24px] text-[#1C1A1B] leading-[1.2]">
                We turn your answers into a clear 30-day roadmap: what to focus on, where to show up, and the exact order to do it.
              </p>
            </div>
          </div>
          
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

    <!-- Final CTA Section -->
    <div class="px-6 py-32 bg-[#F34767] relative overflow-hidden">
      <div class="max-w-7xl mx-auto text-center relative z-10">
        <h2 class="text-7xl md:text-8xl lg:text-[128px] font-bold text-white mb-8 leading-none">
          Get Started
        </h2>
        <p class="text-[36px] text-white mb-12 leading-[1.2]">
          Sign up for your tailored digital marketing strategy and resources now
        </p>
        <router-link 
          to="/register"
          class="inline-block px-12 py-6 bg-white text-[#F34767] rounded-[40px] hover:bg-[#FFEBD0] transition-all duration-200 font-bold text-[40px] uppercase shadow-xl"
        >
          Get started
        </router-link>
      </div>
    </div>

    <!-- Trusted by Small Businesses Section -->
    <div class="px-6 py-32 bg-[#7FB5B5]">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-20">
          <h2 class="text-7xl md:text-8xl lg:text-[128px] font-bold text-[#1C1A1B] mb-8 leading-none">
            Trusted by <span class="text-[#F34767]">Small Businesses</span>
          </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Testimonial 1 -->
          <div class="bg-white rounded-[100px] p-8 shadow-xl">
            <div class="w-full h-[383px] bg-[#7FB5B5] rounded-[100px] mb-6 flex items-center justify-center relative overflow-hidden">
              <div class="absolute w-[292px] h-[261px] bg-white/50 rounded-[50px] transform rotate-[9.924deg]"></div>
              <div class="relative z-10 w-24 h-24 bg-[#3F4369] rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-2xl">A</span>
              </div>
            </div>
            <p class="text-lg text-[#1C1A1B] text-center">
              Lorem ipsum dolor sit amet consectetur.
            </p>
          </div>
          
          <!-- Testimonial 2 -->
          <div class="bg-white rounded-[100px] p-8 shadow-xl">
            <div class="w-full h-[383px] bg-[#7FB5B5] rounded-[100px] mb-6 flex items-center justify-center relative overflow-hidden">
              <div class="absolute w-[292px] h-[261px] bg-white/50 rounded-[50px] transform rotate-[351.683deg]"></div>
              <div class="relative z-10 w-24 h-24 bg-[#3F4369] rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-2xl">B</span>
              </div>
            </div>
            <p class="text-lg text-[#1C1A1B] text-center">
              Lorem ipsum dolor sit amet consectetur.
            </p>
          </div>
          
          <!-- Testimonial 3 -->
          <div class="bg-white rounded-[100px] p-8 shadow-xl">
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
          <div class="bg-white rounded-[100px] p-8 shadow-xl">
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

    <!-- Blog Section -->
    <div class="px-6 py-32 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-16">
          <div>
            <h2 class="text-7xl md:text-8xl lg:text-[128px] font-bold text-[#1C1A1B] mb-4">
            Blogs
          </h2>
            <p class="text-[36px] text-[#1C1A1B] leading-[1.2] max-w-2xl">
            Check out our blogs to learn more about digital marketing and how it can help you grow your business.
          </p>
          </div>
          <router-link 
            to="/blog"
            class="px-12 py-6 bg-white border-2 border-[#F34767] text-[#F34767] rounded-[40px] hover:bg-[#F34767] hover:text-white transition-all duration-200 font-bold text-[40px] uppercase shadow-lg flex items-center gap-4"
          >
            Go to blog
            <svg class="w-11 h-11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </router-link>
        </div>

        <!-- Tags -->
        <div class="flex flex-wrap gap-4 mb-12">
          <span class="px-5 py-3 bg-[#FFEB88] text-[#1C1A1B] text-[32px] font-medium rounded-[20px]">marketing</span>
          <span class="px-5 py-3 bg-[#FFEB88] text-[#1C1A1B] text-[32px] font-medium rounded-[20px]">branding</span>
          <span class="px-5 py-3 bg-[#FFEB88] text-[#1C1A1B] text-[32px] font-medium rounded-[20px]">advertising</span>
          <span class="px-5 py-3 bg-[#FFEB88] text-[#1C1A1B] text-[32px] font-medium rounded-[20px]">creativity</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Blog Post 1 -->
          <div class="border border-[#DCDCDC] rounded-[40px] p-6 hover:shadow-lg transition-shadow">
            <div class="w-full h-[313px] bg-[#DCDCDC] opacity-50 rounded-[40px] mb-6"></div>
            <h3 class="text-[36px] font-bold text-[#1C1A1B] mb-4 leading-[0.95] tracking-[-1.44px]">
              Lorem ipsum dolor sit amet
            </h3>
            <p class="text-[24px] text-[#1C1A1B] leading-[1.2]">
              Lorem ipsum dolor sit amet consectetur. Mattis fames aliquam.
            </p>
          </div>

          <!-- Blog Post 2 -->
          <div class="border border-[#DCDCDC] rounded-[40px] p-6 hover:shadow-lg transition-shadow">
            <div class="w-full h-[313px] bg-[#DCDCDC] opacity-50 rounded-[40px] mb-6"></div>
            <h3 class="text-[36px] font-bold text-[#1C1A1B] mb-4 leading-[0.95] tracking-[-1.44px]">
              Lorem ipsum dolor sit amet
            </h3>
            <p class="text-[24px] text-[#1C1A1B] leading-[1.2]">
              Lorem ipsum dolor sit amet consectetur. Mattis fames aliquam.
            </p>
          </div>

          <!-- Blog Post 3 -->
          <div class="border border-[#DCDCDC] rounded-[40px] p-6 hover:shadow-lg transition-shadow">
            <div class="w-full h-[313px] bg-[#DCDCDC] opacity-50 rounded-[40px] mb-6"></div>
            <h3 class="text-[36px] font-bold text-[#1C1A1B] mb-4 leading-[0.95] tracking-[-1.44px]">
              Lorem ipsum dolor sit amet
            </h3>
            <p class="text-[24px] text-[#1C1A1B] leading-[1.2]">
              Lorem ipsum dolor sit amet consectetur. Mattis fames aliquam.
            </p>
            </div>
          
          <!-- Blog Post 4 -->
          <div class="border border-[#DCDCDC] rounded-[40px] p-6 hover:shadow-lg transition-shadow">
            <div class="w-full h-[313px] bg-[#DCDCDC] opacity-50 rounded-[40px] mb-6"></div>
            <h3 class="text-[36px] font-bold text-[#1C1A1B] mb-4 leading-[0.95] tracking-[-1.44px]">
              Lorem ipsum dolor sit amet
            </h3>
            <p class="text-[24px] text-[#1C1A1B] leading-[1.2]">
              Lorem ipsum dolor sit amet consectetur. Mattis fames aliquam.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white px-6 py-16">
      <div class="max-w-7xl mx-auto">
        <!-- Logo -->
        <div class="mb-12">
          <img :src="logoImage" alt="Peerie" class="h-12 w-auto">
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
          <!-- Services Column -->
          <div>
            <h3 class="text-[40px] font-bold text-[#1C1A1B] mb-6">Services</h3>
            <ul class="space-y-3">
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">Web development</a></li>
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">Consulting</a></li>
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">Request an industry</a></li>
            </ul>
          </div>

          <!-- Resources Column -->
          <div>
            <h3 class="text-[40px] font-bold text-[#1C1A1B] mb-6">Resources</h3>
            <ul class="space-y-3">
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">Blog</a></li>
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">Resource library</a></li>
            </ul>
          </div>

          <!-- Support Column -->
          <div>
            <h3 class="text-[40px] font-bold text-[#1C1A1B] mb-6">Support</h3>
            <ul class="space-y-3">
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">Help centre</a></li>
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">FAQ</a></li>
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">Contact</a></li>
            </ul>
          </div>

          <!-- Company Column -->
          <div>
            <h3 class="text-[40px] font-bold text-[#1C1A1B] mb-6">Company</h3>
            <ul class="space-y-3">
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">About us</a></li>
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">Privacy policy</a></li>
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">Terms of use</a></li>
              <li><a href="#" class="text-[#1C1A1B] hover:text-[#F34767] transition-colors text-[20px]">Legal</a></li>
            </ul>
          </div>
        </div>

        <!-- Newsletter Section -->
        <div class="border-t border-[#DCDCDC] pt-8">
          <div class="max-w-2xl">
            <h3 class="text-[40px] font-bold text-[#1C1A1B] mb-6">Subscribe to our news and special offers!</h3>
            <form class="flex gap-4 mb-6">
              <input 
                type="email" 
                placeholder="Your e-mail"
                class="flex-1 px-6 py-4 border-2 border-[#DCDCDC] rounded-[30px] focus:outline-none focus:ring-2 focus:ring-[#FFEB88] bg-white text-[#1C1A1B] text-[20px]"
              >
              <button 
                type="submit"
                class="px-8 py-4 bg-[#F34767] text-white border-2 border-[#F34767] rounded-[15px] hover:bg-white hover:text-[#F34767] transition-all duration-200 font-bold text-[20px] uppercase"
              >
                Join newsletter
              </button>
            </form>
            <label class="flex items-start space-x-3">
              <input type="checkbox" class="mt-1 rounded border-[#1C1A1B] bg-white w-5 h-5">
              <span class="text-[#1C1A1B] text-[14px] leading-[1.2]">
                Yes, I would like to receive email marketing communications from Peerie. I understand that I can unsubscribe at any time.
              </span>
            </label>
          </div>
        </div>

        <!-- Copyright -->
        <div class="mt-8 pt-8 border-t border-[#DCDCDC] text-center">
          <p class="text-[#DCDCDC] text-[24px] font-medium">
            Copyright 2025 Peerie
          </p>
        </div>
      </div>
    </footer>

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
