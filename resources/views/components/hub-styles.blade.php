<style>
/* ═══════════════════════════════════════════════════
   Hub Navigation — Pipeline Design System
   ═══════════════════════════════════════════════════ */

.hub-page { width: 100%; }

/* ── Page Header ── */
.hub-header {
    margin-bottom: 1.8rem;
    padding: 1.2rem 1.4rem;
    background: var(--bs-card-bg, #fff);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 0.6rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    position: relative;
    overflow: visible;
}
.hub-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 0.6rem 0.6rem 0 0;
    background: linear-gradient(90deg, #d4af37, #e8c84a, #d4af37);
    background-size: 200% 100%;
    animation: hub-shimmer 3s ease-in-out infinite;
}
@keyframes hub-shimmer {
    0%, 100% { background-position: 0% 0%; }
    50% { background-position: 200% 0%; }
}
.hub-header h4 {
    font-weight: 700;
    font-size: 1.15rem;
    color: var(--bs-body-color, #111827);
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.hub-header h4 i {
    color: #d4af37;
    font-size: 1.35rem;
}
.hub-header p {
    font-size: 0.78rem;
    color: var(--bs-surface-500, #6b7280);
    margin: 0;
    letter-spacing: .2px;
}

/* ── Section Labels ── */
.hub-section-label {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.62rem;
    font-weight: 700;
    color: var(--bs-surface-500, #6b7280);
    text-transform: uppercase;
    letter-spacing: 1.2px;
    margin: 28px 0 12px;
    padding-left: 2px;
}
.hub-section-label:first-of-type { margin-top: 0; }
.hub-section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, rgba(212,175,55,.25), transparent);
    margin-left: 8px;
}

/* ── Card Grid ── */
.hub-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 14px;
}

/* ── Hub Card ── */
.hub-card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 18px 20px;
    background: var(--bs-card-bg, #fff);
    border-radius: 0.55rem;
    border: 1px solid rgba(255,255,255,.08);
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    text-decoration: none !important;
    color: inherit !important;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}
.hub-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 3px;
    height: 100%;
    background: linear-gradient(180deg, #d4af37, #e8c84a);
    opacity: 0;
    transition: opacity .25s ease;
}
.hub-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(212,175,55,.12);
    border-color: rgba(212,175,55,.2);
}
.hub-card:hover::before { opacity: 1; }

/* ── Card Icon ── */
.hub-card-icon {
    width: 44px;
    height: 44px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(212,175,55,.08);
    color: #d4af37;
    transition: all .25s ease;
}
.hub-card-icon i { font-size: 22px; transition: transform .25s ease; }
.hub-card:hover .hub-card-icon {
    background: linear-gradient(135deg, #d4af37, #b8922e);
    color: #fff;
    box-shadow: 0 3px 10px rgba(212,175,55,.25);
}
.hub-card:hover .hub-card-icon i { transform: scale(1.1); }

/* ── Card Content ── */
.hub-card-body { flex: 1; min-width: 0; }
.hub-card-title {
    font-weight: 600;
    font-size: 0.88rem;
    color: var(--bs-body-color, #111827);
    margin: 0 0 3px;
    line-height: 1.3;
}
.hub-card-desc {
    font-size: 0.72rem;
    color: var(--bs-surface-500, #6b7280);
    margin: 0;
    line-height: 1.45;
    letter-spacing: .1px;
}

/* ── Card Arrow ── */
.hub-card-arrow {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--bs-surface-400, #9ca3af);
    opacity: 0;
    font-size: 18px;
    transition: all .25s ease;
}
.hub-card:hover .hub-card-arrow {
    opacity: 1;
    right: 12px;
    color: #d4af37;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .hub-grid { grid-template-columns: 1fr; }
    .hub-header { padding: 1rem; }
    .hub-card { padding: 14px 16px; }
}

/* ═══════════════════════════════════════════════════
   Master Lead Search
   ═══════════════════════════════════════════════════ */

.hub-header-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}

.hub-search-wrap {
    position: relative;
}

.hub-search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.hub-search-icon {
    position: absolute;
    left: 14px;
    font-size: 18px;
    color: #d4af37;
    pointer-events: none;
    z-index: 1;
}

.hub-search-input {
    width: 100%;
    padding: 10px 44px 10px 42px;
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 0.5rem;
    background: var(--bs-body-bg, #fff);
    color: var(--bs-body-color, #111827);
    font-size: 0.84rem;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.hub-search-input:focus {
    border-color: #d4af37;
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.12);
}

.hub-search-input::placeholder {
    color: var(--bs-surface-400, #9ca3af);
}

.hub-search-spinner {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #d4af37;
}

/* ── Search Results Dropdown ── */
.hub-search-results {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    background: var(--bs-card-bg, #fff);
    border: 1px solid rgba(212, 175, 55, 0.2);
    border-radius: 0.55rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    z-index: 1060;
    max-height: 420px;
    overflow-y: auto;
    overflow-x: hidden;
}

.hub-search-empty {
    padding: 20px;
    text-align: center;
    font-size: 0.82rem;
    color: var(--bs-surface-500, #6b7280);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.hub-search-empty i { font-size: 18px; color: #d4af37; }

/* ── Result Item ── */
.hub-sr-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    text-decoration: none !important;
    color: inherit !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: background 0.15s ease;
    cursor: pointer;
}

.hub-sr-item:last-child { border-bottom: none; }

.hub-sr-item:hover {
    background: rgba(212, 175, 55, 0.06);
}

.hub-sr-icon {
    width: 36px;
    height: 36px;
    border-radius: 0.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: rgba(212, 175, 55, 0.1);
    color: #d4af37;
    font-size: 18px;
}

.hub-sr-body {
    flex: 1;
    min-width: 0;
}

.hub-sr-name {
    font-weight: 600;
    font-size: 0.86rem;
    color: var(--bs-body-color, #111827);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.hub-sr-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 3px;
}

.hub-sr-meta span {
    font-size: 0.7rem;
    color: var(--bs-surface-500, #6b7280);
    display: flex;
    align-items: center;
    gap: 2px;
}

.hub-sr-meta i { font-size: 12px; }

.hub-sr-stage {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

.hub-sr-stage .badge {
    font-size: 0.65rem;
    font-weight: 600;
    letter-spacing: 0.2px;
    padding: 4px 8px;
    white-space: nowrap;
}

.hub-sr-arrow {
    color: #d4af37;
    font-size: 16px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.hub-sr-item:hover .hub-sr-arrow { opacity: 1; }

@media (max-width: 576px) {
    .hub-sr-item { flex-wrap: wrap; }
    .hub-sr-stage { width: 100%; justify-content: flex-end; }
}
