{{-- resources/views/components/image-zoom.blade.php --}}
@props([
    'src',
    'alt' => '',
    'imgClass' => '',
])

@php
    $zoomId = 'zoom-' . uniqid();
@endphp

<div class="relative">
    {{-- Image cliquable --}}
    <button
        type="button"
        class="focus:outline-none"
        data-zoom-open="{{ $zoomId }}"
        aria-label="Agrandir l’image"
    >
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="{{ $imgClass }}"
        >
    </button>

    {{-- Modal de zoom --}}
    <div
        class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/60 p-4"
        data-zoom-modal="{{ $zoomId }}"
        aria-hidden="true"
    >
        {{-- Overlay cliquable pour fermer --}}
        <div class="absolute inset-0" data-zoom-overlay></div>

        <div class="relative z-10 max-w-4xl w-full flex justify-center">
            {{-- Bouton X pour fermer --}}
            <button
                type="button"
                class="absolute -top-4 right-0 md:-top-6 md:-right-6 rounded-full bg-white/90 text-gray-800 w-9 h-9 flex items-center justify-center shadow-lg hover:bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                data-zoom-close
                aria-label="Fermer l’image agrandie"
            >
                &times;
            </button>

            <img
                src="{{ $src }}"
                alt="{{ $alt }}"
                class="max-h-[80vh] w-auto object-contain rounded-lg shadow-2xl bg-white"
            >
        </div>
    </div>
</div>

@once
    <script>
        (function () {
            function initImageZoom() {
                const triggers = document.querySelectorAll('[data-zoom-open]');

                triggers.forEach(trigger => {
                    const id = trigger.getAttribute('data-zoom-open');
                    const modal = document.querySelector('[data-zoom-modal="' + id + '"]');
                    if (!modal) return;

                    const overlay = modal.querySelector('[data-zoom-overlay]');
                    const closeBtn = modal.querySelector('[data-zoom-close]');

                    const open = () => {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        document.body.dataset.zoomScrollLock = 'true';
                        document.body.style.overflow = 'hidden';
                    };

                    const close = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        if (document.body.dataset.zoomScrollLock) {
                            delete document.body.dataset.zoomScrollLock;
                            document.body.style.overflow = '';
                        }
                    };

                    trigger.addEventListener('click', open);
                    overlay.addEventListener('click', close);
                    closeBtn.addEventListener('click', close);

                    // Touche Échap
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                            close();
                        }
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initImageZoom);
            } else {
                initImageZoom();
            }
        })();
    </script>
@endonce
