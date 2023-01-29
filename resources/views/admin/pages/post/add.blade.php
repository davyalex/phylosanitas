@extends('admin.layout')
@section('title', 'Post')

@section('content')
<section class="section">

    <form action="{{ route('post.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="row mt-4">

                    <div class="col-lg-6">
              
                      <div class="card">
                        <div class="card-body">
              
                          <!-- General Form Elements -->
                            <div class="row mb-3 mt-2">
                                <label for="inputNanme4" class="form-label">Titre du post </label>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid
                                    
                                @enderror" id="inputNanme4">
                                @error('title')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="row mb-3">
                              <label for="inputNumber" class="form-label">Image de presentation</label>
                                <input class="form-control" name="image" type="file" id="formFile">
                            </div>
              
                            <div class="row mb-3">
                              <label class="form-label">Categorie</label>
                                <select name="category" class="form-select  @error('category') is-invalid @enderror" aria-label="Default select example">
                                  <option selected>selectionner</option>
                                  @foreach ($category as $item)
                                  <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
                                  @endforeach
                                </select>
                                @error('category')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            </div>
              
                            {{-- <div class="row mb-3">
                              <label for="inputText" class="form-label">Tags</label>
                                <input type="text" class="form-control">
                            </div> --}}
              
                            <div class="row mb-3">
                              <label for="inputText" class="form-label">Lien</label>
                                <input type="text" class="form-control">
                            </div>
              
              
                        </div>
                      </div>
              
                    </div>
              
                    <div class="col-lg-6">
                      <div class="card">
                        <div class="card-body">
                          <h5 class="card-title">Description</h5>
              
                          <!-- TinyMCE Editor -->
                          <textarea name="description"   class="tinymce-editor">
                          
                          </textarea><!-- End TinyMCE Editor -->
              
                        </div>
                      </div>
              
                    </div>

                </div>
                <div class="col-ld-12">
                 <button type="submit" class="btn btn-primary w-100">Valider</button>
                </div>
            </div>
        </div>
    </form>




    
  </section>

@endsection