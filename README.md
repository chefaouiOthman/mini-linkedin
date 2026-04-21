# Mini LinkedIn — API Backend

Une API RESTful construite avec Laravel, simulant une plateforme de recrutement mettant en relation candidats et recruteurs.

---

## 👥 Équipe & Répartition des tâches

| Développeur | Responsabilité | Branche |
|-------------|---------------|---------|
| Elmounghanizi Ammar | Base de données & Sécurité | `feature/db-auth-setup` |
| Chefaoui Othman | Gestion des profils candidats | `feature/profils-candidats` |
| Nait Abderrahmane Hamza | Gestion des offres d'emploi | `feature/gestion-offres` |
| Sahih Mohamed Rida | Candidatures & Administration | `feature/candidatures-admin` |

---

## 🛠️ Prérequis

- PHP >= 8.4
- Composer
- MySQL
- Laravel 11

---

## ⚙️ Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/chefaouiOthman/mini-linkedin.git
cd mini-linkedin

# 2. Installer les dépendances
composer install

# 3. Installer le package JWT
composer require php-open-source-saver/jwt-auth

# 4. Publier la configuration JWT
php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"

# 5. Copier le fichier d'environnement
cp .env.example .env

# 6. Configurer la base de données dans .env
DB_DATABASE=mini_linkedin
DB_USERNAME=root
DB_PASSWORD=

# 7. Générer la clé de l'application
php artisan key:generate

# 8. Générer la clé JWT
php artisan jwt:secret

# 9. Exécuter les migrations
php artisan migrate

# 10. Exécuter les seeders
php artisan db:seed

# Ou migrations + seeders en une seule commande
php artisan migrate --seed

# 11. Lancer le serveur
php artisan serve
```

---

## 🗄️ Modélisation de la base de données

### Toutes les entités

| Entité | Table | Description |
|--------|-------|-------------|
| User | `users` | Authentification et rôle (`admin`, `recruteur`, `candidat`) |
| Profil | `profils` | Informations professionnelles d'un candidat |
| Competence | `competences` | Dictionnaire global des compétences de la plateforme |
| Offre | `offres` | Offres d'emploi publiées par les recruteurs |
| Candidature | `candidatures` | Lien entre un profil candidat et une offre |
| ProfilCompetence | `profil_competence` | Table pivot entre profils et compétences (avec niveau) |

### Toutes les relations

| Relation | Type | Description |
|----------|------|-------------|
| `User` → `Profil` | One-to-One | Un candidat possède un seul profil |
| `User` → `Offre` | One-to-Many | Un recruteur publie plusieurs offres |
| `Profil` ↔ `Competence` | Many-to-Many | Un profil a plusieurs compétences, une compétence appartient à plusieurs profils (pivot : `niveau`) |
| `Offre` → `Candidature` | One-to-Many | Une offre reçoit plusieurs candidatures |
| `Profil` → `Candidature` | One-to-Many | Un profil peut postuler à plusieurs offres |

### Champs notables

- `users.role` ∈ `{candidat, recruteur, admin}`
- `profil_competence.niveau` ∈ `{débutant, intermédiaire, expert}`
- `offres.type` ∈ `{CDI, CDD, stage}`
- `candidatures.statut` ∈ `{en_attente, acceptee, refusee}`

---

## 🔐 Authentification & Autorisation

L'API utilise **JWT (JSON Web Token)** pour une authentification stateless.

- Le client s'authentifie via `/api/login` et reçoit un token
- Ce token doit être envoyé dans chaque requête protégée : `Authorization: Bearer <token>`
- Un middleware `CheckRole` vérifie le rôle de l'utilisateur pour chaque route protégée

### Codes HTTP retournés par l'API

| Code | Signification | Exemple |
|------|--------------|---------|
| `200` | Succès | Profil consulté, offre modifiée |
| `201` | Création réussie | Profil créé, offre publiée |
| `400` | Requête incorrecte | Profil déjà existant |
| `401` | Non authentifié | Token absent ou expiré |
| `403` | Accès interdit | Rôle non autorisé, ownership violé |
| `404` | Ressource introuvable | Profil, offre ou candidature inexistant(e) |
| `422` | Données invalides | Validation échouée (champ manquant, valeur incorrecte) |

---

## 🌱 Données de test (Seeders)

Les seeders génèrent automatiquement un environnement de test complet :

| Rôle | Quantité | Détails |
|------|----------|---------|
| Administrateurs | 2 | Accès complet à la plateforme |
| Recruteurs | 5 | Chacun avec 2 à 3 offres d'emploi |
| Candidats | 10 | Chacun avec un profil et des compétences |

### Exécution des seeders

```bash
# Exécuter tous les seeders
php artisan db:seed

