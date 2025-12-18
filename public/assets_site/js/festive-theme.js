/**
 * Animations JavaScript pour le thème festif
 */

document.addEventListener('DOMContentLoaded', function() {
    // Créer les flocons de neige
    createSnowflakes();
    
    // Initialiser le compte à rebours
    initCountdown();
    
    // Ajouter les effets sonores (optionnel)
    addFestiveSounds();
});

/**
 * Créer des flocons de neige animés
 */
function createSnowflakes() {
    const snowflakesContainer = document.createElement('div');
    snowflakesContainer.className = 'snowflakes';
    document.body.appendChild(snowflakesContainer);
    
    const snowflakeChars = ['❄', '❅', '❆', '✻', '✼', '❉'];
    const numberOfFlakes = 50;
    
    for (let i = 0; i < numberOfFlakes; i++) {
        const snowflake = document.createElement('div');
        snowflake.className = 'snowflake';
        snowflake.textContent = snowflakeChars[Math.floor(Math.random() * snowflakeChars.length)];
        
        // Position aléatoire
        snowflake.style.left = Math.random() * 100 + '%';
        
        // Taille aléatoire
        const size = Math.random() * 1 + 0.5;
        snowflake.style.fontSize = size + 'em';
        
        // Durée d'animation aléatoire
        const duration = Math.random() * 10 + 10;
        snowflake.style.animationDuration = duration + 's';
        
        // Délai aléatoire
        snowflake.style.animationDelay = Math.random() * 5 + 's';
        
        snowflakesContainer.appendChild(snowflake);
    }
}

/**
 * Initialiser le compte à rebours pour le Nouvel An
 */
function initCountdown() {
    const countdownBanner = document.createElement('div');
    countdownBanner.className = 'countdown-banner';
    countdownBanner.innerHTML = `
        <div class="countdown-label">🎊 Nouvel An 2026 🎊</div>
        <div class="countdown-time">
            <div class="time-unit">
                <div class="time-value" id="days">00</div>
                <div class="time-label">Jours</div>
            </div>
            <div>:</div>
            <div class="time-unit">
                <div class="time-value" id="hours">00</div>
                <div class="time-label">Heures</div>
            </div>
            <div>:</div>
            <div class="time-unit">
                <div class="time-value" id="minutes">00</div>
                <div class="time-label">Min</div>
            </div>
            <div>:</div>
            <div class="time-unit">
                <div class="time-value" id="seconds">00</div>
                <div class="time-label">Sec</div>
            </div>
        </div>
    `;
    document.body.appendChild(countdownBanner);
    
    // Mettre à jour le compte à rebours chaque seconde
    updateCountdown();
    setInterval(updateCountdown, 1000);
}

/**
 * Mettre à jour le compte à rebours
 */
function updateCountdown() {
    const newYear = new Date('January 1, 2026 00:00:00').getTime();
    const now = new Date().getTime();
    const distance = newYear - now;
    
    if (distance < 0) {
        // Le Nouvel An est passé
        document.querySelector('.countdown-banner').innerHTML = `
            <div style="font-size: 20px;">🎉 Bonne Année 2026! 🎉</div>
        `;
        return;
    }
    
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    document.getElementById('days').textContent = String(days).padStart(2, '0');
    document.getElementById('hours').textContent = String(hours).padStart(2, '0');
    document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
    document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
}

/**
 * Ajouter des effets sonores festifs (optionnel)
 */
function addFestiveSounds() {
    // Badge festif avec message
    const festiveBadge = document.createElement('div');
    festiveBadge.className = 'festive-badge';
    festiveBadge.innerHTML = '<i class="bi bi-star-fill"></i> 🎁 Cliquez ici pour une surprise! 🎄';
    festiveBadge.title = 'Cliquez pour découvrir votre message festif';
    festiveBadge.style.cursor = 'pointer';
    
    // Animation pour attirer l'attention
    festiveBadge.style.animation = 'pulse-festive 2s ease-in-out infinite, shake-badge 3s ease-in-out infinite';
    
    festiveBadge.addEventListener('click', function() {
        showFestiveMessage();
    });
    
    document.body.appendChild(festiveBadge);
}

// Ajouter l'animation shake
const shakeStyle = document.createElement('style');
shakeStyle.textContent = `
    @keyframes shake-badge {
        0%, 100% { transform: translateX(-50%) rotate(0deg); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-50%) rotate(-5deg); }
        20%, 40%, 60%, 80% { transform: translateX(-50%) rotate(5deg); }
    }
`;
document.head.appendChild(shakeStyle);

/**
 * Afficher un message festif
 */
function showFestiveMessage() {
    const messages = [
        '🎄 Joyeux Noël! 🎄',
        '🎊 Bonne Année 2026! 🎊',
        '⭐ Meilleurs vœux de santé! ⭐',
        '🎁 Que cette année vous apporte bonheur et santé! 🎁',
        '❄️ Joyeuses Fêtes de fin d\'année! ❄️'
    ];
    
    const randomMessage = messages[Math.floor(Math.random() * messages.length)];
    
    // Créer une alerte festive
    const alert = document.createElement('div');
    alert.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
        padding: 30px 50px;
        border-radius: 20px;
        font-size: 24px;
        font-weight: bold;
        z-index: 99999;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        animation: festive-popup 0.5s ease-out;
        text-align: center;
    `;
    alert.textContent = randomMessage;
    
    document.body.appendChild(alert);
    
    // Retirer après 3 secondes
    setTimeout(() => {
        alert.style.animation = 'festive-popup 0.5s ease-out reverse';
        setTimeout(() => alert.remove(), 500);
    }, 3000);
}

// Animation CSS pour le popup
const style = document.createElement('style');
style.textContent = `
    @keyframes festive-popup {
        0% {
            transform: translate(-50%, -50%) scale(0);
            opacity: 0;
        }
        50% {
            transform: translate(-50%, -50%) scale(1.1);
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
