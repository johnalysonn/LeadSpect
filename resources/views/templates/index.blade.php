<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LeadSpect — Templates de Mensagem</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen flex flex-col bg-[#F4F4F5] text-[#18181B]" x-data="templateComponent()">

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
                <a href="{{ route('leads.index') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Pipeline (Kanban)</a>
                <a href="{{ route('templates.index') }}" class="px-3 py-1.5 rounded-lg font-medium text-white bg-black">Templates</a>
                <a href="{{ route('users.index') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Usuários</a>
            </nav>
        </div>

        <div class="flex items-center gap-3">
            <button @click="openModal()" 
                    class="h-8 px-3 rounded-lg text-xs font-medium bg-black text-white hover:bg-zinc-800 transition-colors flex items-center gap-1.5 cursor-pointer">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Novo Template</span>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto p-6 space-y-6 flex-1 w-full">
        
        <!-- Header -->
        <div class="border-b border-[#E4E4E7] pb-4">
            <h1 class="text-xl font-medium text-[#18181B] tracking-tight">Templates de Mensagens para WhatsApp</h1>
            <p class="text-xs text-[#71717A]">Crie modelos de prospecção com variáveis automáticas para agilizar o contato inicial</p>
        </div>

        <!-- Status Message -->
        @if (session('status'))
            <div class="p-3 rounded-lg text-xs flex items-center gap-2 border border-[#E4E4E7] bg-white text-[#18181B]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Grid de Templates -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($templates as $template)
                <div class="p-5 rounded-xl border border-[#E4E4E7] bg-white space-y-3 shadow-xs">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-[#18181B]">{{ $template->title }}</h3>
                            <span class="text-[11px] text-[#71717A]">{{ $template->category ?: 'Geral' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="openModal({{ json_encode($template) }})" class="text-xs text-[#71717A] hover:text-black font-medium underline cursor-pointer">Editar</button>
                            
                            <form action="{{ route('templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Deseja excluir este template?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-[#71717A] hover:text-black font-medium underline cursor-pointer">Excluir</button>
                            </form>
                        </div>
                    </div>

                    <div class="p-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] font-mono whitespace-pre-wrap">
                        {{ $template->content }}
                    </div>
                </div>
            @empty
                <div class="col-span-2 p-12 text-center border border-dashed border-[#E4E4E7] bg-white rounded-xl space-y-2">
                    <p class="text-xs text-[#71717A]">Nenhum template salvo ainda.</p>
                    <p class="text-[11px] text-[#A1A1AA]">Clique em "Novo Template" para criar sua primeira mensagem personalizada.</p>
                </div>
            @endforelse
        </div>

    </main>

    <!-- Modal Novo/Editar Template -->
    <div x-cloak x-show="modalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs">
        <div @click.away="modalOpen = false" class="w-full max-w-lg p-6 rounded-xl border border-[#E4E4E7] bg-white space-y-4 shadow-lg">
            
            <div class="flex items-center justify-between border-b border-[#E4E4E7] pb-3">
                <h3 class="text-sm font-medium text-[#18181B]" x-text="editMode ? 'Editar Template' : 'Novo Template de Mensagem'"></h3>
                <button @click="modalOpen = false" class="text-[#71717A] hover:text-black text-lg cursor-pointer">&times;</button>
            </div>

            <form :action="editMode ? '/templates/' + form.id : '/templates'" method="POST" class="space-y-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-[#18181B]">Título do Template</label>
                    <input type="text" name="title" x-model="form.title" required placeholder="Ex: Apresentação Landing Page"
                           class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-[#18181B]">Categoria (opcional)</label>
                    <input type="text" name="category" x-model="form.category" placeholder="Ex: Landing Page, Automação, Site"
                           class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-[#18181B]">Conteúdo da Mensagem</label>
                    <div class="flex gap-1 mb-1">
                        <button type="button" @click="insertVar('@{{empresa}}')" class="px-2 py-0.5 rounded-md text-[10px] bg-[#F4F4F5] text-[#18181B] border border-[#E4E4E7] font-medium">+ empresa</button>
                        <button type="button" @click="insertVar('@{{cidade}}')" class="px-2 py-0.5 rounded-md text-[10px] bg-[#F4F4F5] text-[#18181B] border border-[#E4E4E7] font-medium">+ cidade</button>
                        <button type="button" @click="insertVar('@{{categoria}}')" class="px-2 py-0.5 rounded-md text-[10px] bg-[#F4F4F5] text-[#18181B] border border-[#E4E4E7] font-medium">+ categoria</button>
                    </div>
                    <textarea name="content" x-model="form.content" rows="5" required
                              class="w-full p-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="h-9 px-4 rounded-lg text-xs font-medium border border-[#E4E4E7] text-[#71717A] hover:bg-[#F4F4F5] cursor-pointer">Cancelar</button>
                    <button type="submit" class="h-9 px-4 rounded-lg text-xs font-medium bg-black text-white hover:bg-zinc-800 cursor-pointer">Salvar Template</button>
                </div>
            </form>

        </div>
    </div>

    <!-- Script Alpine.js -->
    <script>
        function templateComponent() {
            return {
                modalOpen: false,
                editMode: false,
                form: { id: null, title: '', category: '', content: '' },

                openModal(template = null) {
                    if (template) {
                        this.editMode = true;
                        this.form = { ...template };
                    } else {
                        this.editMode = false;
                        this.form = { id: null, title: '', category: '', content: '' };
                    }
                    this.modalOpen = true;
                },

                insertVar(v) {
                    this.form.content += ' ' + v;
                }
            };
        }
    </script>

    <!-- Partial Toast Notifications -->
    @include('partials.toast')

</body>
</html>
