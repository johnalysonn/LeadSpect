<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LeadSpect — Pesquisa de Leads</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- MapLibre GL JS CSS & JS -->
    <link href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css" rel="stylesheet" />
    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-screen flex flex-col bg-[#F4F4F5] text-[#18181B] overflow-hidden" x-data="searchComponent({{ json_encode($templates) }})">

    <!-- Topbar Header -->
    <header class="h-14 border-b border-[#E4E4E7] bg-white px-4 sm:px-6 flex items-center justify-between shrink-0 z-20 shadow-xs">
        <div class="flex items-center gap-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg border border-[#E4E4E7] flex items-center justify-center bg-black text-white shadow-xs">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                    </svg>
                </div>
                <span class="font-medium text-sm tracking-tight text-[#18181B]">LeadSpect</span>
            </a>

            <nav class="hidden md:flex items-center gap-1 text-xs">
                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Dashboard</a>
                <a href="{{ route('search.index') }}" class="px-3 py-1.5 rounded-lg font-medium text-white bg-black">Pesquisar Leads</a>
                <a href="{{ route('leads.index') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Pipeline (Kanban)</a>
                <a href="{{ route('templates.index') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Templates</a>
                <a href="{{ route('users.index') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Usuários</a>
            </nav>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                @if (auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-7 h-7 rounded-full border border-[#E4E4E7] object-cover">
                @else
                    <div class="w-7 h-7 rounded-full border border-[#E4E4E7] bg-[#F4F4F5] flex items-center justify-center text-xs font-medium text-[#18181B]">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <span class="text-xs font-medium hidden sm:inline text-[#18181B]">{{ auth()->user()->name }}</span>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-1.5 rounded-lg border border-[#E4E4E7] hover:bg-[#F4F4F5] text-[#71717A] hover:text-[#18181B] transition-colors cursor-pointer" title="Sair">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </button>
            </form>
        </div>
    </header>

    <!-- Layout Principal: Card Esquerdo (Filtros + Resultados) + Área Direita (Mapa) -->
    <div class="flex-1 flex flex-col md:flex-row relative overflow-hidden">
        
        <!-- COLUNA DA ESQUERDA: Card Menor de Busca, Filtros e Lista de Resultados -->
        <div class="w-full md:w-[410px] shrink-0 border-r border-[#E4E4E7] bg-white flex flex-col h-full z-10 shadow-xs">
            
            <!-- Card de Pesquisa & Filtros -->
            <div class="p-4 border-b border-[#E4E4E7] bg-white space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-medium text-[#18181B] uppercase tracking-wider flex items-center gap-1.5">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <span>Pesquisa Geográfica</span>
                    </h2>

                    <!-- Botão Filtros -->
                    <button type="button" @click="showFilters = !showFilters" 
                            :class="showFilters ? 'bg-black text-white' : 'bg-[#F4F4F5] text-[#18181B] border-[#E4E4E7]'"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-medium border transition-colors flex items-center gap-1 cursor-pointer">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        <span>Filtros</span>
                    </button>
                </div>

                <form @submit.prevent="validateAndSearch()" class="space-y-3">
                    
                    <!-- Campo de Endereço / Localização com Máscara / GPS -->
                    <div class="space-y-1">
                        <div class="relative">
                            <input type="text" x-model="searchQuery" 
                                   @input="applySearchMask($event); fetchAutocomplete($event.target.value)"
                                   @change="geocodeOnInput()"
                                   @click.away="showAutocomplete = false"
                                   @focus="showAutocomplete = autocompleteResults.length > 0"
                                   placeholder="Digite cidade, endereço, CEP (ex: 01305-000) ou lat, lng..."
                                   class="w-full h-9 pl-8 pr-9 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] placeholder-[#A1A1AA] focus:outline-none focus:border-black focus:bg-white transition-all">
                            <div class="absolute left-2.5 top-2.5 text-[#71717A]">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>

                            <!-- Botão GPS -->
                            <button type="button" @click="useCurrentLocation()" :disabled="loadingGps" title="Usar localização GPS atual"
                                    class="absolute right-1.5 top-1.5 p-1 rounded hover:bg-white text-[#71717A] hover:text-black transition-all cursor-pointer">
                                <svg x-show="!loadingGps" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                                </svg>
                                <svg x-show="loadingGps" x-cloak class="animate-spin" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>

                            <!-- Autocomplete Dropdown -->
                            <div x-cloak x-show="showAutocomplete" 
                                 class="absolute left-0 right-0 z-[2000] mt-1 bg-white border border-[#E4E4E7] rounded-lg shadow-lg max-h-60 overflow-y-auto divide-y divide-[#E4E4E7] text-xs">
                                <template x-for="(item, idx) in autocompleteResults" :key="idx">
                                    <button type="button" @click="selectAutocomplete(item)"
                                            class="w-full text-left px-3 py-2 hover:bg-[#F4F4F5] transition-colors flex items-start gap-2 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 mt-0.5 text-[#71717A] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>
                                        <span class="text-[#18181B] truncate" x-text="item.display_name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <template x-if="validationError">
                            <p class="text-[11px] text-red-600 font-medium" x-text="validationError"></p>
                        </template>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <!-- Raio -->
                        <div>
                            <select x-model="radius" class="w-full h-9 px-2.5 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                                <option value="500">Raio: 500m</option>
                                <option value="1000">Raio: 1 km</option>
                                <option value="2000">Raio: 2 km</option>
                                <option value="5000">Raio: 5 km</option>
                                <option value="10000">Raio: 10 km</option>
                                <option value="20000">Raio: 20 km</option>
                            </select>
                        </div>

                        <!-- Categoria -->
                        <div>
                            <input type="text" x-model="category" placeholder="Categoria (ex: farmácia)" id="category-input" x-ref="categoryInput"
                                   class="w-full h-9 px-2.5 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] placeholder-[#A1A1AA] focus:outline-none focus:border-black">
                        </div>
                    </div>

                    <!-- Filtros Desdobráveis -->
                    <div x-cloak x-show="showFilters" x-transition class="p-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] space-y-2 text-xs">
                        <span class="text-[10px] font-medium uppercase tracking-wider text-[#71717A] block border-b border-[#E4E4E7] pb-1">Filtrar Resultados</span>
                        <div class="grid grid-cols-2 gap-1.5">
                            <label class="flex items-center gap-1.5 text-[#18181B] cursor-pointer select-none text-[11px]">
                                <input type="checkbox" x-model="filters.hasWebsite" class="rounded border-[#E4E4E7] accent-black">
                                <span>Com Website</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-[#18181B] cursor-pointer select-none text-[11px]">
                                <input type="checkbox" x-model="filters.noWebsite" class="rounded border-[#E4E4E7] accent-black">
                                <span>Sem Website</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-[#18181B] cursor-pointer select-none text-[11px]">
                                <input type="checkbox" x-model="filters.hasWhatsapp" class="rounded border-[#E4E4E7] accent-black">
                                <span>Com WhatsApp</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-[#18181B] cursor-pointer select-none text-[11px]">
                                <input type="checkbox" x-model="filters.hasPhone" class="rounded border-[#E4E4E7] accent-black">
                                <span>Com Telefone</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-[#18181B] cursor-pointer select-none text-[11px]">
                                <input type="checkbox" x-model="filters.hasInstagram" class="rounded border-[#E4E4E7] accent-black">
                                <span>Com Instagram</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-[#18181B] cursor-pointer select-none text-[11px]">
                                <input type="checkbox" x-model="filters.hasFacebook" class="rounded border-[#E4E4E7] accent-black">
                                <span>Com Facebook</span>
                            </label>
                        </div>
                    </div>

                    <!-- Botão Submit -->
                    <button type="submit" :disabled="loading"
                            class="w-full h-9.5 rounded-lg flex items-center justify-center gap-2 text-xs font-semibold bg-black text-white hover:bg-zinc-800 transition-colors cursor-pointer disabled:opacity-50">
                        <svg x-show="!loading" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <svg x-show="loading" x-cloak class="animate-spin" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Buscando Empresas...' : 'Buscar Empresas'">Buscar Empresas</span>
                    </button>

                    <!-- Card de Área Buscada Específica -->
                    <template x-if="searchedArea">
                        <div class="mt-3 p-3 rounded-xl border border-[#E4E4E7] bg-[#F9F9FB] space-y-2 text-xs shadow-2xs">
                            <div class="flex items-center justify-between border-b border-[#E4E4E7] pb-1.5">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-[#18181B] flex items-center gap-1.5">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    <span>Área Buscada</span>
                                </span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-black text-white font-medium truncate max-w-[160px]" x-text="searchedArea.city"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-[11px] text-[#71717A]">
                                <div class="col-span-2 truncate"><strong class="text-[#18181B]">Local:</strong> <span class="text-[#18181B]" x-text="searchedArea.query"></span></div>
                                <div><strong class="text-[#18181B]">Raio:</strong> <span class="text-[#18181B]" x-text="searchedArea.formattedRadius"></span></div>
                                <div><strong class="text-[#18181B]">Categoria:</strong> <span class="text-[#18181B]" x-text="searchedArea.category"></span></div>
                                <div class="col-span-2"><strong class="text-[#18181B]">Coordenadas:</strong> <span class="font-mono text-[#18181B]" x-text="searchedArea.coords"></span></div>
                            </div>
                        </div>
                    </template>

                </form>
            </div>

            <!-- Subheader de Resultados da Lista -->
            <div class="px-4 py-2 border-b border-[#E4E4E7] bg-[#F4F4F5] flex items-center justify-between">
                <span class="text-xs font-medium text-[#18181B]">Lista de Resultados</span>
                <span class="text-[11px] px-2 py-0.5 rounded-full border border-[#E4E4E7] bg-white text-[#71717A] tabular-nums"
                      x-text="filteredCompanies.length + ' de ' + companies.length + ' empresas'"></span>
            </div>

            <!-- Lista de Cards de Resultados (Scroll Interno com Paginação) -->
            <div id="leads-list-container" class="flex-1 overflow-y-auto p-4 space-y-3 bg-[#F4F4F5]" @scroll="handleScroll($event)">
                <template x-for="(company, index) in paginatedCompanies" :key="getLeadId(company)">
                    <div :id="'lead-card-' + getLeadId(company)"
                         @click="selectLeadFromList(company)"
                         :class="selectedLeadId === getLeadId(company) ? 'border-black ring-2 ring-black/20 bg-zinc-50 shadow-md scale-[1.01]' : 'border-[#E4E4E7] bg-white hover:border-zinc-400 shadow-xs'"
                         class="p-3.5 rounded-xl border transition-all duration-200 space-y-2.5 cursor-pointer">
                        
                        <!-- Header do Card com Ícone do Tipo de Lead -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <!-- Ícone do Tipo de Lead -->
                                <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center text-white shadow-xs transition-transform hover:scale-105 mt-0.5"
                                     :style="'background-color: ' + getCategoryConfig(company.category, company.name).bg"
                                     x-html="getCategoryConfig(company.category, company.name).iconSvg">
                                </div>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <h3 class="font-semibold text-xs text-[#18181B] leading-tight truncate" x-text="company.name"></h3>
                                        <template x-if="selectedLeadId === getLeadId(company)">
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-black text-white shrink-0 animate-pulse">Selecionado</span>
                                        </template>
                                    </div>
                                    <span class="text-[11px] text-[#71717A] mt-0.5 inline-block" x-text="getCategoryConfig(company.category, company.name).label || company.category || 'Estabelecimento'"></span>
                                </div>
                            </div>

                            <button @click.stop="openWhatsAppModal(company)"
                                    class="h-7 px-2.5 rounded-lg text-[11px] font-medium bg-black hover:bg-zinc-800 text-white flex items-center gap-1 transition-colors cursor-pointer shrink-0"
                                    title="Enviar mensagem via WhatsApp">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                </svg>
                                <span>WhatsApp</span>
                            </button>
                        </div>

                        <!-- Detalhes do Lead (Endereço, Telefone, Site, Avaliações) -->
                        <div class="text-[11px] text-[#71717A] space-y-1.5 pt-0.5">
                            <!-- Endereço -->
                            <div class="flex items-start gap-1.5">
                                <svg class="w-3.5 h-3.5 shrink-0 mt-0.5 text-[#71717A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span class="leading-tight text-[#18181B]" x-text="company.address || company.city || 'Endereço não especificado'"></span>
                            </div>

                            <!-- Telefone / WhatsApp direto -->
                            <template x-if="company.phone || company.whatsapp">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#18181B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span class="text-[#18181B] font-medium" x-text="company.whatsapp || company.phone"></span>
                                </div>
                            </template>

                            <!-- Website -->
                            <template x-if="company.website">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#71717A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                    <a :href="company.website" target="_blank" @click.stop class="text-black font-medium hover:underline truncate" x-text="company.website"></a>
                                </div>
                            </template>

                            <!-- Avaliação / Reviews (se tiver) -->
                            <template x-if="company.rating">
                                <div class="flex items-center gap-1">
                                    <span class="text-yellow-500">★</span>
                                    <span class="text-[#18181B] font-medium" x-text="company.rating.toFixed(1)"></span>
                                    <span x-text="'(' + company.review_count + ' avaliações)'"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Indicadores de Presença -->
                        <div class="flex flex-wrap items-center gap-1.5 pt-0.5 text-[10px]">
                            <span :class="company.has_website ? 'border-black bg-black text-white font-medium' : 'border-[#E4E4E7] bg-[#F4F4F5] text-[#71717A]'"
                                  class="px-2 py-0.5 rounded-md border flex items-center gap-1">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line></svg>
                                <span x-text="company.has_website ? 'Site' : 'Sem site'"></span>
                            </span>

                            <span :class="company.has_whatsapp ? 'border-black bg-black text-white font-medium' : 'border-[#E4E4E7] bg-[#F4F4F5] text-[#71717A]'"
                                  class="px-2 py-0.5 rounded-md border flex items-center gap-1">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                <span x-text="company.has_whatsapp ? 'WhatsApp' : 'Sem Whats'"></span>
                            </span>

                            <span :class="company.has_phone ? 'border-black bg-black text-white font-medium' : 'border-[#E4E4E7] bg-[#F4F4F5] text-[#71717A]'"
                                  class="px-2 py-0.5 rounded-md border flex items-center gap-1">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                <span x-text="company.has_phone ? 'Telefone' : 'Sem fone'"></span>
                            </span>

                            <template x-if="company.has_instagram">
                                <span class="px-2 py-0.5 rounded-md border border-black bg-[#F4F4F5] text-[#18181B] font-medium">Instagram</span>
                            </template>
                        </div>

                        <!-- Footer do Card -->
                        <div class="pt-2 border-t border-[#E4E4E7] flex items-center justify-between">
                            <button @click.stop="focusMarker(company)" class="text-[11px] text-[#71717A] hover:text-black font-medium underline flex items-center gap-1">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span>Ver no mapa</span>
                            </button>
                            
                            <button @click.stop="addLead(company)"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-medium border border-[#E4E4E7] bg-white hover:bg-[#F4F4F5] text-[#18181B] flex items-center gap-1 transition-colors cursor-pointer">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>Adicionar Lead</span>
                            </button>
                        </div>

                    </div>
                </template>

                <template x-if="!hasSearched">
                    <div class="p-8 text-center space-y-2 bg-white rounded-xl border border-[#E4E4E7]">
                        <p class="text-xs text-[#71717A]">Inicie sua pesquisa geográfica.</p>
                        <p class="text-[11px] text-[#A1A1AA]">Digite o local e a categoria desejada acima para buscar leads.</p>
                    </div>
                </template>

                <template x-if="hasSearched && filteredCompanies.length === 0">
                    <div class="p-8 text-center space-y-2 bg-white rounded-xl border border-[#E4E4E7]">
                        <p class="text-xs text-red-600 font-medium">Nenhum lead encontrado.</p>
                        <p class="text-[11px] text-[#71717A]">Não encontramos resultados para esta combinação de local e categoria. Tente ajustar os filtros, aumentar o raio ou pesquisar outra região.</p>
                    </div>
                </template>
            </div>

        </div>

        <!-- ÁREA DA DIREITA: Mapa MapLibre GL JS (Renderiza Marcadores ao Pesquisar) -->
        <div class="flex-1 h-full relative">
            <div id="map" class="w-full h-full min-h-[400px]"></div>

            <!-- Contagem Flutuante de Resultados sobre o Mapa -->
            <div class="absolute bottom-4 left-4 z-[1000] bg-white/95 backdrop-blur-xs border border-[#E4E4E7] px-3.5 py-2 rounded-xl text-xs flex items-center gap-2 text-[#71717A] shadow-md">
                <span class="w-2.5 h-2.5 rounded-full bg-black"></span>
                <span>Empresas no mapa: <strong x-text="filteredCompanies.length" class="text-[#18181B] tabular-nums font-medium">0</strong></span>
            </div>
        </div>

    </div>

    <!-- Modal para Envio de WhatsApp -->
    <div x-cloak x-show="whatsappModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs">
        <div @click.away="closeWhatsAppModal()" class="w-full max-w-md p-6 rounded-xl border border-[#E4E4E7] bg-white space-y-4 shadow-lg">
            
            <div class="flex items-center justify-between border-b border-[#E4E4E7] pb-3">
                <h3 class="text-sm font-medium text-[#18181B]">Enviar WhatsApp para <span x-text="selectedCompany?.name"></span></h3>
                <button @click="closeWhatsAppModal()" class="text-[#71717A] hover:text-black text-lg cursor-pointer">&times;</button>
            </div>

            <!-- Seleção de Template -->
            <div class="space-y-1">
                <label class="block text-xs font-medium text-[#18181B]">Escolher Template Salvo</label>
                <select x-model="selectedTemplateId" @change="applyTemplate($event.target.value)"
                        class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                    <option value="">Mensagem Personalizada</option>
                    <template x-for="t in templatesList" :key="t.id">
                        <option :value="t.id" x-text="t.title"></option>
                    </template>
                </select>
            </div>

            <!-- Número de Telefone / WhatsApp -->
            <div class="space-y-1">
                <label class="block text-xs font-medium text-[#18181B]">Número de Telefone/WhatsApp</label>
                <input type="text" x-model="whatsappPhone" placeholder="Ex: (11) 99999-8888"
                       class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
            </div>

            <!-- Mensagem -->
            <div class="space-y-1">
                <label class="block text-xs font-medium text-[#18181B]">Mensagem que será enviada</label>
                <textarea x-model="whatsappMessage" rows="4"
                          class="w-full p-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button @click="closeWhatsAppModal()" class="h-9 px-4 rounded-lg text-xs font-medium border border-[#E4E4E7] text-[#71717A] hover:bg-[#F4F4F5] cursor-pointer">Cancelar</button>
                
                <button @click="sendWhatsAppMessage()" 
                        class="h-9 px-4 rounded-lg text-xs font-medium bg-black hover:bg-zinc-800 text-white flex items-center gap-1.5 cursor-pointer">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    <span>Abrir WhatsApp Web</span>
                </button>
            </div>

        </div>
    </div>

    <!-- Script de lógica do componente Alpine.js -->
    <script>
        function createGeoJSONCircle(centerLngLat, radiusInMeters, points = 64) {
            const lng = centerLngLat[0];
            const lat = centerLngLat[1];
            const km = radiusInMeters / 1000;
            const ret = [];
            const distanceX = km / (111.320 * Math.cos(lat * Math.PI / 180));
            const distanceY = km / 110.574;

            for (let i = 0; i < points; i++) {
                const theta = (i / points) * (2 * Math.PI);
                const x = distanceX * Math.cos(theta);
                const y = distanceY * Math.sin(theta);
                ret.push([lng + x, lat + y]);
            }
            ret.push(ret[0]);
            return {
                type: 'Feature',
                geometry: {
                    type: 'Polygon',
                    coordinates: [ret]
                }
            };
        }

        window.APP_DEBUG = @json(config('app.debug'));

        function searchComponent(initialTemplates = []) {
            return {
                searchQuery: '',
                latitude: null,
                longitude: null,
                radius: 1000,
                category: '',
                loading: false,
                loadingGps: false,
                validationError: '',
                companies: [],
                showFilters: false,
                map: null,
                markers: [],
                activePopup: null,
                activeMarkerElement: null,
                selectedLeadId: null,

                whatsappModalOpen: false,
                selectedCompany: null,
                selectedTemplateId: '',
                whatsappMessage: '',
                whatsappPhone: '',
                templatesList: initialTemplates,
                hasSearched: false,
                visibleCount: 15,
                filters: {
                    hasWebsite: false,
                    noWebsite: false,
                    hasWhatsapp: false,
                    hasPhone: false,
                    hasInstagram: false,
                    hasFacebook: false,
                },

                getLeadId(company) {
                    if (!company) return '';
                    return company.id || company.osm_id || (company.latitude && company.longitude ? `${company.latitude}_${company.longitude}` : '');
                },

                scrollToLeadInList(company) {
                    const leadId = this.getLeadId(company);
                    if (!leadId) return;

                    this.selectedLeadId = leadId;

                    const indexInFiltered = this.filteredCompanies.findIndex(c => this.getLeadId(c) === leadId);
                    if (indexInFiltered !== -1 && indexInFiltered >= this.visibleCount) {
                        this.visibleCount = Math.min(this.filteredCompanies.length, indexInFiltered + 10);
                    }

                    this.$nextTick(() => {
                        const cardEl = document.getElementById('lead-card-' + leadId);
                        if (cardEl) {
                            cardEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    });
                },

                selectLeadFromList(company) {
                    const leadId = this.getLeadId(company);
                    this.selectedLeadId = leadId;
                    this.focusMarker(company);
                },

                // Camadas de visualização do mapa
                centerMarker: null,
                centerCircle: null,
                cityPolygon: null,
                countryMarker: null,
                cityMarker: null,
                searchedArea: null,

                // Autocomplete
                autocompleteResults: [],
                showAutocomplete: false,
                loadingAutocomplete: false,
                debounceTimer: null,

                init() {
                    window.searchApp = this;
                    this.initMap();
                    this.$watch('filters', () => {
                        this.visibleCount = 15;
                        this.renderMapMarkers();
                    });
                    this.$watch('radius', (newRadius) => {
                        if (this.latitude !== null && this.longitude !== null) {
                            this.drawSearchCenterMarker(this.latitude, this.longitude);
                        }
                    });
                },

                initMap() {
                    // Inicializar MapLibre GL JS cobrindo todo o Brasil (visão nacional inicial)
                    this.map = new maplibregl.Map({
                        container: 'map',
                        style: {
                            version: 8,
                            sources: {
                                'raster-tiles': {
                                    type: 'raster',
                                    tiles: [
                                        'https://a.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
                                        'https://b.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
                                        'https://c.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
                                        'https://d.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png'
                                    ],
                                    tileSize: 256,
                                    attribution: '&copy; OpenStreetMap &copy; CARTO'
                                }
                            },
                            layers: [
                                {
                                    id: 'simple-tiles',
                                    type: 'raster',
                                    source: 'raster-tiles',
                                    minzoom: 0,
                                    maxzoom: 19
                                }
                            ]
                        },
                        center: [-51.92528, -14.235004], // [lng, lat]
                        zoom: 4
                    });

                    this.map.addControl(new maplibregl.NavigationControl(), 'top-right');

                    // Desenhar contorno traçado do território brasileiro no carregamento inicial
                    this.drawBrazilTerritoryOutline();
                },

                applySearchMask(event) {
                    // Ao digitar um novo termo, reseta latitude/longitude antigas para acionar geocodificação no backend
                    this.latitude = null;
                    this.longitude = null;

                    let val = event.target.value;
                    const digits = val.replace(/\D/g, '');

                    // Se for entrada exclusivamente numérica (máscara de CEP 00000-000)
                    if (/^\d[\d\-]*$/.test(val) && digits.length <= 8) {
                        if (digits.length > 5) {
                            this.searchQuery = digits.slice(0, 5) + '-' + digits.slice(5, 8);
                        } else {
                            this.searchQuery = digits;
                        }
                    }
                },

                validateAndSearch() {
                    this.validationError = '';
                    if (!this.searchQuery || this.searchQuery.trim().length < 2) {
                        this.validationError = 'Por favor, informe uma cidade, endereço, CEP ou coordenadas válidas para buscar.';
                        return;
                    }
                    if (!this.category || this.category.trim().length < 2) {
                        this.validationError = 'Por favor, informe uma categoria para filtrar.';
                        return;
                    }

                    // Se a busca for um termo de texto (e não par de coordenadas explícito lat,lng), limpa lat/lng para re-geocodificar
                    if (!/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/.test(this.searchQuery.trim())) {
                        this.latitude = null;
                        this.longitude = null;
                    }

                    this.performSearch();
                },

                async performSearch() {
                    this.loading = true;
                    this.validationError = '';
                    this.visibleCount = 15;
                    this.selectedLeadId = null;
                    try {
                        const payload = {
                            query: this.searchQuery,
                            radius: this.radius,
                            category: this.category
                        };

                        if (this.latitude !== null && this.longitude !== null) {
                            payload.latitude = this.latitude;
                            payload.longitude = this.longitude;
                        }

                        const response = await fetch('/search', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!response.ok) {
                            const errData = await response.json();
                            this.validationError = errData.message || 'Erro ao realizar a busca no servidor.';
                            this.companies = [];
                            this.renderMapMarkers();
                            return;
                        }

                        const data = await response.json();
                        this.companies = data.companies || [];
                        this.hasSearched = true;

                        // Desenhar centro de pesquisa, marcação de cidade e área buscada
                        if (data.center) {
                            const cityName = data.center.city || data.center.query || 'Cidade Pesquisada';
                            const lat = data.center.latitude;
                            const lng = data.center.longitude;

                            this.latitude = lat;
                            this.longitude = lng;

                            this.drawSearchCenterMarker(lat, lng);
                            this.drawCityMarker(cityName, lat, lng);

                            const radiusVal = parseInt(this.radius);
                            const radiusText = radiusVal >= 1000 ? `${(radiusVal / 1000).toFixed(1).replace('.0', '')} km` : `${radiusVal} m`;

                            this.searchedArea = {
                                city: cityName,
                                query: data.center.query || this.searchQuery,
                                radius: this.radius,
                                formattedRadius: radiusText,
                                category: data.category || this.category,
                                coords: `${lat.toFixed(4)}, ${lng.toFixed(4)}`
                            };
                        }

                        // Desenhar contorno territorial da cidade
                        if (data.city_geojson) {
                            this.drawCityBoundary(data.city_geojson);
                        } else {
                            this.clearCityBoundary();
                        }

                        // Renderiza os marcadores de empresa no mapa
                        this.renderMapMarkers();

                        // Se não houver marcadores de empresas, enquadra na cidade ou no centro da busca
                        if (this.markers.length === 0 && data.center) {
                            this.map.flyTo({ center: [data.center.longitude, data.center.latitude], zoom: 13 });
                        }
                    } catch (e) {
                        console.error('Erro na pesquisa:', e);
                        this.validationError = 'Falha ao realizar busca. Verifique sua conexão e tente novamente.';
                    } finally {
                        this.loading = false;
                    }
                },

                renderMapMarkers() {
                    if (this.activePopup) {
                        this.activePopup.remove();
                        this.activePopup = null;
                    }
                    if (this.activeMarkerElement) {
                        this.activeMarkerElement.classList.remove('marker-active');
                        this.activeMarkerElement = null;
                    }

                    this.markers.forEach(m => m.remove());
                    this.markers = [];

                    const visibleCompanies = this.filteredCompanies;
                    const debugMode = window.APP_DEBUG;

                    visibleCompanies.forEach(company => {
                        if (!company || !company.latitude || !company.longitude) return;

                        // Marcador customizado baseado na categoria
                        const el = window.LeadSpectMapMarkers.createMarkerElement(company);
                        if (!el) return;

                        const popupHtml = window.LeadSpectMapMarkers.createPopupHtml(company, debugMode);

                        const popup = new maplibregl.Popup({ 
                            offset: [0, -42], 
                            closeButton: true, 
                            closeOnClick: false 
                        }).setHTML(popupHtml);

                        const marker = new maplibregl.Marker({ element: el, anchor: 'bottom' })
                            .setLngLat([company.longitude, company.latitude])
                            .addTo(this.map);

                        const leadId = this.getLeadId(company);
                        marker.companyOsmId = leadId;
                        marker.companyPopup = popup;
                        marker.companyData = company;

                        this.markers.push(marker);

                        el.addEventListener('click', (e) => {
                            e.stopPropagation();
                            this.openCompanyPopup(company, marker, popup);
                        });

                        popup.on('close', () => {
                            if (el) {
                                el.classList.remove('marker-active');
                            }
                            if (this.activeMarkerElement === el) {
                                this.activeMarkerElement = null;
                            }
                            if (this.activePopup === popup) {
                                this.activePopup = null;
                            }
                        });
                    });

                    // Se houver marcadores visíveis, enquadra o mapa para exibir todos os pontos
                    if (this.markers.length > 0) {
                        const bounds = new maplibregl.LngLatBounds();
                        visibleCompanies.forEach(c => {
                            if (c.longitude && c.latitude) {
                                bounds.extend([c.longitude, c.latitude]);
                            }
                        });
                        this.map.fitBounds(bounds, { padding: 60, maxZoom: 16 });
                    }
                },

                openCompanyPopup(company, marker, popup) {
                    if (!company || !this.map) return;

                    const leadId = this.getLeadId(company);
                    this.selectedLeadId = leadId;

                    // 1. Fechar o popup ativo anterior se for diferente
                    if (this.activePopup && this.activePopup !== popup) {
                        this.activePopup.remove();
                    }

                    // 2. Remover destaque do elemento de marcador anterior
                    if (this.activeMarkerElement) {
                        this.activeMarkerElement.classList.remove('marker-active');
                    }

                    // 3. Associar as coordenadas e abrir o popup no mapa
                    if (popup) {
                        popup.setLngLat([company.longitude, company.latitude]);
                        if (!popup.isOpen()) {
                            popup.addTo(this.map);
                        }
                    }

                    // 4. Adicionar classe ativa no elemento DOM do marcador
                    const el = marker ? marker.getElement() : null;
                    if (el) {
                        el.classList.add('marker-active');
                        this.activeMarkerElement = el;
                    }
                    this.activePopup = popup;

                    // 5. Centralizar suavemente o mapa no marcador
                    this.map.flyTo({
                        center: [company.longitude, company.latitude],
                        zoom: Math.max(this.map.getZoom(), 16),
                        duration: 700,
                        essential: true
                    });

                    // 6. Rolar a lista lateral até o lead correspondente
                    this.scrollToLeadInList(company);
                },

                focusMarker(company) {
                    if (!company || !company.latitude || !company.longitude) return;

                    const leadId = this.getLeadId(company);
                    this.selectedLeadId = leadId;

                    const marker = this.markers.find(m => m.companyOsmId === leadId || m.companyOsmId === company.osm_id || m.companyOsmId === company.id);

                    if (marker && marker.companyPopup) {
                        this.openCompanyPopup(company, marker, marker.companyPopup);
                    } else {
                        this.map.flyTo({
                            center: [company.longitude, company.latitude],
                            zoom: 16,
                            duration: 700,
                            essential: true
                        });
                        this.scrollToLeadInList(company);
                    }
                },

                get filteredCompanies() {
                    if (!Array.isArray(this.companies)) return [];
                    return this.companies.filter(c => {
                        if (this.filters.hasWebsite && !c.has_website) return false;
                        if (this.filters.noWebsite && c.has_website) return false;
                        if (this.filters.hasWhatsapp && !c.has_whatsapp) return false;
                        if (this.filters.hasPhone && !c.has_phone) return false;
                        if (this.filters.hasInstagram && !c.has_instagram) return false;
                        if (this.filters.hasFacebook && !c.has_facebook) return false;
                        return true;
                    });
                },

                get paginatedCompanies() {
                    return this.filteredCompanies.slice(0, this.visibleCount);
                },

                handleScroll(event) {
                    const el = event.target;
                    // Ao scrollar próximo ao fundo, carrega mais 15 registros
                    if (el.scrollHeight - el.scrollTop - el.clientHeight < 50) {
                        if (this.visibleCount < this.filteredCompanies.length) {
                            this.visibleCount += 15;
                        }
                    }
                },

                openWhatsAppModalByOsmId(osmId) {
                    const company = this.companies.find(c => (c.id === osmId || c.osm_id === osmId || this.getLeadId(c) === osmId));
                    if (company) {
                        this.openWhatsAppModal(company);
                    }
                },

                addLeadByOsmId(osmId) {
                    const company = this.companies.find(c => (c.id === osmId || c.osm_id === osmId || this.getLeadId(c) === osmId));
                    if (company) {
                        this.addLead(company);
                    }
                },

                useCurrentLocation() {
                    if (!navigator.geolocation) {
                        alert('Seu navegador não suporta geolocalização por GPS.');
                        return;
                    }

                    this.loadingGps = true;
                    this.validationError = '';
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.latitude = position.coords.latitude;
                            this.longitude = position.coords.longitude;
                            this.searchQuery = `${this.latitude.toFixed(6)}, ${this.longitude.toFixed(6)}`;
                            this.loadingGps = false;

                            if (this.map) {
                                this.map.flyTo({ center: [this.longitude, this.latitude], zoom: 14 });
                                this.drawSearchCenterMarker(this.latitude, this.longitude);
                                this.fetchCityDetailsAndMark(this.latitude, this.longitude, 'Sua Localização');
                            }

                            this.$nextTick(() => {
                                if (this.$refs.categoryInput) {
                                    this.$refs.categoryInput.focus();
                                }
                            });
                        },
                        (error) => {
                            this.loadingGps = false;
                            let msg = 'Erro ao obter localização GPS.';
                            if (error.code === 1) msg = 'Permissão de localização negada pelo navegador.';
                            else if (error.code === 2) msg = 'Sinal de GPS indisponível no momento.';
                            this.validationError = msg;
                        },
                        { timeout: 10000, enableHighAccuracy: true }
                    );
                },

                async fetchCityDetailsAndMark(lat, lng, fallbackName = null) {
                    let cityName = fallbackName;
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1&polygon_geojson=1`, {
                            headers: {
                                'User-Agent': 'LeadSpect/1.0 (leadspect@domain.com)'
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            if (data.address) {
                                cityName = data.address.city || data.address.town || data.address.municipality || data.address.village || data.address.county || fallbackName || 'Cidade Pesquisada';
                            }
                            if (data.geojson && (data.geojson.type === 'Polygon' || data.geojson.type === 'MultiPolygon')) {
                                this.drawCityBoundary(data.geojson);
                            }
                        }
                    } catch (e) {
                        console.error('Erro ao buscar detalhes da cidade:', e);
                    }

                    if (cityName) {
                        this.drawCityMarker(cityName, lat, lng);
                    }
                },

                async geocodeOnInput() {
                    if (!this.searchQuery || this.searchQuery.trim().length < 3) return;
                    if (this.latitude !== null && this.longitude !== null) return;

                    try {
                        const query = this.searchQuery.trim();
                        const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&addressdetails=1&limit=1&countrycodes=br`, {
                            headers: {
                                'User-Agent': 'LeadSpect/1.0 (leadspect@domain.com)'
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            if (data && data.length > 0) {
                                const item = data[0];
                                this.latitude = parseFloat(item.lat);
                                this.longitude = parseFloat(item.lon);
                                const cityName = item.address?.city || item.address?.town || item.address?.municipality || item.address?.village || item.address?.county || item.display_name.split(',')[0];

                                if (this.map) {
                                    this.map.flyTo({ center: [this.longitude, this.latitude], zoom: 14 });
                                    this.drawSearchCenterMarker(this.latitude, this.longitude);
                                    this.drawCityMarker(cityName, this.latitude, this.longitude);
                                    this.fetchCityDetailsAndMark(this.latitude, this.longitude, cityName);
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Erro na geocodificação direta:', e);
                    }
                },

                drawSearchCenterMarker(lat, lng) {
                    if (this.centerMarker) {
                        this.centerMarker.remove();
                        this.centerMarker = null;
                    }

                    const el = document.createElement('div');
                    el.className = 'custom-search-center-icon';
                    el.style.cssText = 'background-color: #18181B; width: 12px; height: 12px; border: 2px solid #fff; border-radius: 50%; box-shadow: 0 0 8px rgba(0,0,0,0.5); cursor: pointer;';

                    const popup = new maplibregl.Popup({ offset: 15 }).setHTML('<div style="font-family:sans-serif;font-size:12px;padding:2px;"><b>Centro da sua pesquisa</b></div>');

                    this.centerMarker = new maplibregl.Marker({ element: el })
                        .setLngLat([lng, lat])
                        .setPopup(popup)
                        .addTo(this.map);

                    const circleGeoJson = createGeoJSONCircle([lng, lat], parseInt(this.radius));

                    if (this.map.getSource('search-center-circle')) {
                        this.map.getSource('search-center-circle').setData(circleGeoJson);
                    } else {
                        this.map.addSource('search-center-circle', {
                            type: 'geojson',
                            data: circleGeoJson
                        });
                        this.map.addLayer({
                            id: 'search-center-circle-fill',
                            type: 'fill',
                            source: 'search-center-circle',
                            paint: {
                                'fill-color': '#18181B',
                                'fill-opacity': 0.05
                            }
                        });
                        this.map.addLayer({
                            id: 'search-center-circle-line',
                            type: 'line',
                            source: 'search-center-circle',
                            paint: {
                                'line-color': '#18181B',
                                'line-width': 1.5,
                                'line-dasharray': [4, 4]
                            }
                        });
                    }
                },

                drawCityBoundary(geojson) {
                    if (this.map.getSource('city-boundary')) {
                        this.map.getSource('city-boundary').setData(geojson);
                    } else {
                        this.map.addSource('city-boundary', {
                            type: 'geojson',
                            data: geojson
                        });
                        this.map.addLayer({
                            id: 'city-boundary-fill',
                            type: 'fill',
                            source: 'city-boundary',
                            paint: {
                                'fill-color': '#000000',
                                'fill-opacity': 0.04
                            }
                        });
                        this.map.addLayer({
                            id: 'city-boundary-line',
                            type: 'line',
                            source: 'city-boundary',
                            paint: {
                                'line-color': '#000000',
                                'line-width': 2.2,
                                'line-dasharray': [3, 3]
                            }
                        });
                    }
                },

                drawBrazilTerritoryOutline() {
                    const brazilGeoJson = {
                        type: 'Feature',
                        geometry: {
                            type: 'Polygon',
                            coordinates: [[
                                [-51.6, 4.4], [-50.0, 1.5], [-48.5, -0.6], [-44.0, -2.4], [-40.0, -2.8],
                                [-35.5, -5.1], [-34.8, -7.1], [-35.2, -9.0], [-38.5, -13.0], [-39.2, -18.0],
                                [-40.3, -20.3], [-42.0, -23.0], [-46.3, -24.0], [-48.5, -25.5], [-48.5, -27.6],
                                [-53.4, -33.7], [-57.1, -29.8], [-53.7, -26.3], [-54.6, -25.5], [-57.8, -19.0],
                                [-65.3, -10.8], [-72.7, -7.6], [-70.0, -4.2], [-67.1, 1.2], [-60.2, 5.2],
                                [-56.0, 2.0], [-51.6, 4.4]
                            ]]
                        }
                    };

                    const addLayers = () => {
                        if (!this.map) return;
                        if (this.map.getSource('brazil-territory')) {
                            this.map.getSource('brazil-territory').setData(brazilGeoJson);
                        } else {
                            this.map.addSource('brazil-territory', {
                                type: 'geojson',
                                data: brazilGeoJson
                            });
                            this.map.addLayer({
                                id: 'brazil-territory-fill',
                                type: 'fill',
                                source: 'brazil-territory',
                                paint: {
                                    'fill-color': '#18181B',
                                    'fill-opacity': 0.04
                                }
                            });
                            this.map.addLayer({
                                id: 'brazil-territory-line',
                                type: 'line',
                                source: 'brazil-territory',
                                paint: {
                                    'line-color': '#18181B',
                                    'line-width': 2.2,
                                    'line-dasharray': [5, 4]
                                }
                            });
                        }
                    };

                    if (this.map.isStyleLoaded()) {
                        addLayers();
                    } else {
                        this.map.once('load', () => addLayers());
                    }
                },

                clearCityBoundary() {
                    if (this.map && this.map.getSource('city-boundary')) {
                        this.map.getSource('city-boundary').setData({
                            type: 'FeatureCollection',
                            features: []
                        });
                    }
                },

                getCategoryConfig(catStr, companyName = '') {
                    if (window.LeadSpectMapMarkers) {
                        return window.LeadSpectMapMarkers.getCategoryConfig(catStr, companyName);
                    }
                    return {
                        bg: '#18181B',
                        color: '#FFFFFF',
                        label: catStr || 'Estabelecimento',
                        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>`
                    };
                },

                drawCityMarker(cityName, lat, lng) {
                    if (this.cityMarker) {
                        this.cityMarker.remove();
                        this.cityMarker = null;
                    }
                },

                fetchAutocomplete(val) {
                    if (this.debounceTimer) clearTimeout(this.debounceTimer);

                    const query = val ? val.trim() : '';
                    if (query.length < 3) {
                        this.autocompleteResults = [];
                        this.showAutocomplete = false;
                        return;
                    }

                    this.debounceTimer = setTimeout(async () => {
                        this.loadingAutocomplete = true;
                        try {
                            const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&addressdetails=1&limit=5&countrycodes=br`, {
                                headers: {
                                    'User-Agent': 'LeadSpect/1.0 (leadspect@domain.com)'
                                }
                            });
                            if (response.ok) {
                                const data = await response.json();
                                this.autocompleteResults = data.map(item => ({
                                    display_name: item.display_name,
                                    lat: parseFloat(item.lat),
                                    lon: parseFloat(item.lon),
                                    address: item.address
                                }));
                                this.showAutocomplete = this.autocompleteResults.length > 0;
                            }
                        } catch (e) {
                            console.error('Erro no autocomplete:', e);
                        } finally {
                            this.loadingAutocomplete = false;
                        }
                    }, 350);
                },

                selectAutocomplete(item) {
                    this.searchQuery = item.display_name;
                    this.latitude = item.lat;
                    this.longitude = item.lon;
                    this.showAutocomplete = false;
                    this.autocompleteResults = [];

                    const cityName = item.address?.city || item.address?.town || item.address?.municipality || item.address?.village || item.address?.county || item.display_name.split(',')[0];

                    if (this.map) {
                        this.map.flyTo({ center: [item.lon, item.lat], zoom: 14 });
                        this.drawSearchCenterMarker(item.lat, item.lon);
                        this.drawCityMarker(cityName, item.lat, item.lon);
                        this.fetchCityDetailsAndMark(item.lat, item.lon, cityName);
                    }

                    this.$nextTick(() => {
                        if (this.$refs.categoryInput) {
                            this.$refs.categoryInput.focus();
                        }
                    });
                },

                async addLead(company) {
                    try {
                        const response = await fetch('/leads', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(company)
                        });
                        const data = await response.json();
                        alert(data.message || 'Lead adicionado com sucesso!');
                    } catch (e) {
                        alert('Erro ao adicionar lead.');
                    }
                },

                openWhatsAppModal(company) {
                    this.selectedCompany = company;
                    this.selectedTemplateId = '';
                    this.whatsappPhone = company.whatsapp || company.phone || '';
                    this.whatsappMessage = `Olá ${company.name}, tudo bem? Vi sua empresa no LeadSpect e gostaria de conversar sobre oportunidades.`;
                    this.whatsappModalOpen = true;
                },

                closeWhatsAppModal() {
                    this.whatsappModalOpen = false;
                },

                applyTemplate(templateId) {
                    if (!templateId) {
                        if (this.selectedCompany) {
                            this.whatsappMessage = `Olá ${this.selectedCompany.name}, tudo bem? Vi sua empresa no LeadSpect e gostaria de conversar sobre oportunidades.`;
                        }
                        return;
                    }
                    const t = this.templatesList.find(item => item.id == templateId);
                    if (t && this.selectedCompany) {
                        this.whatsappMessage = t.content
                            .replace(/@{{empresa}}/g, this.selectedCompany.name || '')
                            .replace(/@{{cidade}}/g, this.selectedCompany.city || '')
                            .replace(/@{{categoria}}/g, this.selectedCompany.category || '');
                    }
                },

                async sendWhatsAppMessage() {
                    try {
                        const response = await fetch('/leads/whatsapp', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                ...this.selectedCompany,
                                whatsapp: this.whatsappPhone,
                                phone: this.whatsappPhone,
                                custom_message: this.whatsappMessage,
                                template_id: this.selectedTemplateId
                            })
                        });
                        const data = await response.json();
                        if (data.whatsapp_url) {
                            window.open(data.whatsapp_url, '_blank');
                        }
                    } catch (e) {
                        alert('Erro ao iniciar contato no WhatsApp.');
                    } finally {
                        this.whatsappModalOpen = false;
                    }
                }
            };
        }
    </script>

</body>
</html>
