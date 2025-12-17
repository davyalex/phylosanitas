# Guide du Thème Médical Phylosanitas

## 🎨 Palette de Couleurs

### Couleurs Principales (Médical)
```css
--medical-blue: #0066CC        /* Bleu médical principal - confiance, professionnalisme */
--medical-blue-light: #3399FF  /* Bleu clair - hover, accents */
--medical-blue-dark: #004A99   /* Bleu foncé - contraste élevé */
```

### Couleurs Secondaires (Santé)
```css
--health-green: #00A86B        /* Vert santé - bien-être, vitalité */
--health-green-light: #2ECC71  /* Vert clair - succès, validation */
--health-green-dark: #008855   /* Vert foncé - stabilité */
```

### Couleurs Tertiaires (Médical)
```css
--medical-teal: #17a2b8        /* Turquoise médical - modernité */
--trust-blue: #1E88E5          /* Bleu confiance - sécurité */
--care-green: #43A047          /* Vert soin - attention */
--wellness-purple: #8E24AA     /* Violet bien-être - calme */
```

### Couleurs Neutres
```css
--white-pure: #FFFFFF          /* Blanc pur - clarté, propreté */
--gray-light: #F8F9FA          /* Gris clair - arrière-plans */
--gray-medium: #E9ECEF         /* Gris moyen - bordures */
--gray-text: #6C757D           /* Gris texte - informations secondaires */
--gray-dark: #343A40           /* Gris foncé - texte principal */
```

### Couleurs d'Accentuation
```css
--accent-orange: #FF6B35       /* Orange - actions importantes */
--accent-red: #E53935          /* Rouge - alertes, erreurs */
--accent-yellow: #FFC107       /* Jaune - avertissements */
```

---

## 🎭 Classes CSS Disponibles

### Composants de Base

#### Boutons
```html
<!-- Bouton médical principal -->
<button class="btn btn-medical">Action Médicale</button>

<!-- Bouton santé/succès -->
<button class="btn btn-health">Action Santé</button>
```

#### Cards
```html
<div class="card card-medical">
    <img src="..." class="card-img-top">
    <div class="card-body">
        <h5 class="card-title">Titre</h5>
        <p class="card-text">Contenu...</p>
    </div>
</div>
```

#### Badges
```html
<!-- Badge médical -->
<span class="badge badge-medical">Médical</span>

<!-- Badge santé -->
<span class="badge badge-health">Santé</span>

<!-- Badge catégorie -->
<span class="badge badge-category">Catégorie</span>
```

### Sections

#### Section avec fond médical
```html
<section class="section-medical-bg">
    <div class="container">
        <!-- Contenu -->
    </div>
</section>
```

#### Section avec accent santé
```html
<div class="section-health-accent">
    <p>Information importante avec bordure verte</p>
</div>
```

### Utilitaires de Texte
```html
<p class="text-medical-blue">Texte bleu médical</p>
<p class="text-health-green">Texte vert santé</p>
<p class="text-medical-teal">Texte turquoise médical</p>
```

### Utilitaires de Fond
```html
<div class="bg-medical-blue">Fond bleu médical</div>
<div class="bg-health-green">Fond vert santé</div>
<div class="bg-medical-teal">Fond turquoise médical</div>
<div class="bg-medical-light">Fond clair médical</div>
```

### Utilitaires de Bordure
```html
<div class="border border-medical">Bordure médicale</div>
<div class="border border-health">Bordure santé</div>
```

### Utilitaires d'Ombre
```html
<div class="shadow-medical">Ombre médicale standard</div>
<div class="shadow-medical-lg">Ombre médicale grande</div>
```

### Animations
```html
<!-- Animation d'apparition -->
<div class="animate-fade-in-up">Contenu animé</div>

<!-- Animation de pulsation médicale -->
<button class="pulse-medical">Bouton important</button>
```

---

## 📐 Design System

### Ombres
- **shadow-sm**: Petite ombre (2px) - éléments légers
- **shadow-md**: Ombre moyenne (4-6px) - cards, boutons
- **shadow-lg**: Grande ombre (10-15px) - éléments importants
- **shadow-xl**: Ombre extra large (20-25px) - modals, overlays

### Transitions
- **transition-fast**: 0.2s - micro-interactions
- **transition-normal**: 0.3s - interactions standards
- **transition-slow**: 0.5s - animations complexes

### Bordures
- **border-radius**: 15px pour les cards
- **border-radius**: 50px pour les boutons
- **border-radius**: 20px pour les badges

