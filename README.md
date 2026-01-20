# Application de Gestion de Réservations de Véhicules – Lux Drive

<p align="center">
    <img src="/public/images/lux-drive-logo.png" alt="Lux Drive Logo" width="120" />
</p>


## Contexte de l’application

Lux Drive est une application web full-stack destinée à la gestion des réservations de véhicules au sein d’une organisation.  
Elle permet aux employés de réserver des véhicules pour leurs déplacements professionnels tout en garantissant la cohérence des disponibilités et en évitant les conflits d’usage et les chevauchements de réservations.

L’application a été conçue dans un contexte proche des contraintes réelles de production, avec une attention particulière portée à la sécurité, à la clarté des règles métier et à la maintenabilité du code.

---

## Cycle de vie des réservations (logique métier)

Les réservations suivent un **cycle de vie basé sur des statuts**, afin de refléter un processus métier réaliste et garantir l’intégrité des données.

- Lorsqu’une réservation est créée, elle est placée dans l’état **En attente**.
- Si un conflit de disponibilité est détecté lors de la création ou de la modification, la réservation est immédiatement **Refusée**.
- À la date de début de la période réservée, la réservation est automatiquement **Confirmée** par une tâche planifiée. Le véhicule est alors considéré comme indisponible.
- Une réservation peut être **Annulée** par son propriétaire avant le début effectif de la période. Le véhicule redevient alors disponible.
- Une fois la date de fin de la période écoulée, la réservation est automatiquement marquée comme **Terminée** par une tâche planifiée.

Cette approche garantit la cohérence des données, la gestion correcte des disponibilités et une expérience utilisateur conforme aux exigences réelles d’une application de gestion de ressources partagées.

---

## Choix techniques

### Backend
- **Laravel 12** : Framework PHP moderne avec architecture MVC claire
- **Laravel Fortify** : Système d’authentification sécurisé
- **Eloquent ORM** : Gestion des relations et requêtes optimisées
- **Form Requests** : Validation centralisée et sécurisée des données
- **Pest** : Framework de tests moderne et expressif

### Frontend
- **Vue.js 3** avec **TypeScript** : Framework réactif avec typage statique
- **Inertia.js v2** : Intégration fluide entre Laravel et Vue sans API REST classique
- **Tailwind CSS** : Framework CSS utilitaire pour un design moderne et responsive
- **Reka UI** : Composants UI accessibles et réutilisables
- **Laravel Wayfinder** : Génération automatique des types TypeScript pour les routes

### Base de données
- **SQLite** : Base de données légère pour le développement (configurable pour MySQL/PostgreSQL en production)
- **Migrations** : Versioning de la structure de la base de données
- **Factories & Seeders** : Génération de données de test

---

## Fonctionnalités implémentées

### Authentification

<img src="/public/images/login.png" alt="Lux Drive Logo" width="120" />
<img src="/public/images/register.png" alt="Lux Drive Logo" width="120" />
<img src="/public/images/forgot.png" alt="Lux Drive Logo" width="120" />
<img src="/public/images/welcome.png" alt="Lux Drive Logo" width="120" />
<img src="/public/images/welcome-connected.png" alt="Lux Drive Logo" width="120" />
<img src="/public/images/dashboard.png" alt="Lux Drive Logo" width="120" />

- Inscription et connexion des utilisateurs
- Authentification à deux facteurs (2FA)
- Gestion sécurisée des sessions

### Gestion des véhicules

<img src="/public/images/vehicles.png" alt="Lux Drive Logo" width="120" />


- Liste des véhicules disponibles
- Recherche et filtrage par disponibilité
- Pagination des résultats
- Consultation des détails d’un véhicule

### Gestion des réservations

#### Création

<img src="/public/images/create-reservation.png" alt="Lux Drive Logo" width="120" />

- Sélection d’un véhicule disponible
- Choix des dates de début et de fin
- Indication de la raison de la réservation
- Validation automatique des conflits de disponibilité

#### Consultation

<img src="/public/images/detail-reservation.png" alt="Lux Drive Logo" width="120" />

- Liste des réservations de l’utilisateur
- Affichage détaillé d’une réservation
- Visualisation du statut

#### Modification

<img src="/public/images/test1.png" alt="Lux Drive Logo" width="120" />


- Modification des dates et de la raison
- Revalidation automatique des conflits

#### Annulation


- Annulation possible par le propriétaire avant le début effectif

### Statuts de réservation

<img src="/public/images/reservations.png" alt="Lux Drive Logo" width="120" />



- **En attente** : La réservation est en attente de confirmation.
- **Confirmée** : La réservation a débuté et le véhicule est en cours d'utilisation.
- **Refusée** : La demande de réservation a été refusée en raison d'un conflit de disponibilité ou d'une incohérence.
- **Annulée** : La réservation a été annulée par l'utilisateur avant son début.
- **Terminée** : La période de réservation est écoulée.

---

## Règles métier critiques
<img src="/public/images/test2.png" alt="Lux Drive Logo" width="120" />
<img src="/public/images/test3.png" alt="Lux Drive Logo" width="120" />

1. **Prévention des chevauchements**
   - Un véhicule ne peut pas être réservé si une autre réservation (en attente ou confirmée) existe sur la même période.
   - Vérification lors de la création et de la modification.

2. **Validation des dates**
   - La date de début doit être future.
   - La date de fin doit être postérieure à la date de début.

