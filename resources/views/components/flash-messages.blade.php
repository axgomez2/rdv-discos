@if (session('success') || session('error') || session('info') || session('warning'))
    <div id="toast-container" class="toast toast-top toast-center">
        @if (session('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if (session('info'))
            <div class="alert alert-info">
                <span>{{ session('info') }}</span>
            </div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">
                <span>{{ session('warning') }}</span>
            </div>
        @endif
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const toastContainer = document.getElementById('toast-container');
        if (toastContainer) {
            setTimeout(() => {
                toastContainer.style.opacity = '0';
                setTimeout(() => {
                    toastContainer.remove();
                }, 300); // Remove after fade out
            }, 5000); // Start fading after 5 seconds
        }
    });
    </script>
@endif
