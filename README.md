# Application de Gestion de Réservations de Véhicules – Lux Drive

## Contexte de l’application

Lux Drive est une application web full-stack destinée à la gestion des réservations de véhicules au sein d’une organisation.  
Elle permet aux employés de réserver des véhicules pour leurs déplacements professionnels tout en garantissant la cohérence des disponibilités et en évitant les conflits d’usage et les chevauchements de réservations.

L’application a été conçue dans un contexte proche des contraintes réelles de production, avec une attention particulière portée à la sécurité, à la clarté des règles métier et à la maintenabilité du code.

---

## Cycle de vie des réservations (logique métier)

Les réservations suivent un **cycle de vie basé sur des statuts**, afin de refléter un processus métier réaliste et garantir l’intégrité des données.

- Lorsqu’une réservation est créée, elle est placée dans l’état **En attente**.
- Avant toute validation, le système vérifie automatiquement l’absence de conflits de disponibilité avec d’autres réservations existantes.
- À la date de début de la période réservée, la réservation est automatiquement **confirmée** si aucune incohérence n’est détectée.
- Une réservation peut être **annulée** par son propriétaire avant le début effectif de la période.
- Une fois la période écoulée, la réservation est considérée comme **terminée**.

Cette approche permet d’éviter les conflits de planning, de sécuriser l’accès aux véhicules et de représenter fidèlement un fonctionnement organisationnel réel.

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
- Inscription et connexion des utilisateurs
- Authentification à deux facteurs (2FA)
- Gestion sécurisée des sessions

### Gestion des véhicules
- Liste des véhicules disponibles
- Recherche et filtrage par disponibilité
- Pagination des résultats
- Consultation des détails d’un véhicule

### Gestion des réservations

#### Création
- Sélection d’un véhicule disponible
- Choix des dates de début et de fin
- Indication de la raison de la réservation
- Validation automatique des conflits de disponibilité

#### Consultation
- Liste des réservations de l’utilisateur
- Affichage détaillé d’une réservation
- Visualisation du statut

#### Modification
- Modification des dates et de la raison
- Revalidation automatique des conflits

#### Annulation
- Annulation possible par le propriétaire avant le début effectif

### Statuts de réservation
- **En attente**
- **Confirmée**
- **Annulée**
- **Terminée**

---

## Règles métier critiques

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

---

## Structure de l’application

app/
├── Http/
│ ├── Controllers/
│ │ ├── VehicleController.php
│ │ └── ReservationController.php
│ └── Requests/
│ ├── StoreReservationRequest.php
│ └── UpdateReservationRequest.php
├── Models/
│ ├── User.php
│ ├── Vehicle.php
│ └── Reservation.php
database/
├── migrations/
│ ├── create_vehicles_table.php
│ └── create_reservations_table.php
├── factories/
│ ├── VehicleFactory.php
│ └── ReservationFactory.php
└── seeders/
└── VehicleSeeder.php
resources/js/
├── Pages/
│ ├── Vehicles/
│ │ ├── Index.vue
│ │ └── Show.vue
│ └── Reservations/
│ ├── Index.vue
│ ├── Create.vue
│ ├── Show.vue
│ └── Edit.vue
tests/
├── Feature/
│ ├── VehicleTest.php
│ └── ReservationTest.php


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
Tests
<!-- Exécution de tous les tests unitaires et fonctionnels -->

Exécuter l’ensemble des tests automatisés :

php artisan test

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