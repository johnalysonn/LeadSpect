<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LeadSpect — Criar conta</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center min-h-screen p-4 bg-[#F4F4F5] text-[#18181B]">

    <div class="w-full max-w-sm space-y-6">
        
        <!-- Header / Branding -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl border border-[#E4E4E7] bg-black text-white mb-2 shadow-xs">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="19" y1="8" x2="19" y2="14"></line>
                    <line x1="16" y1="11" x2="22" y2="11"></line>
                </svg>
            </div>
            <h1 class="text-xl font-medium tracking-tight text-[#18181B]">Criar conta no LeadSpect</h1>
            <p class="text-xs text-[#71717A]">Preencha os dados abaixo para começar</p>
        </div>

        <!-- Card principal -->
        <div class="p-6 rounded-xl border border-[#E4E4E7] bg-white space-y-4 shadow-xs">
            
            <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Campo Nome -->
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-medium text-[#18181B]">Nome completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Seu nome completo"
                           class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                    @error('name')
                        <p class="text-xs text-[#18181B] font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campo E-mail -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-medium text-[#18181B]">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
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
                           placeholder="Mínimo 8 caracteres"
                           class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                    @error('password')
                        <p class="text-xs text-[#18181B] font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmação de Senha -->
                <div class="space-y-1">
                    <label for="password_confirmation" class="block text-xs font-medium text-[#18181B]">Confirmar senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           placeholder="Repita a senha"
                           class="w-full h-9 px-3 rounded-lg border border-[#E4E4E7] bg-[#F4F4F5] text-xs text-[#18181B] focus:outline-none focus:border-black">
                </div>

                <!-- Botão Criar Conta -->
                <button type="submit" 
                        class="w-full h-9 px-4 rounded-lg flex items-center justify-center text-xs font-medium bg-black text-white hover:bg-zinc-800 transition-colors cursor-pointer">
                    Criar conta
                </button>
            </form>

        </div>

        <!-- Link para Login -->
        <p class="text-center text-xs text-[#71717A]">
            Já possui uma conta? 
            <a href="{{ route('login') }}" class="underline font-medium text-[#18181B]">Fazer login</a>
        </p>

        <!-- Footer -->
        <p class="text-center text-[11px] text-[#A1A1AA]">
            LeadSpect &copy; {{ date('Y') }} &bull; Todos os direitos reservados
        </p>

    </div>

</body>
</html>
