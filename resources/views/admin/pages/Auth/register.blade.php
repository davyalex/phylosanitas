@extends('admin.layout')
@section('title', 'Utilisateurs')

@section('content')
<section class="section">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal"><i class="bi bi-plus"></i>
                Ajouter un utilisateur
                </button></h5>


                {{-- modal register --}}
                <div class="modal fade" id="basicModal" tabindex="-1">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="card-title text-center pb-0 fs-4">Créer un utlisateur</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                              <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('user.store') }}">
                                @csrf
                                <div class="col-12">
                                  <label for="yourName" class="form-label">Nom utilisateur</label>
                                  <input type="text" name="name" class="form-control" id="yourName" required>
                                  <div class="invalid-feedback">Champs obligatoire</div>
                                </div>
                                <div class="col-12">
                                    <label for="yourName" class="form-label">Contact</label>
                                    <input type="number" name="phone" class="form-control" id="yourName" required>
                                    <div class="invalid-feedback">Champs obligatoire</div>
                                </div>
            
                                <div class="col-12">
                                  <label for="yourEmail" class="form-label">Email</label>
                                  <input type="email" name="email" class="form-control" id="yourEmail">
                                </div>
            
                                <div class="col-12">
                                  <label for="yourPassword" class="form-label">Password</label>
                                  <input type="password" name="password" class="form-control" id="password" required>
                                  <div class="invalid-feedback">Champs obligatoire</div>
                                  @include('admin.partials.hideShowPwd')
                                </div>
            
                                <div class="col-12">
                                    <label for="yourName" class="form-label">Role</label><br>
                                    <select name="role" class="form-select  @error('role') is-invalid @enderror" aria-label="Default select example" required>
                                        <option selected disabled></option>
                                        @foreach ($role as $item)
                                        <option value="{{ $item['name'] }}">{{ $item['name'] }}</option>
                                        @endforeach
                                      </select>
                                      @error('role')
                                      <p class="text-danger">{{ $message }}</p>
                                  @enderror
                                  <div class="invalid-feedback">Champs obligatoire</div>
                                </div>
                               
                                <div class="col-12">
                                  <button class="btn btn-primary w-100" type="submit">Valider</button>
                                </div>
                              </form>
                        </div>
                      </div>
                    </div>
                  </div> <!-- Formulaire add user -->
      

               
                <!-- Table with stripped rows -->
                <table class="col-sm-12 col-md-12 table datatable table table-hover">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">utilisateur</th>
                        <th scope="col">contact</th>
                        <th scope="col">email</th>
                        <th scope="col">role</th>
                        <th scope="col">date</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($user as $key =>$item)
                      <tr>
                        <th>{{ ++$key }}</td>
                        <td >{{ $item['name'] }}</td>
                        <td>{{ $item['phone'] }}</td>
                        @if ($item['email'])
                        <td>{{ $item['email'] }}</td>
                        @else
                            <td>Non defini</td>
                        @endif
                        <td><span class="badge bg-primary">{{ $item ->roles[0]->name }}</span></td>
                        <td> {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</td>
                      
                        <td>
                         <div class="d-flex">
                            {{-- lock & unlock --}}
                            @if ($item['active']=='yes')
                            <a role="button" href="{{ route('user.lock',$item['id']) }}" class="btn btn-primary rounded-circle"><i class="bi bi-unlock"></i></a>
                            @else
                            <a data-bs-toggle="tooltip" title="over me" role="button" href="{{ route('user.unlock',$item['id']) }}" class="btn btn-danger rounded-circle"><i class="bi bi-lock"></i></a>
                            @endif

                            {{-- cacher les button si lock--}}
                            @if ($item['active']=='yes')
                            <a href="{{ route('user.edit',$item['id']) }}" class="btn btn-success rounded-circle" data-id="{{ $item['id'] }}"  data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}"role="button"  class="btn btn-success mx-2 "><i class="bi bi-pencil"></i></a>
                          
                            <form  action="{{ route('user.delete',$item['id']) }}" method="POST">
                              @csrf
                              <a  class="btn btn-danger rounded-circle"  role="button" data-bs-toggle="modal" data-bs-target="#confirmDelete{{ $item->id }}"><i class="bi bi-trash"></i> </a>
                             @include('admin.partials.deleteConfirm')
                            </form>
                            @endif

                         
                         </div>
                          
                        </td>
                      </tr>

<!-- Formulaire edit user -->
                      <div class="modal fade" id="editModal{{ $item['id'] }}" tabindex="-1">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="card-title text-center pb-0 fs-4">Modifier un utlisateur</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                  <form class="row g-3"  method="POST" action="{{ route('user.update',$item['id']) }}">
                                    @csrf
                                    
                                    <div class="col-12">
                                      <label for="yourName" class="form-label">Nom utilisateur</label>
                                      <input type="text" value="{{ $item['name'] }}" name="name" class="form-control" id="yourName" required>
                                      <div class="invalid-feedback">Champs obligatoire</div>
                                    </div>
                                    <div class="col-12">
                                        <label for="yourName" class="form-label">Contact</label>
                                        <input type="number" value="{{ $item['phone'] }}" name="phone" class="form-control" id="yourName" required>
                                        <div class="invalid-feedback">Champs obligatoire</div>
                                    </div>
                
                                    <div class="col-12">
                                      <label for="yourEmail" class="form-label">Email</label>
                                      <input type="email" value="{{ $item['name'] }}" name="email" class="form-control" id="yourEmail">
                                    </div>
                
                                    <div class="col-12">
                                      <label for="yourPassword" class="form-label">Password</label>
                                      <input type="text"  name="password" class="form-control" id="password" placeholder="Vous pouvez donner un nouveau mot de passe">
                                      <div class="invalid-feedback">Champs obligatoire</div>
                                      {{-- @include('admin.partials.hideShowPwd') --}}
                                    </div>
                
                                    <div class="col-12">
                                        <label for="yourName" class="form-label">Role</label><br>
                                        <select name="role" class="form-select  @error('role') is-invalid @enderror" aria-label="Default select example" required>
                                            <option selected disabled></option>
                                            @foreach ($role as $item_role)
                                            <option value="{{ $item_role['name'] }}" {{ $item['role']==$item_role['name'] ? 'selected': '' }}>{{ $item_role['name'] }}</option>
                                            @endforeach
                                          </select>
                                          @error('role')
                                          <p class="text-danger">{{ $message }}</p>
                                      @enderror
                                      <div class="invalid-feedback">Champs obligatoire</div>
                                    </div><br>
                                   
                                    <div class="col-12">
                                      <button class="btn btn-primary w-100" type="submit">Valider</button>
                                    </div>
                                  </form>
                            </div>
                          </div>
                        </div>
                      </div> <!-- Formulaire edit user -->
          
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