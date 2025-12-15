<template>
  <div id="app" class="min-h-screen">

      <router-view />
      
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'App',
  setup() {
    const mobileMenuOpen = ref(false)

    const toggleMobileMenu = () => {
      mobileMenuOpen.value = !mobileMenuOpen.value
    }

    const closeMobileMenu = () => {
      mobileMenuOpen.value = false
    }

    return {
      mobileMenuOpen,
      toggleMobileMenu,
      closeMobileMenu
    }
  }
}
</script>

<style scoped>
.nav-link {
  @apply text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium transition-colors duration-200;
}

.nav-link-mobile {
  @apply text-gray-700 hover:text-primary-600 px-3 py-2 text-base font-medium transition-colors duration-200;
}
</style>
