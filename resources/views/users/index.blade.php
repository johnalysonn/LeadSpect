<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LeadSpect — Gerenciamento de Usuários</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen flex flex-col bg-[#F4F4F5] text-[#18181B]" x-data="userManagementComponent()">

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
                <a href="{{ route('templates.index') }}" class="px-3 py-1.5 rounded-lg text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] transition-colors">Templates</a>
                <a href="{{ route('users.index') }}" class="px-3 py-1.5 rounded-lg font-medium text-white bg-black">Usuários</a>
            </nav>
        </div>

        <div class="flex items-center gap-3">
            <button @click="openModal()" 
                    class="h-8 px-3 rounded-lg text-xs font-medium bg-black text-white hover:bg-zinc-800 transition-colors flex items-center gap-1.5 cursor-pointer">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Adicionar Usuário</span>
            </button>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-5xl mx-auto p-6 space-y-6 flex-1 w-full">
        
        <!-- Header da Página -->
        <div class="border-b border-[#E4E4E7] pb-4">
            <h1 class="text-xl font-medium text-[#18181B] tracking-tight">Gerenciamento de Usuários</h1>
            <p class="text-xs text-[#71717A]">Adicione, edite e gerencie as contas de acesso ao LeadSpect</p>
        </div>

        <!-- Mensagens de Status / Erro -->
        @if (session('status'))
            <div class="p-3 rounded-lg text-xs flex items-center gap-2 border border-[#E4E4E7] bg-white text-[#18181B]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 rounded-lg text-xs border border-[#E4E4E7] bg-white text-[#18181B] space-y-1">
                @foreach ($errors->all() as $error)
                    <p>&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Tabela de Usuários -->
        <div class="border border-[#E4E4E7] bg-white rounded-xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#E4E4E7] bg-[#F4F4F5] text-[#71717A] font-medium">
                            <th class="py-3 px-4">Usuário</th>
                            <th class="py-3 px-4">E-mail</th>
                            <th class="py-3 px-4">Provedor</th>
                            <th class="py-3 px-4">Data de Cadastro</th>
                            <th class="py-3 px-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E4E4E7]">
                        @foreach ($users as $user)
                            <tr class="hover:bg-[#F4F4F5] transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        @if ($user->avatar)
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-7 h-7 rounded-full border border-[#E4E4E7] object-cover">
                                        @else
                                            <div class="w-7 h-7 rounded-full border border-[#E4E4E7] bg-[#F4F4F5] flex items-center justify-center text-xs font-medium text-[#18181B]">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="font-medium text-[#18181B]">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-[#71717A]">{{ $user->email }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full border border-[#E4E4E7] bg-[#F4F4F5] text-[#18181B] text-[11px] font-medium">
                                        {{ ucfirst($user->auth_provider ?? 'email') }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-[#71717A] tabular-nums">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openModal({{ json_encode($user) }})" class="text-xs text-[#71717A] hover:text-black font-medium underline cursor-pointer">Editar</button>
                                        
                                        @if (auth()->id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este usuário?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-[#71717A] hover:text-black font-medium underline cursor-pointer">Excluir</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="p-4 border-t border-[#E4E4E7]">
                {{ $users->links() }}
            </div>
        </div>

    </main>

    <!-- Modal Adicionar / Editar Usuário -->
    <div x-cloak x-show="modalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs">
        <div @click.away="modalOpen = false" class="w-full max-w-md p-6 rounded-xl border border-[#E4E4E7] bg-white space-y-4 shadow-lg">
            
            <div class="flex items-center justify-between border-b border-[#E4E4E7] pb-3">
                <h3 class="text-sm font-medium text-[#18181B]" x-text="editMode ? 'Editar Usuário' : 'Adicionar Novo Usuário'"></h3>
                <button @click="modalOpen = false" class="text-[#71717A] hover:text-black text-lg cursor-pointer">&times;</button>
            </div>

            <form :action="editMode ? '/users/' + form.id : '/users'" method="POST" class="space-y-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-[#18181B]">Nome Completo</label>
                    <input type="text" name="name" x-model="form.name" required placeholder="Digite o nome..."
                           class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-[#18181B]">E-mail</label>
                    <input type="email" name="email" x-model="form.email" required placeholder="email@exemplo.com"
                           class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-[#18181B]">Senha <span x-show="editMode" class="text-[10px] text-[#71717A]">(opcional se não quiser alterar)</span></label>
                    <input type="password" name="password" :required="!editMode" placeholder="••••••••"
                           class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalOpen = false" class="h-9 px-4 rounded-lg text-xs font-medium border border-[#E4E4E7] text-[#71717A] hover:bg-[#F4F4F5] cursor-pointer">Cancelar</button>
                    <button type="submit" class="h-9 px-4 rounded-lg text-xs font-medium bg-black text-white hover:bg-zinc-800 cursor-pointer">Salvar Usuário</button>
                </div>
            </form>

        </div>
    </div>

    <!-- Script Alpine.js -->
    <script>
        function userManagementComponent() {
            return {
                modalOpen: false,
                editMode: false,
                form: { id: null, name: '', email: '' },

                openModal(user = null) {
                    if (user) {
                        this.editMode = true;
                        this.form = { id: user.id, name: user.name, email: user.email };
                    } else {
                        this.editMode = false;
                        this.form = { id: null, name: '', email: '' };
                    }
                    this.modalOpen = true;
                }
            };
        }
    </script>

    <!-- Partial Toast Notifications -->
    @include('partials.toast')

</body>
</html>
