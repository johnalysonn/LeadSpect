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
</head>
<body class="min-h-screen flex flex-col bg-[#F4F4F5] text-[#18181B]" x-data="kanbanComponent()">

    <!-- Topbar Header -->
    <header class="h-14 border-b border-[#E4E4E7] bg-white px-4 sm:px-6 flex items-center justify-between z-20">
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
    <div class="p-4 border-b border-[#E4E4E7] bg-white flex items-center justify-between">
        <div>
            <h1 class="text-lg font-medium text-[#18181B] tracking-tight">Pipeline de Vendas (Kanban)</h1>
            <p class="text-xs text-[#71717A]">Gerencie o progresso e histórico dos seus leads da prospecção ao fechamento</p>
        </div>

        <div class="flex items-center gap-2 text-xs text-[#71717A]">
            <span>Total de Leads: <strong class="text-[#18181B] tabular-nums">{{ $allLeads->count() }}</strong></span>
        </div>
    </div>

    <!-- Container do Quadro Kanban (Scroll Horizontal) -->
    <div class="flex-1 overflow-x-auto p-4 bg-[#F4F4F5]">
        <div class="flex items-start gap-4 min-w-max pb-6">
            
            @foreach (\App\Enums\LeadStatus::cases() as $status)
                @php
                    $statusLeads = $leadsByStatus[$status->value] ?? collect();
                @endphp
                
                <!-- Coluna de Status -->
                <div class="w-72 rounded-xl border border-[#E4E4E7] bg-white flex flex-col max-h-[calc(100vh-11rem)] shadow-xs">
                    
                    <!-- Header da Coluna -->
                    <div class="p-3 border-b border-[#E4E4E7] flex items-center justify-between bg-[#F4F4F5]">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium border {{ $status->color() }}">
                                {{ $status->label() }}
                            </span>
                        </div>
                        <span class="text-xs font-medium text-[#71717A] tabular-nums">{{ $statusLeads->count() }}</span>
                    </div>

                    <!-- Lista de Cards no Status -->
                    <div class="flex-1 overflow-y-auto p-3 space-y-3 bg-[#F4F4F5]">
                        @forelse ($statusLeads as $lead)
                            <div class="p-3.5 rounded-lg border border-[#E4E4E7] bg-white hover:border-zinc-400 transition-all space-y-2 cursor-pointer shadow-xs"
                                 @click="openLeadModal({{ json_encode($lead) }})">
                                
                                <div class="flex items-start justify-between gap-1">
                                    <h4 class="font-medium text-xs text-[#18181B] leading-tight">{{ $lead->name }}</h4>
                                    @if ($lead->is_favorite)
                                        <span class="text-black text-xs font-bold">★</span>
                                    @endif
                                </div>

                                <p class="text-[11px] text-[#71717A] leading-tight">{{ $lead->category ?: 'Sem categoria' }}</p>

                                <div class="text-[10px] text-[#71717A] flex items-center justify-between pt-1 border-t border-[#E4E4E7]">
                                    <span>{{ $lead->city ?: 'Cidade N/A' }}</span>
                                    <span class="tabular-nums">{{ $lead->updated_at->format('d/m H:i') }}</span>
                                </div>

                                <!-- Mudar de Status Rápido -->
                                <div class="pt-1" @click.stop>
                                    <select @change="changeStatus({{ $lead->id }}, $event.target.value)"
                                            class="w-full h-7 px-2 rounded-md border border-[#E4E4E7] bg-[#F4F4F5] text-[10px] text-[#18181B] focus:outline-none focus:border-black">
                                        @foreach (\App\Enums\LeadStatus::cases() as $st)
                                            <option value="{{ $st->value }}" {{ $lead->status === $st ? 'selected' : '' }}>
                                                {{ $st->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        @empty
                            <div class="p-4 text-center text-[11px] text-[#71717A] border border-dashed border-[#E4E4E7] bg-white rounded-lg">
                                Nenhum lead nesta etapa
                            </div>
                        @endforelse
                    </div>

                </div>
            @endforeach

        </div>
    </div>

    <!-- Modal de Detalhes do Lead -->
    <div x-cloak x-show="leadModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs">
        <div @click.away="leadModalOpen = false" class="w-full max-w-2xl p-6 rounded-xl border border-[#E4E4E7] bg-white space-y-5 max-h-[90vh] overflow-y-auto shadow-lg">
            
            <div class="flex items-start justify-between border-b border-[#E4E4E7] pb-3">
                <div>
                    <h3 class="text-base font-medium text-[#18181B]" x-text="activeLead?.name"></h3>
                    <p class="text-xs text-[#71717A]" x-text="activeLead?.category + ' &bull; ' + (activeLead?.city || '')"></p>
                </div>
                <button @click="leadModalOpen = false" class="text-[#71717A] hover:text-black text-lg cursor-pointer">&times;</button>
            </div>

            <!-- Dados do Lead -->
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5]">
                    <span class="text-[10px] text-[#71717A] uppercase font-medium block">Endereço</span>
                    <span class="text-[#18181B]" x-text="activeLead?.address || 'N/A'"></span>
                </div>
                <div class="p-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5]">
                    <span class="text-[10px] text-[#71717A] uppercase font-medium block">Telefone / WhatsApp</span>
                    <span class="text-[#18181B]" x-text="activeLead?.whatsapp || activeLead?.phone || 'N/A'"></span>
                </div>
                <div class="p-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5]">
                    <span class="text-[10px] text-[#71717A] uppercase font-medium block">Website</span>
                    <a :href="activeLead?.website" target="_blank" class="text-black font-medium hover:underline" x-text="activeLead?.website || 'Sem website'"></a>
                </div>
                <div class="p-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5]">
                    <span class="text-[10px] text-[#71717A] uppercase font-medium block">Enriquecimento de Dados</span>
                    <button @click="enrichWebsite(activeLead?.id)" :disabled="enriching"
                            class="mt-1 px-2.5 py-1 rounded-md bg-black text-white hover:bg-zinc-800 text-[11px] flex items-center gap-1 transition-colors cursor-pointer">
                        <span x-text="enriching ? 'Analisando site...' : 'Analisar site agora'"></span>
                    </button>
                </div>
            </div>

            <!-- Adicionar Nota -->
            <div class="space-y-2 border-t border-[#E4E4E7] pt-3">
                <h4 class="text-xs font-medium text-[#18181B]">Adicionar Observação Interna</h4>
                <div class="flex gap-2">
                    <input type="text" x-model="newNoteContent" placeholder="Digite uma nota sobre a negociação..."
                           class="flex-1 h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                    <button @click="addNote()" class="h-9 px-4 rounded-lg bg-black text-white text-xs font-medium hover:bg-zinc-800 cursor-pointer">Salvar</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Script Alpine.js -->
    <script>
        function kanbanComponent() {
            return {
                leadModalOpen: false,
                activeLead: null,
                enriching: false,
                newNoteContent: '',

                openLeadModal(lead) {
                    this.activeLead = lead;
                    this.leadModalOpen = true;
                },

                async changeStatus(leadId, newStatus) {
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
                            window.location.reload();
                        }
                    } catch (e) {
                        alert('Erro ao atualizar status.');
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
                            alert('Dados enriquecidos com sucesso!');
                        }
                    } catch (e) {
                        alert('Falha ao enriquecer dados.');
                    } finally {
                        this.enriching = false;
                    }
                },

                async addNote() {
                    if (!this.newNoteContent.trim() || !this.activeLead) return;
                    try {
                        const response = await fetch(`/leads/${this.activeLead.id}/notes`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ content: this.newNoteContent })
                        });
                        if (response.ok) {
                            this.newNoteContent = '';
                            alert('Observação adicionada!');
                        }
                    } catch (e) {
                        alert('Erro ao salvar nota.');
                    }
                }
            };
        }
    </script>

</body>
</html>
