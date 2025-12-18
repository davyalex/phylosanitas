@extends('admin.layout')
@section('title', 'Accueil')

@section('content')
<section class="section dashboard">
    <div class="row">

      <!-- Left side columns -->
      <div class="col-lg-12">
        <div class="row">

          <!-- Posts Card -->
          <div class="col-xxl-3 col-md-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <h5 class="card-title">Posts</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-card-text"></i>
                  </div>
                  <div class="ps-3">
                    <h6>{{ $post_count }}</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Categories Card -->
          <div class="col-xxl-3 col-md-6">
            <div class="card info-card revenue-card">
              <div class="card-body">
                <h5 class="card-title">Categories</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-grid"></i>
                  </div>
                  <div class="ps-3">
                    <h6>{{ $category_count }}</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Visiteurs Card -->
          <div class="col-xxl-3 col-md-6">
            <div class="card info-card customers-card">
              <div class="card-body">
                <h5 class="card-title">Visiteurs</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6>{{ number_format($countVisitor) }}</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Vues Card -->
          <div class="col-xxl-3 col-md-6">
            <div class="card info-card">
              <div class="card-body">
                <h5 class="card-title">Total Vues</h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background: #4154f1;">
                    <i class="bi bi-eye-fill" style="color: #fff;"></i>
                  </div>
                  <div class="ps-3">
                    <h6>{{ number_format($total_views) }}</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Articles les plus visités -->
          <div class="col-xxl-6 col-md-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Articles les Plus Visités <span>| Top 10</span></h5>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Article</th>
                        <th scope="col">Catégorie</th>
                        <th scope="col">Vues</th>
                        <th scope="col">Commentaires</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($most_viewed_posts as $index => $post)
                      <tr>
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>
                          <a href="/post/detail?slug={{ $post['slug'] }}" target="_blank">
                            {{ Str::limit($post['title'], 50) }}
                          </a>
                        </td>
                        <td><span class="badge bg-primary">{{ $post['category'] }}</span></td>
                        <td><i class="bi bi-eye text-primary"></i> {{ number_format($post['views']) }}</td>
                        <td><i class="bi bi-chat-left-quote text-success"></i> {{ $post['comments'] }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Vues par Pays -->
          <div class="col-xxl-6 col-md-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Vues par Pays <span>| Top 10</span></h5>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Pays</th>
                        <th scope="col">Nombre de Vues</th>
                        <th scope="col">Pourcentage</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                        $total_country_views = $views_by_country->sum('count');
                      @endphp
                      @foreach($views_by_country as $index => $country)
                      <tr>
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>
                          <i class="bi bi-geo-alt-fill text-danger"></i>
                          {{ $country->country }}
                        </td>
                        <td>{{ number_format($country->count) }}</td>
                        <td>
                          <div class="progress" style="height: 20px;">
                            @php
                              $percentage = $total_country_views > 0 ? ($country->count / $total_country_views) * 100 : 0;
                            @endphp
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $percentage }}%" 
                                 aria-valuenow="{{ $percentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                              {{ number_format($percentage, 1) }}%
                            </div>
                          </div>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Posts par Catégorie -->
          <div class="col-xxl-6 col-md-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Répartition par Catégorie</h5>
                <canvas id="categoryChart" style="max-height: 300px;"></canvas>
                <div class="mt-3">
                  @foreach($posts_by_category as $category)
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ $category->title }}</span>
                    <span class="badge bg-primary">{{ $category->posts_count }} articles</span>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          <!-- Graphique 30 derniers jours -->
          <div class="col-xxl-6 col-md-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Publications <span>| 30 derniers jours</span></h5>
                <canvas id="publicationsChart" style="max-height: 300px;"></canvas>
              </div>
            </div>
          </div>

          <!-- Derniers posts -->
          <div class="col-xxl-12 col-md-12">
            <div class="card">
              <div class="filter">
                <a class="icon text-bold text-primary" href="{{ route('post') }}">Tous voir<i class="bi bi-arrow-bar-right"></i></a>
              </div>
              <div class="card-body pb-0">
                <h5 class="card-title">Derniers posts</h5>
                <div class="news">
                  @foreach ($post_recent as $item)
                  <div class="post-item clearfix">
                    <img src="{{ asset($item->getFirstMediaUrl('image')) }}" alt="">
                    <h4><a href="/post/detail?slug={{ $item['slug'] }}">{{ Str::limit($item['title'], 50) }}</a></h4>         
                    <p>
                      <i class="bi bi-eye"></i> {{ views($item)->count() }} vues &nbsp; &nbsp;
                      <i class="bi bi-chat-left-quote"></i> {{ $item->commentaires->count() }} Commentaires &nbsp; &nbsp;
                      <i class="bi bi-calendar-check"></i> Publié {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                    </p>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des catégories
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($posts_by_category->pluck('title')) !!},
                datasets: [{
                    data: {!! json_encode($posts_by_category->pluck('posts_count')) !!},
                    backgroundColor: [
                        '#4154f1', '#2eca6a', '#ff771d', '#f1416c',
                        '#ffc107', '#17a2b8', '#6610f2', '#e83e8c',
                        '#20c997', '#fd7e14'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Graphique des publications
    const publicationsCtx = document.getElementById('publicationsChart');
    if (publicationsCtx) {
        new Chart(publicationsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($last_30_days_stats->pluck('date')) !!},
                datasets: [{
                    label: 'Publications',
                    data: {!! json_encode($last_30_days_stats->pluck('count')) !!},
                    borderColor: '#4154f1',
                    backgroundColor: 'rgba(65, 84, 241, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
@endsection
    
