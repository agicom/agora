# Agora

Mini-plateforme Laravel de gestion de tournois LAN.

Le projet couvre deux parcours principaux :

- un back-office Filament pour administrer les tournois et consulter les inscriptions ;
- une page publique Livewire permettant d'inscrire une équipe à un tournoi via son slug, sans authentification.

## Stack

- Laravel 13
- Livewire 4, Alpine.js, Tailwind CSS 4
- Flux UI 2
- Filament 5
- PostgreSQL
- Pest 4

## Prérequis

- PHP 8.5 compatible avec les extensions Laravel usuelles
- Composer
- Node.js et npm
- Docker, pour lancer PostgreSQL avec `compose.dev.yaml`

## Installation

1. Lancer PostgreSQL :

```bash
docker compose -f compose.dev.yaml up -d
```

2. Installer l'application :

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
```

3. Migrer et charger les données de démo :

```bash
php artisan migrate:fresh --seed
```

4. Construire les assets ou lancer le serveur de dev :

```bash
npm run build
```

ou :

```bash
composer run dev
```

## Accès de démo

Le seeder crée un administrateur :

- URL admin : `/admin`
- Email : `admin@agora.test`
- Mot de passe : `password`

Les données de démo incluent :

- `Friday Arena`, tournoi ouvert avec deux inscriptions ;
- `Solo Sprint`, tournoi ouvert mais complet ;
- `Strategy Masters`, tournoi clos ;
- `Winter Cup`, tournoi en brouillon.

Exemples de pages publiques :

- `/tournois/friday-arena/inscription`
- `/tournois/solo-sprint/inscription`

## Tests

Lancer la suite applicative :

```bash
php artisan test --compact
```

Le projet couvre notamment :

- le modèle de données tournois / équipes / utilisateurs publics / inscriptions ;
- les règles métier d'inscription et de capacité ;
- le parcours public Livewire ;
- l'accès au panel Filament et la création de tournois ;
- les seeders de démo.

## Choix techniques notables

- Les équipes sont des entités durables et ne sont pas embarquées dans les inscriptions.
- Les règles sensibles restent dans `app/Actions`, hors des couches Filament et Livewire.
- PostgreSQL est utilisé comme base relationnelle de développement.
- `users` porte à la fois les administrateurs et les utilisateurs publics, distingués par `role`.
- Filament est réservé au back-office admin ; le parcours public reste accessible sans authentification.

Les ADR correspondantes sont dans `docs/adr`.
