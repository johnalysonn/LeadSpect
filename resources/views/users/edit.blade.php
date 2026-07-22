<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LeadSpect — Editar Usuário</title>
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
                <span style="color: var(--kimi-color-text-secondary);">Editar #{{ $user->id }}</span>
            </div>
            <h1 class="text-xl font-medium tracking-tight" style="color: var(--kimi-color-text-primary);">Editar Usuário: {{ $user->name }}</h1>
            <p class="text-xs" style="color: var(--kimi-color-text-secondary);">Atualize as informações do perfil de usuário</p>
        </div>

        <!-- Formulário -->
        <div class="p-6 rounded-xl border border-[var(--kimi-color-border-default)] bg-[var(--kimi-color-bg-secondary)] space-y-4">
            <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Campo Nome -->
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-medium" style="color: var(--kimi-color-text-primary);">Nome completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full h-10 px-3 rounded-lg border border-[var(--kimi-color-border-default)] bg-transparent text-sm focus:outline-none focus:border-[var(--kimi-color-border-hover)]"
                           style="color: var(--kimi-color-text-primary);">
                    @error('name')
                        <p class="text-xs" style="color: var(--kimi-color-danger);">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campo E-mail -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-medium" style="color: var(--kimi-color-text-primary);">Endereço de e-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full h-10 px-3 rounded-lg border border-[var(--kimi-color-border-default)] bg-transparent text-sm focus:outline-none focus:border-[var(--kimi-color-border-hover)]"
                           style="color: var(--kimi-color-text-primary);">
                    @error('email')
                        <p class="text-xs" style="color: var(--kimi-color-danger);">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nova Senha (opcional) -->
                <div class="space-y-1">
                    <label for="password" class="block text-xs font-medium" style="color: var(--kimi-color-text-primary);">Nova Senha (deixe em branco para manter a atual)</label>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••"
                           class="w-full h-10 px-3 rounded-lg border border-[var(--kimi-color-border-default)] bg-transparent text-sm focus:outline-none focus:border-[var(--kimi-color-border-hover)]"
                           style="color: var(--kimi-color-text-primary);">
                    @error('password')
                        <p class="text-xs" style="color: var(--kimi-color-danger);">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informações de Método de Autenticação -->
                <div class="p-3 rounded-lg border border-[var(--kimi-color-border-default)] bg-zinc-900/50 space-y-1">
                    <span class="text-[11px] font-medium uppercase tracking-wider" style="color: var(--kimi-color-text-tertiary);">Método de Autenticação Atual</span>
                    <div class="flex items-center gap-2 text-xs" style="color: var(--kimi-color-text-secondary);">
                        @if ($user->github_id || $user->auth_provider === 'github')
                            <span class="font-medium text-zinc-200">GitHub OAuth</span>
                            <span>&bull; ID: {{ $user->github_id }}</span>
                        @else
                            <span class="font-medium text-zinc-200">E-mail / Senha</span>
                        @endif
                    </div>
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
                        Atualizar Usuário
                    </button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>
