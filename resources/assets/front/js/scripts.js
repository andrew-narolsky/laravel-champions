// ─── Init ──────────────────────────────────────────────────────────────────

function init() {
    initStats();
    initWinnersSwiper();
    initTournamentsSwiper();
    initDecadeFilter();
}

// ─── Stats ─────────────────────────────────────────────────────────────────

function initStats() {
    const v3 = document.getElementById('stats-v3');
    if (!v3) return;

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            observer.disconnect();
            animateCounters();
        });
    }, {threshold: 0.3});

    observer.observe(v3);
}

function animateCounters() {
    const els = document.querySelectorAll('.v3-value[data-target]');

    els.forEach(el => {
        const target = parseInt(el.dataset.target, 10);
        const suffix = el.dataset.suffix || '';
        const duration = target >= 1000 ? 1800 : target >= 100 ? 1400 : 1000;
        const start = performance.now();

        function tick(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(eased * target);

            el.textContent = current + suffix;

            if (progress < 1) requestAnimationFrame(tick);
        }

        requestAnimationFrame(tick);
    });
}

function initDecadeFilter() {
    const buttons = document.querySelectorAll('.decade-btn');
    const rows    = document.querySelectorAll('#seasons-table tbody tr');

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            buttons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const decade = btn.dataset.decade;
            rows.forEach(row => {
                row.style.display = (decade === 'all' || row.dataset.decade === decade) ? '' : 'none';
            });
        });
    });
}

// ─── Winners slider ────────────────────────────────────────────────────────

function initWinnersSwiper() {
    new Swiper('.winners-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 16,
        // loop: true,
        grabCursor: true,
        navigation: {
            prevEl: '#prev-v2',
            nextEl: '#next-v2',
        },
    });
}

// ─── Tournaments Swiper ──────────────────────────────────────────────────────

function initTournamentsSwiper() {
    new Swiper('.tc-swiper', {
        slidesPerView: 3,
        spaceBetween: 16,
        // loop: true,
        grabCursor: true,
        navigation: {prevEl: '#tc-prev', nextEl: '#tc-next'},
        breakpoints: {
            0: {slidesPerView: 1},
            600: {slidesPerView: 2},
            900: {slidesPerView: 3},
        },
    });
}

// ─── Run ───────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', init);