# Réinitialiser la base et re-seeder (attention : supprime toutes les données)
php artisan migrate:fresh --seed
```

---

## 📡 Routes de l'API

### Auth (public)
| Méthode | Route | Description |
|---------|-------|-------------|
| POST | `/api/register` | Inscription |
| POST | `/api/login` | Connexion — retourne un token JWT |

### Auth (protégé)
| Méthode | Route | Description |
|---------|-------|-------------|
| POST | `/api/logout` | Déconnexion |
| GET | `/api/me` | Informations de l'utilisateur connecté |

### Profils — `role:candidat`
| Méthode | Route | Description |
|---------|-------|-------------|
| POST | `/api/profil` | Créer son profil (une seule fois) |
| GET | `/api/profil` | Consulter son profil avec compétences |
| PUT | `/api/profil` | Modifier son profil (mise à jour partielle) |
| POST | `/api/profil/competences` | Ajouter une compétence avec niveau |
| DELETE | `/api/profil/competences/{competence}` | Retirer une compétence |

### Offres — `role:recruteur` (création/modification) — authentifié (consultation)
| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/api/offres` | Liste des offres actives (filtres : localisation, type — pagination : 10/page — tri : date) |
| GET | `/api/offres/{offre}` | Détail d'une offre |
| POST | `/api/offres` | Créer une offre |
| PUT | `/api/offres/{offre}` | Modifier une offre (propriétaire uniquement) |
| DELETE | `/api/offres/{offre}` | Supprimer une offre (propriétaire uniquement) |

### Candidatures — `role:candidat` / `role:recruteur`
| Méthode | Route | Description |
|---------|-------|-------------|
| POST | `/api/offres/{offre}/candidater` | Postuler à une offre |
| GET | `/api/mes-candidatures` | Lister ses propres candidatures |
| GET | `/api/offres/{offre}/candidatures` | Candidatures reçues (recruteur propriétaire) |
| PATCH | `/api/candidatures/{candidature}/statut` | Changer le statut d'une candidature |

### Administration — `role:admin`
| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/api/admin/users` | Liste de tous les utilisateurs avec leurs profils |
| DELETE | `/api/admin/users/{user}` | Supprimer un compte (les admins sont protégés) |
| PATCH | `/api/admin/offres/{offre}` | Basculer l'état actif/inactif d'une offre |
---

## ⚡ Events & Listeners

| Event | Déclencheur | Listener | Log |
|-------|------------|----------|-----|
| `CandidatureDeposee` | Candidat postule à une offre | `LogCandidatureDeposee` | `storage/logs/candidatures.log` |
| `StatutCandidatureMis` | Recruteur change le statut | `LogStatutCandidatureMis` | `storage/logs/candidatures.log` |

---

## 📮 Collection Postman

Les collections Postman sont disponibles dans le dossier `/postman` :

| Fichier | Développeur | Couverture |
|---------|-------------|-----------|
| `mini-linkedin-profils.json` | Chefaoui Othman | Auth + Profils candidats |

---

## 📁 Structure du projet

```
app/
├── Events/                  # CandidatureDeposee, StatutCandidatureMis
├── Http/
│   ├── Controllers/         # ProfilController, OffreController, CandidatureController, AdminController
│   ├── Middleware/          # CheckRole
│   └── Requests/            # CreationRequestOffre
├── Listeners/               # LogCandidatureDeposee, LogStatutCandidatureMis
├── Models/                  # User, Profil, Competence, Offre, Candidature
database/
├── factories/               # Factories pour les seeders
├── migrations/              # Migrations de la base de données
└── seeders/                 # DatabaseSeeder et seeders spécifiques
postman/                     # Collections Postman de chaque développeur
routes/
└── api.php                  # Toutes les routes de l'API
storage/
└── logs/
    └── candidatures.log     # Log des événements de candidature
```
