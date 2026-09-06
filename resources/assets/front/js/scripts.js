// ─── Init ──────────────────────────────────────────────────────────────────

function init() {
    initStats();
    initWinnersSwiper();
    initTournamentsSwiper();
    initDecadeFilter();
    initSearch();
    initMobileSearchToggle();
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

            if (progress >= 1) {
                el.textContent = el.dataset.formatted || current + suffix;
            } else {
                el.textContent = current;
            }

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

// ─── Live search ───────────────────────────────────────────────────────────

function initMobileSearchToggle() {
    const toggle = document.getElementById('search-toggle');
    const panel = document.getElementById('header-search-mobile');
    if (!toggle || !panel) return;

    toggle.addEventListener('click', () => {
        panel.classList.toggle('open');
        if (panel.classList.contains('open')) {
            panel.querySelector('input')?.focus();
        }
    });
}

function initSearch() {
    setupSearchField(document.querySelector('.header-search'), 'search-input', 'search-dropdown');
    setupSearchField(document.getElementById('header-search-mobile'), 'search-input-mobile', 'search-dropdown-mobile');
}

function setupSearchField(container, inputId, dropdownId) {
    if (!container) return;

    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    const searchUrl = container.dataset.searchUrl;
    if (!input || !dropdown || !searchUrl) return;

    let debounceTimer = null;
    let requestToken = 0;
    let activeIndex = -1;

    const closeDropdown = () => {
        dropdown.classList.remove('visible');
        dropdown.innerHTML = '';
        activeIndex = -1;
    };

    const renderResults = (results) => {
        dropdown.innerHTML = '';
        activeIndex = -1;

        if (results.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'search-result-item search-result-empty';
            empty.textContent = 'No matches found';
            dropdown.appendChild(empty);
            dropdown.classList.add('visible');
            return;
        }

        results.forEach(result => {
            const item = document.createElement('a');
            item.className = 'search-result-item';
            item.href = result.url;

            if (result.image) {
                const img = document.createElement('img');
                img.className = 'result-thumb';
                img.src = result.image;
                img.alt = '';
                img.loading = 'lazy';
                item.appendChild(img);
            } else {
                const placeholder = document.createElement('span');
                placeholder.className = 'result-thumb result-thumb-placeholder';
                placeholder.textContent = result.name.charAt(0).toUpperCase();
                item.appendChild(placeholder);
            }

            const text = document.createElement('span');
            text.className = 'result-text';

            const name = document.createElement('span');
            name.className = 'result-name';
            name.textContent = result.name;
            text.appendChild(name);

            if (result.subtitle) {
                const subtitle = document.createElement('span');
                subtitle.className = 'result-subtitle';
                subtitle.textContent = result.subtitle;
                text.appendChild(subtitle);
            }

            item.appendChild(text);

            const type = document.createElement('span');
            type.className = 'result-type';
            type.textContent = result.label;
            item.appendChild(type);

            dropdown.appendChild(item);
        });

        dropdown.classList.add('visible');
    };

    const search = (term) => {
        const token = ++requestToken;

        fetch(`${searchUrl}?q=${encodeURIComponent(term)}`, {
            headers: {'Accept': 'application/json'},
        })
            .then(response => response.ok ? response.json() : {results: []})
            .then(data => {
                if (token !== requestToken) return;
                renderResults(data.results || []);
            })
            .catch(() => {
                if (token !== requestToken) return;
                closeDropdown();
            });
    };

    input.addEventListener('input', () => {
        const term = input.value.trim();

        clearTimeout(debounceTimer);

        if (term.length < 2) {
            closeDropdown();
            return;
        }

        debounceTimer = setTimeout(() => search(term), 250);
    });

    input.addEventListener('keydown', (e) => {
        const items = dropdown.querySelectorAll('.search-result-item:not(.search-result-empty)');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = (activeIndex + 1) % items.length;
            items.forEach((el, i) => el.classList.toggle('active', i === activeIndex));
            items[activeIndex].scrollIntoView({block: 'nearest'});
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = (activeIndex - 1 + items.length) % items.length;
            items.forEach((el, i) => el.classList.toggle('active', i === activeIndex));
            items[activeIndex].scrollIntoView({block: 'nearest'});
        } else if (e.key === 'Enter' && activeIndex >= 0) {
            e.preventDefault();
            items[activeIndex].click();
        } else if (e.key === 'Escape') {
            closeDropdown();
            input.blur();
        }
    });

    document.addEventListener('click', (e) => {
        if (!container.contains(e.target)) {
            closeDropdown();
        }
    });
}

// ─── Run ───────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', init);
