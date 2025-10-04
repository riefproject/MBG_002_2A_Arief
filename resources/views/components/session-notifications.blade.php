<?php
$flashMessages = collect([
    ['type' => 'success', 'message' => session('success')],
    ['type' => 'error', 'message' => session('error')],
    ['type' => 'info', 'message' => session('info')],
    ['type' => 'warning', 'message' => session('warning')],
    ['type' => 'info', 'message' => session('status')],
])->filter(fn ($item) => filled($item['message'] ?? null));
?>

<?php if ($flashMessages->isNotEmpty()): ?>
    <?php $payload = $flashMessages->values()->map(function ($item) {
        $type = $item['type'];
        if (!in_array($type, ['success', 'error', 'info'], true)) {
            $type = 'info';
        }

        return [
            'type' => $type,
            'message' => (string) $item['message'],
        ];
    }); ?>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const notifications = @json($payload);

                notifications.forEach(({ type, message }) => {
                    if (!message) {
                        return;
                    }

                    if (window.showNotification) {
                        window.showNotification(message, type);

                        return;
                    }

                    const fallback = document.createElement('div');
                    fallback.className = 'fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-md rounded-lg border border-gray-200 px-4 py-3 text-sm text-gray-700';
                    fallback.textContent = message;
                    document.body.appendChild(fallback);
                    setTimeout(() => fallback.remove(), 4000);
                });
            });
        </script>
    @endpush
<?php endif; ?>
