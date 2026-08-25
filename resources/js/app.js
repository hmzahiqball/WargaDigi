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
