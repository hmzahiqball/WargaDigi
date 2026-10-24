// WargaDigi 21 — Main JavaScript
// ========================================

// Import Bootstrap
import * as bootstrap from 'bootstrap';

// Import Chart.js
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

// Make Chart available globally for Blade templates
window.Chart = Chart;
window.bootstrap = bootstrap;

// ========================================
// Count-Up Animation
// ========================================
function animateCountUp(element, target, duration = 2000) {
    const start = 0;
    const startTime = performance.now();
    const suffix = element.dataset.suffix || '';

    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeOut = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(easeOut * target);

        element.textContent = current.toLocaleString('id-ID') + suffix;

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

// Intersection Observer for count-up
const countObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const el = entry.target;
            const target = parseInt(el.dataset.target);
            if (target) {
                animateCountUp(el, target);
            }
            countObserver.unobserve(el);
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('[data-count-up]').forEach(el => {
    countObserver.observe(el);
});

// ========================================
// Fade-in Animation on Scroll
// ========================================
const fadeObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-in');
            fadeObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.fade-on-scroll').forEach(el => {
    fadeObserver.observe(el);
});

// ========================================
// Calendar Widget
// ========================================
window.initCalendar = function(containerId, events = []) {
    const container = document.getElementById(containerId);
    if (!container) return;

    let currentDate = new Date();
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

    function render() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const today = new Date();
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        let html = `
            <div class="calendar-header">
                <h5>${months[month]} ${year}</h5>
                <div class="calendar-nav">
                    <button id="${containerId}-prev"><i class="bi bi-chevron-left"></i></button>
                    <button id="${containerId}-next"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
            <div class="calendar-grid">
        `;

        days.forEach(d => {
            html += `<div class="day-header">${d}</div>`;
        });

        // Previous month
        for (let i = firstDay - 1; i >= 0; i--) {
            html += `<div class="day-cell other-month">${daysInPrevMonth - i}</div>`;
        }

        // Current month
        for (let d = 1; d <= daysInMonth; d++) {
            const isToday = d === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            const hasEvent = events.some(e => e.day === d && e.month === month);
            let classes = 'day-cell';
            if (isToday) classes += ' today';
            if (hasEvent) classes += ' has-event';
            html += `<div class="${classes}">${d}</div>`;
        }

        // Next month
        const totalCells = firstDay + daysInMonth;
        const remaining = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let i = 1; i <= remaining; i++) {
            html += `<div class="day-cell other-month">${i}</div>`;
        }

        html += '</div>';
        container.innerHTML = html;

        // Event listeners
        document.getElementById(`${containerId}-prev`)?.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            render();
        });

        document.getElementById(`${containerId}-next`)?.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            render();
        });
    }

    render();
};

// ========================================
// Smooth scroll for anchor links
// ========================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// ========================================
// Active Nav Link
// ========================================
const currentPath = window.location.pathname;
document.querySelectorAll('.navbar-warga .nav-link').forEach(link => {
    if (link.getAttribute('href') === currentPath) {
        link.classList.add('active');
    }
});

// ========================================
// Money Input Formatter (pemisah ribuan)
// ========================================
// Penggunaan: tandai input nominal uang dengan atribut data-money
//   <input type="text" name="nominal" data-money inputmode="numeric">
// Util ini:
//   - Menampilkan angka dengan pemisah ribuan (mis. 100000 -> 100.000).
//   - Menormalkan kembali ke angka murni saat form di-submit (mis. 100.000 -> 100000),
//     karena validasi server memakai rule numeric.
//   - Mendukung nilai yang di-set secara programatik (mis. modal edit) via MutationObserver.
(function () {
    const MONEY_SELECTOR = 'input[data-money], input.money-input';

    // Ambil hanya digit dari string.
    function onlyDigits(value) {
        return (value ?? '').toString().replace(/\D/g, '');
    }

    // Format digit menjadi string berpemisah ribuan ala id-ID.
    function formatMoney(value) {
        const digits = onlyDigits(value);
        if (digits === '') return '';
        // Hilangkan leading zero berlebih, tapi pertahankan "0".
        const normalized = digits.replace(/^0+(?=\d)/, '');
        return Number(normalized).toLocaleString('id-ID');
    }

    // Siapkan satu input: ubah type number -> text agar titik ribuan bisa tampil.
    function prepareInput(input) {
        if (input.dataset.moneyReady === '1') return;
        input.dataset.moneyReady = '1';

        if (input.getAttribute('type') === 'number') {
            input.setAttribute('type', 'text');
        }
        if (!input.getAttribute('inputmode')) {
            input.setAttribute('inputmode', 'numeric');
        }

        // Format nilai awal (mis. dari old() atau value server).
        if (input.value !== '') {
            input.value = formatMoney(input.value);
        }

        // Format saat user mengetik, jaga posisi kursor dari sisi kanan.
        input.addEventListener('input', function () {
            const prevLength = input.value.length;
            const caretFromEnd = input.value.length - (input.selectionStart ?? input.value.length);
            input.value = formatMoney(input.value);
            const newPos = Math.max(0, input.value.length - caretFromEnd);
            try { input.setSelectionRange(newPos, newPos); } catch (e) { /* abaikan */ }
        });

        input.addEventListener('blur', function () {
            input.value = formatMoney(input.value);
        });
    }

    // Format ulang input yang nilainya di-set via JS (modal edit, dsb).
    function reformatInput(input) {
        prepareInput(input);
        input.value = formatMoney(input.value);
    }
    window.reformatMoneyInput = reformatInput;

    // Normalisasi semua input uang dalam sebuah form menjadi angka murni.
    function normalizeForm(form) {
        form.querySelectorAll(MONEY_SELECTOR).forEach(function (input) {
            input.value = onlyDigits(input.value);
        });
    }

    function initAll(root) {
        (root || document).querySelectorAll(MONEY_SELECTOR).forEach(prepareInput);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAll(document);

        // Normalisasi sebelum submit. Pakai capture=true agar jalan sebelum handler lain
        // (termasuk anti double-submit) dan sebelum data dikirim.
        document.addEventListener('submit', function (e) {
            if (e.target && e.target.matches && e.target.matches('form')) {
                normalizeForm(e.target);
            }
        }, true);

        // Format ulang nilai saat modal Bootstrap dibuka (nilai sering di-set via JS).
        document.addEventListener('shown.bs.modal', function (e) {
            if (e.target) initAll(e.target);
            if (e.target) e.target.querySelectorAll(MONEY_SELECTOR).forEach(function (input) {
                input.value = formatMoney(input.value);
            });
        });

        // Tangkap input uang yang ditambahkan dinamis ke DOM.
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (m) {
                m.addedNodes && m.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) return;
                    if (node.matches && node.matches(MONEY_SELECTOR)) prepareInput(node);
                    if (node.querySelectorAll) initAll(node);
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });
})();
