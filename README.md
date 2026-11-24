# FlexiDevis

Application web de **devis et factures** pour artisans et petites entreprises : clients,
catalogue produits avec suivi du stock, devis et factures calculés en direct, export PDF.

![Tableau de bord](docs/captures/tableau-de-bord.png)

## Fonctionnalités

- **Devis et factures** : lignes avec quantité, prix, TVA et remise ; remise globale ;
  aperçu en direct pendant la saisie ; 4 modèles de mise en page ; logo ; export PDF.
- **Numérotation automatique** : `D-2026-0001` pour les devis, `F-2026-0001` pour les factures,
  une suite par type et par année, sans doublon.
- **Suivi** : statuts (brouillon, validé, accepté, refusé, payé, impayé), duplication d'un
  document, filtre par client.
- **Clients et produits** : fiches, import CSV, alerte de stock bas.
- **Tableau de bord** : chiffre d'affaires facturé, évolution mensuelle, meilleurs clients.
- **Compte** : coordonnées de l'entreprise (raison sociale, SIRET, adresse) reprises en tête
  des documents ; chaque utilisateur ne voit que ses propres données.

| Devis & factures | Saisie d'un devis | Stock |
| --- | --- | --- |
| ![Documents](docs/captures/documents.png) | ![Nouveau devis](docs/captures/nouveau-devis.png) | ![Produits](docs/captures/produits.png) |

## Calcul des montants

Le même calcul sert au serveur (`app/Services/CalculDocument.php`) et à l'aperçu en direct :

1. ligne : quantité × prix unitaire, moins la remise de la ligne, puis sa TVA ;
2. la remise globale réduit la base HT, donc aussi la TVA ;
3. Total TTC = Total HT + Total TVA, arrondis au centime.

## Technologies

- PHP 8.2+, **Laravel 12** (Breeze pour l'authentification), Blade, Alpine.js, Tailwind CSS
- MySQL 8 en production, SQLite pour le développement et les tests
- DomPDF pour les PDF
- Pest (42 tests), GitHub Actions

## Lancer le projet

```bash
composer install && npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite      # ou MySQL : voir .env.example et docker compose up -d
php artisan migrate --seed          # comptes et données de démonstration
npm run build
php artisan serve                   # http://127.0.0.1:8000
```

Compte de démonstration : `alexis@flexidevis.com`, mot de passe `password`.

## Tests

```bash
php artisan test
```

Ils couvrent le calcul des montants, la numérotation, l'isolement des comptes (impossible
d'utiliser le client ou de voir le document d'un autre compte), la duplication, le PDF, le
tableau de bord, le profil et toute l'authentification. Ils passent sur SQLite et MySQL.

## Structure

```
app/Services/CalculDocument.php     montants des lignes et totaux
app/Services/Numerotation.php       numéros D-/F-AAAA-NNNN
app/Http/Controllers/               documents, clients, produits, tableau de bord
resources/views/documents/          saisie, aperçu, totaux partagés avec le PDF
resources/views/pdf/document.blade.php
tests/                              Pest
```

## Auteur

Alexis Renazé – [github.com/Optiware](https://github.com/Optiware)
