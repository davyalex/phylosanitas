@extends('site.layout')
@section('title', 'Contactez-nous')
@section('description', 'Contactez l\'équipe PhyloSanitas — blog médical et santé.')

@section('content')
<section id="contact" class="contact py-5 mt-4">
    <div class="container" data-aos="fade-up">

        <div class="row mb-5">
            <div class="col-lg-12 text-center">
                <h1 class="text-medical-blue fw-bold">
                    <i class="bi bi-envelope-heart-fill me-2"></i>Contactez-nous
                </h1>
                <p class="text-muted mt-2">Notre équipe répond à toutes vos questions dans les plus brefs délais.</p>
            </div>
        </div>

        {{-- Informations de contact --}}
        <div class="row gy-4 mb-5">
            <div class="col-md-4">
                <div class="info-item text-center card card-medical p-4 h-100">
                    <i class="bi bi-geo-alt-fill text-medical-blue" style="font-size:2rem;"></i>
                    <h5 class="mt-3 fw-bold">Adresse</h5>
                    <address class="text-muted mb-0">Abidjan, Côte d'Ivoire</address>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-item text-center card card-medical p-4 h-100">
                    <i class="bi bi-telephone-fill text-health-green" style="font-size:2rem;"></i>
                    <h5 class="mt-3 fw-bold">Téléphone</h5>
                    <p class="text-muted mb-0">
                        <a href="tel:+2250103487555" class="text-medical-blue">(+225) 01 03 48 75 55</a>
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-item text-center card card-medical p-4 h-100">
                    <i class="bi bi-envelope-fill text-medical-teal" style="font-size:2rem;"></i>
                    <h5 class="mt-3 fw-bold">Email</h5>
                    <p class="text-muted mb-0">
                        <a href="mailto:dr_akencho@yahoo.fr" class="text-medical-blue">dr_akencho@yahoo.fr</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Notification de succès --}}
        @if (session('success_contact'))
            <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <span>{{ session('success_contact') }}</span>
            </div>
        @endif

        {{-- Formulaire de contact --}}
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-medical shadow-medical">
                    <div class="card-header bg-medical-light">
                        <h4 class="mb-0 text-medical-blue fw-bold">
                            <i class="bi bi-pencil-square me-2"></i>Envoyer un message
                        </h4>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('contact.store') }}" method="POST" novalidate>
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="contact-name" class="form-label fw-bold text-medical-blue">
                                        <i class="bi bi-person-fill me-1"></i>Votre nom <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="name"
                                           id="contact-name"
                                           class="form-control form-control-lg @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}"
                                           placeholder="Dr. Jean Dupont"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="contact-email" class="form-label fw-bold text-medical-blue">
                                        <i class="bi bi-envelope-fill me-1"></i>Votre email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           name="email"
                                           id="contact-email"
                                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}"
                                           placeholder="exemple@email.com"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="contact-subject" class="form-label fw-bold text-medical-blue">
                                        <i class="bi bi-chat-left-text-fill me-1"></i>Sujet <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="subject"
                                           id="contact-subject"
                                           class="form-control form-control-lg @error('subject') is-invalid @enderror"
                                           value="{{ old('subject') }}"
                                           placeholder="Question sur un article, partenariat..."
                                           required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="contact-message" class="form-label fw-bold text-medical-blue">
                                        <i class="bi bi-pencil-fill me-1"></i>Message <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="message"
                                              id="contact-message"
                                              class="form-control form-control-lg @error('message') is-invalid @enderror"
                                              rows="6"
                                              placeholder="Décrivez votre demande..."
                                              required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-medical btn-lg px-5">
                                        <i class="bi bi-send-fill me-2"></i>Envoyer le message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
