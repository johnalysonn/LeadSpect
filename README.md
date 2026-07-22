<h1 align="center">
  <br>
  🎯 <b>LeadSpect</b>
  <br>
</h1>

<p align="center">
  <b>Plataforma Inteligente de Prospecção Geográfica, Enriquecimento e Gestão de Leads B2B</b>
</p>

<p align="center">
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13"></a>
  <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS v4"></a>
  <a href="https://vitejs.dev"><img src="https://img.shields.io/badge/Vite-6.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite"></a>
  <a href="https://pestphp.com"><img src="https://img.shields.io/badge/Pest_PHP-v4.x-00B4D8?style=for-the-badge&logo=pest&logoColor=white" alt="Pest PHP"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License MIT"></a>
</p>

---

## 📌 Sobre o Projeto

O **LeadSpect** é uma solução completa desenvolvida para automatizar a descoberta, enriquecimento e qualificação de clientes B2B com base em geolocalização. 

Permite mapear estabelecimentos e empresas em tempo real utilizando integração com APIs de mapas e geocodificação, realizar scraping automático no website dos leads para capturar contatos (WhatsApp, E-mail, Instagram, Facebook, LinkedIn) e gerenciar a jornada comercial através de um funil de vendas (Kanban).

---

## ✨ Funcionalidades Principais

- 🗺️ **Prospecção Geográfica de Leads**: Pesquisa por nichos e cidades utilizando **TomTom Search API** com mecanismo de fallback resiliente via **OpenStreetMap (Overpass API / Nominatim)**.
- 🔍 **Enriquecimento Inteligente de Dados**: Scraping automático em background que vasculha o site do lead para extrair número de WhatsApp, telefone, e-mail e perfis em redes sociais.
- 📊 **Pipeline Kanban de Leads**: Organização da jornada comercial por status (*Novo, Contatado, Em Negociação, Fechado, Perdido*).
- 💬 **Integração Rápida com WhatsApp**: Envio direto de mensagens via WhatsApp Web utilizando templates de mensagem personalizados com variáveis dinâmicas (`{nome}`, `{empresa}`, etc.).
- 🏷️ **Etiquetas e Anotações**: Organização por tags personalizadas e registro de histórico de interações/anotações por lead.
- 🔑 **Autenticação Flexível (Social Login)**: Login tradicional por e-mail e senha + login rápido via **GitHub** e **Google OAuth** (Laravel Socialite).
- 📥 **Exportação de Dados**: Exportação rápida dos leads prospectados em formatos CSV, JSON e Excel.

---

## 🛠️ Tecnologias Utilizadas

### **Backend & Framework**
- **PHP 8.3+**
- **Laravel 13**
- **Laravel Socialite** (OAuth para Google e GitHub)
- **SQLite** (Padrão para ambiente de desenvolvimento)

### **Frontend & Estilização**
- **Blade Templates**
- **Tailwind CSS v4** (Com utilitários modernos e design fluido)
- **Vite** (Bundler e Live Reload)
- **Concurrently** (Execução paralela do PHP Serve + Queue Worker + Vite)

### **Serviços de Geociência & Prospecção**
- **TomTom Search & Places API**
- **OpenStreetMap / Overpass API / Nominatim**

### **Testes & Qualidade de Código**
- **Pest PHP 4** (Testes de unidade, funcionalidade e arquitetura)
- **Laravel Pint** (Linter e formatador de código PHP)

---

## 🔑 Configuração das Chaves de API (`.env`)

Para o funcionamento completo da aplicação (como prospecção via TomTom e Social Login), configure as seguintes variáveis no seu arquivo `.env`:

