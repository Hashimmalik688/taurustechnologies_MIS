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
    /* Sales Flow Navigation Styles - Compact */
    .sales-flow-nav {
        background: rgba(212,175,55,.04);
        border: 1px solid rgba(212,175,55,.12);
        border-radius: 8px;
        padding: 0.6rem 1rem;
        margin-bottom: 1rem;
    }
    
    .sales-flow-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        max-width: 100%;
        overflow-x: auto;
    }
    
    .sales-flow-stage {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.15s ease;
        padding: 0.3rem 0.65rem;
        border-radius: 6px;
        background: transparent;
    }
    
    .sales-flow-stage-circle {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        transition: all 0.15s ease;
        background: #f1f5f9;
        border: 1.5px solid #e2e8f0;
        color: #94a3b8;
        flex-shrink: 0;
    }
    
    .sales-flow-stage-name {
        font-size: 0.7rem;
        font-weight: 500;
        color: #64748b;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    
    /* Current/Active Stage */
    .sales-flow-stage.active .sales-flow-stage-circle {
        background: linear-gradient(135deg, #d4af37 0%, #e8c84a 100%);
        border-color: #d4af37;
        color: #1e293b;
        box-shadow: 0 2px 6px rgba(212,175,55,.25);
    }
    
    .sales-flow-stage.active .sales-flow-stage-name {
        color: #1e293b;
        font-weight: 600;
    }
    
    .sales-flow-stage.active {
        background: rgba(212,175,55,.08);
    }
    
    /* Completed Stages (before current) */
    .sales-flow-stage.completed .sales-flow-stage-circle {
        background: #10b981;
        border-color: #10b981;
        color: #fff;
    }
    
    .sales-flow-stage.completed .sales-flow-stage-name {
        color: #059669;
    }
    
    /* Hover effects for clickable stages */
    .sales-flow-stage:not(.future):hover {
        background: rgba(212,175,55,.06);
    }
    
    .sales-flow-stage:not(.future):hover .sales-flow-stage-circle {
        transform: scale(1.1);
    }
    
    /* Future stages (after current) */
    .sales-flow-stage.future {
        cursor: not-allowed;
        opacity: 0.5;
    }
    
    .sales-flow-stage.future .sales-flow-stage-circle {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #cbd5e1;
    }
    
    /* Connector arrow */
    .sales-flow-connector {
        color: #cbd5e1;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    
    .sales-flow-connector.completed {
        color: #10b981;
    }
    
    .sales-flow-connector.active {
        color: #d4af37;
    }
    
    @media (max-width: 768px) {
        .sales-flow-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            justify-content: flex-start;
            padding-bottom: 0.25rem;
        }
        
        .sales-flow-nav {
            padding: 0.5rem 0.75rem;
        }
        
        .sales-flow-stage-name {
            font-size: 0.65rem;
        }
        
        .sales-flow-stage-circle {
            width: 24px;
            height: 24px;
            font-size: 0.75rem;
        }
    }
</style>

<div class="sales-flow-nav">
    <div class="sales-flow-container">
        @foreach($stages as $index => $stage)
            @php
                $isActive = $stage['key'] === $currentStage;
                $isCompleted = $index < $currentIndex;
                $isFuture = $index > $currentIndex;
                $routeExists = Route::has($stage['route']);
                
                $stageClass = 'sales-flow-stage';
                if ($isActive) {
                    $stageClass .= ' active';
                } elseif ($isCompleted) {
                    $stageClass .= ' completed';
                } elseif ($isFuture) {
                    $stageClass .= ' future';
                }
            @endphp
            
            @if($index > 0)
                <i class="mdi mdi-chevron-right sales-flow-connector {{ $isCompleted ? 'completed' : ($isActive ? 'active' : '') }}"></i>
            @endif
            
            @if($routeExists && !$isFuture)
                <a href="{{ route($stage['route']) }}" class="{{ $stageClass }}">
                    <div class="sales-flow-stage-circle">
                        <i class="mdi {{ $stage['icon'] }}"></i>
                    </div>
                    <div class="sales-flow-stage-name">{{ $stage['name'] }}</div>
                </a>
            @else
                <div class="{{ $stageClass }}">
                    <div class="sales-flow-stage-circle">
                        <i class="mdi {{ $stage['icon'] }}"></i>
                    </div>
                    <div class="sales-flow-stage-name">{{ $stage['name'] }}</div>
                </div>
            @endif
        @endforeach
    </div>
</div>
