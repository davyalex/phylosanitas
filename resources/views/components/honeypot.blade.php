{{--
    Protection anti-robots, à placer dans chaque formulaire public (voir BlockSpamBots).
    Usage : <x-honeypot />
--}}
@php
    $hpId = 'hp-' . \Illuminate\Support\Str::random(6);
@endphp
<div style="position:absolute!important;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden" aria-hidden="true">
    <label for="{{ $hpId }}">Laissez ce champ vide</label>
    <input type="text" id="{{ $hpId }}" name="{{ \App\Http\Middleware\BlockSpamBots::FIELD_TRAP }}" value="" tabindex="-1" autocomplete="off">
</div>
<input type="hidden" name="{{ \App\Http\Middleware\BlockSpamBots::FIELD_TIME }}" value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) time()) }}">
