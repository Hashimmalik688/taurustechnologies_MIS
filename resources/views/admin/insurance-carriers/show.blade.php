<div class="row">
    <div class="col-md-6">
        <h6 class="text-primary">Basic Information</h6>
        <table class="table table-sm">
            <tr>
                <th width="40%">Name:</th>
                <td><strong>{{ $insuranceCarrier->name }}</strong></td>
            </tr>
            <tr>
                <th>Payment Module:</th>
                <td>
                    <span class="badge bg-info">
                        {{ ucwords(str_replace('_', ' ', $insuranceCarrier->payment_module)) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Phone:</th>
                <td>{{ $insuranceCarrier->phone ?: 'Not provided' }}</td>
            </tr>
            <tr>
                <th>SSN Last 4:</th>
                <td>{{ $insuranceCarrier->ssn_last4 ? '***' . $insuranceCarrier->ssn_last4 : 'Not provided' }}</td>
            </tr>
            <tr>
                <th>Status:</th>
                <td>
                    @if($insuranceCarrier->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="text-primary">Commission Details</h6>
        @if($insuranceCarrier->commissionBrackets && $insuranceCarrier->commissionBrackets->count() > 0)
            <div class="d-flex flex-column gap-1">
                @foreach($insuranceCarrier->commissionBrackets as $bracket)
                    <div>
                        <span class="badge bg-primary">
                            Ages {{ $bracket->age_min }}-{{ $bracket->age_max }}: {{ number_format($bracket->commission_percentage, 2) }}%
                        </span>
                        @if($bracket->notes)
                            <small class="text-muted d-block">{{ $bracket->notes }}</small>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif($insuranceCarrier->base_commission_percentage)
            <span class="badge bg-secondary">
                {{ number_format($insuranceCarrier->base_commission_percentage, 2) }}% (All Ages)
            </span>
        @else
            <span class="text-muted">No commission structure defined</span>
        @endif
        
        @if($insuranceCarrier->age_min || $insuranceCarrier->age_max)
            <div class="mt-2">
                <small class="text-muted">
                    Age Range: {{ $insuranceCarrier->age_min ?: '0' }} - {{ $insuranceCarrier->age_max ?: '∞' }}
                </small>
            </div>
        @endif
    </div>
</div>

@if($insuranceCarrier->plan_types)
    <div class="row mt-3">
        <div class="col-12">
            <h6 class="text-primary">Plan Types</h6>
            <div class="d-flex flex-wrap gap-1">
                @foreach($insuranceCarrier->plan_types as $planType)
                    <span class="badge bg-light text-dark">{{ $planType }}</span>
                @endforeach
            </div>
        </div>
    </div>
@endif

@if($insuranceCarrier->calculation_notes)
    <div class="row mt-3">
        <div class="col-12">
            <h6 class="text-primary">Calculation Notes</h6>
            <div class="alert alert-info">
                {{ $insuranceCarrier->calculation_notes }}
            </div>
        </div>
    </div>
@endif

<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Created: {{ $insuranceCarrier->created_at->format('M d, Y') }}
                @if($insuranceCarrier->updated_at != $insuranceCarrier->created_at)
                    | Updated: {{ $insuranceCarrier->updated_at->format('M d, Y') }}
                @endif
            </small>
            <div>
                <a href="{{ route('admin.insurance-carriers.edit', $insuranceCarrier) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="mdi mdi-pencil"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>