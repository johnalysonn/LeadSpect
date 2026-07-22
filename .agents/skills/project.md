# LeadSpect — Sistema de Prospecção Inteligente de Leads

Quero que você desenvolva um sistema SaaS chamado **LeadSpect**, focado em prospecção de empresas por localização.

Respeite a skill style.md para estilizar da forma pedida.

## Stack

Desenvolva como um desenvolvedor sênior utilizando:

* Laravel 13
* PHP 8.5+
* PostgreSQL
* TailwindCSS
* Alpine.js
* Leaflet.js para mapas
* Vite
* Autenticação Google OAuth
* Arquitetura limpa e escalável

## Arquitetura

Quero um projeto extremamente organizado.

Utilize:

* Controllers apenas para receber requests e retornar responses
* Actions para toda regra de negócio
* Services quando necessário
* Repositories para consultas SQL complexas, filtros e buscas
* Policies
* Form Requests
* DTOs quando fizer sentido
* Enums para Status
* Jobs para tarefas demoradas
* Events e Listeners para ações automáticas
* Migrations bem organizadas
* Seeds
* Factories

Evite lógica dentro dos controllers.

O código deve seguir SOLID e boas práticas do Laravel.

## Compatibilidade com Hospedagem Compartilhada (Hostinger)
Compatibilidade com Hospedagem Compartilhada (Hostinger)

O sistema será hospedado inicialmente em uma Hospedagem Compartilhada da Hostinger.

Portanto, todas as decisões técnicas devem ser compatíveis com esse ambiente, evitando dependências que exijam processos permanentes em segundo plano.

Requisitos:
* Compatível com hospedagem compartilhada.
* Não utilizar Redis.
* Não utilizar Horizon.
* Não depender de Queue Workers (php artisan queue:work) em execução contínua.
* Não utilizar Octane.
* Não utilizar WebSockets.
* Não depender de Supervisor.

Caso seja necessário executar tarefas demoradas, utilizar uma destas estratégias:

* processamento síncrono quando o tempo for aceitável;
* processamento incremental utilizando requisições AJAX;
* tarefas agendadas através do Laravel Scheduler utilizando Cron da Hostinger;
sempre que possível, atualizar informações sob demanda ("lazy loading"), evitando processamento desnecessário.

O sistema deve permanecer preparado para uma futura migração para VPS, onde filas e Redis poderão ser ativados sem necessidade de grandes alterações na arquitetura.

## Fonte dos dados (API)
Para a busca de empresas, utilizar uma solução totalmente gratuita baseada em dados Open Source.

### APIs

Utilizar:
* OpenStreetMap como base de dados geográfica;
* Overpass API para pesquisar empresas por localização;
* Nominatim para converter endereços em coordenadas (Geocoding e Reverse Geocoding);
* Leaflet.js para renderização do mapa.

Não utilizar Google Places API na implementação inicial.

A arquitetura deve permitir que, futuramente, seja possível trocar facilmente o provedor por Google Places, Geoapify, Foursquare ou TomTom.

Para isso, criar uma abstração utilizando o padrão Strategy.

Exemplo:

LocationProviderInterface

↓

OverpassLocationProvider

↓

GeoapifyLocationProvider

↓

GooglePlacesLocationProvider

Toda a aplicação deve depender apenas da interface.

Nenhuma Action, Controller ou Service deve conhecer a implementação concreta.

Fluxo da Pesquisa

O fluxo esperado é:

Usuário informa endereço ou utiliza sua localização atual

↓

Nominatim converte para latitude e longitude

↓

Overpass API pesquisa empresas dentro do raio informado

↓

Normalização dos dados em DTOs

↓

Persistência em cache local

↓

Retorno para a interface
Enriquecimento dos Dados

Como o OpenStreetMap nem sempre possui telefone, website ou redes sociais, implementar um mecanismo de enriquecimento de dados.

Caso exista um website cadastrado para a empresa:

