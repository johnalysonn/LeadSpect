<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LeadSpect — Entrar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center min-h-screen p-4 bg-[#F4F4F5] text-[#18181B]">

    <div class="w-full max-w-sm space-y-6">
        
        <!-- Header / Branding -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl border border-[#E4E4E7] bg-black text-white mb-2 shadow-xs">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                </svg>
            </div>
            <h1 class="text-xl font-medium tracking-tight text-[#18181B]">LeadSpect</h1>
            <p class="text-xs text-[#71717A]">Prospecção inteligente de empresas por localização</p>
        </div>

        <!-- Alert messages -->
        @if (session('status'))
            <div class="p-3 rounded-lg text-xs flex items-center gap-2 border border-[#E4E4E7] bg-white text-[#18181B]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-3 rounded-lg text-xs flex items-center gap-2 border border-[#E4E4E7] bg-white text-[#18181B]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Card principal -->
        <div class="p-6 rounded-xl border border-[#E4E4E7] bg-white space-y-4 shadow-xs">
            
            <!-- Botão GitHub OAuth -->
            <a href="{{ route('auth.github') }}" 
               class="w-full h-10 px-4 rounded-lg flex items-center justify-center gap-2 text-xs font-medium bg-black text-white hover:bg-zinc-800 transition-colors cursor-pointer">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                </svg>
                <span>Continuar com GitHub</span>
            </a>

            <!-- Divisor -->
            <div class="relative flex items-center justify-center my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-[#E4E4E7]"></div>
                </div>
                <span class="relative px-3 text-[10px] uppercase tracking-wider bg-white text-[#A1A1AA]">ou entrar com e-mail</span>
            </div>

            <!-- Formulário Login Tradicional -->
            <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Campo E-mail -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-medium text-[#18181B]">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="seu@email.com"
                           class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                    @error('email')
                        <p class="text-xs text-[#18181B] font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campo Senha -->
                <div class="space-y-1">
                    <label for="password" class="block text-xs font-medium text-[#18181B]">Senha</label>
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••"
                           class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                    @error('password')
                        <p class="text-xs text-[#18181B] font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lembrar-me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-[#71717A]">
                        <input type="checkbox" name="remember" value="1"
                               class="w-4 h-4 rounded border-[#E4E4E7] accent-black">
                        <span>Lembrar-me</span>
                    </label>
                </div>

                <!-- Botão Entrar -->
                <button type="submit" 
                        class="w-full h-9 px-4 rounded-lg flex items-center justify-center text-xs font-medium bg-black text-white hover:bg-zinc-800 transition-colors cursor-pointer">
                    Entrar
                </button>
            </form>

            @if (app()->environment('local'))
                <div class="relative flex items-center justify-center my-3">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-[#E4E4E7]"></div>
                    </div>
                    <span class="relative px-2 text-[10px] uppercase tracking-wider bg-white text-[#A1A1AA]">Desenvolvimento</span>
                </div>

                <form action="{{ route('auth.mock') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full h-9 px-3 rounded-lg flex items-center justify-center gap-2 text-xs font-medium border border-[#E4E4E7] bg-[#F4F4F5] hover:bg-white text-[#18181B] transition-colors cursor-pointer">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Entrar com usuário de teste (mock)</span>
                    </button>
                </form>
            @endif

        </div>

        <!-- Link para Cadastro -->
        <p class="text-center text-xs text-[#71717A]">
            Ainda não tem uma conta? 
            <a href="{{ route('register') }}" class="underline font-medium text-[#18181B]">Criar conta</a>
        </p>

        <!-- Footer -->
        <p class="text-center text-[11px] text-[#A1A1AA]">
            LeadSpect &copy; {{ date('Y') }} &bull; Todos os direitos reservados
        </p>

    </div>

</body>
</html>
