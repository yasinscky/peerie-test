<template>
  <div class="min-h-screen bg-white dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Navigation -->
    <nav class="relative px-6 py-4 bg-white">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
          <div class="w-12 h-12 bg-[#3F4369] rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-lg">P</span>
          </div>
        </div>

        <!-- Navigation Links -->
        <div class="hidden md:flex items-center space-x-8">
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Home</a>
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Industries</a>
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Services</a>
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Pricing</a>
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Blog</a>
          <a href="#" class="text-[#1C1A1B] font-semibold text-lg hover:text-[#3F4369] transition-colors">Web design</a>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-4">
          <router-link 
            to="/login"
            class="px-6 py-3 text-[#3F4369] border-2 border-[#3F4369] rounded-2xl hover:bg-[#3F4369] hover:text-white transition-all duration-200 font-semibold"
          >
            Log in
          </router-link>
          <router-link 
            to="/register"
            class="px-6 py-3 bg-[#3F4369] text-white rounded-2xl hover:bg-[#2a2f4a] transition-all duration-200 font-semibold shadow-lg"
          >
            Get started
          </router-link>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative px-6 py-20 bg-white overflow-hidden">
      <!-- Decorative Background Elements -->
      <div class="absolute top-20 right-20 w-96 h-96 bg-[#3F4369] rounded-full opacity-10"></div>
      <div class="absolute top-40 right-40 w-80 h-80 bg-[#1C8E9E] rounded-full opacity-10"></div>
      <div class="absolute top-60 right-60 w-64 h-64 bg-[#FFEB88] rounded-full opacity-20"></div>
      
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="text-center">
          <!-- Main Headline -->
          <h1 class="text-6xl md:text-8xl lg:text-9xl font-bold text-[#1C1A1B] mb-8 leading-tight">
            Digital marketing made<br>
            <span class="text-[#3F4369]">Simple</span>
          </h1>
          
          <!-- Subtitle -->
          <p class="text-2xl md:text-3xl text-[#1C1A1B] mb-12 max-w-4xl mx-auto leading-relaxed">
            Grow online without the stress — with a plan, templates, and tools tailored to your business.
          </p>
          
          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-16">
            <router-link 
              to="/register"
              class="px-8 py-4 bg-[#3F4369] text-white rounded-2xl hover:bg-[#2a2f4a] transition-all duration-200 font-bold text-xl shadow-xl"
            >
              Sign up for early access
            </router-link>
            <router-link 
              to="/register"
              class="px-8 py-4 bg-white text-[#3F4369] border-2 border-[#3F4369] rounded-2xl hover:bg-[#3F4369] hover:text-white transition-all duration-200 font-bold text-xl"
            >
              Get started
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Features Grid -->
    <div class="px-6 py-20 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
          <!-- Feature 1 -->
          <div class="text-center">
            <div class="w-32 h-32 bg-[#3F4369] rounded-3xl mx-auto mb-6 flex items-center justify-center shadow-lg">
              <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <h3 class="text-xl font-medium text-[#1C1A1B] mb-2">Tailored marketing strategy</h3>
          </div>

          <!-- Feature 2 -->
          <div class="text-center">
            <div class="w-32 h-32 bg-[#3F4369] rounded-3xl mx-auto mb-6 flex items-center justify-center shadow-lg">
              <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <h3 class="text-xl font-medium text-[#1C1A1B] mb-2">Ready-to-use templates</h3>
          </div>

          <!-- Feature 3 -->
          <div class="text-center">
            <div class="w-32 h-32 bg-[#3F4369] rounded-3xl mx-auto mb-6 flex items-center justify-center shadow-lg">
              <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
              </svg>
            </div>
            <h3 class="text-xl font-medium text-[#1C1A1B] mb-2">Industry-specific content ideas</h3>
          </div>

          <!-- Feature 4 -->
          <div class="text-center">
            <div class="w-32 h-32 bg-[#3F4369] rounded-3xl mx-auto mb-6 flex items-center justify-center shadow-lg">
              <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <h3 class="text-xl font-medium text-[#1C1A1B] mb-2">Collaborative peer community</h3>
          </div>

          <!-- Feature 5 -->
          <div class="text-center">
            <div class="w-32 h-32 bg-[#3F4369] rounded-3xl mx-auto mb-6 flex items-center justify-center shadow-lg">
              <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </div>
            <h3 class="text-xl font-medium text-[#1C1A1B] mb-2">Done for your services</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- No More Guesswork Section -->
    <div class="px-6 py-32 bg-white">
      <div class="max-w-7xl mx-auto text-center">
        <h2 class="text-6xl md:text-8xl lg:text-9xl font-bold text-[#1C1A1B] mb-8 leading-tight">
          No more guesswork<br>
          No more wasted time<br>
          Just your digital marketing<br>
          <span class="text-[#3F4369]">Done</span>
        </h2>
        
        <!-- Decorative Check Icon -->
        <div class="flex justify-center mb-16">
          <div class="w-32 h-32 bg-[#7FB5B5] rounded-full flex items-center justify-center shadow-xl">
            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Questionnaire Section -->
    <div class="px-6 py-16 bg-[#FFEB88]">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl font-bold text-[#1C1A1B] mb-8">
          Still wondering if Peerie right for you?
        </h2>
        
        <div class="space-y-6">
          <div class="flex items-start space-x-4 text-left">
            <div class="w-12 h-12 bg-[#F34767] rounded-2xl flex-shrink-0 flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
              </svg>
            </div>
            <div>
              <p class="text-[#3F4369] text-lg">
                You realise that digital marketing can generate more revenue, but don't know where to start?
              </p>
              <p class="text-[#3F4369] text-lg mt-2">
                We provide you with a strategy tailored to your specific business, all you need to do is to follow the steps and see your business grow.
              </p>
            </div>
          </div>

          <div class="flex items-start space-x-4 text-left">
            <div class="w-12 h-12 bg-[#1C8E9E] rounded-2xl flex-shrink-0 flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <p class="text-[#3F4369] text-lg">
                You love what you do and want to spend more time with your customers instead of spending hours on creating content?
              </p>
              <p class="text-[#3F4369] text-lg mt-2">
                We have you covered with plug-and-play templates and content ideas for your industry - saving you hours every week.
              </p>
            </div>
          </div>

          <div class="flex items-start space-x-4 text-left">
            <div class="w-12 h-12 bg-[#DCDCDC] rounded-2xl flex-shrink-0 flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-[#3F4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <p class="text-[#3F4369] text-lg">
                You would love to post regularly, but have no idea what to share?
              </p>
              <p class="text-[#3F4369] text-lg mt-2">
                We deliver fresh content ideas for your specific industry—on social media, email marketing, and your website.
              </p>
            </div>
          </div>

          <div class="flex items-start space-x-4 text-left">
            <div class="w-12 h-12 bg-[#F34767] rounded-2xl flex-shrink-0 flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </div>
            <div>
              <p class="text-[#3F4369] text-lg">
                You have tried digital marketing before, but had given up before you saw any results?
              </p>
              <p class="text-[#3F4369] text-lg mt-2">
                We make digital marketing easy to run and help you stay on track.
              </p>
            </div>
          </div>

          <div class="flex items-start space-x-4 text-left">
            <div class="w-12 h-12 bg-[#1C8E9E] rounded-2xl flex-shrink-0 flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
              </svg>
            </div>
            <div>
              <p class="text-[#3F4369] text-lg">
                You're already posting like a pro, but want to explore other aspects of digital marketing?
              </p>
              <p class="text-[#3F4369] text-lg mt-2">
                We help you start your email marketing campaigns, optimise for local SEO, and build your website.
              </p>
            </div>
          </div>

          <div class="flex items-start space-x-4 text-left">
            <div class="w-12 h-12 bg-gradient-to-br from-[#F34767] to-[#1C8E9E] rounded-2xl flex-shrink-0 flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
            </div>
            <div>
              <p class="text-[#3F4369] text-lg">
                You have entirely different challenges when it comes to digital marketing?
              </p>
              <p class="text-[#3F4369] text-lg mt-2">
                Get in touch, and we will try to find a solution that works for you!
              </p>
            </div>
          </div>
        </div>

        <div class="mt-12">
          <p class="text-[#3F4369] text-lg mb-4">
            No generic or one-size-fits-all templates, we optimise strategy and content specifically for YOUR BUSINESS, removing the guesswork and hours of customisation; all at the cost of a monthly subscription instead of an agency retainer.
          </p>
        </div>
      </div>
    </div>

    <!-- What's Inside Section -->
    <div class="px-6 py-32 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-20">
          <h2 class="text-6xl md:text-8xl font-bold text-[#1C1A1B] mb-8">
            What's Inside?
          </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
          <!-- Left Column -->
          <div class="space-y-8">
            <div class="flex items-start space-x-4">
              <div class="w-6 h-6 bg-[#3F4369] rounded-full flex-shrink-0 mt-2"></div>
              <div>
                <h3 class="text-2xl font-bold text-[#3F4369] mb-2">Digital marketing strategy & execution plan</h3>
                <p class="text-lg text-[#1C1A1B] opacity-60">tailored to your business</p>
              </div>
            </div>
            
            <div class="flex items-start space-x-4">
              <div class="w-6 h-6 bg-[#3F4369] rounded-full flex-shrink-0 mt-2"></div>
              <div>
                <h3 class="text-2xl font-bold text-[#3F4369] mb-2">Content templates</h3>
                <p class="text-lg text-[#1C1A1B] opacity-60">including ready-to-use posts</p>
              </div>
            </div>
            
            <div class="flex items-start space-x-4">
              <div class="w-6 h-6 bg-[#3F4369] rounded-full flex-shrink-0 mt-2"></div>
              <div>
                <h3 class="text-2xl font-bold text-[#3F4369] mb-2">Fresh content ideas</h3>
                <p class="text-lg text-[#1C1A1B] opacity-60">updated regularly for your industry</p>
              </div>
            </div>
            
            <div class="flex items-start space-x-4">
              <div class="w-6 h-6 bg-[#3F4369] rounded-full flex-shrink-0 mt-2"></div>
              <div>
                <h3 class="text-2xl font-bold text-[#3F4369] mb-2">Image library</h3>
                <p class="text-lg text-[#1C1A1B]">of 1000+ free stock images</p>
              </div>
            </div>
            
            <div class="flex items-start space-x-4">
              <div class="w-6 h-6 bg-[#3F4369] rounded-full flex-shrink-0 mt-2"></div>
              <div>
                <h3 class="text-2xl font-bold text-[#3F4369] mb-2">Content calendar</h3>
                <p class="text-lg text-[#1C1A1B] opacity-60">to plan, schedule, and stay organised</p>
              </div>
            </div>
            
            <div class="flex items-start space-x-4">
              <div class="w-6 h-6 bg-[#3F4369] rounded-full flex-shrink-0 mt-2"></div>
              <div>
                <h3 class="text-2xl font-bold text-[#3F4369] mb-2">Local SEO handbook</h3>
                <p class="text-lg text-[#1C1A1B] opacity-60">with country- and industry-specific directories to list on</p>
              </div>
            </div>
          </div>
          
          <!-- Right Column -->
          <div class="space-y-8">
            <div class="flex items-start space-x-4">
              <div class="w-6 h-6 bg-[#3F4369] rounded-full flex-shrink-0 mt-2"></div>
              <div>
                <h3 class="text-2xl font-bold text-[#3F4369] mb-2">Hashtag library</h3>
                <p class="text-lg text-[#1C1A1B] opacity-60">handpicked and proved for your niche</p>
              </div>
            </div>
            
            <div class="flex items-start space-x-4">
              <div class="w-6 h-6 bg-[#3F4369] rounded-full flex-shrink-0 mt-2"></div>
              <div>
                <h3 class="text-2xl font-bold text-[#3F4369] mb-2">Peer community</h3>
                <p class="text-lg text-[#1C1A1B] opacity-60">for experience exchange and business opportunities</p>
              </div>
            </div>
            
            <div class="flex items-start space-x-4">
              <div class="w-6 h-6 bg-[#3F4369] rounded-full flex-shrink-0 mt-2"></div>
              <div>
                <h3 class="text-2xl font-bold text-[#3F4369] mb-2">Guides</h3>
                <p class="text-lg text-[#1C1A1B] opacity-60">covering everything you need to know about digital marketing</p>
              </div>
            </div>
            
            <div class="flex items-start space-x-4">
              <div class="w-6 h-6 bg-[#3F4369] rounded-full flex-shrink-0 mt-2"></div>
              <div>
                <h3 class="text-2xl font-bold text-[#3F4369] mb-2">Checklists</h3>
                <p class="text-lg text-[#1C1A1B] opacity-60">to keep you on track</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Why Peerie Section -->
    <div id="features" class="px-6 py-20 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-[#3F4369] mb-8">
            Why Peerie?
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
          <!-- Left Column -->
          <div class="space-y-8">
            <div class="flex items-start space-x-6">
              <div class="w-16 h-16 bg-[#F34767] rounded-2xl flex-shrink-0 flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-[#3F4369] mb-3">Focus on What Works for Your Business</h3>
                <p class="text-[#3F4369]">
                  Receive a step‑by‑step plan tailored to your business—no cookie‑cutter playbooks. Know where to start and what to do each week to see real growth.
                </p>
              </div>
            </div>

            <div class="flex items-start space-x-6">
              <div class="w-16 h-16 bg-[#1C8E9E] rounded-2xl flex-shrink-0 flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-[#3F4369] mb-3">Generate Revenue</h3>
                <p class="text-[#3F4369]">
                  Reach clients where they already are and guide them to purchase with proven conversion tactics and ready-to-use templates.
            </p>
          </div>
            </div>

            <div class="flex items-start space-x-6">
              <div class="w-16 h-16 bg-[#FFEBD0] border-2 border-[#F34767] rounded-2xl flex-shrink-0 flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-[#3F4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-[#3F4369] mb-3">Save Time & Increase Productivity</h3>
                <p class="text-[#3F4369]">
                  Create content in minutes using industry-specific ideas and templates and stay productive without a burnout.
                </p>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="space-y-8">
            <div class="flex items-start space-x-6">
              <div class="w-16 h-16 bg-[#DCDCDC] rounded-2xl flex-shrink-0 flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-[#3F4369]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-[#3F4369] mb-3">Stay Consistent</h3>
                <p class="text-[#3F4369]">
                  Join a community of fellow small‑business owners solving the same challenges. Share feedback, swap tips and get accountability—because marketing works best when you're not doing it alone.
                </p>
              </div>
            </div>

            <div class="flex items-start space-x-6">
              <div class="w-16 h-16 bg-gradient-to-br from-[#F34767] to-[#1C8E9E] rounded-2xl flex-shrink-0 flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-[#3F4369] mb-3">Stop Losing Nights to Marketing</h3>
                <p class="text-[#3F4369]">
                  Need more hands on deck? Our expert team can handle website development or provide individual consulting, so you stay focused on running your business.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Key Benefits Section -->
    <div class="px-6 py-20 bg-[#DCDCDC]">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Benefit 1 -->
          <div class="text-left bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-[#3F4369] mb-4">
              Easy & Affordable: Subscription-based pricing keeps costs predictable, with add-on services when you need to get it done for you.
            </h3>
          </div>

          <!-- Benefit 2 -->
          <div class="text-left bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-[#3F4369] mb-4">
              Constantly Evolving: Templates and strategies are updated in real time to match the latest trends and best practices, so you never fall behind.
            </h3>
          </div>

          <!-- Benefit 3 -->
          <div class="text-left bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-[#3F4369] mb-4">
              Tailored by Industry: Our resources aren't generic—they're crafted with your industry in mind, so you get relevant, ready-to-use content without spending hours tweaking it to fit.
            </h3>
          </div>

          <!-- Benefit 4 -->
          <div class="text-left bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-[#3F4369] mb-4">
              More Than Just Templates: Peerie combines plug-and-play content with strategic guidance tailored to your industry, so every action supports your business goals.
            </h3>
          </div>

          <!-- Benefit 5 -->
          <div class="text-left bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-[#3F4369] mb-4">
              Bonus: Educational resources, checklists, and guides to help you stay ahead.
            </h3>
          </div>
        </div>

        <div class="text-center mt-16">
          <p class="text-lg text-[#3F4369] mb-8 font-semibold">
            No more guesswork. No more wasted time. Just your digital marketing, done.
          </p>
        </div>
      </div>
    </div>

    <!-- How It Works Section -->
    <div class="px-6 py-32 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-20">
          <h2 class="text-6xl md:text-8xl font-bold text-[#1C1A1B] mb-8">
            How It Works
          </h2>
        </div>
        
        <div class="space-y-16">
          <!-- Step 1 -->
          <div class="flex items-start space-x-8">
            <div class="w-32 h-32 bg-[#3F4369] rounded-full flex items-center justify-center flex-shrink-0">
              <span class="text-white font-bold text-4xl">1</span>
            </div>
            <div class="flex-1">
              <h3 class="text-3xl font-bold text-[#1C1A1B] mb-4">Tell us about your business</h3>
              <p class="text-xl text-[#1C1A1B] leading-relaxed">
                Sign up and answer a few quick questions—industry, goals, audience, location, content preferences. The more you share, the sharper your plan.
              </p>
            </div>
          </div>
          
          <!-- Step 2 -->
          <div class="flex items-start space-x-8">
            <div class="w-32 h-32 bg-[#3F4369] rounded-full flex items-center justify-center flex-shrink-0">
              <span class="text-white font-bold text-4xl">2</span>
            </div>
            <div class="flex-1">
              <h3 class="text-3xl font-bold text-[#1C1A1B] mb-4">Get your tailored strategy</h3>
              <p class="text-xl text-[#1C1A1B] leading-relaxed">
                We turn your answers into a clear 30-day roadmap: what to focus on, where to show up, and the exact order to do it.
              </p>
            </div>
          </div>
          
          <!-- Step 3 -->
          <div class="flex items-start space-x-8">
            <div class="w-32 h-32 bg-[#3F4369] rounded-full flex items-center justify-center flex-shrink-0">
              <span class="text-white font-bold text-4xl">3</span>
            </div>
            <div class="flex-1">
              <h3 class="text-3xl font-bold text-[#1C1A1B] mb-4">Open your workspace</h3>
              <p class="text-xl text-[#1C1A1B] leading-relaxed">
                Your account is preloaded with your plan, content ideas, templates, and workbooks — tuned to your industry and country.
              </p>
            </div>
          </div>
          
          <!-- Step 4 -->
          <div class="flex items-start space-x-8">
            <div class="w-32 h-32 bg-[#3F4369] rounded-full flex items-center justify-center flex-shrink-0">
              <span class="text-white font-bold text-4xl">4</span>
            </div>
            <div class="flex-1">
              <h3 class="text-3xl font-bold text-[#1C1A1B] mb-4">Follow the plan</h3>
              <p class="text-xl text-[#1C1A1B] leading-relaxed">
                Each week, open the checklist and deliver: post content, send email, tick the SEO task. Simple. No starting from scratch.
              </p>
            </div>
          </div>
          
          <!-- Step 5 -->
          <div class="flex items-start space-x-8">
            <div class="w-32 h-32 bg-[#3F4369] rounded-full flex items-center justify-center flex-shrink-0">
              <span class="text-white font-bold text-4xl">5</span>
            </div>
            <div class="flex-1">
              <h3 class="text-3xl font-bold text-[#1C1A1B] mb-4">Keep momentum</h3>
              <p class="text-xl text-[#1C1A1B] leading-relaxed">
                Your plan refreshes monthly with new ideas and templates—so staying consistent stays easy.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="px-6 py-20 bg-gradient-to-r from-[#3F4369] to-[#1C8E9E]">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl font-bold text-white mb-6">
          Try Peerie for free!
        </h2>
        <p class="text-lg text-white mb-8">
          Start your 7‑day FREE trial — cancel anytime!
        </p>
        <router-link 
          to="/register"
          class="px-8 py-3 bg-white text-[#3F4369] rounded-xl hover:bg-[#FFEBD0] transition-colors font-medium shadow-lg"
        >
          Get started
        </router-link>
      </div>
    </div>

    <!-- Trusted by Small Businesses Section -->
    <div class="px-6 py-32 bg-[#7FB5B5]">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-20">
          <h2 class="text-6xl md:text-8xl font-bold text-[#1C1A1B] mb-8">
            Trusted by Small Businesses
          </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Testimonial 1 -->
          <div class="bg-white rounded-3xl p-8 shadow-xl">
            <div class="w-full h-48 bg-[#7FB5B5] rounded-2xl mb-6 flex items-center justify-center">
              <div class="w-24 h-24 bg-[#3F4369] rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-2xl">A</span>
              </div>
            </div>
            <p class="text-lg text-[#1C1A1B] text-center">
              Lorem ipsum dolor sit amet consectetur.
            </p>
          </div>
          
          <!-- Testimonial 2 -->
          <div class="bg-white rounded-3xl p-8 shadow-xl">
            <div class="w-full h-48 bg-[#7FB5B5] rounded-2xl mb-6 flex items-center justify-center">
              <div class="w-24 h-24 bg-[#3F4369] rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-2xl">B</span>
              </div>
            </div>
            <p class="text-lg text-[#1C1A1B] text-center">
              Lorem ipsum dolor sit amet consectetur.
            </p>
          </div>
          
          <!-- Testimonial 3 -->
          <div class="bg-white rounded-3xl p-8 shadow-xl">
            <div class="w-full h-48 bg-[#7FB5B5] rounded-2xl mb-6 flex items-center justify-center">
              <div class="w-24 h-24 bg-[#3F4369] rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-2xl">C</span>
              </div>
            </div>
            <p class="text-lg text-[#1C1A1B] text-center">
              Lorem ipsum dolor sit amet consectetur.
            </p>
          </div>
          
          <!-- Testimonial 4 -->
          <div class="bg-white rounded-3xl p-8 shadow-xl">
            <div class="w-full h-48 bg-[#7FB5B5] rounded-2xl mb-6 flex items-center justify-center">
              <div class="w-24 h-24 bg-[#3F4369] rounded-full flex items-center justify-center">
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
    <div class="px-6 py-20 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-[#3F4369] mb-4">
            Blogs
          </h2>
          <p class="text-lg text-[#3F4369]">
            Check out our blogs to learn more about digital marketing and how it can help you grow your business.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Blog Post 1 -->
          <div class="border border-[#DCDCDC] rounded-2xl p-6 hover:shadow-lg transition-shadow">
            <div class="w-full h-48 bg-[#F34767] rounded-2xl mb-4 flex items-center justify-center shadow-lg">
              <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-[#3F4369] mb-3">Digital Marketing Trends 2025</h3>
            <p class="text-[#3F4369] mb-4">Discover the latest trends and strategies that will shape digital marketing in 2025</p>
            <div class="flex flex-wrap gap-2">
              <span class="px-3 py-1 bg-[#FFEBD0] text-[#3F4369] text-sm rounded-full">#trends</span>
              <span class="px-3 py-1 bg-[#DCDCDC] text-[#3F4369] text-sm rounded-full">#strategy</span>
              <span class="px-3 py-1 bg-[#FFEBD0] text-[#3F4369] text-sm rounded-full">#2025</span>
              <span class="px-3 py-1 bg-[#DCDCDC] text-[#3F4369] text-sm rounded-full">#marketing</span>
            </div>
          </div>

          <!-- Blog Post 2 -->
          <div class="border border-[#DCDCDC] rounded-2xl p-6 hover:shadow-lg transition-shadow">
            <div class="w-full h-48 bg-[#1C8E9E] rounded-2xl mb-4 flex items-center justify-center shadow-lg">
              <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-[#3F4369] mb-3">Small Business Growth Hacks</h3>
            <p class="text-[#3F4369] mb-4">Proven strategies to scale your small business with limited resources</p>
            <div class="flex flex-wrap gap-2">
              <span class="px-3 py-1 bg-[#FFEBD0] text-[#3F4369] text-sm rounded-full">#growth</span>
              <span class="px-3 py-1 bg-[#DCDCDC] text-[#3F4369] text-sm rounded-full">#smallbiz</span>
              <span class="px-3 py-1 bg-[#FFEBD0] text-[#3F4369] text-sm rounded-full">#hacks</span>
              <span class="px-3 py-1 bg-[#DCDCDC] text-[#3F4369] text-sm rounded-full">#scaling</span>
            </div>
          </div>

          <!-- Blog Post 3 -->
          <div class="border border-[#DCDCDC] rounded-2xl p-6 hover:shadow-lg transition-shadow">
            <div class="w-full h-48 bg-gradient-to-br from-[#F34767] to-[#1C8E9E] rounded-2xl mb-4 flex items-center justify-center shadow-lg">
              <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-[#3F4369] mb-3">Building Your Brand Community</h3>
            <p class="text-[#3F4369] mb-4">Learn how to create an engaged community around your brand</p>
            <div class="flex flex-wrap gap-2">
              <span class="px-3 py-1 bg-[#FFEBD0] text-[#3F4369] text-sm rounded-full">#community</span>
              <span class="px-3 py-1 bg-[#DCDCDC] text-[#3F4369] text-sm rounded-full">#branding</span>
              <span class="px-3 py-1 bg-[#FFEBD0] text-[#3F4369] text-sm rounded-full">#engagement</span>
              <span class="px-3 py-1 bg-[#DCDCDC] text-[#3F4369] text-sm rounded-full">#loyalty</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#3F4369] px-6 py-16">
      <div class="max-w-7xl mx-auto">
        <!-- Logo -->
        <div class="mb-12">
          <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
            <span class="text-[#3F4369] font-bold text-lg">P</span>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
          <!-- Services Column -->
          <div>
            <h3 class="text-2xl font-bold text-white mb-6">Services</h3>
            <ul class="space-y-3">
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">Web development</a></li>
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">Consulting</a></li>
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">Request an industry</a></li>
            </ul>
          </div>

          <!-- Resources Column -->
          <div>
            <h3 class="text-2xl font-bold text-white mb-6">Resources</h3>
            <ul class="space-y-3">
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">Blog</a></li>
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">Resource library</a></li>
            </ul>
          </div>

          <!-- Support Column -->
          <div>
            <h3 class="text-2xl font-bold text-white mb-6">Support</h3>
            <ul class="space-y-3">
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">Help centre</a></li>
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">FAQ</a></li>
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">Contact</a></li>
            </ul>
          </div>

          <!-- Company Column -->
          <div>
            <h3 class="text-2xl font-bold text-white mb-6">Company</h3>
            <ul class="space-y-3">
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">About us</a></li>
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">Privacy policy</a></li>
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">Terms of use</a></li>
              <li><a href="#" class="text-white hover:text-[#FFEB88] transition-colors text-lg">Legal</a></li>
            </ul>
          </div>
        </div>

        <!-- Newsletter Section -->
        <div class="border-t border-[#DCDCDC] pt-8">
          <div class="max-w-2xl">
            <h3 class="text-2xl font-bold text-white mb-6">Subscribe to our news and special offers!</h3>
            <form class="flex gap-4 mb-6">
              <input 
                type="email" 
                placeholder="Your e-mail"
                class="flex-1 px-6 py-4 border-2 border-[#DCDCDC] rounded-3xl focus:outline-none focus:ring-2 focus:ring-[#FFEB88] bg-white text-[#3F4369] text-lg"
              >
              <button 
                type="submit"
                class="px-8 py-4 bg-[#3F4369] text-white border-2 border-[#3F4369] rounded-2xl hover:bg-white hover:text-[#3F4369] transition-all duration-200 font-bold text-lg"
              >
                Join newsletter
              </button>
            </form>
            <label class="flex items-start space-x-3">
              <input type="checkbox" class="mt-1 rounded border-white bg-white w-5 h-5">
              <span class="text-white text-sm leading-relaxed">
                Yes, I would like to receive email marketing communications from Peerie. I understand that I can unsubscribe at any time.
              </span>
            </label>
          </div>
        </div>

        <!-- Copyright -->
        <div class="mt-8 pt-8 border-t border-[#DCDCDC] text-center">
          <p class="text-[#DCDCDC] text-lg">
            Copyright 2025 Peerie
          </p>
        </div>
      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'

const isDark = ref(false)

const toggleTheme = () => {
  isDark.value = !isDark.value
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

const scrollToFeatures = () => {
  document.getElementById('features')?.scrollIntoView({ behavior: 'smooth' })
}

onMounted(() => {
  // Load saved theme
  const savedTheme = localStorage.getItem('theme')
  isDark.value = savedTheme === 'dark'
})

// Apply theme to body
watch(isDark, (newValue) => {
  if (newValue) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}, { immediate: true })
</script>
