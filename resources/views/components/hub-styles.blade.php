<style>
    .hub-page {
        width: 100%;
    }

    .hub-header {
        margin-bottom: 2rem;
    }

    .hub-header h4 {
        font-weight: 700;
        font-size: 1.4rem;
        color: var(--text-primary, #111827);
        margin: 0 0 6px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .hub-header h4 i {
        color: var(--gold, #d4af37);
    }

    .hub-header p {
        font-size: 0.88rem;
        color: var(--text-muted, #6b7280);
        margin: 0;
    }

    .hub-section-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-muted, #6b7280);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 24px 0 10px;
        padding-left: 4px;
    }

    .hub-section-label:first-of-type {
        margin-top: 0;
    }

    .hub-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }

    .hub-card {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 20px;
        background: var(--bg-panel, #ffffff);
        border-radius: 12px;
        border: 1px solid var(--panel-border, #e6e9ee);
        text-decoration: none !important;
        color: inherit !important;
        transition: all 0.25s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .hub-card:hover {
        border-color: rgba(212, 175, 55, 0.3);
        box-shadow: 0 4px 16px rgba(212, 175, 55, 0.1);
        transform: translateY(-2px);
    }

    .hub-card:hover .hub-card-icon {
        background: linear-gradient(135deg, var(--gold, #d4af37), #b8922e);
        color: #fff;
    }

    .hub-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(212, 175, 55, 0.1);
        color: var(--gold, #d4af37);
        transition: all 0.25s ease;
    }

    .hub-card-icon i {
        font-size: 24px;
    }

    .hub-card-body {
        flex: 1;
        min-width: 0;
    }

    .hub-card-title {
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--text-primary, #111827);
        margin: 0 0 4px;
    }

    .hub-card-desc {
        font-size: 0.8rem;
        color: var(--text-muted, #6b7280);
        margin: 0;
        line-height: 1.4;
    }

    .hub-card-arrow {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted, #6b7280);
        opacity: 0;
        transition: all 0.25s ease;
    }

    .hub-card:hover .hub-card-arrow {
        opacity: 1;
        right: 12px;
        color: var(--gold, #d4af37);
    }

    @media (max-width: 768px) {
        .hub-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
