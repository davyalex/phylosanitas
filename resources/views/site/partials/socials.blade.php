{{-- Liens réseaux sociaux renseignés dans Admin > Configuration --}}
@foreach(($site['socials'] ?? []) as $net)
    <a href="{{ $net['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $net['label'] }}"><i class="bi bi-{{ $net['icon'] }}" aria-hidden="true"></i></a>
@endforeach
