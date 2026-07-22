<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LeadSpect — Adicionar Usuário</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen p-6" style="background-color: var(--kimi-color-bg-main); color: var(--kimi-color-text-primary);">

    <div class="max-w-xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="border-b border-[var(--kimi-color-border-default)] pb-4">
            <div class="flex items-center gap-2 text-xs mb-1" style="color: var(--kimi-color-text-tertiary);">
                <a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a>
                <span>&rsaquo;</span>
                <a href="{{ route('users.index') }}" class="hover:text-white">Usuários</a>
                <span>&rsaquo;</span>
                <span style="color: var(--kimi-color-text-secondary);">Novo</span>
            </div>
            <h1 class="text-xl font-medium tracking-tight" style="color: var(--kimi-color-text-primary);">Adicionar Novo Usuário</h1>
            <p class="text-xs" style="color: var(--kimi-color-text-secondary);">Cadastre um usuário diretamente pelo painel administrativo</p>
        </div>

        <!-- Formulário -->
        <div class="p-6 rounded-xl border border-[var(--kimi-color-border-default)] bg-[var(--kimi-color-bg-secondary)] space-y-4">
            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Campo Nome -->
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-medium" style="color: var(--kimi-color-text-primary);">Nome completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Nome do usuário"
                           class="w-full h-10 px-3 rounded-lg border border-[var(--kimi-color-border-default)] bg-transparent text-sm focus:outline-none focus:border-[var(--kimi-color-border-hover)]"
                           style="color: var(--kimi-color-text-primary);">
                    @error('name')
                        <p class="text-xs" style="color: var(--kimi-color-danger);">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campo E-mail -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-medium" style="color: var(--kimi-color-text-primary);">Endereço de e-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="email@dominio.com"
                           class="w-full h-10 px-3 rounded-lg border border-[var(--kimi-color-border-default)] bg-transparent text-sm focus:outline-none focus:border-[var(--kimi-color-border-hover)]"
                           style="color: var(--kimi-color-text-primary);">
                    @error('email')
                        <p class="text-xs" style="color: var(--kimi-color-danger);">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campo Senha -->
                <div class="space-y-1">
                    <label for="password" class="block text-xs font-medium" style="color: var(--kimi-color-text-primary);">Senha inicial</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Mínimo 8 caracteres"
                           class="w-full h-10 px-3 rounded-lg border border-[var(--kimi-color-border-default)] bg-transparent text-sm focus:outline-none focus:border-[var(--kimi-color-border-hover)]"
                           style="color: var(--kimi-color-text-primary);">
                    @error('password')
                        <p class="text-xs" style="color: var(--kimi-color-danger);">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botões -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('users.index') }}" 
                       class="h-9 px-4 rounded-lg flex items-center text-xs font-medium border border-[var(--kimi-color-border-default)] hover:bg-zinc-800 transition-colors"
                       style="color: var(--kimi-color-text-secondary);">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="h-9 px-4 rounded-lg flex items-center text-xs font-medium bg-zinc-100 text-zinc-950 hover:opacity-90 transition-opacity cursor-pointer">
                        Salvar Usuário
                    </button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>