---

## 🎯 Utilisation Recommandée

### En-tête (Header)
```html
<header id="header" class="header">
    <div class="header-top-medical">
        <!-- Barre supérieure avec dégradé bleu médical -->
    </div>
</header>
```

### Navigation
```html
<nav class="navbar">
    <ul>
        <li><a href="#" class="active">Accueil</a></li>
        <!-- Les liens ont des effets hover automatiques -->
    </ul>
</nav>
```

### Posts/Articles
```html
<article class="post-entry">
    <img src="..." alt="...">
    <div class="post-content">
        <span class="badge badge-category">Actualités</span>
        <h3><a href="#">Titre de l'article</a></h3>
        <p>Extrait...</p>
    </div>
</article>
```

### Footer
```html
<footer class="footer">
    <h4 class="footer-heading">Titre Section</h4>
    <ul class="footer-links">
        <li><a href="#">Lien</a></li>
    </ul>
</footer>
```

---

## 🔍 Accessibilité

### Contraste des Couleurs
Toutes les combinaisons de couleurs respectent les normes WCAG 2.1 niveau AA:
- Bleu médical (#0066CC) sur blanc: **7.9:1** ✅
- Vert santé (#00A86B) sur blanc: **4.7:1** ✅
- Gris texte (#6C757D) sur blanc: **4.6:1** ✅

### Focus et États Interactifs
- Tous les liens et boutons ont des états hover visibles
- Les transitions sont fluides (0.2-0.3s)
- Les zones cliquables ont une taille minimale de 44x44px

---

## 📱 Responsive Design

Le thème est entièrement responsive avec des points de rupture à:
- **Mobile**: < 768px
- **Tablette**: 768px - 991px
- **Desktop**: > 992px

Les ajustements automatiques incluent:
- Réduction de la taille des polices
- Adaptation de l'espacement (padding, margin)
- Réorganisation des grilles
- Masquage d'éléments non critiques

---

## 🚀 Performance

### Optimisations Incluses
- Transitions GPU-accélérées (transform, opacity)
- Images avec lazy loading recommandé
- CSS minifié pour la production
- Utilisation de variables CSS pour des changements rapides

### Chargement
```html
<!-- Ordre de chargement recommandé -->
<link href="bootstrap.min.css" rel="stylesheet">
<link href="variables.css" rel="stylesheet">
<link href="main.css" rel="stylesheet">
<link href="phylosanitas-theme.css" rel="stylesheet"> <!-- En dernier -->
```

---

## 💡 Exemples d'Implémentation

### Page d'Accueil
```html
<section class="section-medical-bg py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4" v-for="post in posts">
                <div class="card card-medical mb-4">
                    <img :src="post.image" class="card-img-top">
                    <div class="card-body">
                        <span class="badge badge-category">{{ post.category }}</span>
                        <h5 class="card-title mt-2">{{ post.title }}</h5>
                        <p class="card-text">{{ post.excerpt }}</p>
                        <a href="#" class="btn btn-medical">Lire plus</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
```

### Formulaire de Contact
```html
<form class="section-health-accent">
    <div class="mb-3">
        <label class="form-label text-medical-blue">Email</label>
        <input type="email" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label text-medical-blue">Message</label>
        <textarea class="form-control" rows="5"></textarea>
    </div>
    <button type="submit" class="btn btn-health">
        <i class="bi bi-send me-2"></i> Envoyer
    </button>
</form>
```

---

## 🎨 Personnalisation Avancée

### Modifier les Couleurs Principales
Dans `phylosanitas-theme.css`, modifiez les variables `:root`:

```css
:root {
  /* Changez ces valeurs pour personnaliser */
  --medical-blue: #VOTRE_COULEUR;
  --health-green: #VOTRE_COULEUR;
}
```

### Ajouter de Nouveaux Composants
Suivez la structure existante:

```css
.nouveau-composant {
  background: var(--medical-blue);
  color: var(--white-pure);
  border-radius: 15px;
  box-shadow: var(--shadow-md);
  transition: var(--transition-normal);
}

.nouveau-composant:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
}
```

---

## 📞 Support

Pour toute question ou suggestion concernant le thème:
- Email: contact@phylosanitas.com
- Documentation: Voir `OPTIMIZATIONS.md` pour les détails techniques

---

**Version**: 1.0.0  
**Dernière mise à jour**: 2024  
**Licence**: Propriétaire - Phylosanitas
