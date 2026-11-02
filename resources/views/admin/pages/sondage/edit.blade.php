@extends('admin.layout')
@section('title', 'Modifier un sondage')

@section('content')
<section class="section">
    <form action="{{ route('post.update-sondage', $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">

            {{-- Image --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Image</h6>
                        <input class="form-control" name="image" type="file" accept="image/*">
                        @if ($post->getFirstMediaUrl('image'))
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img src="{{ $post->getFirstMediaUrl('image') }}"
                                     alt="Image actuelle"
                                     class="rounded"
                                     style="width:60px; height:60px; object-fit:cover;">
                                <small class="text-muted">Image actuelle</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Catégorie (masquée, conserve la valeur existante) --}}
            <div class="d-none">
                <select name="category">
                    @foreach ($category as $cat)
                        <option value="{{ $cat->id }}"
                                {{ $cat->id == $post->category_id ? 'selected' : '' }}>
                            {{ $cat->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Question --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Question du sondage <span class="text-danger">*</span></h6>
                        <textarea name="description" id="tinymce-sondage-edit">{{ $post->description }}</textarea>
                        @error('description')
                            <p class="text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Options de réponse --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Options de réponse <span class="text-danger">*</span></h6>
                        @error('option.*.title')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror

                        <table class="table table-borderless" id="dynamicAddRemove">
                            <tbody>
                                {{-- Les options existantes sont insérées par JS ci-dessous --}}
                            </tbody>
                        </table>

                        <button type="button" id="add-btn" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>Ajouter une option
                        </button>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-2"></i>Enregistrer les modifications
                    </button>
                    <a href="{{ route('post', ['type' => 'sondage']) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Annuler
                    </a>
                </div>
            </div>

        </div>
    </form>
</section>

@include('admin.partials.tinymce-init', ['selector' => 'textarea#tinymce-sondage-edit', 'postId' => $post->id])

<script>
    var reponses = @json($reponseSondage);
    var i = reponses.length;

    $.each(reponses, function (index, value) {
        $('#dynamicAddRemove tbody').append(
            '<tr>' +
            '<td><input type="text" name="option[' + index + '][title]" value="' + $('<div>').text(value.title).html() + '" class="form-control" required></td>' +
            '<td style="width:50px;"><button type="button" class="btn btn-danger btn-sm remove-tr"><i class="bi bi-trash"></i></button></td>' +
            '</tr>'
        );
    });

    $('#add-btn').click(function () {
        $('#dynamicAddRemove tbody').append(
            '<tr>' +
            '<td><input type="text" name="option[' + i + '][title]" class="form-control" placeholder="Nouvelle option" required></td>' +
            '<td style="width:50px;"><button type="button" class="btn btn-danger btn-sm remove-tr"><i class="bi bi-trash"></i></button></td>' +
            '</tr>'
        );
        i++;
    });

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });
</script>
@endsection
