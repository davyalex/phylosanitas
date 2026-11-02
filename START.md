# 🚀 Guide de démarrage - Phylosanitas

## ✅ État du projet

- ✅ Base de données créée et migrée
- ✅ 2 catégories initialisées (sondage, actualites)
- ✅ Optimisations de cache implémentées
- ✅ Observers configurés
- ✅ Gestion d'erreurs robuste

---

## 🎯 Démarrage rapide

### 1. Démarrer le serveur de développement

```bash
php artisan serve
```

L'application sera accessible à : **http://127.0.0.1:8000**

### 2. Alternative avec Laragon

Si vous utilisez Laragon, l'application est accessible à :
**http://phylosanitas.test**

---

## 📦 Installation (si nécessaire)

### Si vous clonez le projet pour la première fois :

```bash
# 1. Installer les dépendances PHP
composer install

# 2. Installer les dépendances JavaScript
npm install

# 3. Copier le fichier .env
copy .env.example .env

# 4. Générer la clé d'application
php artisan key:generate

# 5. Configurer la base de données dans .env
# DB_DATABASE=phylosanitas
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Exécuter les migrations
php artisan migrate

# 7. Initialiser les catégories
php artisan categories:initialize

# 8. Créer le lien symbolique pour le storage
php artisan storage:link

# 9. Compiler les assets
npm run dev
# ou pour la production :
npm run build
```

---

## 🔧 Commandes de maintenance

### Cache et optimisation

```bash
# Nettoyer tous les caches
php artisan optimize:clear

# Mettre en cache pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimiser l'autoloader
composer dump-autoload -o
```

### Base de données

```bash
# Voir l'état des migrations
php artisan migrate:status

# Rafraîchir la base de données (ATTENTION : efface les données)
php artisan migrate:fresh

# Rafraîchir avec seeders
php artisan migrate:fresh --seed

# Initialiser les catégories de base
php artisan categories:initialize
```

### Développement

```bash
# Démarrer le serveur
php artisan serve

# Démarrer Vite pour le hot reload des assets
npm run dev

# Compiler les assets pour la production
npm run build

# Lancer les tests
php artisan test
```

---

## 👤 Créer un utilisateur administrateur

```bash
php artisan tinker
```

Puis dans tinker :

```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@phylosanitas.com';
$user->password = bcrypt('password123');
$user->save();
```

Ou créez une commande dédiée :

```bash
php artisan make:command CreateAdminUser
```

---

## 📊 Vérifier le statut de l'application

```bash
# Informations générales
php artisan about

# Lister les routes
php artisan route:list

# Lister les commandes disponibles
php artisan list
```

---

## 🐛 Résolution de problèmes

### L'application ne démarre pas

1. Vérifier que le fichier `.env` existe et est correctement configuré
2. Vérifier que la base de données est accessible
3. Nettoyer tous les caches : `php artisan optimize:clear`
4. Régénérer la clé : `php artisan key:generate`

### Erreurs de permissions

```bash
# Windows (PowerShell en tant qu'administrateur)
icacls "storage" /grant Users:F /T
icacls "bootstrap\cache" /grant Users:F /T
```

### Erreurs de base de données

1. Vérifier que MySQL est démarré dans Laragon
2. Vérifier les credentials dans `.env`
3. S'assurer que la base de données `phylosanitas` existe :

```sql
CREATE DATABASE IF NOT EXISTS phylosanitas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Le cache ne fonctionne pas correctement

```bash
# Nettoyer complètement
php artisan optimize:clear
php artisan cache:clear

# Redémarrer le serveur
```

---

## 🌐 URLs importantes

| Route | Description |
|-------|-------------|
| `/` | Page d'accueil |
| `/login` | Connexion admin |
| `/admin` | Dashboard admin |
| `/post` | Liste des posts |
| `/contact` | Page de contact |

---

## 📁 Structure importante

```
phylosanitas/
├── app/
│   ├── Console/Commands/
│   │   └── InitializeCategories.php    # Commande d'initialisation
│   ├── Http/Controllers/               # Contrôleurs
│   ├── Models/                          # Modèles Eloquent
│   ├── Observers/                       # Observers pour le cache
│   │   ├── CategoryObserver.php
│   │   └── PostObserver.php
│   └── Providers/
│       └── AppServiceProvider.php       # Service provider principal (optimisé)
├── database/
│   └── migrations/                      # Migrations SQL
├── public/                              # Fichiers publics
├── resources/
│   ├── views/                           # Vues Blade
│   ├── js/                              # JavaScript
│   └── css/                             # CSS
└── routes/
    └── web.php                          # Routes web
```

---

## 🎓 Documentation Laravel

- [Laravel 9 Documentation](https://laravel.com/docs/9.x)
- [Blade Templates](https://laravel.com/docs/9.x/blade)
- [Eloquent ORM](https://laravel.com/docs/9.x/eloquent)
- [Caching](https://laravel.com/docs/9.x/cache)

---

## 📞 Support

Pour toute question ou problème, consultez :
1. Le fichier [OPTIMIZATIONS.md](OPTIMIZATIONS.md) pour les détails des optimisations
2. Les logs dans `storage/logs/laravel.log`
3. La documentation Laravel

---

**Date de mise à jour** : 17 décembre 2025  
**Version** : 1.0.0  
**Status** : ✅ Prêt pour le développement
