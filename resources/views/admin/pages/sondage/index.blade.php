  <!-- Table with stripped rows -->
  <table class="table datatable">
      <thead>
          <tr>
              <th scope="col">#</th>
              <th scope="col-2">image</th>
              <th scope="col">Question</th>
              <th scope="col">categorie</th>
              <th scope="col">vues</th>
              <th scope="col">Date</th>
              <th scope="col">Action</th>
          </tr>
      </thead>
      <tbody>
          @foreach ($sondage as $key => $item)
              <tr>
                  <th scope="row">{{ ++$key }}</th>
                  <td class="col-2"> <img src="{{ $item->getFirstMediaUrl('image') }}"
                          alt="{{ $item->getFirstMediaUrl('image') }}" style="width: 45px; height: 45px"
                          class="rounded-circle" />
                      @if ($item['published'] == 'prive')
                          <br><span class=""> <i class="bi bi-circle-fill text-warning"></i> non publié</span>
                      @elseif ($item['published'] == 'public')
                          <br> <span class=""> <i class="bi bi-circle-fill text-success"></i> en ligne</span>
                      @endif
                  </td>
                  <td> {!! substr(strip_tags($item->description), 0, 50) !!}.... </td>
                  <td><span class="badge bg-secondary">{{ $item->category->title }}</span></td>
                  <td>{{ views($item)->count() }}</td>
                  <td> {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}</td>

                  <td>
                      <div class="d-flex">

                          <div class="dropdown">
                              <button role="button" class="btn btn-primary rounded-circle dropdown-toggle"
                                  data-bs-toggle="dropdown" aria-haspopup="false"><i class="bi bi-globe"></i></button>
                              <div class="dropdown-menu">
                                  <!-- ========== Start published status ========== -->
                                  <a href="{{ route('post.published', $item->id) }}" class="dropdown-item "
                                      style="font-weight:700; font-size:1em"><i class="bi bi-lock-fill"></i>
                                      {{ $item->published == 'public' ? 'Privé' : 'Public' }} </a>
                                  <!-- ========== End published status ========== -->

                              </div>
                          </div>
                          <a href="/post/detail?slug={{ $item['slug'] }}" role="button"
                              class="btn btn-warning rounded-circle"><i class="bi bi-eye"></i></a>
                          <a href="{{ route('post.edit-sondage',$item['slug']) }}" class="btn btn-success rounded-circle" role="button"  class="btn btn-success mx-2 "><i class="bi bi-pencil"></i></a>

                          <form action="{{ route('post.delete', $item['id']) }}" method="POST">
                              @csrf
                              <a class="btn btn-danger rounded-circle" data-bs-toggle="modal"
                                  data-bs-target="#confirmDelete{{ $item->id }}"><i class="bi bi-trash"></i> </a>
                              @include('admin.partials.deleteConfirm')
                          </form>
                      </div>

                  </td>
              </tr>
          @endforeach
      </tbody>
  </table>
  <!-- End Table with stripped rows -->
