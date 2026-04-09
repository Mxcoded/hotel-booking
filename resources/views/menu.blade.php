@extends('layouts.app')

@section('title', 'Food Menu - Brickspoint Boutique Aparthotel')

@push('styles')
<style>
    #book-section {
        background: radial-gradient(ellipse at center, #3b2010 0%, #150a03 100%);
    }
    .page-wrap {
        background-color: #fffdf7;
        /* Inward shadow at the spine edge to simulate page curl */
        box-shadow: inset -6px 0 18px -6px rgba(0,0,0,0.25), 4px 4px 20px rgba(0,0,0,0.5);
    }
    .page-wrap.right {
        box-shadow: inset 6px 0 18px -6px rgba(0,0,0,0.25), -4px 4px 20px rgba(0,0,0,0.5);
    }
    #book-spine {
        background: linear-gradient(to right,
            #5c3d1e 0%,
            #a0713a 20%,
            #e8d5b0 45%,
            #fff8ee 50%,
            #e8d5b0 55%,
            #a0713a 80%,
            #5c3d1e 100%
        );
        box-shadow: 0 0 12px rgba(0,0,0,0.6);
    }
    .nav-btn {
        transition: transform 0.15s ease, background-color 0.15s ease;
    }
    .nav-btn:hover:not(:disabled) {
        transform: scale(1.12);
        background-color: rgba(234, 88, 12, 1);
    }
    .nav-btn:disabled {
        opacity: 0.25;
        cursor: not-allowed;
        transform: none;
    }
    /* Prevent any browser context menu or drag on canvases */
    canvas {
        -webkit-user-drag: none;
        display: block;
    }
    @keyframes bookPulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.4; }
    }
    .loading-pulse { animation: bookPulse 1.6s ease-in-out infinite; }
    /* Zoom controls */
    .zoom-btn {
        transition: background-color 0.15s ease, opacity 0.15s ease;
    }
    .zoom-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="bg-stone-900 py-12 px-6 text-center">
    <p class="text-xs font-semibold uppercase tracking-widest text-orange-400 mb-2">Brickspoint Restaurant</p>
    <h1 class="text-4xl font-bold tracking-tight text-white">Our Food Menu</h1>
    <p class="mt-3 max-w-lg mx-auto text-stone-400 text-base">
        Explore our freshly prepared selection of dishes and beverages.
    </p>
</div>

