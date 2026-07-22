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

        </div>

        <!-- Footer -->
        <p class="text-center text-[11px] text-[#A1A1AA]">
            LeadSpect &copy; {{ date('Y') }} &bull; Todos os direitos reservados
        </p>

    </div>

</body>
</html>