3. **Sécurité**
   - Les utilisateurs ne peuvent consulter et modifier que leurs propres réservations.
   - Authentification obligatoire pour toutes les actions.
   - Protection CSRF intégrée.



<img src="/public/images/settings1.png" alt="Lux Drive Logo" width="120" />
<img src="/public/images/settings2.png" alt="Lux Drive Logo" width="120" />
<img src="/public/images/settings3.png" alt="Lux Drive Logo" width="120" />


---

## Structure de l’application

app/
├── Console/
│   └── Commands/
│       └── UpdateReservationStatus.php
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── VehicleController.php
│   │   └── ReservationController.php
│   └── Requests/
│       ├── StoreReservationRequest.php
│       └── UpdateReservationRequest.php
├── Models/
│   ├── User.php
│   ├── Vehicle.php
│   └── Reservation.php
database/
├── migrations/
│   ├── create_vehicles_table.php
│   └── create_reservations_table.php
├── factories/
│   ├── VehicleFactory.php
│   └── ReservationFactory.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── VehicleSeeder.php
    └── ReservationSeeder.php
resources/js/
├── Components/
│   ├── AppLogo.vue
│   └── AppSidebar.vue
├── Layouts/
│   └── Auth/
│       └── AuthSimpleLayout.vue
├── Pages/
│   ├── Dashboard.vue
│   ├── Vehicles/
│   │   ├── Index.vue
│   │   └── Show.vue
│   └── Reservations/
│       ├── Index.vue
│       ├── Create.vue
│       ├── Show.vue
│       └── Edit.vue
├── router.ts
tests/
├── Feature/
│   ├── DashboardTest.php
│   ├── VehicleTest.php
│   └── ReservationTest.php


---

## Installation et lancement

### Prérequis
- PHP 8.3 ou supérieur
- Composer
- Node.js 18+
- SQLite (ou MySQL/PostgreSQL)

### Installation

```bash
git clone <https://github.com/Johncross00/car-reservation-lux-drive-.git>
cd car-reservation-lux-drive
composer install
npm install
cp .env.example .env
php artisan key:generate

<!-- ===================================================== -->
<!-- CONFIGURATION DE LA BASE DE DONNÉES                  -->
<!-- ===================================================== -->

## Configuration de la base de données

<!-- Section expliquant la configuration par défaut -->
### SQLite (par défaut)

<!-- Création du fichier SQLite -->
```bash
touch database/database.sqlite


<!-- Exécution des migrations et insertion des données de test -->
php artisan migrate --seed

<!-- Alternative pour les SGBD plus robustes -->
MySQL / PostgreSQL
<!-- Configuration à effectuer dans le fichier .env -->

Il est également possible d’utiliser MySQL ou PostgreSQL en modifiant le fichier .env :

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=car_reservation
DB_USERNAME=root
DB_PASSWORD=

<!-- Application des migrations après configuration -->
php artisan migrate --seed

<!-- ===================================================== --> <!-- COMPILATION DES ASSETS FRONTEND --> <!-- ===================================================== -->
Compilation des assets frontend
<!-- Compilation des fichiers CSS/JS pour la production -->
npm run build

<!-- ===================================================== --> <!-- LANCEMENT DE L’APPLICATION --> <!-- ===================================================== -->
Lancement de l’application
Mode développement
<!-- Commande unifiée pour lancer backend + frontend -->
composer run dev

<!-- Explication de ce que fait la commande -->

Cette commande lance simultanément :

le serveur PHP Laravel

le serveur Vite pour le frontend

les services nécessaires au bon fonctionnement de l’application

<!-- Adresse d’accès local -->

L’application est accessible à l’adresse suivante :

http://localhost:8000

<!-- ===================================================== --> <!-- COMPTE DE TEST --> <!-- ===================================================== -->
Compte de test
<!-- Compte généré automatiquement par les seeders -->

Un utilisateur de test est automatiquement créé via les seeders :

Email : test@example.com

Mot de passe : password

<!-- ===================================================== --> <!-- TESTS AUTOMATISÉS --> <!-- ===================================================== -->
## Tests

Pour exécuter l’ensemble des tests automatisés (unitaires et fonctionnels) :

```bash
php artisan test --compact
```

Pour exécuter les tests pour un fichier spécifique :

```bash
php artisan test --compact tests/Feature/DashboardTest.php
```

Pour filtrer les tests par nom (utile après avoir modifié un fichier lié) :

```bash
php artisan test --compact --filter="testName"
```

<!-- ===================================================== --> <!-- SÉCURITÉ --> <!-- ===================================================== -->
Sécurité
<!-- Liste des mécanismes de sécurité implémentés -->

L’application intègre plusieurs mécanismes de sécurité :

Hashage sécurisé des mots de passe

Protection CSRF sur tous les formulaires

Validation stricte des données d’entrée

Autorisation basée sur la propriété des ressources

Protection contre les injections SQL via Eloquent ORM

<!-- ===================================================== --> <!-- ÉVOLUTIONS FUTURES --> <!-- ===================================================== -->
Améliorations futures possibles



<!-- Fonctionnalités non implémentées mais envisagées -->

Notifications par email pour les réservations

Calendrier visuel des disponibilités

Export des réservations (PDF / Excel)

Gestion des administrateurs avec droits étendus

Historique détaillé des réservations

Intégration avec des calendriers externes


---