{{-- Book Viewer --}}
<section id="book-section" class="py-10 min-h-screen">
    <div class="px-2 md:px-6">

        @if($menuPdf)

            {{-- Loading State --}}
            <div id="loading-state" class="flex flex-col items-center justify-center py-28 text-stone-400">
                <i class="fas fa-book-open text-5xl text-orange-500 mb-5 loading-pulse"></i>
                <p class="text-lg tracking-wide">Opening your menu&hellip;</p>
            </div>

            {{-- Book Wrapper (shown after load) --}}
            <div id="book-wrapper" class="hidden">

                {{-- Page counter --}}
                <p class="text-center text-stone-400 text-sm mb-5 tracking-wide" id="page-label">&nbsp;</p>

                {{-- Navigation + Book --}}
                <div class="flex items-center justify-center gap-2 md:gap-5">

                    {{-- Prev --}}
                    <button id="prev-btn" disabled aria-label="Previous pages"
                        class="nav-btn shrink-0 w-11 h-11 md:w-13 md:h-13 rounded-full bg-orange-700/80 text-white flex items-center justify-center shadow-xl text-base md:text-lg">
                        <i class="fas fa-chevron-left"></i>
                    </button>

                    {{-- Book --}}
                    <div id="book-container" class="flex items-stretch overflow-hidden rounded-sm">

                        {{-- Left page --}}
                        <div id="left-page" class="page-wrap overflow-hidden">
                            <canvas id="left-canvas" oncontextmenu="return false" draggable="false"></canvas>
                        </div>

                        {{-- Spine (desktop only) --}}
                        <div id="book-spine" class="w-5 shrink-0 self-stretch" style="display:none;"></div>

                        {{-- Right page (desktop only) --}}
                        <div id="right-page" class="page-wrap right overflow-hidden" style="display:none;">
                            <canvas id="right-canvas" oncontextmenu="return false" draggable="false"></canvas>
                        </div>

                    </div>

                    {{-- Next --}}
                    <button id="next-btn" disabled aria-label="Next pages"
                        class="nav-btn shrink-0 w-11 h-11 md:w-13 md:h-13 rounded-full bg-orange-700/80 text-white flex items-center justify-center shadow-xl text-base md:text-lg">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                </div>

                {{-- Zoom Controls --}}
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button id="zoom-out-btn" aria-label="Zoom out"
                        class="zoom-btn w-8 h-8 rounded-full bg-stone-700 hover:bg-stone-600 text-stone-300 flex items-center justify-center text-sm">
                        <i class="fas fa-minus"></i>
                    </button>
                    <div class="flex items-center gap-2 bg-stone-800/70 border border-stone-700 rounded-full px-3 py-1">
                        <i class="fas fa-magnifying-glass text-stone-500 text-xs"></i>
                        <span id="zoom-label" class="text-stone-300 text-sm font-mono w-10 text-center">100%</span>
                    </div>
                    <button id="zoom-in-btn" aria-label="Zoom in"
                        class="zoom-btn w-8 h-8 rounded-full bg-stone-700 hover:bg-stone-600 text-stone-300 flex items-center justify-center text-sm">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button id="zoom-reset-btn" aria-label="Reset zoom"
                        class="text-stone-600 hover:text-stone-300 text-xs transition-colors ml-1 underline underline-offset-2">
                        Reset
                    </button>
                </div>

                {{-- Keyboard hint --}}
                <p class="text-center mt-4 text-stone-600 text-xs">
                    <i class="fas fa-keyboard mr-1"></i>
                    <kbd class="bg-stone-800 text-stone-300 border border-stone-700 px-1.5 py-0.5 rounded text-xs">&larr;</kbd>
                    <kbd class="bg-stone-800 text-stone-300 border border-stone-700 px-1.5 py-0.5 rounded text-xs">&rarr;</kbd>
                    turn pages &nbsp;&middot;&nbsp;
                    <kbd class="bg-stone-800 text-stone-300 border border-stone-700 px-1.5 py-0.5 rounded text-xs">+</kbd>
                    <kbd class="bg-stone-800 text-stone-300 border border-stone-700 px-1.5 py-0.5 rounded text-xs">-</kbd>
                    zoom
                </p>

            </div>

        @else
            {{-- No menu uploaded --}}
            <div class="flex flex-col items-center justify-center py-28 text-center">
                <i class="fas fa-utensils text-6xl text-stone-700 mb-6"></i>
                <h2 class="text-2xl font-semibold text-stone-300 mb-2">Menu Coming Soon</h2>
                <p class="text-stone-500 max-w-sm">
                    Our food menu is being prepared. Please check back shortly or contact us.
                </p>
                <a href="{{ route('home') }}#contact"
                   class="mt-6 inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-5 rounded-lg shadow transition-colors">
                    <i class="fas fa-envelope"></i> Contact Us
                </a>
            </div>
        @endif

    </div>
</section>

