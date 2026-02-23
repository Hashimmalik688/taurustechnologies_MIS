@extends('layouts.master')

@section('title')
    Theme Settings
@endsection

@section('css')
<style>
    .theme-page { max-width: 960px; margin: 0 auto; padding: 1.5rem; }
    .theme-page-header { margin-bottom: 2rem; }
    .theme-page-header h4 { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: .5rem; margin-bottom: .25rem; }
    .theme-page-header h4 i { color: var(--gold, #d4af37); font-size: 1.3rem; }
    .theme-page-header p { font-size: .78rem; color: var(--text-secondary); margin: 0; }

    .theme-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem; }

    .theme-card {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        cursor: pointer;
        transition: all .3s cubic-bezier(.4,0,.2,1);
        border: 2px solid var(--border-color, #e5e7eb);
        background: var(--bg-card, var(--bg-primary, #fff));
    }
    .theme-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,.15);
    }
    .theme-card.active {
        border-color: var(--gold, #d4af37);
        box-shadow: 0 0 0 3px rgba(var(--accent-rgb, 212,175,55),.2), 0 12px 32px rgba(0,0,0,.12);
    }
    .theme-card.active .theme-check {
        opacity: 1;
        transform: scale(1);
    }

    .theme-check {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--gold, #d4af37);
        color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        opacity: 0;
        transform: scale(.5);
        transition: all .3s cubic-bezier(.4,0,.2,1);
        z-index: 5;
        box-shadow: 0 2px 8px rgba(0,0,0,.2);
    }

    /* Preview area */
    .theme-preview {
        height: 140px;
        position: relative;
        overflow: hidden;
        display: flex;
    }
    .theme-preview-sidebar {
        width: 50px;
        display: flex;
        flex-direction: column;
        padding: 8px 6px;
        gap: 5px;
    }
    .theme-preview-sidebar .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        opacity: .6;
    }
    .theme-preview-sidebar .dot.active { opacity: 1; }
    .theme-preview-sidebar .line {
        height: 4px;
        border-radius: 2px;
        opacity: .3;
        margin: 1px 0;
    }
    .theme-preview-main {
        flex: 1;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .theme-preview-header {
        height: 16px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 0 6px;
        gap: 4px;
    }
    .theme-preview-header .h-dot { width: 6px; height: 6px; border-radius: 50%; }
    .theme-preview-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        flex: 1;
    }
    .theme-preview-card {
        border-radius: 6px;
        padding: 6px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .theme-preview-card .bar {
        height: 3px;
        border-radius: 2px;
    }
    .theme-preview-card .bar.w60 { width: 60%; }
    .theme-preview-card .bar.w40 { width: 40%; }
    .theme-preview-card .bar.w80 { width: 80%; }

    .theme-info {
        padding: .85rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .theme-info-left h6 {
        font-size: .82rem;
        font-weight: 700;
        margin: 0 0 .1rem;
        color: var(--text-primary);
    }
    .theme-info-left span {
        font-size: .68rem;
        color: var(--text-muted);
    }
    .theme-apply-btn {
        font-size: .7rem;
        font-weight: 600;
        padding: .3rem .65rem;
        border-radius: 8px;
        border: 1.5px solid var(--border-color, #e5e7eb);
        background: transparent;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all .2s;
    }
    .theme-card.active .theme-apply-btn {
        border-color: var(--gold, #d4af37);
        color: var(--gold, #d4af37);
        background: rgba(var(--accent-rgb, 212,175,55),.08);
    }
    .theme-apply-btn:hover {
        border-color: var(--gold, #d4af37);
        color: var(--gold, #d4af37);
    }

    .theme-note {
        margin-top: 1.5rem;
        padding: .6rem .85rem;
        border-radius: 10px;
        background: rgba(var(--accent-rgb, 212,175,55),.04);
        border: 1px solid rgba(var(--accent-rgb, 212,175,55),.1);
        font-size: .72rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .theme-note i { color: var(--gold, #d4af37); font-size: .9rem; }
</style>
@endsection

@section('content')
<div class="theme-page">
    <div class="theme-page-header">
        <h4><i class="bx bx-palette"></i> Theme Settings</h4>
        <p>Choose a visual theme for your CRM experience. Your selection is saved locally.</p>
    </div>

    <div class="theme-grid" id="themeGrid">

        {{-- LIGHT --}}
        <div class="theme-card" data-theme-value="light" onclick="applyTheme('light')">
            <div class="theme-check"><i class="bx bx-check"></i></div>
            <div class="theme-preview" style="background:#f8f9fa">
                <div class="theme-preview-sidebar" style="background:#ffffff;border-right:1px solid #e5e7eb">
                    <div class="dot active" style="background:#d4af37"></div>
                    <div class="line" style="background:#6b7280;width:80%"></div>
                    <div class="line" style="background:#6b7280;width:65%"></div>
                    <div class="line" style="background:#d4af37;width:75%"></div>
                    <div class="line" style="background:#6b7280;width:60%"></div>
                </div>
                <div class="theme-preview-main">
                    <div class="theme-preview-header" style="background:#ffffff;border:1px solid #e5e7eb">
                        <div class="h-dot" style="background:#d4af37"></div>
                        <div class="h-dot" style="background:#9ca3af"></div>
                    </div>
                    <div class="theme-preview-cards">
                        <div class="theme-preview-card" style="background:#ffffff;border:1px solid #e5e7eb">
                            <div class="bar w60" style="background:#1f2937"></div>
                            <div class="bar w40" style="background:#d4af37"></div>
                            <div class="bar w80" style="background:#e5e7eb"></div>
                        </div>
                        <div class="theme-preview-card" style="background:#ffffff;border:1px solid #e5e7eb">
                            <div class="bar w80" style="background:#1f2937"></div>
                            <div class="bar w60" style="background:#e5e7eb"></div>
                            <div class="bar w40" style="background:#d4af37"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-info-left">
                    <h6>Classic Light</h6>
                    <span>Clean gold on white</span>
                </div>
                <button class="theme-apply-btn">Apply</button>
            </div>
        </div>

        {{-- DARK --}}
        <div class="theme-card" data-theme-value="dark" onclick="applyTheme('dark')">
            <div class="theme-check"><i class="bx bx-check"></i></div>
            <div class="theme-preview" style="background:#0f0f0f">
                <div class="theme-preview-sidebar" style="background:#1a1a1a;border-right:1px solid #333">
                    <div class="dot active" style="background:#d4af37"></div>
                    <div class="line" style="background:#b0b0b0;width:80%"></div>
                    <div class="line" style="background:#b0b0b0;width:65%"></div>
                    <div class="line" style="background:#d4af37;width:75%"></div>
                    <div class="line" style="background:#b0b0b0;width:60%"></div>
                </div>
                <div class="theme-preview-main">
                    <div class="theme-preview-header" style="background:#1a1a1a;border:1px solid #333">
                        <div class="h-dot" style="background:#d4af37"></div>
                        <div class="h-dot" style="background:#737373"></div>
                    </div>
                    <div class="theme-preview-cards">
                        <div class="theme-preview-card" style="background:#1f1f1f;border:1px solid #333">
                            <div class="bar w60" style="background:#e5e5e5"></div>
                            <div class="bar w40" style="background:#d4af37"></div>
                            <div class="bar w80" style="background:#333"></div>
                        </div>
                        <div class="theme-preview-card" style="background:#1f1f1f;border:1px solid #333">
                            <div class="bar w80" style="background:#e5e5e5"></div>
                            <div class="bar w60" style="background:#333"></div>
                            <div class="bar w40" style="background:#d4af37"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-info-left">
                    <h6>Classic Dark</h6>
                    <span>Gold accent on dark gray</span>
                </div>
                <button class="theme-apply-btn">Apply</button>
            </div>
        </div>

        {{-- EMERALD GLASS --}}
        <div class="theme-card" data-theme-value="emerald-glass" onclick="applyTheme('emerald-glass')">
            <div class="theme-check"><i class="bx bx-check"></i></div>
            <div class="theme-preview" style="background:#061209">
                <div class="theme-preview-sidebar" style="background:rgba(10,31,20,.92);border-right:1px solid rgba(0,230,118,.15)">
                    <div class="dot active" style="background:#00e676"></div>
                    <div class="line" style="background:#a5d6b7;width:80%"></div>
                    <div class="line" style="background:#a5d6b7;width:65%"></div>
                    <div class="line" style="background:#00e676;width:75%"></div>
                    <div class="line" style="background:#a5d6b7;width:60%"></div>
                </div>
                <div class="theme-preview-main">
                    <div class="theme-preview-header" style="background:rgba(10,31,20,.85);border:1px solid rgba(0,230,118,.12)">
                        <div class="h-dot" style="background:#00e676"></div>
                        <div class="h-dot" style="background:#6da882"></div>
                    </div>
                    <div class="theme-preview-cards">
                        <div class="theme-preview-card" style="background:rgba(10,31,20,.8);border:1px solid rgba(0,230,118,.1);backdrop-filter:blur(4px)">
                            <div class="bar w60" style="background:#e0f2e9"></div>
                            <div class="bar w40" style="background:#00e676"></div>
                            <div class="bar w80" style="background:rgba(0,230,118,.15)"></div>
                        </div>
                        <div class="theme-preview-card" style="background:rgba(10,31,20,.8);border:1px solid rgba(0,230,118,.1)">
                            <div class="bar w80" style="background:#e0f2e9"></div>
                            <div class="bar w60" style="background:rgba(0,230,118,.15)"></div>
                            <div class="bar w40" style="background:#00e676"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-info-left">
                    <h6>Emerald Glass</h6>
                    <span>Glass-morphism with lime green</span>
                </div>
                <button class="theme-apply-btn">Apply</button>
            </div>
        </div>

        {{-- MIDNIGHT BLACK --}}
        <div class="theme-card" data-theme-value="midnight-black" onclick="applyTheme('midnight-black')">
            <div class="theme-check"><i class="bx bx-check"></i></div>
            <div class="theme-preview" style="background:#000000">
                <div class="theme-preview-sidebar" style="background:#050505;border-right:1px solid rgba(255,255,255,.06)">
                    <div class="dot active" style="background:#e0e0e0"></div>
                    <div class="line" style="background:#666;width:80%"></div>
                    <div class="line" style="background:#666;width:65%"></div>
                    <div class="line" style="background:#e0e0e0;width:75%"></div>
                    <div class="line" style="background:#666;width:60%"></div>
                </div>
                <div class="theme-preview-main">
                    <div class="theme-preview-header" style="background:#050505;border:1px solid rgba(255,255,255,.06)">
                        <div class="h-dot" style="background:#e0e0e0"></div>
                        <div class="h-dot" style="background:#666"></div>
                    </div>
                    <div class="theme-preview-cards">
                        <div class="theme-preview-card" style="background:#0f0f0f;border:1px solid rgba(255,255,255,.06)">
                            <div class="bar w60" style="background:#f5f5f5"></div>
                            <div class="bar w40" style="background:#e0e0e0"></div>
                            <div class="bar w80" style="background:rgba(255,255,255,.08)"></div>
                        </div>
                        <div class="theme-preview-card" style="background:#0f0f0f;border:1px solid rgba(255,255,255,.06)">
                            <div class="bar w80" style="background:#f5f5f5"></div>
                            <div class="bar w60" style="background:rgba(255,255,255,.08)"></div>
                            <div class="bar w40" style="background:#e0e0e0"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-info-left">
                    <h6>Midnight Black</h6>
                    <span>Pure OLED-black, minimal silver</span>
                </div>
                <button class="theme-apply-btn">Apply</button>
            </div>
        </div>

        {{-- OCEAN BLUE --}}
        <div class="theme-card" data-theme-value="ocean-blue" onclick="applyTheme('ocean-blue')">
            <div class="theme-check"><i class="bx bx-check"></i></div>
            <div class="theme-preview" style="background:linear-gradient(180deg, #060e1a, #0a1628)">
                <div class="theme-preview-sidebar" style="background:#0a1628;border-right:1px solid rgba(66,165,245,.15)">
                    <div class="dot active" style="background:#42a5f5"></div>
                    <div class="line" style="background:#5c8fcc;width:80%"></div>
                    <div class="line" style="background:#5c8fcc;width:65%"></div>
                    <div class="line" style="background:#42a5f5;width:75%"></div>
                    <div class="line" style="background:#5c8fcc;width:60%"></div>
                </div>
                <div class="theme-preview-main">
                    <div class="theme-preview-header" style="background:#0a1628;border:1px solid rgba(66,165,245,.12)">
                        <div class="h-dot" style="background:#42a5f5"></div>
                        <div class="h-dot" style="background:#5c8fcc"></div>
                    </div>
                    <div class="theme-preview-cards">
                        <div class="theme-preview-card" style="background:#0d1b30;border:1px solid rgba(66,165,245,.15)">
                            <div class="bar w60" style="background:#e3f2fd"></div>
                            <div class="bar w40" style="background:#42a5f5"></div>
                            <div class="bar w80" style="background:rgba(66,165,245,.15)"></div>
                        </div>
                        <div class="theme-preview-card" style="background:#0d1b30;border:1px solid rgba(66,165,245,.15)">
                            <div class="bar w80" style="background:#e3f2fd"></div>
                            <div class="bar w60" style="background:rgba(66,165,245,.15)"></div>
                            <div class="bar w40" style="background:#42a5f5"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-info-left">
                    <h6>Ocean Blue</h6>
                    <span>Deep sea professional</span>
                </div>
                <button class="theme-apply-btn">Apply</button>
            </div>
        </div>

        {{-- ROYAL PURPLE --}}
        <div class="theme-card" data-theme-value="royal-purple" onclick="applyTheme('royal-purple')">
            <div class="theme-check"><i class="bx bx-check"></i></div>
            <div class="theme-preview" style="background:#0d061a">
                <div class="theme-preview-sidebar" style="background:#150a28;border-right:1px solid rgba(179,136,255,.15)">
                    <div class="dot active" style="background:#b388ff"></div>
                    <div class="line" style="background:#8e5cac;width:80%"></div>
                    <div class="line" style="background:#8e5cac;width:65%"></div>
                    <div class="line" style="background:#b388ff;width:75%"></div>
                    <div class="line" style="background:#8e5cac;width:60%"></div>
                </div>
                <div class="theme-preview-main">
                    <div class="theme-preview-header" style="background:#150a28;border:1px solid rgba(179,136,255,.12)">
                        <div class="h-dot" style="background:#b388ff"></div>
                        <div class="h-dot" style="background:#8e5cac"></div>
                    </div>
                    <div class="theme-preview-cards">
                        <div class="theme-preview-card" style="background:#1a0d30;border:1px solid rgba(179,136,255,.15)">
                            <div class="bar w60" style="background:#f3e5f5"></div>
                            <div class="bar w40" style="background:#b388ff"></div>
                            <div class="bar w80" style="background:rgba(179,136,255,.15)"></div>
                        </div>
                        <div class="theme-preview-card" style="background:#1a0d30;border:1px solid rgba(179,136,255,.15)">
                            <div class="bar w80" style="background:#f3e5f5"></div>
                            <div class="bar w60" style="background:rgba(179,136,255,.15)"></div>
                            <div class="bar w40" style="background:#b388ff"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-info-left">
                    <h6>Royal Purple</h6>
                    <span>Rich violet premium</span>
                </div>
                <button class="theme-apply-btn">Apply</button>
            </div>
        </div>

        {{-- ROSE GOLD --}}
        <div class="theme-card" data-theme-value="rose-gold" onclick="applyTheme('rose-gold')">
            <div class="theme-check"><i class="bx bx-check"></i></div>
            <div class="theme-preview" style="background:#12080c">
                <div class="theme-preview-sidebar" style="background:#1a0f14;border-right:1px solid rgba(244,143,177,.15)">
                    <div class="dot active" style="background:#f48fb1"></div>
                    <div class="line" style="background:#a05672;width:80%"></div>
                    <div class="line" style="background:#a05672;width:65%"></div>
                    <div class="line" style="background:#f48fb1;width:75%"></div>
                    <div class="line" style="background:#a05672;width:60%"></div>
                </div>
                <div class="theme-preview-main">
                    <div class="theme-preview-header" style="background:#1a0f14;border:1px solid rgba(244,143,177,.12)">
                        <div class="h-dot" style="background:#f48fb1"></div>
                        <div class="h-dot" style="background:#a05672"></div>
                    </div>
                    <div class="theme-preview-cards">
                        <div class="theme-preview-card" style="background:#1f1018;border:1px solid rgba(244,143,177,.15)">
                            <div class="bar w60" style="background:#fce4ec"></div>
                            <div class="bar w40" style="background:#f48fb1"></div>
                            <div class="bar w80" style="background:rgba(244,143,177,.15)"></div>
                        </div>
                        <div class="theme-preview-card" style="background:#1f1018;border:1px solid rgba(244,143,177,.15)">
                            <div class="bar w80" style="background:#fce4ec"></div>
                            <div class="bar w60" style="background:rgba(244,143,177,.15)"></div>
                            <div class="bar w40" style="background:#f48fb1"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-info-left">
                    <h6>Rose Gold</h6>
                    <span>Warm elegant pink</span>
                </div>
                <button class="theme-apply-btn">Apply</button>
            </div>
        </div>

        {{-- COPPER STEEL --}}
        <div class="theme-card" data-theme-value="copper-steel" onclick="applyTheme('copper-steel')">
            <div class="theme-check"><i class="bx bx-check"></i></div>
            <div class="theme-preview" style="background:#110e0b">
                <div class="theme-preview-sidebar" style="background:#1a1612;border-right:1px solid rgba(255,171,64,.15)">
                    <div class="dot active" style="background:#ffab40"></div>
                    <div class="line" style="background:#8a7d6e;width:80%"></div>
                    <div class="line" style="background:#8a7d6e;width:65%"></div>
                    <div class="line" style="background:#ffab40;width:75%"></div>
                    <div class="line" style="background:#8a7d6e;width:60%"></div>
                </div>
                <div class="theme-preview-main">
                    <div class="theme-preview-header" style="background:#1a1612;border:1px solid rgba(255,171,64,.12)">
                        <div class="h-dot" style="background:#ffab40"></div>
                        <div class="h-dot" style="background:#8a7d6e"></div>
                    </div>
                    <div class="theme-preview-cards">
                        <div class="theme-preview-card" style="background:#1f1a14;border:1px solid rgba(255,171,64,.15)">
                            <div class="bar w60" style="background:#efebe7"></div>
                            <div class="bar w40" style="background:#ffab40"></div>
                            <div class="bar w80" style="background:rgba(255,171,64,.15)"></div>
                        </div>
                        <div class="theme-preview-card" style="background:#1f1a14;border:1px solid rgba(255,171,64,.15)">
                            <div class="bar w80" style="background:#efebe7"></div>
                            <div class="bar w60" style="background:rgba(255,171,64,.15)"></div>
                            <div class="bar w40" style="background:#ffab40"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-info-left">
                    <h6>Copper Steel</h6>
                    <span>Industrial warm amber</span>
                </div>
                <button class="theme-apply-btn">Apply</button>
            </div>
        </div>

    </div>

    <div class="theme-note">
        <i class="bx bx-info-circle"></i>
        Theme preference is saved in your browser. It persists across sessions but is specific to this device.
    </div>
</div>
@endsection

@section('script')
<script>
    // List of all custom themes (non-light/dark)
    const customThemes = ['emerald-glass', 'midnight-black', 'ocean-blue', 'royal-purple', 'rose-gold', 'copper-steel'];

    function applyTheme(themeName) {
        const html = document.documentElement;

        // Set the data-theme attribute
        html.setAttribute('data-theme', themeName);
        localStorage.setItem('theme', themeName);

        // Update the header theme icon
        const themeIcon = document.getElementById('themeIcon');
        if (themeIcon) {
            if (themeName === 'light') {
                themeIcon.classList.remove('bx-sun');
                themeIcon.classList.add('bx-moon');
            } else {
                themeIcon.classList.remove('bx-moon');
                themeIcon.classList.add('bx-sun');
            }
        }

        // Update active card
        document.querySelectorAll('.theme-card').forEach(card => {
            card.classList.toggle('active', card.dataset.themeValue === themeName);
        });

        // Refresh theme colors bridge
        setTimeout(refreshThemeColors, 100);
    }

    function refreshThemeColors() {
        var s = getComputedStyle(document.documentElement);
        var g = function(v) { return s.getPropertyValue('--bs-' + v).trim() || s.getPropertyValue('--' + v).trim(); };
        window.themeColors = {
            gold: g('gold'), goldDark: g('gold-dark'), goldLight: g('gold-light'), goldBright: g('gold-bright'),
            success: g('ui-success') || g('success'), successDark: g('ui-success-dark'),
            danger: g('ui-danger') || g('danger'), dangerDark: g('ui-danger-dark'),
            warning: g('ui-warning') || g('warning'),
            info: g('ui-info') || g('info'), infoDark: g('ui-info-dark'),
            purple: g('ui-purple'), indigo: g('ui-indigo'),
            gradientStart: g('gradient-start'), gradientEnd: g('gradient-end'),
            chartPrimary: g('chart-primary'), chartSuccess: g('chart-success'),
            chartWarning: g('chart-warning'), chartDanger: g('chart-danger'),
            chartInfo: g('chart-info'), chartMuted: g('chart-muted'),
        };
    }

    // Highlight the currently active theme on page load
    document.addEventListener('DOMContentLoaded', function() {
        const current = localStorage.getItem('theme') || 'light';
        document.querySelectorAll('.theme-card').forEach(card => {
            card.classList.toggle('active', card.dataset.themeValue === current);
        });
    });
</script>
@endsection
