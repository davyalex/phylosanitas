{{-- Une affirmation du quiz. Variables : $i (index du champ), $q (valeurs), $n (numéro affiché) --}}
@php
    $err = fn($f) => is_numeric($i) && $errors->has("questions.$i.$f");
    $rep = (string) ($q['reponse'] ?? '');
@endphp
<section class="fsec qz-card">
    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
        <h2 class="fsec__title mb-0"><i class="bi bi-patch-question"></i>Affirmation <span class="js-qz-num">{{ $n }}</span></h2>
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-sm btn-outline-secondary icon-action js-qz-up" title="Monter" aria-label="Monter"><i class="bi bi-arrow-up"></i></button>
            <button type="button" class="btn btn-sm btn-outline-secondary icon-action js-qz-down" title="Descendre" aria-label="Descendre"><i class="bi bi-arrow-down"></i></button>
            <button type="button" class="btn btn-sm btn-outline-danger icon-action js-qz-del" title="Retirer" aria-label="Retirer cette affirmation"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>

    <label class="form-label" for="qz-a-{{ $i }}">Affirmation <span class="req">*</span></label>
    <textarea id="qz-a-{{ $i }}" name="questions[{{ $i }}][affirmation]" rows="2" maxlength="300" required
              class="form-control {{ $err('affirmation') ? 'is-invalid' : '' }}"
              placeholder="Ex. : Le paludisme se transmet par simple contact.">{{ $q['affirmation'] ?? '' }}</textarea>

    <div class="mt-3">
        <span class="form-label d-block">Bonne réponse <span class="req">*</span></span>
        <div class="seg {{ $err('reponse') ? 'border border-danger rounded' : '' }}">
            <input type="radio" name="questions[{{ $i }}][reponse]" id="qz-v-{{ $i }}" value="1" {{ $rep === '1' ? 'checked' : '' }} required>
            <label for="qz-v-{{ $i }}"><strong class="text-success"><i class="bi bi-check-circle me-1"></i>Vrai</strong></label>
            <input type="radio" name="questions[{{ $i }}][reponse]" id="qz-f-{{ $i }}" value="0" {{ $rep === '0' ? 'checked' : '' }}>
            <label for="qz-f-{{ $i }}"><strong class="text-danger"><i class="bi bi-x-circle me-1"></i>Faux</strong></label>
        </div>
    </div>

    <label class="form-label mt-3" for="qz-e-{{ $i }}">Explication <span class="req">*</span></label>
    <textarea id="qz-e-{{ $i }}" name="questions[{{ $i }}][explication]" rows="3" maxlength="1000" required
              class="form-control {{ $err('explication') ? 'is-invalid' : '' }}"
              placeholder="Affichée après la réponse : pourquoi c'est vrai ou faux, en 1 à 3 phrases.">{{ $q['explication'] ?? '' }}</textarea>

    <label class="form-label mt-3" for="qz-p-{{ $i }}">Article pour en savoir plus</label>
    <select id="qz-p-{{ $i }}" name="questions[{{ $i }}][post_id]" class="form-select">
        <option value="">— Aucun —</option>
        @foreach($articles as $a)
            <option value="{{ $a->id }}" {{ (string) ($q['post_id'] ?? '') === (string) $a->id ? 'selected' : '' }}>{{ Str::limit($a->title, 90) }}</option>
        @endforeach
    </select>
</section>
