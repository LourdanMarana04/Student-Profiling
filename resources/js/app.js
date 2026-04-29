import './bootstrap';

const routes = {
    '/dashboard': () => '',
    '/users': () => `
        <div class="page-header">
            <h1>Users</h1>
            <p class="text-gray-500">Browse and manage the user directory in a client-side route.</p>
        </div>
        <div class="stat-grid">
            <div class="stat-box">
                <div class="stat-box-value">Users</div>
                <div class="stat-box-label">Directory</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-value">Students</div>
                <div class="stat-box-label">Profiles</div>
            </div>
        </div>
        <div class="action-cards-grid">
            <a href="/students" class="action-card coral">
                <div class="card-icon">👥</div>
                <div class="card-title">Student Directory</div>
                <div class="card-subtitle">Open the full student list.</div>
                <div class="card-footer"><div class="card-stats"><strong>Manage</strong></div><div class="card-arrow">→</div></div>
            </a>
        </div>
    `,
    '/reports': () => `
        <div class="page-header">
            <h1>Reports</h1>
            <p class="text-gray-500">View analytics, download reports, and track activity.</p>
        </div>
        <div class="stat-grid">
            <div class="stat-box">
                <div class="stat-box-value">${new Date().getFullYear()}</div>
                <div class="stat-box-label">Year</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-value">3</div>
                <div class="stat-box-label">Report Types</div>
            </div>
        </div>
        <div class="action-cards-grid">
            <div class="action-card cyan">
                <div class="card-icon">📊</div>
                <div class="card-title">Progress Overview</div>
                <div class="card-subtitle">Student and activity analytics.</div>
            </div>
        </div>
    `,
};

function updateActiveNav(path) {
    document.querySelectorAll('[data-spa-link]').forEach((link) => {
        const isActive = link.getAttribute('href') === path;
        link.classList.toggle('active', isActive);
    });
}

function renderRoute(path) {
    const page = document.querySelector('#spa-page');
    if (!page) {
        return;
    }

    if (routes[path] && path !== '/dashboard') {
        page.innerHTML = routes[path]();
    }

    updateActiveNav(path);
}

window.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;
    const currentPath = window.location.pathname;

    renderRoute(currentPath);

    root.addEventListener('click', (event) => {
        const link = event.target.closest('a[data-spa-link]');
        if (!link) {
            return;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('http') || href.startsWith('#')) {
            return;
        }

        event.preventDefault();
        window.history.pushState({}, '', href);
        renderRoute(href);
    });
});

window.addEventListener('popstate', () => renderRoute(window.location.pathname));