acessar o site utilizando um serviço interno;
identificar automaticamente:
telefone;
e-mail;
WhatsApp;
Instagram;
Facebook;
LinkedIn;
demais redes sociais encontradas.

Como o sistema será hospedado na Hostinger Compartilhada, esse enriquecimento não deve depender de filas.

Implementar uma abordagem de carregamento sob demanda ("lazy enrichment"):

ao abrir os detalhes da empresa, caso os dados ainda não existam no banco, realizar a análise do website;
armazenar permanentemente o resultado para reutilização futura.
Cache

Implementar cache em banco de dados para evitar chamadas repetidas às APIs.

Sugestão:

Tabela:

company_search_cache

Campos:

hash da pesquisa
latitude
longitude
raio
categoria
resposta da API
data da consulta

Toda pesquisa deve verificar primeiro o cache.

Somente consultar a Overpass API quando o cache estiver expirado.

Escalabilidade

Mesmo utilizando apenas APIs gratuitas inicialmente, toda integração deve ser desacoplada.

Nenhuma regra de negócio poderá depender diretamente da Overpass API.

A camada de integração deve permitir substituir a fonte de dados futuramente sem alterar Controllers, Actions, Services ou a interface do sistema.

Eu também alteraria um ponto do seu prompt

Hoje você colocou:

Jobs para tarefas demoradas
Events e Listeners para ações automáticas

Eu mudaria para:

• Controllers apenas para receber Requests e retornar Responses
• Actions para toda regra de negócio
• Services quando necessário
• Repositories para consultas SQL complexas
• DTOs
• Form Requests
• Policies
• Enums
• Events e Listeners quando compatíveis com hospedagem compartilhada
• Laravel Scheduler para tarefas agendadas
• Migrations
• Seeds
• Factories

# Autenticação

Antes de qualquer funcionalidade, o sistema deve possuir um sistema completo de autenticação.

O sistema deve permitir duas formas de acesso:

- Login tradicional utilizando e-mail e senha.
- Login via GitHub OAuth.

A autenticação deve ser moderna, segura e preparada para crescimento futuro.

---

## Tela de Login

Criar uma tela moderna de login contendo:

- Campo de e-mail.
- Campo de senha.
- Botão de entrar.
- Opção "Lembrar-me".
- Botão "Continuar com GitHub".
- Link para criação de conta.

Características:

- Interface moderna.
- Responsiva.
- Minimalista.
- Seguir o padrão visual do sistema.

---

## Cadastro de Usuários

Usuários que não desejarem utilizar GitHub devem conseguir criar uma conta normalmente.

Criar tela de cadastro contendo:

- Nome.
- E-mail.
- Senha.
- Confirmação de senha.

Regras:

- E-mail deve ser único.
- Senhas devem ser armazenadas utilizando Hash do Laravel.
- Validar todos os dados recebidos.
- Após cadastro, o usuário deve poder realizar login normalmente.

---

## Login via GitHub OAuth

Implementar autenticação utilizando Laravel Socialite.

Fluxo:

1. Usuário seleciona "Continuar com GitHub".
2. Sistema redireciona para autorização do GitHub.
3. Após autorização, usuário retorna ao sistema.
4. Sistema verifica se existe uma conta vinculada.

Regras:

- Caso exista uma conta vinculada ao GitHub, realizar login.
- Caso exista um usuário com o mesmo e-mail, vincular a conta GitHub existente.
- Caso não exista usuário, criar uma nova conta automaticamente.
- Nunca criar usuários duplicados.

Armazenar:

- Nome.
- E-mail.
- Avatar.
- ID do usuário no GitHub.
- Provedor de autenticação.

A arquitetura deve permitir adicionar outros provedores OAuth futuramente.

---

## Gerenciamento de Usuários

Implementar CRUD completo de usuários.

Permitir:

- Criar usuários.
- Listar usuários.
- Editar usuários.
- Excluir usuários.

Exibir informações:

- Nome.
- E-mail.
- Data de cadastro.
- Método de autenticação.
- Conta GitHub vinculada.

---

## Segurança

Implementar proteção contra ataques:

- Rate Limit nas rotas de autenticação.
- Bloqueio temporário após múltiplas tentativas inválidas.
- Sessões seguras.
- Proteção CSRF.
- Sanitização e validação de dados recebidos.
- Proteção contra SQL Injection utilizando recursos nativos do Laravel.
- Controle de acesso utilizando middleware.

---

## Isolamento de Dados

Todo dado criado no sistema deve estar vinculado ao usuário autenticado.

Cada usuário deve possuir seus próprios dados isolados:

- Leads.
- Templates de mensagens.
- Pipeline de vendas.
- Configurações pessoais.
- Histórico de ações.

Nenhum usuário pode visualizar ou alterar dados pertencentes a outro usuário.

Todas as consultas devem considerar o usuário autenticado como filtro obrigatório.

---

## Boas práticas

A implementação deve seguir:

- Laravel 13 Authentication.
- Laravel Socialite.
- Form Requests para validação.
- Policies para autorização.
- Services/Actions para regras de negócio.
- Código limpo.
- SOLID.
- PSR-12.

Não colocar regras de autenticação diretamente nas Controllers.

---

# Objetivo

O sistema serve para encontrar empresas próximas de uma localização e gerenciar toda a prospecção até o fechamento.

---

# Pesquisa de Leads

Tela principal contendo:

## Campo de pesquisa

O usuário poderá pesquisar por:

* localização atual (GPS)
* endereço
* cidade
* bairro
* CEP
* coordenadas

Também poderá definir um raio de busca:

* 500 metros
* 1 km
* 2 km
* 5 km
* 10 km
* personalizado

Botão:

**Buscar Empresas**

---

# Resultado da pesquisa

Ao pesquisar, exibir duas áreas simultaneamente:

## Mapa

Utilizar Leaflet.js.

Mostrar:

* marcador da localização pesquisada
* marcador para cada empresa encontrada
* clusterização quando houver muitos pontos
* linhas opcionais ligando o centro aos estabelecimentos
* zoom automático

Ao clicar em um marcador:

Abrir popup contendo:

* nome
* categoria
* telefone
* WhatsApp
* website
* avaliação
* endereço
* botão "Ver detalhes"
* botão "Adicionar Lead"

---

## Lista lateral

Ao lado do mapa deve existir uma lista de empresas.

Cada card deve possuir:

* Nome
* Categoria
* Foto (quando existir)
* Avaliação
* Quantidade de avaliações
* Endereço
* Distância
* Telefone
* WhatsApp
* Site
* Horário de funcionamento
* Status (Aberto / Fechado)

Também mostrar indicadores visuais como:

✔ Possui site

❌ Não possui site

✔ Possui WhatsApp

✔ Possui Instagram

✔ Possui Facebook

✔ Possui telefone

---

# Resumo do Lead

Cada empresa deve mostrar um pequeno resumo contendo:

* Tipo de negócio
* Segmento
* Cidade
* Bairro
* Quantidade de avaliações
* Nota média
* Distância
* Possui site?
* Possui WhatsApp?
* Possui Instagram?
* Possui Facebook?

---

# Filtros

Criar um painel lateral com diversos filtros.

Exemplos:

Localização

Raio

Categoria

Cidade

Bairro

Nota mínima

Quantidade mínima de avaliações

Empresa aberta agora

Empresa sem site

Empresa com site

Empresa sem WhatsApp

Empresa com WhatsApp

Empresa sem Instagram

Empresa com Instagram

Empresa sem Facebook

Empresa com Facebook

Empresa sem telefone

Empresa com telefone

Empresa com poucas avaliações

Empresa recém cadastrada (quando disponível)

Apenas empresas verificadas

Apenas empresas ainda não adicionadas aos meus leads

Ocultar empresas já contatadas

Ocultar empresas recusadas