@if($menuPdf)
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function () {
    'use strict';

    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const PDF_URL = @json(asset('storage/' . $menuPdf));

    // ── State ────────────────────────────────────────────────────────────────
    let pdfDoc      = null;
    let totalPages  = 0;
    let currentPage = 1;      // always the left / primary page
    let zoomScale   = 1.0;
    const ZOOM_MIN  = 0.5;
    const ZOOM_MAX  = 3.0;
    const ZOOM_STEP = 0.25;

    function isDesktop() {
        return window.innerWidth >= 768;
    }

    // ── Elements ─────────────────────────────────────────────────────────────
    const loadingEl    = document.getElementById('loading-state');
    const wrapperEl    = document.getElementById('book-wrapper');
    const leftCanvas   = document.getElementById('left-canvas');
    const rightCanvas  = document.getElementById('right-canvas');
    const leftPageEl   = document.getElementById('left-page');
    const rightPageEl  = document.getElementById('right-page');
    const spineEl      = document.getElementById('book-spine');
    const prevBtn      = document.getElementById('prev-btn');
    const nextBtn      = document.getElementById('next-btn');
    const pageLabelEl  = document.getElementById('page-label');
    const zoomInBtn    = document.getElementById('zoom-in-btn');
    const zoomOutBtn   = document.getElementById('zoom-out-btn');
    const zoomResetBtn = document.getElementById('zoom-reset-btn');
    const zoomLabelEl  = document.getElementById('zoom-label');

    // ── Render helpers ────────────────────────────────────────────────────────
    function pageWidthPx() {
        // Available horizontal space minus nav buttons (≈120px each side) and padding
        const maxW  = Math.min(window.innerWidth - 180, 1100);
        // On desktop split between two pages; on mobile full width
        return isDesktop() ? Math.floor((maxW - 20) / 2) : Math.min(maxW, window.innerWidth - 48);
    }

    async function renderPage(pageNum, canvas) {
        if (pageNum < 1 || pageNum > totalPages) return;

        const page   = await pdfDoc.getPage(pageNum);
        const vp0    = page.getViewport({ scale: 1 });
        const maxH   = window.innerHeight * 0.82;

        // Base scale: fit to width, clamp to 82 vh (at zoom = 1), then apply zoom multiplier
        let baseScale = pageWidthPx() / vp0.width;
        if (vp0.height * baseScale > maxH) baseScale = maxH / vp0.height;
        const scale = baseScale * zoomScale;

        const vp = page.getViewport({ scale });
        canvas.width  = vp.width;
        canvas.height = vp.height;

        await page.render({ canvasContext: canvas.getContext('2d'), viewport: vp }).promise;
    }

    async function renderSpread() {
        const desktop    = isDesktop();
        const rightPage  = currentPage + 1;
        const showRight  = desktop && rightPage <= totalPages;

        // Render left (always)
        await renderPage(currentPage, leftCanvas);

        // Right page & spine
        if (showRight) {
            await renderPage(rightPage, rightCanvas);
            rightPageEl.style.display = '';
            spineEl.style.display     = '';
        } else {
            rightPageEl.style.display = 'none';
            spineEl.style.display     = 'none';
        }

        // Page label
        pageLabelEl.textContent = showRight
            ? `Pages ${currentPage} – ${rightPage}  ·  of ${totalPages}`
            : `Page ${currentPage}  ·  of ${totalPages}`;

        // Button states
        prevBtn.disabled = currentPage <= 1;
        nextBtn.disabled = desktop
            ? currentPage + 2 > totalPages
            : currentPage >= totalPages;
    }

    // ── Load ─────────────────────────────────────────────────────────────────
    pdfjsLib.getDocument(PDF_URL).promise
        .then(async (pdf) => {
            pdfDoc     = pdf;
            totalPages = pdf.numPages;
            await renderSpread();
            loadingEl.classList.add('hidden');
            wrapperEl.classList.remove('hidden');
        })
        .catch(() => {
            loadingEl.innerHTML =
                '<i class="fas fa-exclamation-circle text-4xl text-red-400 mb-3"></i>' +
                '<p class="text-stone-300 mt-2">Could not load the menu. Please try again later.</p>';
        });

    // ── Zoom ──────────────────────────────────────────────────────────────────
    function applyZoom(newScale) {
        zoomScale = Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, Math.round(newScale * 100) / 100));
        zoomLabelEl.textContent  = Math.round(zoomScale * 100) + '%';
        zoomOutBtn.disabled      = zoomScale <= ZOOM_MIN;
        zoomInBtn.disabled       = zoomScale >= ZOOM_MAX;
        renderSpread();
    }

    zoomInBtn.addEventListener('click',    () => applyZoom(zoomScale + ZOOM_STEP));
    zoomOutBtn.addEventListener('click',   () => applyZoom(zoomScale - ZOOM_STEP));
    zoomResetBtn.addEventListener('click', () => applyZoom(1.0));

    // ── Navigation ────────────────────────────────────────────────────────────
    function step() { return isDesktop() ? 2 : 1; }

    prevBtn.addEventListener('click', () => {
        currentPage = Math.max(1, currentPage - step());
        renderSpread();
    });

    nextBtn.addEventListener('click', () => {
        const next = currentPage + step();
        if (next <= totalPages) { currentPage = next; renderSpread(); }
    });

    // Keyboard
    document.addEventListener('keydown', (e) => {
        // Only handle shortcuts when not typing in an input
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;
        if (e.key === 'ArrowLeft'           && !prevBtn.disabled)    prevBtn.click();
        if (e.key === 'ArrowRight'          && !nextBtn.disabled)    nextBtn.click();
        if ((e.key === '+' || e.key === '=') && !zoomInBtn.disabled)  applyZoom(zoomScale + ZOOM_STEP);
        if ((e.key === '-' || e.key === '_') && !zoomOutBtn.disabled) applyZoom(zoomScale - ZOOM_STEP);
        if (e.key === '0' && (e.ctrlKey || e.metaKey)) { e.preventDefault(); applyZoom(1.0); }
    });

    // Touch swipe
    let touchX = 0;
    const bookEl = document.getElementById('book-container');
    bookEl.addEventListener('touchstart', (e) => { touchX = e.touches[0].clientX; }, { passive: true });
    bookEl.addEventListener('touchend', (e) => {
        const diff = touchX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
            if (diff > 0 && !nextBtn.disabled) nextBtn.click();
            if (diff < 0 && !prevBtn.disabled) prevBtn.click();
        }
    }, { passive: true });

    // Resize (debounced)
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(renderSpread, 280);
    });

})();
</script>
@endif

@endsection
