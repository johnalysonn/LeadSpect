/**
 * LeadSpect Map Markers & Modern Popup Card Manager
 * Centralized Category -> Icon & Palette Mapping System
 */

const CATEGORY_MAP = [
    {
        key: 'restaurant',
        keywords: ['restaurante', 'restaurant', 'alimentacao', 'comida', 'gastronomia', 'pizzeria', 'pizzaria', 'churrascaria', 'lanchonete', 'burger', 'hamburgueria', 'fast_food', 'food', 'bistro', 'bistrô', 'grill', 'snack', 'pastelaria', 'espetinho', 'sushi', 'temakeria'],
        label: 'Restaurante',
        bg: '#EF4444',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2"/><path d="M15 2v16"/><path d="M21 2v16"/><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/></svg>`
    },
    {
        key: 'bar',
        keywords: ['bar', 'pub', 'choperia', 'boteco', 'cervejaria', 'lounge', 'nightclub', 'boate', 'adega'],
        label: 'Bar / Pub',
        bg: '#8B5CF6',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 22h8"/><path d="M12 15v7"/><path d="M19 3L12 11 5 3z"/><line x1="5" x2="19" y1="3" y2="3"/></svg>`
    },
    {
        key: 'cafe',
        keywords: ['cafeteria', 'cafe', 'café', 'coffee', 'coffeeshop', 'padaria', 'confeitaria', 'bakery', 'panificadora', 'doceira', 'sorveteria', 'gelateria'],
        label: 'Cafeteria / Padaria',
        bg: '#D97706',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/></svg>`
    },
    {
        key: 'hospital',
        keywords: ['hospital', 'pronto socorro', 'upa', 'emergencia', 'urgencia'],
        label: 'Hospital',
        bg: '#3B82F6',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6v12"/><path d="M6 12h12"/><rect width="18" height="18" x="3" y="3" rx="2"/></svg>`
    },
    {
        key: 'pharmacy',
        keywords: ['farmacia', 'farmácia', 'drogaria', 'drugstore', 'pharmacy', 'remedios', 'chemist', 'medicamento'],
        label: 'Farmácia',
        bg: '#10B981',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><line x1="8.5" y1="8.5" x2="15.5" y2="15.5"/></svg>`
    },
    {
        key: 'dentist',
        keywords: ['dentista', 'odontologia', 'odontologica', 'odontológico', 'dentist', 'sorriso', 'ortodontia', 'odonto'],
        label: 'Clínica Odontológica',
        bg: '#06B6D4',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a5 5 0 0 0-5 5c0 3 1.5 6.5 3 9 1 1.5 1.5 3 2 4 1-2 3-5 5-8 1.5-2.5 3-6 3-9a5 5 0 0 0-5-5Z"/><path d="M12 7v4"/></svg>`
    },
    {
        key: 'clinic',
        keywords: ['clinica', 'clínica', 'medica', 'médico', 'medico', 'saude', 'saúde', 'doctor', 'doctors', 'consultorio', 'consultório', 'laboratorio', 'laboratório', 'diagnostico', 'healthcare', 'fisioterapia'],
        label: 'Clínica Médica',
        bg: '#6366F1',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>`
    },
    {
        key: 'gym',
        keywords: ['gym', 'fitness', 'academia', 'crossfit', 'treino', 'musculacao', 'musculação', 'pilates', 'esporte', 'sport', 'sports_centre', 'sports_center', 'fitness_centre', 'fitness_center', 'gymnasium', 'health_club', 'lutador', 'jiu', 'judo', 'boxe', 'personal', 'natacao', 'natação'],
        label: 'Academia',
        bg: '#F97316',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6.5 6.5l11 11"/><path d="M21 21l-1-1"/><path d="M3 3l1 1"/><path d="M18 22l4-4"/><path d="M2 6l4-4"/><path d="M3 10l7-7"/><path d="M14 21l7-7"/></svg>`
    },
    {
        key: 'hotel',
        keywords: ['hotel', 'pousada', 'resort', 'hospedagem', 'lodging', 'hostel', 'flat', 'motel', 'guest_house'],
        label: 'Hotel',
        bg: '#8B5CF6',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>`
    },
    {
        key: 'supermarket',
        keywords: ['supermercado', 'supermarket', 'mercado', 'hypermarket', 'hipermercado', 'mercearia', 'hortifruti', 'atacadão', 'atacadao', 'atacado', 'convenience', 'conveniencia', 'sacolao'],
        label: 'Supermercado',
        bg: '#16A34A',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>`
    },
    {
        key: 'shop',
        keywords: ['loja', 'shop', 'store', 'varejo', 'comercio', 'boutique', 'calcados', 'calçados', 'roupas', 'vestuario', 'vestuário', 'eletronicos', 'eletrônicos', 'shopping', 'optician', 'otica', 'ótica'],
        label: 'Loja',
        bg: '#EC4899',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>`
    },
    {
        key: 'auto_repair',
        keywords: ['oficina', 'mecanica', 'mecânica', 'auto repair', 'auto_repair', 'car repair', 'car_repair', 'lataria', 'pneus', 'mecanico', 'mecânico', 'auto pecas', 'auto peças', 'funilaria', 'borracharia', 'car_wash', 'lava rapido'],
        label: 'Oficina',
        bg: '#64748B',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>`
    },
    {
        key: 'gas_station',
        keywords: ['posto', 'posto de combustivel', 'posto de combustível', 'gas station', 'gas_station', 'gasolina', 'fuel', 'combustivel', 'combustível', 'br', 'ipiranga', 'shell'],
        label: 'Posto de Combustível',
        bg: '#EAB308',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="15" y1="22" y2="22"/><line x1="4" x2="14" y1="9" y2="9"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/><path d="M14 13h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 5.41"/></svg>`
    },
    {
        key: 'school',
        keywords: ['escola', 'school', 'colegio', 'colégio', 'faculdade', 'universidade', 'educacao', 'educação', 'ensino', 'curso', 'creche', 'kindergarten'],
        label: 'Escola',
        bg: '#1E40AF',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>`
    },
    {
        key: 'bank',
        keywords: ['banco', 'bank', 'financeira', 'cooperativa', 'caixa', 'bradesco', 'itau', 'santander', 'credito', 'crédito', 'atm'],
        label: 'Banco',
        bg: '#059669',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="22" y2="22"/><line x1="6" x2="6" y1="18" y2="11"/><line x1="10" x2="10" y1="18" y2="11"/><line x1="14" x2="14" y1="18" y2="11"/><polygon points="12 2 20 7 4 7 12 2"/></svg>`
    },
    {
        key: 'pet_shop',
        keywords: ['pet shop', 'petshop', 'pet', 'veterinaria', 'veterinária', 'vet', 'banho e tosa', 'animais', 'racoess'],
        label: 'Pet Shop',
        bg: '#F59E0B',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="20" cy="16" r="2"/><path d="M9 10a5 5 0 0 1 5 5v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4a5 5 0 0 1 5-5Z"/></svg>`
    },
    {
        key: 'beauty_salon',
        keywords: ['salao', 'salão', 'salao de beleza', 'cabeleireiro', 'barbearia', 'barbershop', 'estetica', 'estética', 'manicure', 'sobrancelhas', 'spa', 'beauty', 'hairdresser'],
        label: 'Salão de Beleza',
        bg: '#D946EF',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" x2="8.12" y1="4" y2="15.88"/><line x1="14.47" x2="20" y1="14.48" y2="20"/><line x1="8.12" x2="12" y1="8.12" y2="12"/></svg>`
    },
    {
        key: 'office',
        keywords: ['escritorio', 'escritório', 'advocacia', 'advogado', 'lawyer', 'contabilidade', 'consultoria', 'legal', 'juridico', 'cartorio', 'cartório'],
        label: 'Escritório',
        bg: '#3F3F46',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>`
    },
    {
        key: 'company',
        keywords: ['empresa', 'company', 'corporate', 'negocio', 'negócio', 'industria', 'indústria', 'office', 'tecnologia', 'software', 'agencia', 'agência'],
        label: 'Empresa',
        bg: '#18181B',
        color: '#FFFFFF',
        iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>`
    }
];

