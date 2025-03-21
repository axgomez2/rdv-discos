@props(['type' => 'info', 'message' => '', 'position' => 'bottom-right', 'duration' => 3000])

<div id="toast-container" 
     class="fixed {{ $position === 'top-right' ? 'top-4 right-4' : 
                    ($position === 'top-left' ? 'top-4 left-4' : 
                    ($position === 'bottom-left' ? 'bottom-4 left-4' : 'bottom-4 right-4')) }} 
            z-50 flex flex-col space-y-2 max-w-xs hidden">
    <div
        id="toast"
        class="bg-black text-white rounded-lg shadow-md px-4 py-3 flex items-center w-full opacity-0 transform translate-x-full transition-all duration-300"
        role="alert"
    >
        <div class="mr-3">
            @if($type === 'success')
                <i class="fas fa-check-circle text-white"></i>
            @elseif($type === 'error')
                <i class="fas fa-exclamation-circle text-white"></i>
            @elseif($type === 'warning')
                <i class="fas fa-exclamation-triangle text-white"></i>
            @else
                <i class="fas fa-info-circle text-white"></i>
            @endif
        </div>
        <div class="text-sm font-medium">{{ $message }}</div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-white rounded-lg p-1.5 inline-flex h-8 w-8 hover:bg-gray-700" onclick="hideToast()">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

@once
    @push('scripts')
    <script>
        let toastTimeout;
        
        function showToast(message, type = 'info', duration = {{ $duration }}, position = '{{ $position }}') {
            const container = document.getElementById('toast-container');
            const toast = document.getElementById('toast');
            
            if (!container || !toast) {
                console.error('Toast container or toast element not found');
                return;
            }
            
            // Clear any existing timeouts
            clearTimeout(toastTimeout);
            
            // Update toast content and class (todos os ícones são brancos)
            if (type === 'success') {
                toast.querySelector('div:first-child i').className = 'fas fa-check-circle text-white';
            } else if (type === 'error') {
                toast.querySelector('div:first-child i').className = 'fas fa-exclamation-circle text-white';
            } else if (type === 'warning') {
                toast.querySelector('div:first-child i').className = 'fas fa-exclamation-triangle text-white';
            } else {
                toast.querySelector('div:first-child i').className = 'fas fa-info-circle text-white';
            }
            
            // Garantir que o toast sempre tenha fundo preto e texto branco
            toast.className = 'bg-black text-white rounded-lg shadow-md px-4 py-3 flex items-center w-full transition-all duration-300';
            
            toast.querySelector('div.text-sm').textContent = message;
            
            // Update position
            container.className = 'fixed z-50 flex flex-col space-y-2 max-w-xs';
            if (position === 'top-right') {
                container.classList.add('top-4', 'right-4');
            } else if (position === 'top-left') {
                container.classList.add('top-4', 'left-4');
            } else if (position === 'bottom-left') {
                container.classList.add('bottom-4', 'left-4');
            } else {
                container.classList.add('bottom-4', 'right-4');
            }
            
            // Show toast
            container.classList.remove('hidden');
            toast.classList.remove('opacity-0', 'translate-x-full');
            toast.classList.add('opacity-100', 'translate-x-0');
            
            // Auto hide after duration
            toastTimeout = setTimeout(() => {
                hideToast();
            }, duration);
        }
        
        function hideToast() {
            const toast = document.getElementById('toast');
            const container = document.getElementById('toast-container');
            
            if (!toast || !container) {
                return;
            }
            
            toast.classList.remove('opacity-100', 'translate-x-0');
            toast.classList.add('opacity-0', 'translate-x-full');
            
            setTimeout(() => {
                container.classList.add('hidden');
            }, 300);
        }
        
        function showLoginToast() {
            showToast('Você precisa estar logado para realizar esta ação.', 'warning');
        }
        
        // Listen for toast events - usamos apenas um event listener
        document.addEventListener('DOMContentLoaded', function() {
            // Remover qualquer listener existente para evitar duplicação
            document.removeEventListener('show-toast', handleToastEvent);
            
            // Adicionar novo listener
            document.addEventListener('show-toast', handleToastEvent);
            
            // Verificar se há mensagens de flash do Laravel para mostrar
            @if (session('success'))
                showToast('{{ session('success') }}', 'success');
            @endif
            
            @if (session('error'))
                showToast('{{ session('error') }}', 'error');
            @endif
            
            @if (session('warning'))
                showToast('{{ session('warning') }}', 'warning');
            @endif
            
            @if (session('info'))
                showToast('{{ session('info') }}', 'info');
            @endif
        });
        
        function handleToastEvent(e) {
            showToast(
                e.detail.message || 'Notificação', 
                e.detail.type || 'info', 
                e.detail.duration || {{ $duration }}, 
                e.detail.position || '{{ $position }}'
            );
        }
    </script>
    @endpush
@endonce
