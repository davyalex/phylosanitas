{{--
    Illustrations santé animées (SVG inline, décoratives).
    Usage : <x-health-art name="food|move|vaccine|screening|hospital|read" />
    Les animations sont en CSS (section 27 de site.css) et s'arrêtent si l'utilisateur
    a demandé moins d'animations dans son système.
--}}
@props(['name' => 'move'])

<span {{ $attributes->merge(['class' => 'ha ha--' . $name]) }} aria-hidden="true">
@switch($name)

    {{-- Alimentation : pomme, la feuille se balance --}}
    @case('food')
        <svg viewBox="0 0 120 120" fill="none">
            <circle cx="60" cy="60" r="52" fill="#e4f5ed"/>
            <path d="M60 44c-9-7-31-8-37 12-6 21 6 45 20 49 7 2 11-2 17-2s10 4 17 2c14-4 26-28 20-49-6-20-28-19-37-12Z" fill="#f05d5e"/>
            <path d="M42 58c-3 6-3 14 0 20" stroke="#fff" stroke-opacity=".6" stroke-width="4" stroke-linecap="round"/>
            <path d="M60 44c1-8 4-13 9-16" stroke="#7a4b2a" stroke-width="3.5" stroke-linecap="round"/>
            <path class="ha-sway" d="M66 30c9-9 20-9 25-4-6 10-17 11-25 4Z" fill="#00a86b"/>
            <path class="ha-twinkle" d="M94 66v8M90 70h8" stroke="#00a86b" stroke-width="3" stroke-linecap="round"/>
        </svg>
        @break

    {{-- Activité physique : cœur qui bat + ondes --}}
    @case('move')
        <svg viewBox="0 0 120 120" fill="none">
            <circle cx="60" cy="60" r="52" fill="#e8f1fb"/>
            <circle class="ha-ring" cx="60" cy="62" r="30" stroke="#0066cc" stroke-width="2"/>
            <circle class="ha-ring ha-ring--2" cx="60" cy="62" r="30" stroke="#0066cc" stroke-width="2"/>
            <g class="ha-beat">
                <path d="M60 90C36 74 28 60 32 47c4-12 21-16 28-4 7-12 24-8 28 4 4 13-4 27-28 43Z" fill="#0066cc"/>
                <path d="M38 63h11l4-9 6 17 5-13 3 5h15" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
        </svg>
        @break

    {{-- Vaccination : la seringue se vide, bouclier de protection --}}
    @case('vaccine')
        <svg viewBox="0 0 120 120" fill="none">
            <circle cx="60" cy="60" r="52" fill="#e8f1fb"/>
            <g transform="rotate(-38 60 60)">
                <path d="M22 60H10" stroke="#3b4b62" stroke-width="3" stroke-linecap="round"/>
                <rect x="22" y="51" width="52" height="18" rx="4" fill="#fff" stroke="#3b4b62" stroke-width="3"/>
                <rect class="ha-dose" x="25" y="54" width="34" height="12" rx="2" fill="#00a86b"/>
                <path d="M34 51v6M44 51v6M54 51v6" stroke="#3b4b62" stroke-width="2" stroke-linecap="round"/>
                <g class="ha-plunger">
                    <path d="M60 60h30" stroke="#3b4b62" stroke-width="4" stroke-linecap="round"/>
                    <path d="M90 50v20" stroke="#3b4b62" stroke-width="5" stroke-linecap="round"/>
                    <rect x="58" y="53" width="5" height="14" rx="1.5" fill="#3b4b62"/>
                </g>
                <path d="M74 54v12" stroke="#3b4b62" stroke-width="3" stroke-linecap="round"/>
            </g>
            <g class="ha-pop">
                <path d="M86 70l13 5v9c0 8-6 13-13 16-7-3-13-8-13-16v-9l13-5Z" fill="#0066cc"/>
                <path d="M80 85l4 4 8-8" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
        </svg>
        @break

    {{-- Dépistage : la loupe parcourt un bilan --}}
    @case('screening')
        <svg viewBox="0 0 120 120" fill="none">
            <circle cx="60" cy="60" r="52" fill="#fff6e5"/>
            <rect x="30" y="24" width="52" height="70" rx="6" fill="#fff" stroke="#3b4b62" stroke-width="3"/>
            <rect x="46" y="19" width="20" height="10" rx="3" fill="#3b4b62"/>
            <path class="ha-check" d="M38 44l4 4 7-7" stroke="#00a86b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M55 45h18" stroke="#c9d4e0" stroke-width="3" stroke-linecap="round"/>
            <path class="ha-check ha-check--2" d="M38 60l4 4 7-7" stroke="#00a86b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M55 61h18" stroke="#c9d4e0" stroke-width="3" stroke-linecap="round"/>
            <path d="M38 77h35" stroke="#c9d4e0" stroke-width="3" stroke-linecap="round"/>
            <g class="ha-scan">
                <circle cx="76" cy="70" r="13" fill="#fff" fill-opacity=".6" stroke="#0066cc" stroke-width="4"/>
                <path d="M85 80l11 11" stroke="#0066cc" stroke-width="6" stroke-linecap="round"/>
            </g>
        </svg>
        @break

    {{-- Fin de lecture : livre ouvert, un cœur s'en échappe --}}
    @case('read')
        <svg viewBox="0 0 120 120" fill="none">
            <circle cx="60" cy="60" r="52" fill="#e4f5ed"/>
            <path d="M60 84c-10-6-24-8-36-6V44c12-2 26 0 36 6 10-6 24-8 36-6v34c-12-2-26 0-36 6Z" fill="#fff" stroke="#067a50" stroke-width="3" stroke-linejoin="round"/>
            <path d="M60 50v34" stroke="#067a50" stroke-width="3"/>
            <path d="M32 56c7-1 14 0 20 3M32 65c7-1 14 0 20 3M68 59c6-3 13-4 20-3M68 68c6-3 13-4 20-3" stroke="#b9dccb" stroke-width="2.5" stroke-linecap="round"/>
            <path class="ha-float" d="M60 40c-7-5-10-9-8-13 1-3 6-4 8 0 2-4 7-3 8 0 2 4-1 8-8 13Z" fill="#f05d5e"/>
            <path class="ha-float ha-float--2" d="M80 34v6M77 37h6" stroke="#00a86b" stroke-width="2.5" stroke-linecap="round"/>
            <path class="ha-float ha-float--3" d="M40 36v5M37.5 38.5h5" stroke="#0066cc" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        @break

    {{-- Hôpital : fenêtres qui s'allument, croix qui pulse, ambulance en route --}}
    @case('hospital')
        <svg viewBox="0 0 400 300" fill="none">
            <circle cx="200" cy="150" r="140" fill="#fff" fill-opacity=".06"/>
            <g class="ha-cloud" fill="#fff" fill-opacity=".14">
                <path d="M70 70a14 14 0 0 1 27-5 11 11 0 1 1 3 22H70a9 9 0 0 1 0-17Z"/>
            </g>
            <g class="ha-cloud ha-cloud--2" fill="#fff" fill-opacity=".1">
                <path d="M300 52a12 12 0 0 1 23-4 9 9 0 1 1 3 18h-26a7 7 0 0 1 0-14Z"/>
            </g>

            {{-- Ailes --}}
            <rect x="92" y="138" width="62" height="102" rx="6" fill="#dbe8f6"/>
            <rect x="246" y="138" width="62" height="102" rx="6" fill="#dbe8f6"/>
            {{-- Bâtiment principal --}}
            <rect x="140" y="96" width="120" height="144" rx="8" fill="#fff"/>
            <rect x="140" y="96" width="120" height="16" rx="8" fill="#cfe2f7"/>

            {{-- Croix --}}
            <g class="ha-cross">
                <circle cx="200" cy="72" r="24" fill="#fff"/>
                <path d="M193 58h14v7h7v14h-7v7h-14v-7h-7V65h7Z" fill="#e5484d"/>
            </g>

            {{-- Fenêtres --}}
            <g fill="#9cc3ea">
                <rect class="ha-win" x="156" y="124" width="18" height="16" rx="3"/>
                <rect class="ha-win ha-win--2" x="191" y="124" width="18" height="16" rx="3"/>
                <rect class="ha-win ha-win--3" x="226" y="124" width="18" height="16" rx="3"/>
                <rect class="ha-win ha-win--4" x="156" y="154" width="18" height="16" rx="3"/>
                <rect class="ha-win ha-win--5" x="226" y="154" width="18" height="16" rx="3"/>
                <rect class="ha-win ha-win--3" x="104" y="154" width="14" height="14" rx="3"/>
                <rect class="ha-win" x="128" y="154" width="14" height="14" rx="3"/>
                <rect class="ha-win ha-win--4" x="104" y="186" width="14" height="14" rx="3"/>
                <rect class="ha-win ha-win--2" x="258" y="154" width="14" height="14" rx="3"/>
                <rect class="ha-win ha-win--5" x="282" y="154" width="14" height="14" rx="3"/>
                <rect class="ha-win" x="282" y="186" width="14" height="14" rx="3"/>
            </g>
            {{-- Entrée --}}
            <rect x="182" y="196" width="36" height="44" rx="4" fill="#0b3f78"/>
            <path d="M200 196v44" stroke="#fff" stroke-opacity=".4" stroke-width="2"/>
            <rect x="170" y="186" width="60" height="8" rx="3" fill="#00a86b"/>

            {{-- Arbres --}}
            <circle cx="70" cy="214" r="18" fill="#00a86b" fill-opacity=".85"/>
            <rect x="68" y="226" width="4" height="16" fill="#0a2c52"/>
            <circle cx="334" cy="218" r="14" fill="#00a86b" fill-opacity=".7"/>
            <rect x="332" y="228" width="4" height="14" fill="#0a2c52"/>

            {{-- Route --}}
            <path d="M20 242h360" stroke="#fff" stroke-opacity=".35" stroke-width="3" stroke-linecap="round"/>
            <path d="M40 262h40M120 262h40M200 262h40M280 262h40" stroke="#fff" stroke-opacity=".2" stroke-width="3" stroke-linecap="round"/>

            {{-- Ambulance --}}
            <g class="ha-amb">
                <rect x="0" y="216" width="56" height="26" rx="5" fill="#fff"/>
                <path d="M56 224h12l8 9v9H56Z" fill="#fff"/>
                <path d="M59 227h8l5 6H59Z" fill="#9cc3ea"/>
                <path d="M0 232h56" stroke="#e5484d" stroke-width="3"/>
                <path d="M24 220h6v4h4v6h-4v4h-6v-4h-4v-6h4Z" fill="#e5484d"/>
                <rect class="ha-siren" x="16" y="210" width="8" height="6" rx="2" fill="#e5484d"/>
                <rect class="ha-siren ha-siren--b" x="26" y="210" width="8" height="6" rx="2" fill="#4da3ff"/>
                <circle cx="14" cy="244" r="6" fill="#0a2c52" stroke="#fff" stroke-width="2"/>
                <circle cx="62" cy="244" r="6" fill="#0a2c52" stroke="#fff" stroke-width="2"/>
            </g>

            {{-- Pouls --}}
            <path class="ha-ecg" d="M296 104h20l6-14 9 30 8-24 5 8h22" stroke="#3fd6a0" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
@endswitch
</span>