const DEFAULT_CATEGORY = {
    key: 'default',
    keywords: [],
    label: 'Estabelecimento',
    bg: '#18181B',
    color: '#FFFFFF',
    iconSvg: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>`
};

export const LeadSpectMapMarkers = {
    /**
     * Get Category Configuration by Category Name or Raw String
     */
    getCategoryConfig(categoryStr, companyName = '') {
        const catTarget = categoryStr || companyName || '';
        if (!catTarget || typeof catTarget !== 'string') {
            return DEFAULT_CATEGORY;
        }

        const normalized = catTarget
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();

        for (const item of CATEGORY_MAP) {
            if (item.key === normalized || item.keywords.some(kw => normalized.includes(kw))) {
                return item;
            }
        }

        if (companyName && typeof companyName === 'string' && companyName !== categoryStr) {
            const normalizedName = companyName
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();

            for (const item of CATEGORY_MAP) {
                if (item.keywords.some(kw => normalizedName.includes(kw))) {
                    return item;
                }
            }
        }

        return DEFAULT_CATEGORY;
    },

    /**
     * Create Custom Marker DOM Element
     */
    createMarkerElement(company) {
        if (!company || !company.latitude || !company.longitude) {
            return null;
        }

        const catConfig = this.getCategoryConfig(company.category, company.name);
        const el = document.createElement('div');
        el.className = 'leadspect-custom-marker';
        el.setAttribute('data-osm-id', company.id || company.osm_id || '');
        
        el.innerHTML = `
            <div class="marker-pin-wrapper" style="--marker-bg: ${catConfig.bg}; --marker-color: ${catConfig.color};">
                <div class="marker-pin-body">
                    ${catConfig.iconSvg}
                </div>
                <div class="marker-pin-pointer"></div>
                <div class="marker-pulse-ring"></div>
            </div>
        `;

        return el;
    },

    /**
     * Copy text to clipboard and show toast feedback
     */
    copyToClipboard(text, successMessage = 'Copiado para a área de transferência!') {
        if (!text) return;
        
        navigator.clipboard.writeText(text).then(() => {
            this.showToast(successMessage, 'success');
        }).catch(err => {
            console.error('Erro ao copiar:', err);
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            this.showToast(successMessage, 'success');
        });
    },

    /**
     * Show mini toast notification
     */
    showToast(message, type = 'success') {
        const toastId = 'leadspect-map-toast';
        let toastEl = document.getElementById(toastId);
        
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.id = toastId;
            toastEl.className = 'fixed bottom-5 right-5 z-[9999] px-4 py-2.5 rounded-xl bg-[#18181B] text-white text-xs font-medium shadow-2xl flex items-center gap-2 transition-all duration-300 transform translate-y-4 opacity-0 pointer-events-none';
            document.body.appendChild(toastEl);
        }

        toastEl.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>${message}</span>
        `;

        requestAnimationFrame(() => {
            toastEl.classList.remove('translate-y-4', 'opacity-0', 'pointer-events-none');
            toastEl.classList.add('translate-y-0', 'opacity-100');
        });

        if (window.toastTimeout) clearTimeout(window.toastTimeout);
        window.toastTimeout = setTimeout(() => {
            toastEl.classList.remove('translate-y-0', 'opacity-100');
            toastEl.classList.add('translate-y-4', 'opacity-0', 'pointer-events-none');
        }, 2500);
    },

    /**
     * Generate HTML for Modern Popup Card / Details Modal
     */
    createPopupHtml(company, debugMode = false) {
        if (!company) return '';

        const catConfig = this.getCategoryConfig(company.category);
        const categoryLabel = catConfig.label || company.category || 'Estabelecimento';

        // Rating
        const reviewsCount = company.reviews_count || company.review_count || 0;
        const hasRating = company.rating && company.rating > 0;
        const ratingVal = hasRating ? parseFloat(company.rating).toFixed(1) : null;
        const reviewCountText = reviewsCount > 0 ? `(${reviewsCount} avaliações)` : '';

        // Status / Hours
        let hoursBadgeHtml = '';
        if (company.is_open_now !== null && company.is_open_now !== undefined) {
            hoursBadgeHtml = company.is_open_now 
                ? `<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aberto agora</span>`
                : `<span class="inline-flex items-center gap-1 text-[11px] font-medium text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Fechado</span>`;
        }

        const openingHoursText = company.opening_hours || null;

        // Coords
        const lat = parseFloat(company.latitude || 0);
        const lng = parseFloat(company.longitude || 0);
        const coordsText = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        const gmapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;

        // Provider (always available in PlaceDTO)
        const providerName = company.provider || company.raw_data?.provider || 'Provider Indefinido';
        const providerBadge = `<div class="mt-2 pt-2 border-t border-dashed border-[#E4E4E7] flex items-center justify-between text-[10px] text-[#71717A]">
            <span>Provider:</span>
            <span class="font-mono font-medium text-[#18181B] bg-[#F4F4F5] px-1.5 py-0.5 rounded border border-[#E4E4E7]">${providerName}</span>
           </div>`;

        // Address formatting
        const fullAddress = company.address || company.city || 'Endereço não informado';
        const osmId = company.id || company.osm_id || '';

        return `
            <div class="leadspect-popup-card">
                
                <!-- Popup Header -->
                <div class="popup-card-header">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="popup-category-badge" style="background-color: ${catConfig.bg}15; color: ${catConfig.bg}; border-color: ${catConfig.bg}30;">
                            <span class="w-3.5 h-3.5 inline-flex items-center justify-center">${catConfig.iconSvg}</span>
                            <span>${categoryLabel}</span>
                        </span>
                        
                        ${hoursBadgeHtml}
                    </div>

                    <h3 class="popup-company-title">${company.name}</h3>

                    ${hasRating ? `
                        <div class="flex items-center gap-1.5 mt-1 text-xs">
                            <div class="flex items-center text-amber-500 font-semibold">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                <span class="ml-1">${ratingVal}</span>
                            </div>
                            <span class="text-[#71717A] text-[11px]">${reviewCountText}</span>
                        </div>
                    ` : ''}
                </div>

                <!-- Popup Details Body -->
                <div class="popup-card-body">
                    
                    <!-- Address -->
                    <div class="popup-info-row">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-[#71717A] mt-0.5"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="text-xs text-[#3F3F46] line-clamp-2">${fullAddress}</span>
                    </div>

                    ${openingHoursText ? `
                        <div class="popup-info-row">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-[#71717A] mt-0.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span class="text-xs text-[#71717A]">${openingHoursText}</span>
                        </div>
                    ` : ''}

                    ${company.phone ? `
                        <div class="popup-info-row">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-[#71717A] mt-0.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span class="text-xs font-medium text-[#18181B]">${company.phone}</span>
                        </div>
                    ` : ''}

                    ${company.website ? `
                        <div class="popup-info-row">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-[#71717A] mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            <a href="${company.website}" target="_blank" rel="noopener noreferrer" class="text-xs text-blue-600 hover:underline truncate">
                                ${company.website.replace(/^https?:\/\//, '')}
                            </a>
                        </div>
                    ` : ''}

                    <div class="popup-info-row text-[10px] text-[#A1A1AA]">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><circle cx="12" cy="12" r="10"/><path d="m12 8-4 4 4 4"/><path d="M16 12H8"/></svg>
                        <span>Coordenadas: ${coordsText}</span>
                    </div>

                    ${providerBadge}
                </div>

                <!-- Action Buttons Footer Grid -->
                <div class="popup-card-actions">
                    <div class="grid grid-cols-2 gap-1.5 mb-2">
                        <!-- Abrir no Google Maps -->
                        <a href="${gmapsUrl}" target="_blank" rel="noopener noreferrer" 
                           class="popup-btn popup-btn-secondary">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>Google Maps</span>
                        </a>

                        <!-- Copiar Endereço -->
                        <button type="button" onclick="window.LeadSpectMapMarkers.copyToClipboard('${fullAddress.replace(/'/g, "\\'")}', 'Endereço copiado!')"
                                class="popup-btn popup-btn-secondary">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                            <span>Copiar Endereço</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-1.5 mb-2">
                        <!-- Copiar Telefone (se houver) -->
                        ${company.phone ? `
                            <button type="button" onclick="window.LeadSpectMapMarkers.copyToClipboard('${company.phone.replace(/'/g, "\\'")}', 'Telefone copiado!')"
                                    class="popup-btn popup-btn-secondary">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                <span>Copiar Fone</span>
                            </button>
                        ` : `
                            <div class="popup-btn popup-btn-disabled opacity-50 cursor-not-allowed">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                <span>Sem Fone</span>
                            </div>
                        `}

                        <!-- Abrir Website (se houver) -->
                        ${company.website ? `
                            <a href="${company.website}" target="_blank" rel="noopener noreferrer"
                               class="popup-btn popup-btn-secondary">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                <span>Website</span>
                            </a>
                        ` : `
                            <div class="popup-btn popup-btn-disabled opacity-50 cursor-not-allowed">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                <span>Sem Site</span>
                            </div>
                        `}
                    </div>

                    <!-- LeadSpect Main Actions -->
                    <div class="flex items-center gap-2 pt-2 border-t border-[#E4E4E7]">
                        <button type="button" onclick="window.searchApp.openWhatsAppModalByOsmId('${osmId}')"
                                class="popup-btn popup-btn-primary flex-1">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>WhatsApp</span>
                        </button>
                        
                        <button type="button" onclick="window.searchApp.addLeadByOsmId('${osmId}')"
                                class="popup-btn popup-btn-secondary flex-1">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span>Lead</span>
                        </button>
                    </div>

                </div>

            </div>
        `;
    }
};

window.LeadSpectMapMarkers = LeadSpectMapMarkers;
