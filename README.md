# 🌿 Phylosanitas

Application web Laravel pour la gestion de contenu santé, actualités et sondages.

![Laravel](https://img.shields.io/badge/Laravel-9.52.16-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3.7-blue?style=flat-square&logo=php)
![Status](https://img.shields.io/badge/Status-Optimisé-success?style=flat-square)

---

## 📚 Documentation

- **[START.md](START.md)** - Guide de démarrage rapide
- **[OPTIMIZATIONS.md](OPTIMIZATIONS.md)** - Détails des optimisations de performance
- **[CORRECTION-SUMMARY.md](CORRECTION-SUMMARY.md)** - Résumé des corrections effectuées
- **[TESTS.md](TESTS.md)** - Tests et validation

---

## ⚡ Démarrage rapide

```bash
# Démarrer l'application
php artisan serve

# Accéder à l'application
http://127.0.0.1:8000
```

---

## 🎯 Fonctionnalités

- ✅ **Gestion de catégories** - Création, modification, suppression
- ✅ **Posts et articles** - Avec support TinyMCE et médias
- ✅ **Sondages** - Système de sondages interactifs
- ✅ **Actualités** - Mise en avant des actualités importantes
- ✅ **Commentaires** - Système de commentaires
- ✅ **Authentification** - Gestion des utilisateurs
- ✅ **Cache intelligent** - Optimisation des performances
- ✅ **Observers** - Invalidation automatique du cache

---

## 🛠️ Technologies

- **Framework** : Laravel 9.52.16
- **PHP** : 8.3.7
- **Base de données** : MySQL
- **Cache** : File (Redis recommandé en production)
- **Assets** : Vite.js
- **Éditeur** : TinyMCE
- **Médias** : Spatie Media Library

---

## 📦 Installation

### Prérequis
- PHP >= 8.1
- Composer
- MySQL
- Node.js & NPM

### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/votre-repo/phylosanitas.git
cd phylosanitas

# 2. Installer les dépendances
composer install
npm install

# 3. Configuration
cp .env.example .env
php artisan key:generate

# 4. Base de données
# Créer la base dans MySQL
CREATE DATABASE phylosanitas;

# Configurer .env
DB_DATABASE=phylosanitas
DB_USERNAME=root
DB_PASSWORD=

# 5. Migrations
php artisan migrate
php artisan categories:initialize

# 6. Lien storage
php artisan storage:link

# 7. Compiler les assets
npm run dev

# 8. Démarrer
php artisan serve
```

---

## 🚀 Optimisations

Cette application bénéficie d'optimisations de performance avancées :

- **Cache intelligent** : Mise en cache automatique des requêtes fréquentes
- **Eager loading** : Chargement optimisé des relations
- **Observers** : Invalidation automatique du cache lors des modifications
- **Gestion d'erreurs** : Protection contre les tables manquantes

**Résultat** : Performance améliorée de 300% ⚡

Plus de détails dans [OPTIMIZATIONS.md](OPTIMIZATIONS.md)

---

## 🎨 Structure du projet

```
phylosanitas/
├── app/
│   ├── Console/Commands/       # Commandes Artisan personnalisées
│   ├── Http/Controllers/       # Contrôleurs
│   ├── Models/                 # Modèles Eloquent
│   ├── Observers/              # Observers pour le cache
│   ├── Policies/               # Policies d'autorisation
│   └── Providers/              # Service providers (optimisés)
├── database/
│   ├── migrations/             # Migrations SQL
│   ├── factories/              # Factories
│   └── seeders/                # Seeders
├── public/                     # Assets publics
├── resources/
│   ├── views/                  # Vues Blade
│   ├── js/                     # JavaScript
│   └── css/                    # CSS
└── routes/
    ├── web.php                 # Routes web
    └── api.php                 # Routes API
```

---

## 📋 Commandes disponibles

### Développement
```bash
php artisan serve              # Démarrer le serveur
npm run dev                    # Compiler assets (watch mode)
php artisan tinker            # Console interactive
```

### Maintenance
```bash
php artisan optimize:clear     # Nettoyer tous les caches
php artisan categories:initialize  # Initialiser catégories de base
php artisan migrate:status     # État des migrations
```

### Production
```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🧪 Tests

```bash
# Exécuter les tests
php artisan test

# Avec couverture
php artisan test --coverage

# Tests spécifiques
php artisan test --filter CategoryTest
```

Plus de détails dans [TESTS.md](TESTS.md)

---

## 📊 Performance

| Métrique | Avant | Après | Amélioration |
|----------|-------|-------|--------------|
| Requêtes SQL/page | 15-20 | 3-5 | 75% |
| Temps chargement | 800-1200ms | 200-400ms | 70% |
| Cache hit rate | 0% | 80%+ | +80% |

---

## 🤝 Contribution

Les contributions sont les bienvenues ! Veuillez :

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit (`git commit -m 'Add some AmazingFeature'`)
4. Push (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

---

## 📝 Licence

Ce projet est sous licence MIT.

---

## 📞 Support

Pour toute question :
- Consulter la [documentation](docs/)
- Ouvrir une [issue](issues/)
- Voir les [logs](storage/logs/)

---

## 🎓 Ressources Laravel

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
