<div class="card card-medical p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="aside-title text-medical-blue fw-bold mb-0">
            <i class="bi bi-bar-chart-fill me-2"></i>
            Sondages
        </h3>
        <a href="/post?category=sondage" class="btn btn-sm btn-health">
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    
    <div class="sondage-carousel position-relative">
        @foreach ($sondage_front as $item)
            <div class="sondage-item position-absolute w-100" style="display: none; opacity: 0;">
                <div class="sondage-content p-3 rounded section-health-accent">
                    <div class="sondage-icon text-center mb-3">
                        <i class="bi bi-question-circle-fill text-medical-blue" style="font-size: 2.5rem;"></i>
                    </div>
                    <p class="text-dark mb-3" style="font-size: 0.95rem; line-height: 1.5;">
                        {!! Str::limit(strip_tags($item->description), 120, '...') !!}
                    </p>
                    <div class="text-center">
                        <a href="/post/detail?slug={{ $item['slug'] }}" class="btn btn-medical btn-sm w-100">
                            <i class="bi bi-hand-thumbs-up-fill me-2"></i>
                            Participer au sondage
                        </a>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            <i class="bi bi-clock-history"></i>
                            {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Indicateurs de pagination -->
    @if(count($sondage_front) > 1)
        <div class="text-center mt-2">
            <div class="sondage-dots">
                @foreach ($sondage_front as $index => $item)
                    <span class="dot" data-index="{{ $index }}"></span>
                @endforeach
            </div>
        </div>
    @endif
</div>

<style>
    .sondage-carousel {
        position: relative;
        min-height: 280px;
        overflow: hidden;
    }
    
    .sondage-item {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        transition: opacity 0.5s ease-in-out;
    }
    
    .sondage-item.active {
        display: block !important;
        opacity: 1 !important;
        z-index: 2;
    }
    
    .sondage-content {
        animation: fadeInDown 0.5s ease-in-out;
    }
    
    .sondage-dots {
        display: inline-flex;
        gap: 8px;
    }
    
    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--gray-medium);
        cursor: pointer;
        transition: var(--transition-fast);
    }
    
    .dot.active {
        background: var(--medical-blue);
        width: 24px;
        border-radius: 5px;
    }
    
    .dot:hover {
        background: var(--medical-blue-light);
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        var sondageItems = $('.sondage-item');
        var dots = $('.dot');
        var currentIndex = 0;
        
        if (sondageItems.length <= 1) {
            sondageItems.first().addClass('active').css({display: 'block', opacity: 1});
            return;
        }
        
        // Mettre à jour l'affichage
        function showSondage(index) {
            // Cacher tous les items
            sondageItems.removeClass('active').css('opacity', 0);
            
            // Afficher l'item actuel avec transition
            setTimeout(function() {
                sondageItems.eq(index).addClass('active');
            }, 100);
            
            // Mettre à jour les dots
            dots.removeClass('active').eq(index).addClass('active');
        }
        
        // Auto-rotation
        function nextSondage() {
            currentIndex = (currentIndex + 1) % sondageItems.length;
            showSondage(currentIndex);
        }
        
        // Initialiser
        showSondage(0);
        
        // Click sur les dots
        dots.click(function() {
            currentIndex = $(this).data('index');
            showSondage(currentIndex);
        });
        
        // Rotation automatique toutes les 7 secondes
        setInterval(nextSondage, 7000);
    });
</script>
