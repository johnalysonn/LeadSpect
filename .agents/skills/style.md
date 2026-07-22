# Guia de Estilo Visual & Design System (Estética Minimalista Monocromática)

> **INSTRUÇÃO**: Aplique as regras de estilo abaixo em TODA e QUALQUER página desenvolvida neste sistema. O visual deve seguir rigorosamente a estética de alta precisão, minimalista e monocromática da referência.

---

## 1. PALETA DE CORES & SUPERFÍCIES

*   **100% Monocromático**: Preto, branco e escala de cinzas. NENHUMA cor vibrante (azul, verde, vermelho) deve ser usada no chrome da UI ou nos gráficos.
*   **Fundo da Aplicação (Background)**: Cinza ultra-claro / Off-white (`#F3F4F6` ou `#F4F4F5`).
*   **Superfícies & Cards**: Branco puro (`#FFFFFF`).
*   **Bordas**: Linhas finas de `1px` em cinza claro (`#E4E4E7`).
*   **Textos**:
    *   *Primário*: Preto escuro (`#18181B`).
    *   *Secundário*: Cinza médio (`#71717A`).
    *   *Terciário / Muted*: Cinza claro (`#A1A1AA`).
*   **Acentos e Estados Ativos**: Preto absoluto (`#000000`) para elementos selecionados, botões primários e dados de maior relevância.

---

## 2. TIPOGRAFIA & NÚMEROS

*   **Fonte**: Sans-serif limpa e moderna (ex: `Inter`, `SF Pro Display` ou `var(--font-sans)`).
*   **Pesos**: Utilize APENAS dois pesos: **400 (Regular)** e **500 (Medium)**. Nunca use negritos pesados (600/700).
*   **Caixa do Texto**: *Sentence case* em quase tudo. Use *ALL CAPS* apenas para rótulos pequenos de métricas, com `letter-spacing` sutil.
*   **Números Tabulares (Obrigatório)**: Todos os números, valores financeiros, porcentagens e dados de tabelas/gráficos DEVEM usar `font-variant-numeric: tabular-nums` para alinhamento vertical perfeito.

---

## 3. ARREDONDAMENTO (BORDER RADIUS) & ELEVAÇÃO

*   **Escala de Radius**:
    *   *Contêineres / Cards grandes*: `12px`
    *   *Inputs / Cards menores / Toggles*: `8px` ou `10px`
    *   *Badges / Tags*: `6px` ou `full` (pill format)
*   **Sombra (Shadows)**: Evite sombras pesadas. Use apenas bordas finas de `1px` (`#E4E4E7`). Se precisar de elevação, use uma sombra quase imperceptível: `0 1px 2px rgba(0,0,0,0.05)`.

---

## 4. COMPONENTES VISUAIS

### A. Navegação (Sidebar & Menus)
*   **Item Inativo**: Ícone em linha (stroke fino) + texto em cinza médio (`#71717A`).
*   **Item Ativo**: Fundo branco puro destacado, borda/sombra leve, texto e ícone em preto (`#18181B`) com peso `500`.

### B. Cards & Métricas (KPIs)
*   Card branco com borda de `1px`.
*   Rótulo em caixa alta sutil (cinza).
*   Valor grande em destaque com `font-weight: 500`.
*   Micro-gráficos (Sparklines): Barras verticais ultra-finas (estilo código de barras/equalizador) em cinza claro, com a barra atual/máxima em destaque preto.

### C. Gráficos & Visualização de Dados
*   **Estilo "Pixel / Waffle Grid"**: Barras de gráficos devem preferencialmente ser formadas por matrizes de pequenos blocos empilhados (estilo pixel), e não blocos sólidos genéricos.
*   **Linhas de Grade**: Pontilhadas, extremamente finas e discretas (`#E4E4E7`).
*   **Tooltips**: Cards flutuantes brancos com borda fina de `1px` e sombra leve.

### D. Controles (Toggles, Inputs e Pílulas)
*   **Pill Toggles**: Fundo em cinza claro com a opção selecionada envelopada em um card branco interno destacado (efeito "segment control").

---

## 5. RESUMO DE REGRA DE OURO

> **"Preto e branco estruturam, cinza hierarquiza, dados preenchem."**
> Mantenha o layout limpo, espaçado e ultra-profissional, sem gradientes, sem glassmorphism, sem cores saturadas e sem sombras pronunciadas.