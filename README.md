# HostyStats – Addon CLIENTXCMS

HostyStats est un module CLIENTXCMS permettant de surveiller l’état de votre infrastructure via des sondes (HTTP, PING, TCP) et d’afficher dynamiquement un message de maintenance global ou ciblé.

---

## Vue d’ensemble

- Surveillance de services ou IP en temps réel
- Support multi-types de sondes :
  - `HTTP` : code attendu + latence (ms)
  - `PING` : ping IP + temps de réponse
  - `TCP`  : vérification d’un port/service
- Statut intelligent avec priorité :
  1. `forced_status` (statut forcé par l’admin)
  2. `last_status` (dernier état connu)
  3. fallback → `down`
- États possibles :
  - `ok` → UP (Opérationnel)
  - `degraded` → Dégradé
  - `maintenance` → Maintenance
  - `down` → DOWN (Incident)
- Message de maintenance paramétrable :
  - Activation ON/OFF
  - Portée : **globale** ou par sondes sélectionnées
  - Couleur : **yellow / orange / red**
  - Titre + description + dates (début/fin optionnelles)
- Intégration automatique dans la page **Paramètres CLIENTXCMS** lorsque le module est activé (aucun changement requis dans `admin.php`)

---

## Installation

1. Copier le dossier dans :

/addons/hostystats

2. Exécuter les migrations :

php artisan migrate --addon=hostystats

3. (Si auto-hébergement + assets à compiler) :

npm install
npm run build


---

## Utilisation

### Côté Client
- Affiche le statut réel des services
- Override automatiquement en `maintenance` si la sonde est impactée par un message actif

### Côté Admin
- Gestion des **Catégories**
- Création et configuration des **Sondes**
- Possibilité de **forcer un statut**
- Gestion du **Message de maintenance**

---

## Structure du projet



addons/
└── hostystats/
├── src/Controllers/
│ ├── DashboardController.php
│ └── MaintenanceController.php
├── src/Models/
│ └── Monitor.php
├── views/admin/
├── views/client/
├── database/migrations/
└── lang/fr/


---

## Configuration requise

- CLIENTXCMS 1.x+
- PHP 8.3+
- NodeJS + NPM (uniquement si build assets nécessaire)

---

## Licence

Usage **personnel et non-commercial** autorisé.  
Toute exploitation dans un projet générant du profit nécessite une autorisation CLIENTXCMS.

Contact : `contact@hostalis.fr`

---

## Contribution

1. Forker le projet
2. Créer une branche `feature/...`
3. Ouvrir une Pull Request avec description détaillée

---

## Support & Ressources

- Démo : `demo.hostalis.fr`
- Documentation : `docs.hostalis.fr`
- Contact : `contact@hostalis.fr`

---

**Développé avec CLIENTXCMS par Hostalis**
