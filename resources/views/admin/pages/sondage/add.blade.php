<div class="row g-3">

    {{-- Image --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Image (optionnelle)</h6>
                <input class="form-control" name="image" type="file" accept="image/*">
            </div>
        </div>
    </div>

    {{-- Catégorie (cachée, auto-sélectionnée sur "sondage") --}}
    <div class="col-lg-6 d-none">
        <select name="category" required>
            @foreach ($category as $item)
                <option value="{{ $item['id'] }}" selected>{{ $item['title'] }}</option>
            @endforeach
        </select>
    </div>

    {{-- Question --}}
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Question du sondage <span class="text-danger">*</span></h6>
                <textarea name="description" id="tinymce-sondage-add"></textarea>
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
                        <tr>
                            <td>
                                <input type="text" name="option[0][title]" class="form-control"
                                       placeholder="Option 1" required>
                            </td>
                            <td style="width:50px;">
                                <button type="button" id="add-btn" class="btn btn-success btn-sm">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<input type="hidden" name="sondage" value="sondage">

<div class="mt-3">
    <button type="submit" class="btn btn-primary px-4">
        <i class="bi bi-check-lg me-2"></i>Créer le sondage
    </button>
</div>

@include('admin.partials.tinymce-init', ['selector' => 'textarea#tinymce-sondage-add'])

<script>
    var i = 0;
    $('#add-btn').click(function () {
        ++i;
        $('#dynamicAddRemove tbody').append(
            '<tr>' +
            '<td><input type="text" name="option[' + i + '][title]" class="form-control" placeholder="Option ' + (i + 1) + '" required></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm remove-tr"><i class="bi bi-trash"></i></button></td>' +
            '</tr>'
        );
    });
    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });
</script>
