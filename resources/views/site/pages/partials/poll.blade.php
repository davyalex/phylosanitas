{{-- Sondage affiché dans l'article : formulaire avant le vote, pourcentages après --}}
@php
    // Toutes les réponses, y compris celles à 0 vote, triées par nombre de votes
    $votesByOption = collect($statistic_sondage)->pluck('choice', 'option_sondage_id');
    $results = $post->optionSondages->map(fn($o) => [
        'id'    => $o->id,
        'title' => $o->title,
        'votes' => (int) ($votesByOption[$o->id] ?? 0),
        'pct'   => $sondage_total > 0 ? round(($votesByOption[$o->id] ?? 0) * 100 / $sondage_total, 1) : 0,
    ])->sortByDesc('votes')->values();
    $topVotes = $results->max('votes');
@endphp

<section class="poll-inline" id="sondage" aria-labelledby="poll-title">
    @if(session('poll_status') === 'success')
        <div class="alert-simple alert-simple--success" role="status">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <span><strong>Merci, votre réponse a bien été enregistrée.</strong> Voici les résultats du sondage.</span>
        </div>
    @elseif(session('poll_status') === 'already')
        <div class="alert-simple alert-simple--info" role="status">
            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
            <span>Vous avez déjà participé à ce sondage. Voici les résultats.</span>
        </div>
    @endif
    @error('sondage_option')
        <div class="alert-simple alert-simple--error" role="alert">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <span>{{ $message }}</span>
        </div>
    @enderror

    @if($my_vote)
        {{-- Après le vote : pourcentages --}}
        <h2 id="poll-title" class="panel__title">
            Résultats du sondage
            <span class="panel__badge">{{ number_format($sondage_total, 0, ',', ' ') }} participant{{ $sondage_total > 1 ? 's' : '' }}</span>
        </h2>
        <div class="poll-results">
            @foreach($results as $r)
                <div class="poll-bar {{ $r['id'] == $my_vote ? 'is-mine' : '' }} {{ $r['votes'] === $topVotes && $topVotes > 0 ? 'is-top' : '' }}">
                    <div class="poll-bar__label">
                        <span>
                            {{ $r['title'] }}
                            @if($r['id'] == $my_vote)<em class="poll-bar__mine"><i class="bi bi-check2" aria-hidden="true"></i>Votre réponse</em>@endif
                        </span>
                        <strong>{{ number_format($r['pct'], 1, ',', ' ') }}&nbsp;%</strong>
                    </div>
                    <div class="poll-bar__track" role="progressbar" aria-label="{{ $r['title'] }}" aria-valuenow="{{ $r['pct'] }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="poll-bar__fill" style="width: {{ $r['pct'] }}%"></div>
                    </div>
                    <span class="poll-bar__count">{{ $r['votes'] }} vote{{ $r['votes'] > 1 ? 's' : '' }}</span>
                </div>
            @endforeach
        </div>
    @else
        {{-- Avant le vote : le formulaire seul (les résultats s'affichent après) --}}
        <form action="{{ route('sondage.store') }}" method="post" class="poll-form">
            @csrf
            <x-honeypot />
            <input type="hidden" name="post_id" value="{{ $post->id }}">
            <fieldset>
                <legend class="panel__title" id="poll-title">Votre réponse</legend>
                <p class="muted mb-3">Choisissez une réponse : les résultats s'affichent dès que vous avez voté.</p>
                @foreach($post->optionSondages as $opt)
                    <label class="poll-option" for="opt{{ $opt->id }}">
                        <input type="radio" name="sondage_option" id="opt{{ $opt->id }}" value="{{ $opt->id }}" required>
                        <span>{{ $opt->title }}</span>
                    </label>
                @endforeach
            </fieldset>
            <button type="submit" class="btn btn-medical btn-block">Valider ma réponse</button>
        </form>
    @endif
</section>
