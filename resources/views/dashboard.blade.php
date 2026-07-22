<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LeadSpect — Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen flex flex-col bg-[#F4F4F5] text-[#18181B]">

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
                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg font-medium text-white bg-black">Dashboard</a>
                <a href="{{ route('search.index') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Pesquisar Leads</a>
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

    <!-- Conteúdo Principal -->
    <main class="max-w-7xl mx-auto p-6 space-y-6 flex-1 w-full">
        
        <!-- Header da Página -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#E4E4E7] pb-4">
            <div>
                <h1 class="text-xl font-medium text-[#18181B] tracking-tight">Visão Geral da Prospecção</h1>
                <p class="text-xs text-[#71717A]">Métricas, taxa de conversão e desempenho do funil em tempo real</p>
            </div>

            <a href="{{ route('search.index') }}" 
               class="h-9 px-4 rounded-lg bg-black text-white text-xs font-medium hover:bg-zinc-800 flex items-center justify-center gap-2 transition-colors cursor-pointer self-start sm:self-auto">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <span>Nova Pesquisa de Leads</span>
            </a>
        </div>

        <!-- Grid de Cards KPI (4 Colunas, Monocromático) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Empresas Encontradas -->
            <div class="p-5 rounded-xl border border-[#E4E4E7] bg-white space-y-2 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-medium text-[#71717A] uppercase tracking-wider">Empresas Encontradas</span>
                    <span class="text-xs text-[#A1A1AA]">Mapeadas</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-medium text-[#18181B] tabular-nums">{{ number_format($totalCompaniesFound) }}</span>
                    <!-- Sparkline Monocromático -->
                    <div class="flex items-end gap-1 h-6">
                        <span class="w-1 bg-[#E4E4E7] rounded-t h-2"></span>
                        <span class="w-1 bg-[#E4E4E7] rounded-t h-4"></span>
                        <span class="w-1 bg-[#E4E4E7] rounded-t h-3"></span>
                        <span class="w-1 bg-black rounded-t h-6"></span>
                    </div>
                </div>
                <p class="text-[11px] text-[#71717A]">Busca geográfica ativa</p>
            </div>

            <!-- Card 2: Total de Leads Salvos -->
            <div class="p-5 rounded-xl border border-[#E4E4E7] bg-white space-y-2 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-medium text-[#71717A] uppercase tracking-wider">Total de Leads</span>
                    <span class="text-xs text-[#A1A1AA]">No Pipeline</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-medium text-[#18181B] tabular-nums">{{ number_format($totalLeads) }}</span>
                    <div class="flex items-end gap-1 h-6">
                        <span class="w-1 bg-[#E4E4E7] rounded-t h-3"></span>
                        <span class="w-1 bg-[#E4E4E7] rounded-t h-5"></span>
                        <span class="w-1 bg-black rounded-t h-6"></span>
                    </div>
                </div>
                <p class="text-[11px] text-[#71717A]">Empresas salvas no funil</p>
            </div>

            <!-- Card 3: Clientes Fechados -->
            <div class="p-5 rounded-xl border border-[#E4E4E7] bg-white space-y-2 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-medium text-[#71717A] uppercase tracking-wider">Clientes Fechados</span>
                    <span class="text-xs text-[#A1A1AA]">Convertidos</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-medium text-[#18181B] tabular-nums">{{ number_format($closedClients) }}</span>
                    <div class="flex items-end gap-1 h-6">
                        <span class="w-1 bg-[#E4E4E7] rounded-t h-2"></span>
                        <span class="w-1 bg-black rounded-t h-6"></span>
                    </div>
                </div>
                <p class="text-[11px] text-[#71717A]">Vendas concluídas</p>
            </div>

            <!-- Card 4: Taxa de Conversão -->
            <div class="p-5 rounded-xl border border-[#E4E4E7] bg-white space-y-2 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-medium text-[#71717A] uppercase tracking-wider">Taxa de Conversão</span>
                    <span class="text-xs text-[#A1A1AA]">Eficiência</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-medium text-[#18181B] tabular-nums">{{ $conversionRate }}%</span>
                    <div class="flex items-end gap-1 h-6">
                        <span class="w-1 bg-[#E4E4E7] rounded-t h-4"></span>
                        <span class="w-1 bg-black rounded-t h-6"></span>
                    </div>
                </div>
                <p class="text-[11px] text-[#71717A]">Fechados vs. Total de Leads</p>
            </div>

        </div>

        <!-- Seção 2: Distribuição do Funil e Cidades -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Funil por Etapa (Kanban Breakdown) -->
            <div class="lg:col-span-2 p-6 rounded-xl border border-[#E4E4E7] bg-white space-y-4 shadow-xs">
                <div class="flex items-center justify-between border-b border-[#E4E4E7] pb-3">
                    <div>
                        <h3 class="text-sm font-medium text-[#18181B]">Distribuição do Funil de Vendas</h3>
                        <p class="text-xs text-[#71717A]">Volume de leads por cada uma das 9 etapas</p>
                    </div>
                    <a href="{{ route('leads.index') }}" class="text-xs font-medium text-[#18181B] hover:underline">Ver Kanban &rarr;</a>
                </div>

                <div class="space-y-3 pt-1">
                    @foreach ($leadsByStatus as $status => $count)
                        @php
                            $statusEnum = \App\Enums\LeadStatus::tryFrom($status);
                            $percentage = $totalLeads > 0 ? round(($count / $totalLeads) * 100, 1) : 0;
                        @endphp
                        <div class="space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-[#18181B] font-medium">{{ $statusEnum ? $statusEnum->label() : ucfirst($status) }}</span>
                                <div class="flex items-center gap-2 text-[#71717A] tabular-nums">
                                    <span>{{ $count }} leads</span>
                                    <span class="text-[11px] font-mono text-[#A1A1AA]">({{ $percentage }}%)</span>
                                </div>
                            </div>
                            <!-- Barra Waffle/Pixel Monocromática -->
                            <div class="w-full h-2 rounded-full bg-[#F4F4F5] overflow-hidden border border-[#E4E4E7]">
                                <div class="h-full bg-black rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Coluna Direita: Top Categorias & Cidades -->
            <div class="space-y-6">
                
                <!-- Top Categorias -->
                <div class="p-6 rounded-xl border border-[#E4E4E7] bg-white space-y-4 shadow-xs">
                    <h3 class="text-sm font-medium text-[#18181B] border-b border-[#E4E4E7] pb-3">Top Categorias Mapeadas</h3>
                    
                    <div class="space-y-2.5">
                        @forelse ($topCategories as $cat)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-[#18181B]">{{ $cat->category ?: 'Outros' }}</span>
                                <span class="px-2 py-0.5 rounded-full border border-[#E4E4E7] bg-[#F4F4F5] text-[#71717A] font-medium tabular-nums">{{ $cat->total }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-[#71717A]">Nenhuma categoria registrada.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Top Cidades -->
                <div class="p-6 rounded-xl border border-[#E4E4E7] bg-white space-y-4 shadow-xs">
                    <h3 class="text-sm font-medium text-[#18181B] border-b border-[#E4E4E7] pb-3">Principais Cidades Mapeadas</h3>
                    
                    <div class="space-y-2.5">
                        @forelse ($topCities as $c)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-[#18181B]">{{ $c->city ?: 'Não informada' }}</span>
                                <span class="px-2 py-0.5 rounded-full border border-[#E4E4E7] bg-[#F4F4F5] text-[#71717A] font-medium tabular-nums">{{ $c->total }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-[#71717A]">Nenhuma cidade registrada.</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

        <!-- Seção 3: Tabela de Leads Recentes -->
        <div class="p-6 rounded-xl border border-[#E4E4E7] bg-white space-y-4 shadow-xs">
            <div class="flex items-center justify-between border-b border-[#E4E4E7] pb-3">
                <div>
                    <h3 class="text-sm font-medium text-[#18181B]">Leads Adicionados Recentes</h3>
                    <p class="text-xs text-[#71717A]">Últimos estabelecimentos salvos no seu funil de prospecção</p>
                </div>
                <a href="{{ route('leads.index') }}" class="text-xs font-medium text-[#18181B] hover:underline">Ver todos &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#E4E4E7] text-[#71717A] font-medium">
                            <th class="py-2.5 px-3">Empresa</th>
                            <th class="py-2.5 px-3">Categoria</th>
                            <th class="py-2.5 px-3">Cidade</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3 text-right">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E4E4E7]">
                        @forelse ($recentLeads as $lead)
                            <tr class="hover:bg-[#F4F4F5] transition-colors">
                                <td class="py-3 px-3 font-medium text-[#18181B]">{{ $lead->name }}</td>
                                <td class="py-3 px-3 text-[#71717A]">{{ $lead->category ?: '-' }}</td>
                                <td class="py-3 px-3 text-[#71717A]">{{ $lead->city ?: '-' }}</td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full border border-[#E4E4E7] bg-[#F4F4F5] text-[#18181B] text-[11px] font-medium">
                                        {{ $lead->status->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right text-[#71717A] tabular-nums">{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-[#71717A]">
                                    Nenhum lead cadastrado ainda. Use a barra de pesquisa para começar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Partial Toast Notifications -->
    @include('partials.toast')

</body>
</html>