Ocultar empresas aprovadas

Mostrar somente favoritos

---

# Ações do Lead

Cada empresa deve possuir botões rápidos.

## WhatsApp

Ao clicar no botão WhatsApp:

Abrir opções:

### Enviar mensagem padrão

O sistema utilizará um template salvo.

### Enviar mensagem personalizada

Abrirá um modal permitindo editar a mensagem antes de abrir o WhatsApp.

Após clicar em "Abrir WhatsApp":

Automaticamente criar um Lead no banco com status:

**Contato iniciado**

Não quero utilizar "Pendente" para quem já recebeu contato.

Também registrar:

* data
* hora
* usuário
* mensagem utilizada
* origem da pesquisa

---

## Adicionar Lead

Outro botão:

**Adicionar Lead**

Este botão adiciona a empresa sem abrir o WhatsApp.

Status inicial:

**A prospectar**

Esse status representa empresas salvas para contato futuro.

---

# Pipeline (Kanban)

Criar uma tela semelhante ao Trello.

Cada card representa um lead.

Permitir Drag and Drop.

Status sugeridos:

🟦 A prospectar

🟨 Contato iniciado

🟧 Aguardando resposta

🟪 Em negociação

🟩 Proposta enviada

🟢 Cliente fechado

🔴 Não interessado

⚫ Sem retorno

⚪ Arquivado

Cada movimentação deve registrar histórico.

---

# Histórico

Cada Lead deve possuir Timeline.

Exemplo:

Lead criado

Mensagem enviada

Mudança de status

Observação adicionada

Proposta enviada

Cliente aprovado

Lead recusado

---

# Observações

Cada lead poderá possuir:

Notas internas

Comentários

Próximo contato

Data de follow-up

Responsável

Tags

---

# Templates de Mensagens

Criar uma área para gerenciamento de templates.

Exemplos:

Landing Page

Sistema

Site Institucional

Automação

Marketing

Cada usuário possui seus próprios templates.

Permitir:

Variáveis

Exemplo:

```
Olá {{empresa}}, tudo bem?

Vi que vocês ficam em {{cidade}} e preparei uma ideia que pode ajudar seu negócio...
```

---

# Dashboard

Criar dashboard moderno mostrando:

Total de empresas encontradas

Leads cadastrados

Contato iniciado

Aguardando resposta

Em negociação

Clientes fechados

Negados

Taxa de conversão

Gráfico de conversão

Gráfico por categoria

Gráfico por cidade

Últimos leads adicionados

---

# Banco de Dados

Modelos sugeridos:

Users

Leads

LeadStatusHistory

LeadNotes

LeadTags

LeadTemplates

SearchHistory

Companies (cache opcional)

MessageTemplates

UserSettings

---

# Funcionalidades Extras

Adicionar também:

* Favoritar empresas
* Histórico de pesquisas realizadas
* Pesquisas recentes
* Duplicar pesquisa
* Exportar leads para Excel e CSV
* Exportar contatos
* Buscar novamente empresas de uma pesquisa anterior
* Sistema de tags coloridas
* Busca rápida de leads
* Ordenação por distância, avaliação e nome
* Paginação infinita (Infinite Scroll)
* Cache inteligente das pesquisas para reduzir chamadas externas
* Logs de auditoria para ações importantes

---

# Interface

Desejo uma interface extremamente moderna.

Inspire-se em:

* Linear
* Notion
* Stripe Dashboard
* Vercel
* Raycast
* Trello

Características:

* Clean
* Minimalista
* Dark Mode nativo
* Componentes reutilizáveis
* Ícones Lucide
* Animações suaves
* Skeleton Loading
* Empty States elegantes
* Toasts
* Modais modernos
* Totalmente responsivo

O objetivo é que o LeadSpect pareça um produto SaaS premium, rápido, intuitivo e escalável, preparado para evoluir com novas funcionalidades e manter uma arquitetura limpa, desacoplada e de fácil manutenção.
