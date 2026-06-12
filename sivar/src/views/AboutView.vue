<template>
  <div class="space-y-8 w-full max-w-5xl mx-auto px-4 py-6">
    <!-- Back Button -->
    <div class="flex justify-start">
      <router-link
        class="group flex items-center px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-emerald-50 hover:text-cenicana-800 hover:border-emerald-200 transition-all duration-200"
        :to="{ name: 'main.show' }"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Dashboard
      </router-link>
    </div>

    <!-- Title Header -->
    <div class="border-b border-slate-100 pb-5">
      <div class="flex items-center">
        <div class="p-2.5 bg-emerald-50 text-cenicana rounded-2xl mr-4 shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
          </svg>
        </div>
        <div>
          <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">System Info & Architecture</h1>
          <p class="text-slate-500 mt-1 text-sm">Technical breakdown of the SIVAR application development stack.</p>
        </div>
      </div>
    </div>

    <!-- Introduction Card -->
    <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 text-white rounded-3xl p-8 shadow-premium relative overflow-hidden">
      <!-- Background Abstract Circle -->
      <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-700/25 rounded-full blur-2xl pointer-events-none"></div>
      <div class="absolute -left-10 -top-10 w-48 h-48 bg-emerald-600/10 rounded-full blur-2xl pointer-events-none"></div>
      
      <div class="relative z-10 max-w-3xl">
        <span class="bg-emerald-500/35 border border-emerald-400/20 text-emerald-100 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Overview</span>
        <h2 class="text-2xl font-extrabold mt-4 mb-3 tracking-tight">SIVAR (Sistema de Información de Variedades Cenicaña)</h2>
        <p class="text-emerald-100/90 text-base leading-relaxed">
          SIVAR is a cutting-edge web platform tailored for agricultural research and variety improvement at Cenicaña. It automates complex genetic crossing matrices, registers detailed flowering cycles, archives historical variety data, manages field logs, and processes molecular-genetic distance matrices to identify variety compatibility and prevent inbreeding.
        </p>
      </div>
    </div>

    <!-- Three Column Stack Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Frontend Card -->
      <div class="bg-white border border-slate-100/80 rounded-3xl p-6 shadow-premium hover:shadow-premium-hover transition-all duration-300 flex flex-col justify-between">
        <div>
          <div class="flex items-center space-x-3 mb-5">
            <div class="p-2.5 bg-teal-50 rounded-xl text-teal-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Frontend Layer</h3>
          </div>
          
          <ul class="space-y-4">
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-teal-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">Vue 3 (Composition API)</strong>
                <span class="text-xs text-slate-500">Structured using &lt;script setup lang="ts"&gt; for reactive components.</span>
              </div>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-teal-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">Vite</strong>
                <span class="text-xs text-slate-500">Lightning-fast modern frontend bundler and dev server.</span>
              </div>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-teal-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">Tailwind CSS (v3.x)</strong>
                <span class="text-xs text-slate-500">Provides modern responsive layouts and glassmorphic designs.</span>
              </div>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-teal-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">Pinia & Vue Router</strong>
                <span class="text-xs text-slate-500">Modular state stores and guard-protected hash routing.</span>
              </div>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-teal-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">Data Utilities</strong>
                <span class="text-xs text-slate-500">Chart.js, ExcelJS, PDFMake, and AmCharts 5 for advanced data sheets and reports.</span>
              </div>
            </li>
          </ul>
        </div>
        <div class="mt-6 pt-4 border-t border-slate-50">
          <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-2.5 py-1 rounded-full uppercase">Client Side</span>
        </div>
      </div>

      <!-- Backend Card -->
      <div class="bg-white border border-slate-100/80 rounded-3xl p-6 shadow-premium hover:shadow-premium-hover transition-all duration-300 flex flex-col justify-between">
        <div>
          <div class="flex items-center space-x-3 mb-5">
            <div class="p-2.5 bg-red-50 rounded-xl text-red-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Backend API</h3>
          </div>
          
          <ul class="space-y-4">
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-red-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">Laravel 10.x</strong>
                <span class="text-xs text-slate-500">PHP web application framework implementing clean MVC/Service patterns.</span>
              </div>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-red-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">PHP 8.2</strong>
                <span class="text-xs text-slate-500">Modern backend execution environment using static typing & typed properties.</span>
              </div>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-red-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">JWT Authentication</strong>
                <span class="text-xs text-slate-500">Secured via stateless JSON Web Tokens using Tymon JWT-Auth & Laravel Sanctum.</span>
              </div>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-red-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">Eloquent ORM</strong>
                <span class="text-xs text-slate-500">Object-Relational Mapper offering smooth, active-record database transactions.</span>
              </div>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-red-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">Guzzle & Tinker</strong>
                <span class="text-xs text-slate-500">Includes robust HTTP clients for RStudio API integrations and CLI debugging tools.</span>
              </div>
            </li>
          </ul>
        </div>
        <div class="mt-6 pt-4 border-t border-slate-50">
          <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2.5 py-1 rounded-full uppercase">Server Side</span>
        </div>
      </div>

      <!-- Infrastructure & DB Card -->
      <div class="bg-white border border-slate-100/80 rounded-3xl p-6 shadow-premium hover:shadow-premium-hover transition-all duration-300 flex flex-col justify-between">
        <div>
          <div class="flex items-center space-x-3 mb-5">
            <div class="p-2.5 bg-blue-50 rounded-xl text-blue-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Database & Env</h3>
          </div>
          
          <ul class="space-y-4">
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">PostgreSQL</strong>
                <span class="text-xs text-slate-500">Primary database engine configured with isolation:</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">• <span class="text-slate-500 font-semibold">sivar</span>: Main agricultural & crossing data.</span>
                <span class="text-[11px] text-slate-400 block">• <span class="text-slate-500 font-semibold">users</span>: Centralized user session control.</span>
              </div>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">MAMP Server</strong>
                <span class="text-xs text-slate-500">Local development web environment manager for Apache, Nginx, and PHP configuration.</span>
              </div>
            </li>
            <li class="flex items-start">
              <span class="flex-shrink-0 w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 mr-2"></span>
              <div>
                <strong class="text-slate-700 text-sm block">Redis Cache</strong>
                <span class="text-xs text-slate-500">Configured as an optional high-performance cache store for rapid JWT session resolution.</span>
              </div>
            </li>
          </ul>
        </div>
        <div class="mt-6 pt-4 border-t border-slate-50">
          <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full uppercase">Storage & Engine</span>
        </div>
      </div>
    </div>

    <!-- Workflow Section -->
    <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-premium hover:shadow-premium-hover transition-all duration-300">
      <div class="flex items-center space-x-3 mb-8 border-b border-slate-100 pb-4">
        <div class="p-2 bg-emerald-50 rounded-xl text-cenicana-800">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800">Flowering & Crossing Planning Workflow</h3>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 relative pb-8 border-b border-slate-100 mb-8">
        <!-- Vertical Divider (only visible on lg screens) -->
        <div class="absolute left-1/2 top-4 bottom-4 w-px bg-slate-100 hidden lg:block pointer-events-none"></div>

        <!-- Column 1: Flowering Phase -->
        <div class="space-y-6">
          <div class="flex items-center space-x-2">
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full uppercase tracking-wider">Phase 1</span>
            <h4 class="text-lg font-extrabold text-slate-700">Flowering Registration (Floración)</h4>
          </div>

          <!-- Step 1 -->
          <div class="flex space-x-4 group">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-cenicana-800 flex items-center justify-center font-bold text-sm group-hover:bg-cenicana group-hover:text-white transition-all duration-300">
              1
            </div>
            <div>
              <h5 class="text-sm font-bold text-slate-800">Daily Field Inspection</h5>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Agronomists visit the crop fields on a daily basis to identify sugarcane varieties that are initiating their natural flowering stage.
              </p>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="flex space-x-4 group">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-cenicana-800 flex items-center justify-center font-bold text-sm group-hover:bg-cenicana group-hover:text-white transition-all duration-300">
              2
            </div>
            <div>
              <h5 class="text-sm font-bold text-slate-800">Logging Parents Data</h5>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Observed active varieties are registered in SIVAR, detailing date, time, variety code, gender classification (Macho/Hembra), and pollen intensity rating.
              </p>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="flex space-x-4 group">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-cenicana-800 flex items-center justify-center font-bold text-sm group-hover:bg-cenicana group-hover:text-white transition-all duration-300">
              3
            </div>
            <div>
              <h5 class="text-sm font-bold text-slate-800">Availability Activation</h5>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                The registered plants are activated in the database with their respective parameters, opening up their compatibility window for the crossing process.
              </p>
            </div>
          </div>
        </div>

        <!-- Column 2: Crossing Phase -->
        <div class="space-y-6">
          <div class="flex items-center space-x-2">
            <span class="text-xs font-bold text-teal-600 bg-teal-50 px-3 py-1 rounded-full uppercase tracking-wider">Phase 2</span>
            <h4 class="text-lg font-extrabold text-slate-700">Crossing Planning (Cruzamientos)</h4>
          </div>

          <!-- Step 4 -->
          <div class="flex space-x-4 group">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm group-hover:bg-teal-600 group-hover:text-white transition-all duration-300">
              4
            </div>
            <div>
              <h5 class="text-sm font-bold text-slate-800">Project Selection</h5>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Researchers define target projects and select the focus agronomic traits to prioritize (e.g. high sucrose level, disease resistance index).
              </p>
            </div>
          </div>

          <!-- Step 5 -->
          <div class="flex space-x-4 group">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm group-hover:bg-teal-600 group-hover:text-white transition-all duration-300">
              5
            </div>
            <div>
              <h5 class="text-sm font-bold text-slate-800">Compatibility & Inbreeding Verification</h5>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                SIVAR automatically retrieves candidate parents and runs molecular distance checks to detect genetic closeness, filtering out pairs to avoid inbreeding depression.
              </p>
            </div>
          </div>

          <!-- Step 6 -->
          <div class="flex space-x-4 group">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm group-hover:bg-teal-600 group-hover:text-white transition-all duration-300">
              6
            </div>
            <div>
              <h5 class="text-sm font-bold text-slate-800">Interactive Suggestions Matrix</h5>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                A crossing matrix is generated showing compatible pairs with scores. Selected matches are scheduled in crossing field books and assigned to physical crossbags.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Technical Specifications Sub-Section -->
      <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
        <h4 class="text-md font-bold text-slate-800 mb-4 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          Technical & Mathematical Specification of Calculations
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs text-slate-600">
          <!-- Viability Math -->
          <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <h5 class="font-extrabold text-slate-800 mb-2 uppercase tracking-wide">1. Parent Viability Threshold</h5>
            <p class="leading-relaxed mb-3">
              Determines if the crossing of Mother (A) and Father (B) is viable. SIVAR converts raw values into scoring levels (scale ratings) and sums them:
            </p>
            <div class="bg-slate-50 p-2 rounded font-mono text-center text-[10px] border border-slate-100 mb-3 text-slate-700">
              Level_A + Level_B &lt;= Max_Susceptibility_Limit
            </div>
            <ul class="space-y-1.5 list-disc pl-4 text-[11px] text-slate-500">
              <li><strong>Disease susceptibility (Mosaic, Smut)</strong>: Mapped to levels 1–9 based on raw values.</li>
              <li><strong>Quantitative traits (TCHM, Sucrose, Height)</strong>: Mapped to levels 1–5 calculated relative to the witness variety: <span class="font-mono text-[10px] text-slate-700 block mt-0.5">Porcentaje = (Parent_Val * 100) / Witness_Val</span></li>
              <li><strong>Lodging (Volcamiento)</strong>: Mapped to levels 1-5 relative to witness percentage.</li>
            </ul>
          </div>

          <!-- Merit Value Math -->
          <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <h5 class="font-extrabold text-slate-800 mb-2 uppercase tracking-wide">2. Variety Merit Value (VM)</h5>
            <p class="leading-relaxed mb-3">
              Measures the overall genetic quality of each variety for a project. SIVAR multiplies the user-defined weight parameter by the variety's performance level for each trait:
            </p>
            <div class="bg-slate-50 p-2 rounded font-mono text-center text-[10px] border border-slate-100 mb-3 text-slate-700">
              VM = &Sigma; (Weight_i * Level_i) / 100
            </div>
            <p class="leading-relaxed text-[11px] text-slate-500">
              The total merit score is computed for both candidates. Higher values represent a higher genetic performance alignment with the research program criteria.
            </p>
          </div>

          <!-- Genetic Distance Math -->
          <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <h5 class="font-extrabold text-slate-800 mb-2 uppercase tracking-wide">3. Genetic Distance Calculation</h5>
            <p class="leading-relaxed mb-3">
              Prevents inbreeding depression and maintains genetic diversity. The system performs matrix lookups matching the female and male genotypes:
            </p>
            <div class="bg-slate-50 p-2 rounded font-mono text-center text-[10px] border border-slate-100 mb-3 text-slate-700">
              LookUp(fla: Var_A, clmna: Var_B)
            </div>
            <p class="leading-relaxed text-[11px] text-slate-500">
              Queries the molecular characterization matrix <code class="bg-slate-100 px-1 py-0.5 rounded text-[10px]">caracterizacion_molecular_BG</code> to fetch the divergence coefficient <code class="bg-slate-100 px-1 py-0.5 rounded text-[10px]">vlor</code>. High distance values indicate optimal genetic variety pairing.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Component logic
</script>
