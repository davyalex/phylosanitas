@extends('admin.layout')
@section('title', 'Post')

@section('content')
    <section class="section">

        <form class="needs-validation" action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data"
            novalidate>
            @csrf
            <div class="card">
                <div class="card-body">

                    @if (request('type') == 'sondage')
                        @include('admin.pages.sondage.add')
                    @else
                        {{-- formulaire pour les post --}}
                        <div class="row mt-4">

                            <div class="col-lg-6">

                                <div class="card">
                                    <div class="card-body">

                                        <!-- General Form Elements -->
                                        <div class="row mb-3 mt-2">
                                            <label for="inputNanme4" class="form-label">Titre du post </label>
                                            <input type="text" name="title" value="{{ old('title') }}"
                                                class="form-control @error('title') is-invalid
                                    
                                @enderror"
                                                id="inputNanme4" required>
                                            @error('title')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                            <div class="invalid-feedback">Veuillez remplir ce champs</div>

                                        </div>
                                        <div class="row mb-3">
                                            <label for="inputNumber" class="form-label">Image de presentation</label>
                                            <input class="form-control" name="image" type="file" id="formFile">
                                        </div>




                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <label class="form-label">Categorie</label>
                                            <select name="category" id="category"
                                                class="form-select  @error('category') is-invalid @enderror"
                                                aria-label="Default select example" required>
                                                <option disabled selected></option>
                                                @foreach ($category as $item)
                                                    <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('category')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                            <div class="invalid-feedback">Veuillez sélectionner une categorie</div>

                                        </div>

                                        <div class="row mb-3" id="lien">
                                            <label for="inputText" class="form-label">Lien</label>
                                            <input type="url" name="lien" class="form-control">
                                        </div>

                                    </div>
                                </div>

                            </div>


                            <div class="col-lg-12" id="description">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Description</h5>

                                        <!-- TinyMCE Editor -->
                                        <textarea name="description" class="tinymce-editor">
                          
                          </textarea><!-- End TinyMCE Editor -->

                                    </div>
                                </div>

                            </div>


                        </div>
                        <div class="col-ld-12">
                            <button type="submit" class="btn btn-primary w-100">Valider</button>
                        </div>
                    @endif
                </div>
            </div>
        </form>





    </section>


@endsection
