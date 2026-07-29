{{--
    Small team-origin badge, reused across report tables.
    Props: $team (string|null), $assignedPartner (string|null, only meaningful for CC_PARTNER)
--}}
@php
    $team = $team ?? null;
    $assignedPartner = $assignedPartner ?? null;
@endphp
@if($team === \App\Support\Teams::PEREGRINE)
    <span class="badge bg-purple" title="Peregrine" style="font-size:.55rem;padding:.08rem .3rem;margin-left:.2rem;vertical-align:middle">P</span>
@elseif($team === \App\Support\Teams::RAVENS)
    <span class="badge bg-dark" title="Ravens" style="font-size:.55rem;padding:.08rem .3rem;margin-left:.2rem;vertical-align:middle">R</span>
@elseif($team === \App\Support\Teams::CC_PARTNER)
    <span class="badge bg-info text-dark" title="CC Partner" style="font-size:.55rem;padding:.08rem .35rem;margin-left:.2rem;vertical-align:middle;white-space:nowrap">{{ \App\Support\Teams::label($team, $assignedPartner) }}</span>
@endif
