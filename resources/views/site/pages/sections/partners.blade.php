<!-- Section Partenaires & Publicités -->
<section class="partners-section py-4" style="background: #f8f9fa;">
    <div class="container">
        <div class="row mb-3">
            <div class="col-12 text-center">
                <h5 class="text-muted fw-bold">
                    <i class="bi bi-star-fill text-warning me-2"></i>
                    Nos Partenaires de Confiance
                </h5>
            </div>
        </div>

        <div class="row g-3">
            <!-- Bannière principale large -->
            <div class="col-12">
                <div class="partner-banner partner-banner-large">
                    <div class="partner-content">
                        <span class="badge bg-warning text-dark mb-2">Partenaire Principal</span>
                        <h4 class="mb-2">Espace Publicitaire Premium</h4>
                        <p class="mb-0 text-muted">728 x 90 px - Bannière horizontale</p>
                    </div>
                </div>
            </div>

            <!-- Bannières moyennes -->
            <div class="col-lg-6 col-md-6">
                <div class="partner-banner partner-banner-medium">
                    <div class="partner-content">
                        <span class="badge bg-primary mb-2">Partenaire Santé</span>
                        <h5 class="mb-2">Espace Publicitaire</h5>
                        <p class="mb-0 text-muted small">336 x 280 px</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6">
                <div class="partner-banner partner-banner-medium">
                    <div class="partner-content">
                        <span class="badge bg-success mb-2">Partenaire Médical</span>
                        <h5 class="mb-2">Espace Publicitaire</h5>
                        <p class="mb-0 text-muted small">336 x 280 px</p>
                    </div>
                </div>
            </div>

            <!-- Bannières petites -->
            <div class="col-lg-4 col-md-6">
                <div class="partner-banner partner-banner-small">
                    <div class="partner-content">
                        <span class="badge bg-info text-dark mb-2">Partenaire</span>
                        <p class="mb-0 fw-bold">Publicité</p>
                        <small class="text-muted">250 x 250 px</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="partner-banner partner-banner-small">
                    <div class="partner-content">
                        <span class="badge bg-info text-dark mb-2">Partenaire</span>
                        <p class="mb-0 fw-bold">Publicité</p>
                        <small class="text-muted">250 x 250 px</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="partner-banner partner-banner-small">
                    <div class="partner-content">
                        <span class="badge bg-info text-dark mb-2">Partenaire</span>
                        <p class="mb-0 fw-bold">Publicité</p>
                        <small class="text-muted">250 x 250 px</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Note pour les partenaires -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Intéressé par un partenariat ? <a href="{{ route('contact') }}" class="text-medical-blue fw-bold">Contactez-nous</a>
                </p>
            </div>
        </div>
    </div>
</section>

<style>
.partner-banner {
    background: white;
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.partner-banner:hover {
    border-color: var(--medical-blue);
    box-shadow: 0 5px 15px rgba(0, 102, 204, 0.1);
    transform: translateY(-2px);
}

.partner-banner-large {
    min-height: 120px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.partner-banner-medium {
    min-height: 280px;
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
}

.partner-banner-small {
    min-height: 250px;
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
}

.partner-content {
    padding: 20px;
    width: 100%;
}

.partner-banner::before {
    content: '';
    position: absolute;
    top: 10px;
    right: 10px;
    width: 40px;
    height: 40px;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="rgba(0,102,204,0.1)"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>') no-repeat center;
    background-size: contain;
    opacity: 0.3;
}

@media (max-width: 768px) {
    .partner-banner-large {
        min-height: 100px;
    }
    
    .partner-banner-medium {
        min-height: 200px;
    }
    
    .partner-banner-small {
        min-height: 180px;
    }
}
</style>
