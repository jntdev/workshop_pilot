<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Workshop Pilot - Configuration du projet

### Stack technique

Ce projet Laravel est configuré avec les technologies suivantes :

- **PHP** 8.2.27
- **Laravel** 12
- **Livewire** 3.6 - Framework frontend réactif sans JavaScript
- **Tailwind CSS** 4.0 - Framework CSS utility-first
- **Vite** 7.0 - Build tool moderne
- **Laravel Boost** 1.8 - Serveur MCP pour le développement

### Livewire

Livewire est installé et configuré. Il permet de créer des interfaces utilisateur interactives sans écrire de JavaScript.

**Composant de démonstration :**
- Route : `/counter`
- Composant : `App\Livewire\Counter`
- Vue : `resources/views/livewire/counter.blade.php`

**Tests :**
Tous les tests Livewire sont dans `tests/Feature/CounterTest.php` et passent avec succès.

### Laravel Boost MCP

Laravel Boost est configuré comme serveur MCP (Model Context Protocol) pour améliorer l'expérience de développement.

**Configuration MCP :**
Fichier `.mcp.json` à la racine du projet :
```json
{
    "mcpServers": {
        "laravel-boost": {
            "command": "/opt/homebrew/opt/php@8.2/bin/php",
            "args": ["artisan", "boost:mcp"]
        }
    }
}
```

**Outils disponibles via Laravel Boost :**
- `search-docs` - Recherche dans la documentation Laravel/packages
- `list-artisan-commands` - Liste des commandes Artisan
- `get-absolute-url` - Génération d'URLs absolues
- `tinker` - Exécution de code PHP/Eloquent
- `database-query` - Requêtes de base de données
- `browser-logs` - Lecture des logs navigateur

### Développement

**Démarrer le serveur de développement :**
```bash
npm run dev
```

Cette commande lance automatiquement :
1. Vite (hot reload des assets)
2. Laravel Boost MCP (serveur MCP)

**Autres commandes utiles :**
```bash
composer run dev        # Lance serveur, queue, logs et vite
php artisan test        # Exécute les tests
vendor/bin/pint        # Formatage du code PHP
npm run build          # Build des assets pour production
```

### Routes disponibles

- `/` - Page d'accueil Laravel
- `/counter` - Démo du composant Livewire Counter

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
