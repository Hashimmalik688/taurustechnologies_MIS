{{--
    Carrier filter pill bar — include on any partner page.
    Expects: $activeCarriers (Collection), $carrierId (nullable int)
    Preserves all other query params (period, month, etc.)
--}}
@if($activeCarriers->count() > 1)
<div class="pp-carrier-filter">
    @php
        $allUrl  = request()->fullUrlWithQuery(['carrier_id' => null]);
        $isAll   = !$carrierId;
    @endphp
    <span class="pp-cf-label"><i class="bx bx-filter-alt"></i> Carrier</span>
    <a href="{{ $allUrl }}"
       class="pp-cf-pill {{ $isAll ? 'pp-cf-active' : '' }}">
        All
    </a>
    @foreach($activeCarriers as $c)
    @php $url = request()->fullUrlWithQuery(['carrier_id' => $c['id']]); @endphp
    <a href="{{ $url }}"
       class="pp-cf-pill {{ $carrierId == $c['id'] ? 'pp-cf-active' : '' }}">
        {{ $c['name'] }}
    </a>
    @endforeach
</div>
@endif
