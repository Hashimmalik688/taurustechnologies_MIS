<div class="wbs-item" style="margin-left: {{ $level * 20 }}px;">
    <div class="d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
            <h6 class="text-white mb-1">
                <span class="badge bg-primary me-2">{{ $item->code }}</span>
                {{ $item->name }}
            </h6>
            @if($item->description)
                <p class="text-muted-dark mb-2 small">{{ $item->description }}</p>
            @endif
            <div class="d-flex gap-3 flex-wrap">
                <small class="text-muted-dark">
                    <i class="bx bx-layer me-1"></i>{{ ucfirst(str_replace('_', ' ', $item->level)) }}
                </small>
                @if($item->estimated_hours)
                    <small class="text-muted-dark">
                        <i class="bx bx-time me-1"></i>{{ $item->estimated_hours }}h
                    </small>
                @endif
                @if($item->estimated_cost)
                    <small class="text-muted-dark">
                        <i class="bx bx-dollar me-1"></i>{{ number_format($item->estimated_cost, 2) }}
                    </small>
                @endif
            </div>
        </div>
    </div>
</div>

@if($item->children->count() > 0)
    <div class="wbs-children">
        @foreach($item->children as $child)
            @include('admin.epms.partials.wbs-item', ['item' => $child, 'level' => $level + 1])
        @endforeach
    </div>
@endif
