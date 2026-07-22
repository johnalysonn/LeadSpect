<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LeadSpect — Pipeline de Vendas (Kanban)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Custom scrollbars */
        .kanban-scroll::-webkit-scrollbar {
            height: 8px;
        }
        .kanban-scroll::-webkit-scrollbar-track {
            background: #F4F4F5;
        }
        .kanban-scroll::-webkit-scrollbar-thumb {
            background: #D4D4D8;
            border-radius: 9999px;
        }
        .kanban-scroll::-webkit-scrollbar-thumb:hover {
            background: #A1A1AA;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[#F4F4F5] text-[#18181B] select-none" x-data="kanbanComponent(@js($leadsByStatus), @js($templates))">

    <!-- Topbar Header -->
    <header class="h-14 border-b border-[#E4E4E7] bg-white px-4 sm:px-6 flex items-center justify-between z-20 shrink-0">
        <div class="flex items-center gap-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg border border-[#E4E4E7] flex items-center justify-center bg-black text-white">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                    </svg>
                </div>
                <span class="font-medium text-sm tracking-tight text-[#18181B]">LeadSpect</span>
            </a>

            <nav class="hidden md:flex items-center gap-1 text-xs">
                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Dashboard</a>
                <a href="{{ route('search.index') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Pesquisar Leads</a>
                <a href="{{ route('leads.index') }}" class="px-3 py-1.5 rounded-lg font-medium text-white bg-black">Pipeline (Kanban)</a>
                <a href="{{ route('templates.index') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Templates</a>
                <a href="{{ route('users.index') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Usuários</a>
            </nav>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('leads.export') }}" 
               class="h-8 px-3 rounded-lg border border-[#E4E4E7] bg-white text-xs font-medium text-[#18181B] hover:bg-[#F4F4F5] flex items-center gap-1.5 transition-colors cursor-pointer">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Exportar CSV</span>
            </a>

            <div class="flex items-center gap-2">
                @if (auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-7 h-7 rounded-full border border-[#E4E4E7] object-cover">
                @else
                    <div class="w-7 h-7 rounded-full border border-[#E4E4E7] bg-[#F4F4F5] flex items-center justify-center text-xs font-medium text-[#18181B]">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Subheader Kanban Header -->
    <div class="p-4 border-b border-[#E4E4E7] bg-white flex items-center justify-between shrink-0">
        <div>
            <h1 class="text-lg font-medium text-[#18181B] tracking-tight">Pipeline de Vendas (Kanban)</h1>
            <p class="text-xs text-[#71717A]">Arraste os cards entre as colunas para atualizar os status. Clique no card para ver detalhes, notas e contato rápido.</p>
        </div>

        <div class="flex items-center gap-2 text-xs text-[#71717A]">
            <span>Total de Leads: <strong class="text-[#18181B] tabular-nums">{{ $allLeads->count() }}</strong></span>
        </div>
    </div>

    <!-- Container do Quadro Kanban (Scroll Horizontal com Mouse Drag Panning) -->
    <div x-ref="boardContainer"
         @mousedown="startPan($event)"
         @mousemove="doPan($event)"
         @mouseup="stopPan()"
         @mouseleave="stopPan()"
         :class="isPanning ? 'cursor-grabbing' : 'cursor-grab'"
         class="flex-1 overflow-x-auto p-4 bg-[#F4F4F5] kanban-scroll transition-colors">
        
        <div class="flex items-start gap-4 min-w-max pb-6">
            
            @foreach (\App\Enums\LeadStatus::cases() as $status)
                <!-- Coluna de Status (Dropzone) -->
                <div @dragover.prevent="handleDragOver($event, '{{ $status->value }}')"
                     @dragleave="handleDragLeave($event, '{{ $status->value }}')"
                     @drop="handleDrop($event, '{{ $status->value }}', '{{ $status->label() }}')"
                     :class="{
                         'ring-2 ring-black bg-zinc-100 border-black scale-[1.01]': dragOverStatus === '{{ $status->value }}',
                         'border-[#E4E4E7] bg-white': dragOverStatus !== '{{ $status->value }}'
                     }"
                     class="w-72 rounded-xl border border-t-4 {{ $status->topBorderColor() }} flex flex-col max-h-[calc(100vh-11rem)] shadow-xs transition-all duration-150">
                    
                    <!-- Header da Coluna com Badge Colorido e Contador Reativo -->
                    <div class="p-3 border-b border-[#E4E4E7] flex items-center justify-between bg-white rounded-t-lg">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold border flex items-center gap-1.5 {{ $status->color() }}">
                                <span class="w-2 h-2 rounded-full {{ $status->dotColor() }}"></span>
                                <span>{{ $status->label() }}</span>
                            </span>
                        </div>
                        <span class="text-xs font-semibold text-[#71717A] bg-[#F4F4F5] px-2 py-0.5 rounded-full border border-[#E4E4E7] tabular-nums"
                              x-text="leadsByStatus['{{ $status->value }}']?.length || 0">
                        </span>
                    </div>

                    <!-- Lista Reativa de Cards no Status -->
                    <div class="flex-1 overflow-y-auto p-3 space-y-3 bg-[#F4F4F5]">
                        <template x-for="lead in (leadsByStatus['{{ $status->value }}'] || [])" :key="lead.id">
                            <div draggable="true"
                                 @dragstart="handleDragStart($event, lead)"
                                 @dragend="handleDragEnd($event)"
                                 @click="openLeadModal(lead)"
                                 :class="draggedLead?.id === lead.id ? 'opacity-40 border-dashed border-zinc-400' : 'bg-white hover:border-zinc-400 hover:shadow-md'"
                                 class="p-3.5 rounded-xl border border-[#E4E4E7] transition-all space-y-2.5 cursor-grab active:cursor-grabbing shadow-xs group">
                                
                                <div class="flex items-start justify-between gap-1.5">
                                    <h4 class="font-semibold text-xs text-[#18181B] leading-snug group-hover:text-black transition-colors" x-text="lead.name"></h4>
                                    <template x-if="lead.is_favorite">
                                        <span class="text-amber-500 text-xs font-bold shrink-0" title="Lead Favorito">★</span>
                                    </template>
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-[#71717A]">
                                    <span class="truncate max-w-[140px]" x-text="lead.category || 'Sem categoria'"></span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#F4F4F5] border border-[#E4E4E7] text-[#71717A] uppercase tracking-wider font-medium" x-text="lead.city || 'N/A'"></span>
                                </div>

                                <!-- Snippet da Observação do Lead (Máx ~150 caracteres) -->
                                <template x-if="lead.notes && lead.notes.length > 0">
                                    <div class="p-2.5 rounded-lg bg-amber-50/80 border border-amber-200/70 text-[11px] text-amber-950 flex items-start gap-1.5 leading-snug">
                                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span class="line-clamp-2 italic font-normal text-amber-900" x-text="truncateText(lead.notes[0].content, 150)"></span>
                                    </div>
                                </template>

                                <div class="text-[10px] text-[#71717A] flex items-center justify-between pt-2 border-t border-[#E4E4E7]">
                                    <span class="flex items-center gap-1">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <span class="tabular-nums" x-text="formatDate(lead.updated_at)"></span>
                                    </span>

                                    <span class="text-[10px] text-zinc-400 font-medium flex items-center gap-1 group-hover:text-black transition-colors">
                                        <span>Arrastar</span>
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="9" cy="5" r="1.5"></circle>
                                            <circle cx="9" cy="12" r="1.5"></circle>
                                            <circle cx="9" cy="19" r="1.5"></circle>
                                            <circle cx="15" cy="5" r="1.5"></circle>
                                            <circle cx="15" cy="12" r="1.5"></circle>
                                            <circle cx="15" cy="19" r="1.5"></circle>
                                        </svg>
                                    </span>
                                </div>

                            </div>
                        </template>

                        <template x-if="!leadsByStatus['{{ $status->value }}'] || leadsByStatus['{{ $status->value }}'].length === 0">
                            <div class="p-6 text-center text-[11px] text-[#71717A] border border-dashed border-[#E4E4E7] bg-white/60 rounded-xl space-y-1">
                                <svg class="w-6 h-6 mx-auto text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <span>Solte cards aqui</span>
                            </div>
                        </template>
                    </div>

                </div>
            @endforeach

        </div>
    </div>

    <!-- Modal de Detalhes do Lead (Senior UI/UX Redesign) -->
    <div x-cloak x-show="leadModalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-sm overflow-y-auto">
        <div @click.away="leadModalOpen = false" class="w-full max-w-3xl rounded-2xl border border-[#E4E4E7] bg-white shadow-2xl overflow-hidden flex flex-col max-h-[92vh] my-auto">
            
            <!-- Modal Header Top -->
            <div class="px-6 py-4 border-b border-[#E4E4E7] bg-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-zinc-900 text-white flex items-center justify-center font-semibold text-sm shadow-xs shrink-0">
                        <span x-text="activeLead?.name ? activeLead.name.substring(0, 2).toUpperCase() : 'LE'"></span>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-[#18181B] leading-tight" x-text="activeLead?.name"></h3>
                        <p class="text-xs text-[#71717A] flex items-center gap-1.5 mt-0.5">
                            <span x-text="activeLead?.category || 'Sem categoria'"></span>
                            <span>&bull;</span>
                            <span x-text="activeLead?.city || 'Cidade N/A'"></span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Botão Toggle Favorito -->
                    <button @click="toggleFavorite(activeLead)"
                            type="button"
                            class="p-2 rounded-xl border border-[#E4E4E7] hover:border-amber-300 hover:bg-amber-50 transition-all cursor-pointer group"
                            :title="activeLead?.is_favorite ? 'Remover dos Favoritos' : 'Marcar como Favorito'">
                        <svg class="w-4 h-4 transition-colors"
                             :class="activeLead?.is_favorite ? 'text-amber-500 fill-amber-500' : 'text-zinc-400 group-hover:text-amber-500'"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </button>
                    <!-- Botão Fechar Modal -->
                    <button @click="leadModalOpen = false" type="button" class="w-8 h-8 rounded-xl border border-[#E4E4E7] bg-white text-[#71717A] hover:text-black hover:bg-[#F4F4F5] flex items-center justify-center transition-all cursor-pointer">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Status da Negociação -->
            <div class="px-6 py-3 border-b border-[#E4E4E7] bg-[#F4F4F5] flex flex-wrap items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-[#18181B]">Status da Negociação:</span>
                    <select :value="activeLead?.status"
                            @change="updateModalStatus(activeLead.id, $event.target.value)"
                            class="h-9 px-3 rounded-xl border border-[#E4E4E7] bg-white text-xs font-medium text-[#18181B] focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black cursor-pointer shadow-2xs">
                        @foreach (\App\Enums\LeadStatus::cases() as $st)
                            <option value="{{ $st->value }}">{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-[#E4E4E7] text-[#71717A] font-medium" x-text="'Atualizado: ' + formatDate(activeLead?.updated_at)"></span>
                </div>
            </div>

            <!-- Conteúdo Interno com Scroll -->
            <div class="p-6 space-y-6 overflow-y-auto flex-1">

                <!-- FORMA DE CONTATO RÁPIDA (WHATSAPP) -->
                <div class="p-4 rounded-2xl border border-emerald-200/80 bg-emerald-50/30 space-y-3">
                    <div class="flex items-center justify-between border-b border-emerald-200/60 pb-2.5">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-[#25D366] text-white flex items-center justify-center shadow-xs">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </div>
                            <h4 class="text-xs font-bold text-emerald-950 uppercase tracking-wide">Forma de Contato Rápida via WhatsApp</h4>
                        </div>

                        <!-- Seletor de Modalidade (Padrão vs Personalizada) -->
                        <div class="flex items-center bg-white p-1 rounded-xl border border-emerald-200/80 shadow-2xs">
                            <button type="button"
                                    @click="activeContactTab = 'direct'"
                                    :class="activeContactTab === 'direct' ? 'bg-[#25D366] text-white font-semibold shadow-2xs' : 'text-zinc-600 hover:text-black font-medium'"
                                    class="px-3 py-1 rounded-lg text-xs transition-all cursor-pointer">
                                Mensagem Direta / Padrão
                            </button>
                            <button type="button"
                                    @click="activeContactTab = 'custom'"
                                    :class="activeContactTab === 'custom' ? 'bg-[#25D366] text-white font-semibold shadow-2xs' : 'text-zinc-600 hover:text-black font-medium'"
                                    class="px-3 py-1 rounded-lg text-xs transition-all cursor-pointer">
                                Mensagem Personalizada
                            </button>
                        </div>
                    </div>

                    <!-- Tab 1: Mensagem Direta / Padrão -->
                    <div x-show="activeContactTab === 'direct'" class="space-y-3">
                        <p class="text-xs text-emerald-900/80">Inicie o atendimento rapidamente abrindo a conversa com a mensagem padrão de apresentação:</p>
                        <div class="p-3 rounded-xl bg-white border border-emerald-200/60 text-xs text-zinc-700 italic">
                            &ldquo;Olá <span class="font-semibold text-black" x-text="activeLead?.name || 'Empresa'"></span>, tudo bem? Vi sua empresa no LeadSpect e gostaria de apresentar uma oportunidade para o seu negócio.&rdquo;
                        </div>
                        <button type="button"
                                @click="sendWhatsAppDirect()"
                                :disabled="sendingWhatsApp"
                                class="w-full h-10 px-4 rounded-xl bg-[#25D366] hover:bg-[#20bd5a] active:scale-[0.98] text-white text-xs font-semibold flex items-center justify-center gap-2 transition-all cursor-pointer shadow-xs disabled:opacity-50">
                            <template x-if="sendingWhatsApp">
                                <span>Iniciando WhatsApp...</span>
                            </template>
                            <template x-if="!sendingWhatsApp">
                                <span class="flex items-center gap-1.5">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="22" y1="2" x2="11" y2="13"></line>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                    </svg>
                                    Abrir WhatsApp com Mensagem Padrão
                                </span>
                            </template>
                        </button>
                    </div>

                    <!-- Tab 2: Mensagem Personalizada ou Template -->
                    <div x-show="activeContactTab === 'custom'" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="block text-[11px] font-semibold text-emerald-950 mb-1">Selecionar Template Salvo:</label>
                                <select x-model="selectedTemplateId"
                                        @change="onTemplateChange()"
                                        class="w-full h-9 px-3 rounded-xl border border-emerald-200/80 bg-white text-xs font-medium text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#25D366] cursor-pointer">
                                    <option value="">-- Escrever mensagem do zero --</option>
                                    <template x-for="tmpl in templates" :key="tmpl.id">
                                        <option :value="tmpl.id" x-text="tmpl.title"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-emerald-950 mb-1">Variáveis Rápidas (Clique para Inserir):</label>
                                <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                                    <button type="button" @click="insertTemplateVariable('@{{empresa}}')" class="px-2 py-1 rounded-md bg-white border border-emerald-200 text-[10px] font-mono text-emerald-800 hover:bg-emerald-100 transition-colors cursor-pointer">@{{empresa}}</button>
                                    <button type="button" @click="insertTemplateVariable('@{{cidade}}')" class="px-2 py-1 rounded-md bg-white border border-emerald-200 text-[10px] font-mono text-emerald-800 hover:bg-emerald-100 transition-colors cursor-pointer">@{{cidade}}</button>
                                    <button type="button" @click="insertTemplateVariable('@{{categoria}}')" class="px-2 py-1 rounded-md bg-white border border-emerald-200 text-[10px] font-mono text-emerald-800 hover:bg-emerald-100 transition-colors cursor-pointer">@{{categoria}}</button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <textarea x-model="customMessageText"
                                      rows="3"
                                      placeholder="Digite a mensagem personalizada que será enviada pelo WhatsApp..."
                                      class="w-full p-3 rounded-xl border border-emerald-200/80 bg-white text-xs text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#25D366] transition-all placeholder:text-zinc-400"></textarea>
                        </div>

                        <button type="button"
                                @click="sendWhatsAppCustom()"
                                :disabled="sendingWhatsApp"
                                class="w-full h-10 px-4 rounded-xl bg-[#25D366] hover:bg-[#20bd5a] active:scale-[0.98] text-white text-xs font-semibold flex items-center justify-center gap-2 transition-all cursor-pointer shadow-xs disabled:opacity-50">
                            <template x-if="sendingWhatsApp">
                                <span>Processando envio...</span>
                            </template>
                            <template x-if="!sendingWhatsApp">
                                <span class="flex items-center gap-1.5">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="22" y1="2" x2="11" y2="13"></line>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                    </svg>
                                    Enviar Mensagem Personalizada via WhatsApp
                                </span>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- DADOS DO LEAD & ENRIQUECIMENTO DE IA -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Endereço -->
                    <div class="p-3.5 rounded-xl border border-[#E4E4E7] bg-[#F4F4F5] space-y-1">
                        <span class="text-[10px] text-[#71717A] uppercase font-bold tracking-wider block">Endereço Completo</span>
                        <span class="text-[#18181B] font-medium block" x-text="activeLead?.address || 'N/A'"></span>
                        <span class="text-[11px] text-[#71717A] block" x-text="(activeLead?.city || '') + ' - ' + (activeLead?.neighborhood || '')"></span>
                    </div>

                    <!-- Telefone / WhatsApp -->
                    <div class="p-3.5 rounded-xl border border-[#E4E4E7] bg-[#F4F4F5] space-y-1">
                        <span class="text-[10px] text-[#71717A] uppercase font-bold tracking-wider block">Telefone / WhatsApp</span>
                        <span class="text-[#18181B] font-semibold block text-sm" x-text="activeLead?.whatsapp || activeLead?.phone || 'Não informado'"></span>
                        <template x-if="activeLead?.phone">
                            <a :href="'tel:' + activeLead.phone" class="text-[11px] text-zinc-600 hover:text-black underline block">Ligar para número</a>
                        </template>
                    </div>

                    <!-- Website -->
                    <div class="p-3.5 rounded-xl border border-[#E4E4E7] bg-[#F4F4F5] space-y-1">
                        <span class="text-[10px] text-[#71717A] uppercase font-bold tracking-wider block">Website Oficial</span>
                        <template x-if="hasValidWebsite(activeLead)">
                            <a :href="activeLead.website" target="_blank" class="text-black font-semibold hover:underline truncate flex items-center gap-1">
                                <span x-text="activeLead.website"></span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                    <polyline points="15 3 21 3 21 9"></polyline>
                                    <line x1="10" y1="14" x2="21" y2="3"></line>
                                </svg>
                            </a>
                        </template>
                        <template x-if="!hasValidWebsite(activeLead)">
                            <span class="text-[#71717A] italic block">Sem website cadastrado</span>
                        </template>
                    </div>

                    <!-- Botão de Enriquecimento de Dados Inteligente -->
                    <div class="p-3.5 rounded-xl border border-[#E4E4E7] bg-[#F4F4F5] space-y-1.5">
                        <span class="text-[10px] text-[#71717A] uppercase font-bold tracking-wider block">Enriquecimento de Inteligência</span>
                        
                        <!-- Botão Ativo (Caso possua site) -->
                        <template x-if="hasValidWebsite(activeLead)">
                            <div>
                                <button type="button"
                                        @click="enrichWebsite(activeLead?.id)"
                                        :disabled="enriching"
                                        class="w-full h-9 px-3 rounded-xl bg-black text-white hover:bg-zinc-800 active:scale-[0.98] text-xs font-semibold flex items-center justify-center gap-1.5 transition-all cursor-pointer shadow-2xs disabled:opacity-50">
                                    <template x-if="enriching">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Analisando site...
                                        </span>
                                    </template>
                                    <template x-if="!enriching">
                                        <span class="flex items-center gap-1.5">
                                            <span>✨</span>
                                            <span>Analisar site agora</span>
                                        </span>
                                    </template>
                                </button>
                                <p class="text-[10px] text-[#71717A] mt-1" x-text="activeLead?.enriched_at ? '✓ Analisado em ' + formatDate(activeLead.enriched_at) : 'Extrai emails, redes sociais e metadados.'"></p>
                            </div>
                        </template>

                        <!-- Botão Desabilitado com Aviso (Caso NÃO possua site) -->
                        <template x-if="!hasValidWebsite(activeLead)">
                            <div class="space-y-1">
                                <button type="button"
                                        disabled
                                        class="w-full h-9 px-3 rounded-xl border border-[#E4E4E7] bg-white text-[#71717A] text-xs font-medium flex items-center justify-center gap-1.5 cursor-not-allowed select-none opacity-75">
                                    <span>🚫</span>
                                    <span>Indisponível (Sem site)</span>
                                </button>
                                <p class="text-[10px] text-amber-700 bg-amber-50 p-1.5 rounded-lg border border-amber-200/60 leading-tight">
                                    Este lead não possui um site cadastrado para análise.
                                </p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- OBSERVAÇÕES INTERNAS DO LEAD -->
                <div class="border-t border-[#E4E4E7] pt-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-[#18181B] flex items-center gap-2">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span>Observações Internas</span>
                        </h4>
                        <span class="text-xs font-semibold text-[#71717A] bg-[#F4F4F5] px-2.5 py-0.5 rounded-full border border-[#E4E4E7] tabular-nums"
                              x-text="(activeLead?.notes?.length || 0) + ' observações'"></span>
                    </div>

                    <!-- Form de Adicionar Nota -->
                    <div class="space-y-2">
                        <textarea x-model="newNoteContent"
                                  @keydown.ctrl.enter="addNote()"
                                  rows="2"
                                  placeholder="Digite uma nova observação interna sobre o andamento da negociação..."
                                  class="w-full p-3 rounded-xl border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:bg-white focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-black transition-all placeholder:text-[#A1A1AA]"></textarea>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-[#71717A]">Dica: Pressione Ctrl + Enter para salvar rápido</span>
                            <button type="button"
                                    @click="addNote()"
                                    :disabled="addingNote || !newNoteContent.trim()"
                                    class="h-9 px-4 rounded-xl bg-black text-white text-xs font-medium hover:bg-zinc-800 active:scale-[0.98] transition-all cursor-pointer shadow-2xs disabled:opacity-50">
                                <span x-text="addingNote ? 'Salvando...' : 'Salvar Observação'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Lista de Observações Cadastradas -->
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        <template x-for="(note, nIdx) in (activeLead?.notes || [])" :key="note.id || nIdx">
                            <div class="p-3 rounded-xl border border-[#E4E4E7] bg-[#F4F4F5] space-y-1">
                                <p class="text-xs text-[#18181B] leading-relaxed whitespace-pre-line" x-text="note.content"></p>
                                <div class="flex items-center justify-between text-[10px] text-[#71717A] pt-1">
                                    <span class="font-medium">Observação cadastrada</span>
                                    <span class="tabular-nums" x-text="formatDate(note.created_at)"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="!activeLead?.notes || activeLead.notes.length === 0">
                            <div class="p-4 text-center text-xs text-[#71717A] border border-dashed border-[#E4E4E7] bg-[#F4F4F5]/50 rounded-xl">
                                Nenhuma observação cadastrada para este lead ainda.
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Partial Toast Notifications -->
    @include('partials.toast')

    <!-- Script Alpine.js -->
    <script>
        function kanbanComponent(initialLeadsByStatus, initialTemplates) {
            return {
                leadsByStatus: initialLeadsByStatus || {},
                templates: initialTemplates || [],

                leadModalOpen: false,
                activeLead: null,
                enriching: false,
                addingNote: false,
                sendingWhatsApp: false,

                // Modal Contact State
                activeContactTab: 'direct',
                selectedTemplateId: '',
                customMessageText: '',
                newNoteContent: '',

                // Drag & Drop State
                draggedLead: null,
                dragOverStatus: null,
                isDraggingCard: false,

                // Mouse Drag Panning State
                isPanning: false,
                startX: 0,
                scrollLeft: 0,

                openLeadModal(lead) {
                    this.activeLead = lead;
                    if (!this.activeLead.notes) {
                        this.activeLead.notes = [];
                    }
                    this.activeContactTab = 'direct';
                    this.selectedTemplateId = '';
                    this.customMessageText = '';
                    this.newNoteContent = '';
                    this.leadModalOpen = true;
                },

                hasValidWebsite(lead) {
                    if (!lead || !lead.website) return false;
                    const w = String(lead.website).trim().toLowerCase();
                    return w !== '' && w !== 'sem website' && w !== 'n/a' && w !== 'null' && w !== 'undefined';
                },

                formatDate(dateStr) {
                    if (!dateStr) return 'N/A';
                    try {
                        const date = new Date(dateStr);
                        if (isNaN(date.getTime())) return dateStr;
                        return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' }) + ' ' +
                               date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                    } catch(e) {
                        return dateStr;
                    }
                },

                truncateText(text, maxLen = 150) {
                    if (!text) return '';
                    if (text.length <= maxLen) return text;
                    return text.substring(0, maxLen).trim() + '...';
                },

                onTemplateChange() {
                    if (!this.selectedTemplateId) {
                        this.customMessageText = '';
                        return;
                    }
                    const tmpl = this.templates.find(t => t.id == this.selectedTemplateId);
                    if (tmpl && this.activeLead) {
                        let text = tmpl.content;
                        text = text.replaceAll('@{{empresa}}', this.activeLead.name || '');
                        text = text.replaceAll('@{{cidade}}', this.activeLead.city || '');
                        text = text.replaceAll('@{{categoria}}', this.activeLead.category || '');
                        this.customMessageText = text;
                    }
                },

                insertTemplateVariable(varName) {
                    let text = this.customMessageText || '';
                    this.customMessageText = text + (text ? ' ' : '') + varName;
                },

                async sendWhatsAppDirect() {
                    if (!this.activeLead) return;
                    this.sendingWhatsApp = true;
                    const defaultMsg = `Olá ${this.activeLead.name}, tudo bem? Vi sua empresa no LeadSpect e gostaria de apresentar uma oportunidade para o seu negócio.`;
                    
                    try {
                        const response = await fetch('/leads/whatsapp', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                lead_id: this.activeLead.id,
                                custom_message: defaultMsg
                            })
                        });
                        const data = await response.json();
                        if (data.success && data.whatsapp_url) {
                            this.updateLeadStatusLocally(this.activeLead.id, 'contato_iniciado');
                            if (window.showToast) {
                                window.showToast('Contato via WhatsApp iniciado com sucesso!', 'success');
                            }
                            window.open(data.whatsapp_url, '_blank');
                        } else {
                            if (window.showToast) window.showToast('Erro ao iniciar WhatsApp.', 'error');
                        }
                    } catch (e) {
                        if (window.showToast) window.showToast('Falha na requisição ao WhatsApp.', 'error');
                    } finally {
                        this.sendingWhatsApp = false;
                    }
                },

                async sendWhatsAppCustom() {
                    if (!this.activeLead) return;
                    if (!this.customMessageText.trim()) {
                        if (window.showToast) window.showToast('Digite uma mensagem ou escolha um template.', 'error');
                        return;
                    }
                    this.sendingWhatsApp = true;
                    try {
                        const response = await fetch('/leads/whatsapp', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                lead_id: this.activeLead.id,
                                template_id: this.selectedTemplateId || null,
                                custom_message: this.customMessageText
                            })
                        });
                        const data = await response.json();
                        if (data.success && data.whatsapp_url) {
                            this.updateLeadStatusLocally(this.activeLead.id, 'contato_iniciado');
                            if (window.showToast) {
                                window.showToast('Mensagem enviada com sucesso para o WhatsApp!', 'success');
                            }
                            window.open(data.whatsapp_url, '_blank');
                        } else {
                            if (window.showToast) window.showToast('Erro ao enviar mensagem personalizada.', 'error');
                        }
                    } catch (e) {
                        if (window.showToast) window.showToast('Falha na requisição ao WhatsApp.', 'error');
                    } finally {
                        this.sendingWhatsApp = false;
                    }
                },

                async toggleFavorite(lead) {
                    if (!lead) return;
                    try {
                        const response = await fetch(`/leads/${lead.id}/favorite`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            lead.is_favorite = data.is_favorite;
                            if (this.activeLead && this.activeLead.id === lead.id) {
                                this.activeLead.is_favorite = data.is_favorite;
                            }
                            if (window.showToast) {
                                window.showToast(data.is_favorite ? 'Lead marcado como favorito!' : 'Lead removido dos favoritos.', 'info');
                            }
                        }
                    } catch (e) {
                        if (window.showToast) window.showToast('Erro ao favoritar lead.', 'error');
                    }
                },

                updateLeadStatusLocally(leadId, newStatus) {
                    if (this.activeLead && this.activeLead.id === leadId) {
                        this.activeLead.status = newStatus;
                    }
                    for (const st in this.leadsByStatus) {
                        const idx = this.leadsByStatus[st].findIndex(l => l.id === leadId);
                        if (idx !== -1) {
                            const [lead] = this.leadsByStatus[st].splice(idx, 1);
                            lead.status = newStatus;
                            if (!this.leadsByStatus[newStatus]) {
                                this.leadsByStatus[newStatus] = [];
                            }
                            this.leadsByStatus[newStatus].unshift(lead);
                            break;
                        }
                    }
                },

                // Mouse Horizontal Panning
                startPan(e) {
                    if (this.isDraggingCard || e.target.closest('[draggable="true"]') || e.target.closest('button, input, select, a, textarea')) {
                        return;
                    }
                    this.isPanning = true;
                    const container = this.$refs.boardContainer;
                    this.startX = e.pageX - container.offsetLeft;
                    this.scrollLeft = container.scrollLeft;
                },

                doPan(e) {
                    if (!this.isPanning) return;
                    e.preventDefault();
                    const container = this.$refs.boardContainer;
                    const x = e.pageX - container.offsetLeft;
                    const walk = (x - this.startX) * 1.5;
                    container.scrollLeft = this.scrollLeft - walk;
                },

                stopPan() {
                    this.isPanning = false;
                },

                // Card Drag & Drop Handlers
                handleDragStart(e, lead) {
                    this.draggedLead = lead;
                    this.isDraggingCard = true;
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', lead.id);
                },

                handleDragEnd(e) {
                    this.draggedLead = null;
                    this.dragOverStatus = null;
                    this.isDraggingCard = false;
                },

                handleDragOver(e, statusValue) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    this.dragOverStatus = statusValue;
                },

                handleDragLeave(e, statusValue) {
                    if (this.dragOverStatus === statusValue) {
                        this.dragOverStatus = null;
                    }
                },

                async handleDrop(e, targetStatus, targetStatusLabel) {
                    e.preventDefault();
                    const lead = this.draggedLead;
                    this.dragOverStatus = null;
                    
                    if (!lead || lead.status === targetStatus) {
                        return;
                    }

                    const oldStatus = lead.status;
                    this.updateLeadStatusLocally(lead.id, targetStatus);

                    try {
                        const response = await fetch(`/leads/${lead.id}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ status: targetStatus })
                        });
                        
                        if (response.ok) {
                            if (window.showToast) {
                                window.showToast(`Status de "${lead.name}" alterado para "${targetStatusLabel}"!`, 'success');
                            }
                        } else {
                            this.updateLeadStatusLocally(lead.id, oldStatus);
                            if (window.showToast) {
                                window.showToast('Erro ao atualizar status do lead.', 'error');
                            }
                        }
                    } catch (e) {
                        this.updateLeadStatusLocally(lead.id, oldStatus);
                        if (window.showToast) {
                            window.showToast('Falha na requisição ao atualizar status.', 'error');
                        }
                    }
                },

                async updateModalStatus(leadId, newStatus) {
                    const select = event.target;
                    const targetLabel = select.options[select.selectedIndex].text;
                    const oldStatus = this.activeLead?.status;

                    this.updateLeadStatusLocally(leadId, newStatus);

                    try {
                        const response = await fetch(`/leads/${leadId}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ status: newStatus })
                        });
                        
                        if (response.ok) {
                            if (window.showToast) {
                                window.showToast(`Status de "${this.activeLead?.name}" alterado para "${targetLabel}"!`, 'success');
                            }
                        } else {
                            if (oldStatus) this.updateLeadStatusLocally(leadId, oldStatus);
                            if (window.showToast) {
                                window.showToast('Erro ao atualizar status do lead.', 'error');
                            }
                        }
                    } catch (e) {
                        if (oldStatus) this.updateLeadStatusLocally(leadId, oldStatus);
                        if (window.showToast) {
                            window.showToast('Falha na requisição ao atualizar status.', 'error');
                        }
                    }
                },

                async enrichWebsite(leadId) {
                    if (!leadId) return;
                    this.enriching = true;
                    try {
                        const response = await fetch(`/leads/${leadId}/enrich`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const data = await response.json();
                        if (data.lead) {
                            this.activeLead = data.lead;
                            for (const st in this.leadsByStatus) {
                                const idx = this.leadsByStatus[st].findIndex(l => l.id === leadId);
                                if (idx !== -1) {
                                    this.leadsByStatus[st][idx] = data.lead;
                                    break;
                                }
                            }
                            if (window.showToast) {
                                window.showToast('Dados do site analisados e enriquecidos com sucesso!', 'success');
                            }
                        }
                    } catch (e) {
                        if (window.showToast) {
                            window.showToast('Falha ao enriquecer dados do site.', 'error');
                        }
                    } finally {
                        this.enriching = false;
                    }
                },

                async addNote() {
                    if (!this.newNoteContent.trim() || !this.activeLead) return;
                    this.addingNote = true;
                    try {
                        const response = await fetch(`/leads/${this.activeLead.id}/notes`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ content: this.newNoteContent })
                        });
                        const data = await response.json();
                        if (response.ok && data.success) {
                            this.newNoteContent = '';
                            if (!this.activeLead.notes) {
                                this.activeLead.notes = [];
                            }
                            this.activeLead.notes.unshift(data.note);

                            // Update board object as well
                            for (const st in this.leadsByStatus) {
                                const lead = this.leadsByStatus[st].find(l => l.id === this.activeLead.id);
                                if (lead) {
                                    if (!lead.notes) lead.notes = [];
                                    lead.notes.unshift(data.note);
                                    break;
                                }
                            }

                            if (window.showToast) {
                                window.showToast('Observação interna salva com sucesso!', 'success');
                            }
                        } else {
                            if (window.showToast) window.showToast('Erro ao salvar observação.', 'error');
                        }
                    } catch (e) {
                        if (window.showToast) window.showToast('Erro ao salvar observação.', 'error');
                    } finally {
                        this.addingNote = false;
                    }
                }
            };
        }
    </script>

</body>
</html>
