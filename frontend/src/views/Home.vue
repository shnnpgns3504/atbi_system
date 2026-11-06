<template>
  <div class="flex flex-col items-center justify-center min-h-[80vh] text-center space-y-6 px-4">
    
    <!-- Main Welcome Graphic/Icon -->
    <!-- <div class="w-32 h-32 md:w-40 md:h-40 rounded-full bg-gradient-to-br from-emerald-100 to-blue-100 flex items-center justify-center shadow-inner mb-4 animate-float">
      <img src="/logos/main.png" alt="ATBI System" class="w-24 h-24 md:w-28 md:h-28 object-contain drop-shadow-xl" />
    </div> -->

    <!-- Welcome Text -->
    <h1 class="text-4xl md:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-700 to-blue-700 tracking-tight pb-2">
      Welcome to the ATBI Intertrade System
    </h1>
    
    <p class="text-lg md:text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
      Explore our 6 Agricultural Technology Business Incubators. 
      Please select an ATBI from the top navigation bar to view their products and details.
    </p>

    <!-- Quick hint pointing to the navbar -->
    <div class="mt-8 animate-bounce">
      <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-full shadow-sm text-sm font-semibold text-gray-600">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
        <!-- Select an ATBI in the Navigation Bar above -->
      </span>
    </div>

  </div>
</template>

<script setup lang="ts">
// No scripts needed for the welcome screen anymore
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}
.animate-float {
  animation: float 4s ease-in-out infinite;
}
</style>