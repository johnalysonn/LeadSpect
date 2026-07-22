<!-- Global Toast Notification Container -->
<div x-data="toastNotifier()"
     @show-toast.window="addToast($event.detail)"
     class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             :class="{
                 'border-emerald-200 bg-emerald-900/90 text-white shadow-emerald-950/20': toast.type === 'success',
                 'border-red-200 bg-red-900/90 text-white shadow-red-950/20': toast.type === 'error',
                 'border-amber-200 bg-amber-900/90 text-white shadow-amber-950/20': toast.type === 'warning',
                 'border-zinc-700 bg-zinc-900/95 text-white shadow-black/30': toast.type === 'info' || !toast.type
             }"
             class="pointer-events-auto flex items-start gap-3 p-3.5 rounded-xl border shadow-xl backdrop-blur-md transition-all text-xs font-medium">
            
            <!-- Icon -->
            <div class="shrink-0 mt-0.5">
                <template x-if="toast.type === 'success'">
                    <div class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                </template>
                <template x-if="toast.type === 'error'">
                    <div class="w-5 h-5 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </div>
                </template>
                <template x-if="toast.type === 'warning'">
                    <div class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                </template>
                <template x-if="toast.type === 'info' || !toast.type">
                    <div class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </div>
                </template>
            </div>

            <!-- Message Text -->
            <div class="flex-1 leading-snug tracking-tight">
                <span x-text="toast.message"></span>
            </div>

            <!-- Close Button -->
            <button type="button" @click="removeToast(toast.id)" class="text-zinc-400 hover:text-white transition-colors cursor-pointer p-0.5 shrink-0">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('toastNotifier', () => ({
            toasts: [],
            nextId: 1,

            init() {
                window.showToast = (message, type = 'success') => {
                    this.addToast({ message, type });
                };

                @if (session('status'))
                    this.addToast({ message: @json(session('status')), type: 'success' });
                @endif
                @if (session('success'))
                    this.addToast({ message: @json(session('success')), type: 'success' });
                @endif
                @if (session('error'))
                    this.addToast({ message: @json(session('error')), type: 'error' });
                @endif
                @if (session('info'))
                    this.addToast({ message: @json(session('info')), type: 'info' });
                @endif
            },

            addToast(detail) {
                if (!detail || !detail.message) return;
                const id = this.nextId++;
                const toast = {
                    id,
                    message: detail.message,
                    type: detail.type || 'success',
                    visible: true
                };

                this.toasts.push(toast);

                setTimeout(() => {
                    this.removeToast(id);
                }, 4000);
            },

            removeToast(id) {
                const target = this.toasts.find(t => t.id === id);
                if (target) {
                    target.visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 300);
                }
            }
        }));
    });
</script>
