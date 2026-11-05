{{-- Champs d'une journée santé. Variables : $j (JourneeSante|null), $pfx (préfixe des id) --}}
@php
    $isNew = $j === null;
    $val   = fn($f, $d = null) => $isNew ? old($f, $d) : $j->$f;
    $mensuelle = $isNew ? (bool) old('tout_le_mois') : $j->estMensuelle();
@endphp

<div class="mb-3">
    <label class="form-label" for="{{ $pfx }}-titre">Nom <span class="req">*</span></label>
    <input id="{{ $pfx }}-titre" name="titre" value="{{ $val('titre') }}" class="form-control {{ $isNew && $errors->has('titre') ? 'is-invalid' : '' }}" maxlength="150" required placeholder="Ex. : Journée mondiale de l'hygiène des mains">
</div>

<div class="row g-2 mb-2">
    <div class="col-5">
        <label class="form-label" for="{{ $pfx }}-jour">Jour</label>
        <input id="{{ $pfx }}-jour" type="number" min="1" max="31" name="jour" value="{{ $val('jour') }}" class="form-control {{ $isNew && $errors->has('jour') ? 'is-invalid' : '' }}">
    </div>
    <div class="col-7">
        <label class="form-label" for="{{ $pfx }}-mois">Mois <span class="req">*</span></label>
        <select id="{{ $pfx }}-mois" name="mois" class="form-select" required>
            @foreach(\App\Models\JourneeSante::MOIS as $n => $nom)
                <option value="{{ $n }}" {{ (int) $val('mois', now()->month) === $n ? 'selected' : '' }}>{{ ucfirst($nom) }}</option>
            @endforeach
        </select>
    </div>
</div>
<label class="d-flex align-items-center gap-2 small mb-3">
    <input type="checkbox" class="form-check-input m-0 js-tout-mois" name="tout_le_mois" value="1" data-jour="{{ $pfx }}-jour" {{ $mensuelle ? 'checked' : '' }}>
    Tout le mois (campagne comme Octobre rose)
</label>
@if($isNew)@error('jour')<div class="text-danger small mb-2">{{ $message }}</div>@enderror @endif

<div class="mb-3">
    <label class="form-label" for="{{ $pfx }}-desc">Message du bandeau</label>
    <textarea id="{{ $pfx }}-desc" name="description" rows="2" maxlength="400" class="form-control" placeholder="Une phrase de sensibilisation.">{{ $val('description') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label" for="{{ $pfx }}-lien">Lien (facultatif)</label>
    <input id="{{ $pfx }}-lien" name="lien" value="{{ $val('lien') }}" class="form-control {{ $isNew && $errors->has('lien') ? 'is-invalid' : '' }}" maxlength="500" placeholder="/article/…  ou  /dossiers/…">
    <div class="form-text">Article ou dossier sur le sujet. Vide = recherche sur le site.</div>
</div>

<div class="row g-2">
    <div class="col-6">
        <label class="form-label" for="{{ $pfx }}-couleur">Couleur</label>
        <input id="{{ $pfx }}-couleur" type="color" name="couleur" value="{{ $val('couleur', '#0066cc') }}" class="form-control form-control-color w-100">
    </div>
    <div class="col-6">
        <label class="form-label" for="{{ $pfx }}-annonce">Annoncer</label>
        <div class="input-group">
            <input id="{{ $pfx }}-annonce" type="number" min="0" max="30" name="annonce_jours" value="{{ $val('annonce_jours', 7) }}" class="form-control">
            <span class="input-group-text">j avant</span>
        </div>
    </div>
</div>

<label class="d-flex align-items-center gap-2 small mt-3">
    <input type="checkbox" class="form-check-input m-0" name="actif" value="1" {{ ($isNew ? old('actif', true) : $j->actif) ? 'checked' : '' }}>
    Active (affichée sur le site)
</label>
