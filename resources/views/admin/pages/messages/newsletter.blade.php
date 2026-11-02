@extends('admin.layout')
@section('title', 'Newsletter')

@section('page_header')
<div class="page-bar">
    <div>
        <h1 class="page-bar__title">Abonnés newsletter</h1>
        <p class="page-bar__sub">{{ number_format($activeCount, 0, ',', ' ') }} abonné{{ $activeCount > 1 ? 's' : '' }} actif{{ $activeCount > 1 ? 's' : '' }}. Exportez la liste pour l'importer dans votre outil d'emailing.</p>
    </div>
    <div class="page-bar__actions">
        <a href="{{ route('newsletter.export') }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Exporter (CSV)</a>
    </div>
</div>
@endsection

@section('content')
<div class="panel-a">
    @if($subscribers->isEmpty())
        <div class="empty-admin">
            <i class="bi bi-envelope-paper-heart"></i>
            <h3>Aucun abonné pour le moment</h3>
            <p>Le formulaire « Restez informé » de la page d'accueil alimente cette liste.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th class="ps-3">Email</th><th>Nom</th><th>Statut</th><th class="pe-3">Inscription</th></tr></thead>
                <tbody>
                    @foreach($subscribers as $s)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $s->email }}</td>
                            <td>{{ $s->name ?: '—' }}</td>
                            <td><span class="status {{ $s->status === 'active' ? 'status--live' : 'status--expired' }}">{{ $s->status === 'active' ? 'Actif' : 'Désinscrit' }}</span></td>
                            <td class="pe-3 text-muted small">{{ $s->subscribed_at ? \Carbon\Carbon::parse($s->subscribed_at)->translatedFormat('d M Y') : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="mt-3">{{ $subscribers->links('pagination::bootstrap-5') }}</div>
@endsection
