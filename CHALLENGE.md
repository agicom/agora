# Agora — Challenge technique

> Mini-plateforme de gestion de tournois / LAN — challenge technique Agicom

**Poste :** Développeur·se web full-stack
**Durée estimée :** 4 à 8 h, à réaliser chez toi
**Restitution :** présentation et échange lors d'un prochain entretien

---

## Contexte

On a regardé ton portfolio avec attention : ton profil AdonisJS + Nuxt/Vue, ton appétence pour le full-stack, le temps réel et l'outillage nous parlent beaucoup. En interne, on travaille sur la **TALL stack** (Tailwind, Alpine.js, Laravel, Livewire), complétée par **Filament** pour nos back-offices.

AdonisJS et Laravel partagent énormément (MVC, ORM, migrations, conventions), donc la marche est faible côté backend. La vraie nouveauté pour toi sera sans doute le paradigme **Livewire** (réactivité côté serveur, pas de SPA) et son écosystème.

**Ce challenge n'attend pas une maîtrise préalable de la stack.** On veut surtout voir comment tu transposes ton expérience, comment tu structures, et comment tu raisonnes sur le découpage des responsabilités. Découvrir Livewire / Filament pendant l'exercice fait partie du jeu, et on en discutera ensemble.

---

## Le sujet

Une mini-plateforme de gestion de **tournois / LAN** :

- les **administrateurs** gèrent les tournois et les inscriptions via un back-office ;
- le **public** s'inscrit à un tournoi via une page d'inscription dédiée.

Volontairement simple sur le périmètre fonctionnel, pour laisser la place à la qualité d'exécution.

---

## Stack imposée

- **Laravel** (dernière version stable)
- **Livewire** + **Alpine.js** + **Tailwind CSS**
- **Filament v5** pour le back-office
- **Livewire Flux** pour les composants d'UI (voir « Outils » ci-dessous)
- **Pest** pour les tests
- Base de données au choix (MySQL ou PostgreSQL)

---

## Périmètre attendu

### Partie A — Back-office en Filament (~3 h)

- Panel Filament authentifié
- Une `Resource` complète pour les tournois : table (recherche, filtres, tri, badges de statut) + formulaire de création/édition avec validation
- Un **Relation Manager** sur les inscriptions, accessible depuis la fiche d'un tournoi
- Un **widget de stats** (ex. nombre d'inscrits), idéalement rafraîchi en quasi temps réel (polling)

L'idée est de voir un usage **idiomatique** de Filament — pas de tout recoder à la main.

### Partie B — Page publique en Livewire / Alpine (~2 h, hors Filament)

- Page d'inscription publique accessible via un slug, **sans authentification**
- Un composant **Livewire écrit à la main** : formulaire d'inscription avec **validation en temps réel** et logique de capacité (blocage quand le tournoi est complet, badge « Complet »)
- **Alpine** pour le micro-interactif uniquement (toast de confirmation, bouton « copier le lien d'inscription », etc.)
- Rendu **Tailwind** propre et responsive

Cette partie nous permet d'évaluer ton Livewire « brut », là où Filament ne t'assiste pas.

### Partie C — Tests avec Pest (~1 h)

On attend des **tests automatisés écrits avec [Pest](https://pestphp.com)**, couvrant au minimum la logique métier sensible :

- **Tests unitaires** sur les règles d'inscription / capacité (un tournoi complet refuse les inscriptions, le décompte des places est correct, pas de double inscription…).
- Au moins un **test de composant Livewire** sur le formulaire public (validation, ajout d'une inscription valide) — Livewire fournit `Livewire::test()`, parfaitement utilisable depuis Pest.
- L'utilisation de **factories** dans les tests est attendue (et te servira aussi pour les seeders).

Pas besoin d'une couverture exhaustive : on regarde surtout la **pertinence** des cas choisis et la lisibilité des tests. La suite doit passer au vert avec un simple `php artisan test` (ou `./vendor/bin/pest`).

---

## Bonus (si tu vas vers les 8 h, pour te démarquer)

- Seeders pour disposer de données de démo dès l'installation
- Une action personnalisée côté Filament (clôturer les inscriptions, export CSV des inscrits…)
- Gestion des autorisations via Policies (et tests associés)

---

## Outils recommandés

Tu es libre de ton environnement, mais voici ce qu'on utilise et qu'on te conseille :

- **Laravel Herd** — environnement de dev local (PHP, serveur, base de données) sur macOS / Windows. Le plus simple pour démarrer un projet Laravel proprement.
- **TablePlus** — client graphique pour inspecter et requêter ta base de données.
- **Laravel Boost** — package officiel (`composer require laravel/boost --dev` puis `php artisan boost:install`) qui expose un serveur MCP et des guidelines à ton agent IA, pour qu'il génère du code Laravel idiomatique et à jour. Très utile si tu codes avec un assistant IA.
- **Livewire Flux** — bibliothèque de composants d'UI officielle de l'équipe Livewire. Les composants Pro nécessitent une licence : on te la fournit.
  - **Licence Flux Pro : `xxx-xxx`**

### Usage de l'IA

**Liberté totale.** Tu peux (et c'est encouragé) t'appuyer sur les assistants IA de ton choix. On s'intéresse au résultat, à tes choix d'architecture et à ta capacité à expliquer ce que tu livres — pas à savoir si tu as tapé chaque ligne à la main.

---

## Livrables

- Tu travailles **directement dans ce dépôt** (GitHub, qu'on standardise en interne). Commits réguliers et lisibles appréciés.
- Complète ce README (ou ajoute une section dédiée) avec : **prérequis et installation** (idéalement un démarrage en une commande) et tes **choix techniques** notables.
- Une **courte démo vidéo** (2–3 min, type Loom) qui présente le résultat.

---

## Ce qu'on évaluera ensemble en entretien

- **Filament** : usage idiomatique (Resources, Relation Managers, widgets) plutôt que du code réinventé
- **Livewire brut** : qualité du composant public, validation, gestion des cas limites (capacité atteinte, double inscription)
- **Tests Pest** : pertinence des cas couverts, lisibilité, usage des factories
- **Découpage des responsabilités** : pourquoi Filament ici, Livewire là, Alpine seulement pour le micro-interactif
- **Qualité générale** : relations Eloquent, structure, lisibilité, README
- **Ton recul** sur le passage Adonis/Nuxt → TALL + Filament : ce qui t'a surpris, ce qui se transpose bien, ce qui coince

---

## Ressources pour démarrer

- Documentation Laravel — https://laravel.com/docs
- Documentation Livewire 3 — https://livewire.laravel.com/docs
- Documentation Filament v5 — https://filamentphp.com/docs
- Livewire Flux — https://fluxui.dev
- Pest — https://pestphp.com/docs
- Laravel Boost — https://laravel.com/docs/boost

Bon courage, et surtout : amuse-toi. On a hâte de voir ce que tu en fais et d'en discuter avec toi.