### 1. **TomTom API (Geolocalização & Prospecção de Locais)**
- **Como obter**: Cadastre-se gratuitamente no [Portal do Desenvolvedor TomTom](https://developer.tomtom.com/) e crie uma **API Key**.
- **Variáveis**:
  ```env
  TOMTOM_API_KEY=sua_chave_tomtom_aqui
  ```
  *(Nota: Se a chave TomTom não for informada ou atingir o limite, o sistema aciona automaticamente o provedor de fallback gratuito OpenStreetMap/Overpass API).*

---

### 2. **Google OAuth (Login com Google)**
- **Como obter**: Acesse o [Google Cloud Console](https://console.cloud.google.com/), crie um projeto, configure a *Tela de Consentimento OAuth* e crie credenciais do tipo **ID do cliente OAuth 2.0 (Web Application)**.
- **URI de Redirecionamento autorizada**: `http://localhost:8000/auth/google/callback` (ou a URL do seu ambiente).
- **Variáveis**:
  ```env
  GOOGLE_CLIENT_ID=seu_client_id_google.apps.googleusercontent.com
  GOOGLE_CLIENT_SECRET=seu_client_secret_google
  GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
  ```

---

### 3. **GitHub OAuth (Login com GitHub)**
- **Como obter**: Acesse [GitHub Developer Settings -> OAuth Apps](https://github.com/settings/developers), crie um novo OAuth App.
- **Authorization callback URL**: `http://localhost:8000/auth/github/callback`
- **Variáveis**:
  ```env
  GITHUB_CLIENT_ID=seu_github_client_id
  GITHUB_CLIENT_SECRET=seu_github_client_secret
  GITHUB_REDIRECT_URI="${APP_URL}/auth/github/callback"
  ```

---

## 📦 Passo a Passo de Instalação e Execução

### **Prerequisites**
- PHP `>= 8.3` (com extensões `sqlite3`, `curl`, `mbstring`, `pdo_sqlite` ativas)
- Composer `>= 2.x`
- Node.js `>= 18.x` e NPM

---

### 1️⃣ **Clonar o Repositório**
```bash
git clone https://github.com/johnalysonn/LeadSpect.git
cd LeadSpect
```

### 2️⃣ **Instalar Dependências do PHP**
```bash
composer install
```

### 3️⃣ **Instalar Dependências do Frontend**
```bash
npm install
```

### 4️⃣ **Configurar o Arquivo de Ambiente (`.env`)**
Copie o arquivo de exemplo `.env.example` para `.env`:
```bash
cp .env.example .env
```
Abra o `.env` e preencha com as suas API Keys (TomTom, Google, GitHub, etc.) conforme explicado na seção anterior.

### 5️⃣ **Gerar a Chave de Criptografia da Aplicação**
```bash
php artisan key:generate
```

### 6️⃣ **Criar o Banco de Dados e Rodar as Migrações**
Caso utilize SQLite, crie o arquivo do banco (se não for criado automaticamente):
```bash
touch database/database.sqlite
php artisan migrate --seed
```

### 7️⃣ **Iniciar o Ambiente de Desenvolvimento**
O projeto conta com um script rápido via Composer que executa simultaneamente o **Servidor Web PHP**, o **Queue Worker** e o **Vite**:
```bash
composer dev
```
Após executar o comando acima, acesse no seu navegador:
👉 **[http://localhost:8000](http://localhost:8000)**

---

## 🧪 Testes e Qualidade de Código

### **Executar Suíte de Testes (Pest PHP)**
```bash
composer test
# ou
php artisan test
```

### **Verificar & Formatar Estilo de Código (Laravel Pint)**
```bash
./vendor/bin/pint
```

---

## 📂 Estrutura Principal do Projeto

```text
LeadSpect/
├── app/
│   ├── Http/Controllers/       # Controladores da aplicação (Auth, Leads, Busca, Templates)
│   ├── Models/                 # Modelos Eloquent (Lead, Tag, SearchHistory, User, etc.)
│   └── Services/
│       ├── Enrichment/         # Serviço de raspagem web e enriquecimento de dados de contato
│       └── Location/           # Provedores de busca (TomTom API, Overpass/OSM Fallback)
├── database/
│   ├── migrations/             # Estrutura das tabelas do banco de dados
│   └── seeders/                # População inicial de dados
├── resources/
│   ├── css/                    # Estilos Tailwind CSS
│   └── views/                  # Templates Blade da interface do usuário
├── routes/
│   └── web.php                 # Definição das rotas da aplicação
├── tests/                      # Suíte de testes com Pest PHP
├── .env.example                # Exemplo das variáveis de ambiente
└── composer.json               # Configurações do PHP e scripts de dev/teste
```

---

## 📄 Licença

Este projeto está sob a licença [MIT](LICENSE). Desenvolvido para facilitar e acelerar a prospecção B2B de forma eficiente.
