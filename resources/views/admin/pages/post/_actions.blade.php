{{--
    Actions d'une ligne (articles ET sondages, même menu partout).
    « Modifier » reste visible ; les autres actions sont dans le menu « ⋯ ».
    Variables : $item (Post), $isPoll (bool)
--}}
@php
    $isPoll  = $isPoll ?? false;
    $online  = $item->published === 'public';
    $editUrl = $isPoll ? route('post.edit-sondage', $item->id) : route('post.edit', $item->slug);
    $label   = $isPoll ? 'ce sondage' : 'cet article';
@endphp

<div class="row-menu">
    <a href="{{ $editUrl }}" class="btn btn-sm btn-outline-primary icon-action" title="Modifier" aria-label="Modifier">
        <i class="bi bi-pencil"></i>
    </a>

    <div class="dropdown">
        <button type="button" class="btn btn-sm btn-light border icon-action" data-bs-toggle="dropdown"
                aria-expanded="false" aria-label="Plus d'actions" title="Plus d'actions">
            <i class="bi bi-three-dots"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end action-menu">
            <li>
                <a class="dropdown-item" href="{{ route('post.show', $item->id) }}">
                    <i class="bi bi-bar-chart-line"></i>Détail et statistiques
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('post.detail', ['slug' => $item->slug]) }}" target="_blank" rel="noopener">
                    <i class="bi bi-box-arrow-up-right"></i>Voir sur le site
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('post.published', $item->id) }}">
                    @if($online)
                        <i class="bi bi-eye-slash"></i>Mettre en brouillon
                    @else
                        <i class="bi bi-send"></i>Publier
                    @endif
                </a>
            </li>
            @unless($isPoll)
                <li>
                    <a class="dropdown-item" href="{{ url('/admin/post/actualite') }}?actualite_une={{ $item->actualite_une ? 0 : 1 }}&actualite={{ $item->id }}">
                        <i class="bi bi-star{{ $item->actualite_une ? '-fill' : '' }}"></i>{{ $item->actualite_une ? 'Retirer de la une' : 'Mettre à la une' }}
                    </a>
                </li>
            @endunless
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('post.delete', $item->id) }}" method="POST" data-confirm="Supprimer définitivement {{ $label }} ?">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash"></i>Supprimer</button>
                </form>
            </li>
        </ul>
    </div>
</div>
