@extends('admin.layout')
@section('title', 'Actualites')

@section('content')
<section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">

             <!-- Formulaire add actualite -->
            <h5 class="card-title">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal"><i class="bi bi-plus"></i>
            Ajouter une actualite
            </button></h5>
            <div class="modal fade" id="basicModal" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Ajouter une actualite</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                     <!-- Vertical Form -->
              <form class="row g-3 needs-validation" method="post" action="{{ route('actualite.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="col-12">
                  <label for="inputNanme4" class="form-label">Title</label>
                  <input type="text" name="title" class="form-control" id="inputNanme4">
                </div>
                <div class="col-12">
                    <label for="inputNumber" class="form-label">Image de l'actualite <br> <span> Taille image(L:1900px / h:750px)</span></label>
                      <input class="form-control" name="image" type="file" id="formFile" required>
                      <div class="invalid-feedback">Veuillez ajouter une image</div>
                  </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Valider</button>
                </div>
              </form><!-- Vertical Form -->
                  </div>
                </div>
              </div>
            </div> <!-- Formulaire add actualite -->



            <!-- Table with stripped rows -->
            <table class="table datatable">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">image</th>
                  <th scope="col">Title</th>
                  <th scope="col">date</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($actualite as $key =>$item)
                <tr>
                  <th scope="row">{{ ++$key }}</th>

                  <td>  <img
                    src="{{ $item ->getFirstMediaUrl('image') }}"
                    alt="{{ $item ->getFirstMediaUrl('image') }}"
                    style="width: 45px; height: 45px"
                    class=""
                    /></td>
                  <td>{{ $item['title'] }}</td>
                  <td> {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</td>

                  <td>
                   <div class="d-flex">
                    <a href="{{ route('accueil') }}"  role="button" class="btn btn-warning rounded-circle"><i class="bi bi-eye"></i></a>

                    <a href="{{ route('actualite.edit',$item['id']) }}" role="button" data-id = {{ $item['id'] }} data-bs-toggle="modal" data-bs-target="#basicModalEdit{{ $item['id'] }}" class="btn btn-success rounded-circle mx-2 "><i class="bi bi-pencil me-1"></i></a>
                    
                    <form  action="{{ route('actualite.delete',$item->id) }}" method="POST">
                      @csrf
                      <a  class="btn btn-danger rounded-circle" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#confirmDelete{{ $item->id }}"><i class="bi bi-trash me-1"></i> </a>
                     @include('admin.partials.deleteConfirm')
                    </form>
                   </div>
                    
                  </td>
                </tr>
                            <!-- start Formulaire edit actualite -->
            <div class="modal fade" id="basicModalEdit{{ $item['id'] }}" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Modifier une actualite </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                     <!-- Vertical Form -->
              <form class="row g-3" method="post" action="{{ route('actualite.update',$item['id']) }}" enctype="multipart/form-data">
                @csrf
                <div class="col-12">
                    <label for="inputNanme4" class="form-label">Title</label>
                    <input type="text" name="title" value="{{ $item['title'] }}" class="form-control" id="inputNanme4">
                  </div>
                  <div class="col-12 mt-2">
                      <label for="inputNumber" class="form-label">Image de l'actualité</label>
                        <input class="form-control" name="image" type="file" id="formFile">

                        <div>
                            <img
                            src="{{ $item ->getFirstMediaUrl('image') }}"
                            alt="{{ $item ->getFirstMediaUrl('image') }}"
                            style="width: 45px; height: 45px"
                            class="rounded-circle"
                            />
                        </div>
                    </div>
                <div class="text-center mt-2">
                  <button type="submit" class="btn btn-primary">Valider</button>
                </div>
              </form><!-- Vertical Form -->
                  </div>
                </div>
              </div>
            </div> <!-- Formulaire edit actualite -->
                @endforeach
              </tbody>
            </table>
            <!-- End Table with stripped rows -->





          </div>
        </div>

      </div>
    </div>
 
    
    
  </section>
@endsection