/*
 * Animations JavaScript pour le thème de Pâques
 */

document.addEventListener('DOMContentLoaded', function() {
    createEasterEggs();
    addEasterBadge();
});

function createEasterEggs() {
    const eggsContainer = document.createElement('div');
    eggsContainer.className = 'easter-eggs';
    document.body.appendChild(eggsContainer);

    const eggChars = ['🥚', '🐣', '🐰', '🌷', '🥕', '🐇'];
    const numberOfEggs = 40;

    for (let i = 0; i < numberOfEggs; i++) {
        const egg = document.createElement('div');
        egg.className = 'easter-egg';
        egg.textContent = eggChars[Math.floor(Math.random() * eggChars.length)];
        egg.style.left = Math.random() * 100 + '%';
        const size = Math.random() * 1 + 1;
        egg.style.fontSize = size + 'em';
        const duration = Math.random() * 8 + 8;
        egg.style.animationDuration = duration + 's';
        egg.style.animationDelay = Math.random() * 5 + 's';
        eggsContainer.appendChild(egg);
    }
}

function addEasterBadge() {
    const badge = document.createElement('div');
    badge.className = 'easter-badge';
    badge.innerHTML = '🐰 Joyeuses Pâques ! Cliquez ici !';
    badge.title = 'Cliquez pour découvrir votre surprise de Pâques';
    badge.style.cursor = 'pointer';
    badge.addEventListener('click', function() {
        showEasterMessage();
    });
    document.body.appendChild(badge);
}

function showEasterMessage() {
    const messages = [
        '🐣 Joyeuses Pâques à tous !',
        '🥚 Que la chasse aux œufs soit fructueuse !',
        '🐰 Beaucoup de bonheur et de chocolat !',
        '🌷 Profitez du printemps et des fêtes !',
        '🥕 Santé, joie et gourmandises !',
        '🐇 Un panier rempli de surprises !'
    ];
    const randomMessage = messages[Math.floor(Math.random() * messages.length)];
    const alert = document.createElement('div');
    alert.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, #ffe5b4 0%, #f7e7ce 100%);
        color: #a0522d;
        padding: 30px 50px;
        border-radius: 20px;
        font-size: 24px;
        font-weight: bold;
        z-index: 99999;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: easter-popup 0.5s ease-out;
        text-align: center;
    `;
    alert.textContent = randomMessage;
    document.body.appendChild(alert);
    setTimeout(() => {
        alert.style.animation = 'easter-popup 0.5s ease-out reverse';
        setTimeout(() => alert.remove(), 500);
    }, 3000);
}

// Animation CSS pour le popup
const style = document.createElement('style');
style.textContent = `
    @keyframes easter-popup {
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
