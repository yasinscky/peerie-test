<template>
  <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 bg-neutral-100">
    <!-- Promo -->
    <div class="hidden lg:flex lg:col-span-7 relative overflow-hidden bg-gradient-to-br from-primary-600 to-secondary-600 text-white">
      <div class="absolute inset-0">
        <div class="absolute -top-24 -left-24 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-16 w-80 h-80 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-12 left-12 w-64 h-64 bg-white/10 rounded-2xl rotate-12"></div>
      </div>
      <div class="relative z-10 w-full px-16 py-20 flex flex-col justify-between">
        <div>
          <div class="flex items-center gap-3 mb-10">
            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
              <img src="@/assets/images/logos/logo.png" alt="Peerie logo" class="w-10 h-10">
            </div>
            <div>
              <p class="uppercase tracking-widest text-xs text-white/70">Peerie Marketing OS</p>
              <h1 class="text-2xl font-semibold">Grow faster with focus</h1>
            </div>
          </div>

          <div class="space-y-6">
            <h2 class="text-4xl font-bold leading-tight max-w-xl">
              {{ headline }}
            </h2>
            <p class="text-white/80 max-w-lg text-lg leading-relaxed">
              {{ subheadline }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mt-16">
          <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-2">Personalised playbooks</h3>
            <p class="text-sm text-white/70 leading-relaxed">
              Instantly get a four-week marketing plan aligned with your goals, tools and capacity.
            </p>
          </div>
          <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-2">Actionable guidance</h3>
            <p class="text-sm text-white/70 leading-relaxed">
              Every task includes context, templates and examples so your team knows exactly what to do.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="lg:col-span-5 flex items-center justify-center px-6 py-16 sm:px-10">
      <div class="w-full max-w-md">
        <div class="mb-10 flex items-center gap-3 lg:hidden">
          <img src="@/assets/images/logos/logo.png" alt="Peerie logo" class="w-10 h-10">
          <div>
            <p class="uppercase tracking-widest text-xs text-primary-600">Peerie Marketing OS</p>
            <h1 class="text-xl font-semibold text-gray-900">Grow faster with focus</h1>
          </div>
        </div>

        <div class="bg-white shadow-xl shadow-primary-600/10 rounded-3xl border border-neutral-200/70 p-10">
          <slot />
        </div>

        <p class="mt-10 text-xs text-neutral-400 text-center">
          © {{ new Date().getFullYear() }} Peerie. All rights reserved.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AuthLayout',
  props: {
    headline: {
      type: String,
      default: 'Create clarity for your marketing'
    },
    subheadline: {
      type: String,
      default: 'Marketing shouldn’t feel overwhelming. Peerie helps you prioritise, act and learn across all growth channels in one workspace.'
    }
  }
}
</script>

