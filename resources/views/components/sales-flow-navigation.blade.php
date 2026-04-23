@props(['currentStage'])

@php
    $stages = [
        [
            'name' => 'Sales Records',
            'short' => 'SR',
            'route' => 'sales.index',
            'key' => 'sales'
        ],
        [
            'name' => 'Pending Submissions',
            'short' => 'PS',
            'route' => 'submissions.index',
            'key' => 'submissions'
        ],
        [
            'name' => 'Pending Contracts',
            'short' => 'PC',
            'route' => 'issuance.index',
            'key' => 'contracts'
        ],
        [
            'name' => 'Pending Draft',
            'short' => 'PD',
            'route' => 'pending-draft.index',
            'key' => 'draft'
        ],
        [
            'name' => 'Paid Sales',
            'short' => 'Paid',
            'route' => 'paid-sales.index',
            'key' => 'paid'
        ],
    ];

    $currentIndex = array_search($currentStage, array_column($stages, 'key'));
@endphp

<style>
    .sales-flow-strip {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 0;
        margin-bottom: 0.75rem;
        font-size: 0.68rem;
    }
    
    .sales-flow-item {
        display: inline-flex;
        align-items: center;
        padding: 0.3rem 0.55rem;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.15s;
        background: rgba(0,0,0,.02);
        border: 1px solid rgba(0,0,0,.06);
        color: #64748b;
        cursor: pointer;
    }
    
    .sales-flow-item:hover {
        background: rgba(0,0,0,.04);
        border-color: rgba(212,175,55,.3);
        color: #1e293b;
    }
    
    .sales-flow-item.active {
        background: #d4af37;
        color: #1e293b;
        border-color: #d4af37;
        font-weight: 600;
    }
    
    .sales-flow-item.completed {
        background: #10b981;
        color: #fff;
        border-color: #10b981;
    }
    
    .sales-flow-item.future {
        background: transparent;
        color: #94a3b8;
        border-color: rgba(0,0,0,.04);
    }
    
    .sales-flow-sep {
        color: #cbd5e1;
        font-size: 0.7rem;
        user-select: none;
    }
    
    @media (max-width: 768px) {
        .sales-flow-strip {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .sales-flow-item {
            font-size: 0.65rem;
            padding: 0.25rem 0.45rem;
            white-space: nowrap;
        }
    }
    
    [data-bs-theme=dark] .sales-flow-item,
    :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .sales-flow-item {
        background: rgba(30,41,59,0.4);
        border-color: rgba(255,255,255,.08);
    }
</style>

<div class="sales-flow-strip">
    @foreach($stages as $index => $stage)
        @php
            $isActive = $stage['key'] === $currentStage;
            $isCompleted = $index < $currentIndex;
            $isFuture = $index > $currentIndex;
            $routeExists = Route::has($stage['route']);
            
            $itemClass = 'sales-flow-item';
            if ($isActive) $itemClass .= ' active';
            elseif ($isCompleted) $itemClass .= ' completed';
            elseif ($isFuture) $itemClass .= ' future';
        @endphp
        
        @if($index > 0)
            <span class="sales-flow-sep">›</span>
        @endif
        
        @if($routeExists)
            <a href="{{ route($stage['route']) }}" class="{{ $itemClass }}" title="{{ $stage['name'] }}">
                {{ $stage['short'] }}
            </a>
        @else
            <span class="{{ $itemClass }}" title="{{ $stage['name'] }}">
                {{ $stage['short'] }}
            </span>
        @endif
    @endforeach
</div>
