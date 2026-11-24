# Ton Pote Musclé — Brief juridique : application mobile

**Objet.** Présenter **uniquement l’application mobile athlète** (et l’API qui la sert), ses fonctions et les données traitées, pour rédaction des conditions d’utilisation store, politique de confidentialité app, disclaimers, éventuel règlement de challenge, etc.

**Périmètre.** Ce brief **ne couvre pas** le site web de suivi coach / membre. Un document distinct existe : `Brief-juridique-app-web.md`.

**Nature.** Fiche produit factuelle. Pas un avis juridique, pas un projet de CGU. Cases `[à compléter]` : éditeur.

**Date.** 24 novembre 2026.  
**Statut.** En développement. Périmètre **cible V1** ci-dessous. Tout n’est pas encore publié sur les stores.

---

## 1. Identité de l’éditeur (à compléter)

| Élément | Valeur |
|--------|--------|
| Nom commercial | Ton Pote Musclé |
| Application concernée | **App mobile d’entraînement** (athlète) |
| Stores | Apple App Store, Google Play — `[comptes développeur]` |
| Nom affiché sur les stores | `[à figé]` |
| Personne morale / entrepreneur | `[raison sociale, forme, capital]` |
| Siège | `[adresse]` |
| Immatriculation | `[SIRET / RCS / RNA]` |
| Contact | `[e-mail / téléphone]` |
| DPO (si désigné) | `[nom / e-mail]` |
| Hébergeur API / base | `[nom, adresse, pays]` |
| Public visé | Athlètes. Adultes. **Contrôle d’âge : non implémenté.** |
| Territoire | France / `[UE ?]` |
| Langue | Français |
| Paiement in-app | **Aucun.** Un champ technique `premium` existe côté API, **non vendu**. |

**Lien avec le web.** Même marque, même éditeur pressenti. Produit **distinct** : le mobile est l’app **pour s’entraîner** (exercices, programmes solo, challenges). Ce n’est **pas** le back-office coach. Chat, hydratation, repas prescrits, bilans santé détaillés et « dons » de plan restent sur le **web** et sont hors V1 mobile.

---

## 2. À quoi sert l’app

App d’entraînement : un **exercice** à la carte, un **programme solo**, ou un **challenge** (saison datée, groupe, classement, palier au-dessus pour les cinq premiers éligibles).

Promesse : s’entraîner pour de vrai, avec un calendrier commun en challenge — pas une ligue cosmétique.

Ce n’est **pas** un dispositif médical, un réseau social, une marketplace, un service de paiement.

**Technique.** App Expo / React Native. Backend **API FastAPI** (pas d’interface utilisateur propre). Le coach **ne gère pas** les saisons depuis le téléphone en V1.

---

## 3. Rôles (dans cette app)

| Rôle | Accès | Rôle |
|------|--------|------|
| **Athlète** | App mobile | Compte, profil d’objectif, catalogue, live, solo, challenge, classement de **sa** cohorte |
| **Coach** | *Hors app* | Pas de « Donner », pas de suivi fiche / chat / repas dans le mobile V1 |
| **Visiteur store** | Fiche store | Téléchargement ; pas de contenu membre sans compte |

---

## 4. Fonctionnalités cibles V1

### 4.1 Compte et onboarding

- Authentification.
- Écrans d’accueil / onboarding.
- Parcours **objectif** : genre, taille, poids, niveau d’activité, niveau d’entraînement, motivations, objectif.

### 4.2 Entraînement

- Catalogue **exercices** : détail, **live à la carte** (sans points de challenge).
- **Programme solo** : l’athlète commence quand il veut ; calendrier ; live ou déclarée ; **pas de classement**.
- **Challenge** : saisons ouvertes de son palier, inscription, séance du jour, place provisoire, % de lives, clôture + résultat.
- Player live : timer, pause, skip. Règles de **live valide** (ci-dessous).
- Profil minimal.
- Classement de **sa** cohorte, pendant la saison et après snapshot.

### 4.3 Paliers (challenge uniquement)

| Palier | Accès |
|--------|--------|
| Base | Ouvert |
| Relève | Top 5 **éligibles** d’une saison Base |
| Cime | Top 5 éligibles d’une saison Relève |

V1 : **3 paliers**. Pas de relégation. Monter = s’inscrire à un **programme plus dur**, pas un lot d’argent.

**1 saison à la fois** par palier. Inscription jusqu’au kickoff ou J+2.

### 4.4 Live, déclarée, live valide

Les deux modes existent. **Seuls les lives valides** donnent des points de challenge. Une **déclarée** (« c’est fait ») = **0** point challenge (suivi perso seulement).

Live valide :

1. Durée du player ≥ 60 % du temps prescrit.
2. Skips ≤ 30 % des exercices.
3. Durée absurde / abandon : 0.
4. Un log par séance de calendrier et par jour.

On ne prouve pas l’effort physiologique (pas de caméra, GPS, wearable). On rend la triche **inutile pour monter de palier**.

### 4.5 Points (challenge uniquement)

| Action | Points |
|--------|--------|
| Live valide le jour J | 10 |
| Live valide J±1 | 7 |
| Live valide sans skip | +2 |
| Déclarée / incomplet / hors fenêtre | 0 |

Hors challenge (solo, à la carte) : pas de ligue, pas de points de saison.

**Éligibilité top 5** (en plus des points) :

- ≥ 70 % des séances du calendrier en live valide ;
- ≥ 1 live valide dans la **dernière semaine**.

Pas de points pour l’eau, les repas, les kilos (ces modules ne sont **pas** dans le mobile V1).

### 4.6 Hors V1 mobile (ne pas mettre dans les CGU app comme des fonctions actuelles)

Chat, hydratation, repas, bilans, dons de plan, back-office coach, invitation magique, duels, équipes, fil d’activité, paiement, badges hors palier.

---

## 5. Données personnelles (app + API)

### 5.1 Compte API

E-mail, mot de passe (hash), téléphone optionnel, pseudo, compte actif.

### 5.2 Profil d’objectif

Genre, poids, (taille côté parcours mobile), niveau d’activité, niveau sportif, objectif, motivations.

Pas de questionnaire « bilan de santé » (blessures, traitements) **dans le mobile V1**.

### 5.3 Activité

Logs de séances (live / déclarée, durées, skips, rattachement programme / saison), inscriptions aux saisons, palier, points, rang, snapshot de cohorte.

### 5.4 Visibilité

| Donnée | Qui voit |
|--------|----------|
| Profil, logs solo | L’athlète (et l’API / ops) |
| Classement de saison | Les inscrits de **cette** cohorte (pseudo, points, lives, badge live / déclaré) |
| Catalogue exercices | Utilisateurs connectés |

Pas de page de partage public type `/p/` dans le mobile V1. Pas de messages coach dans l’app.

### 5.5 Technique

Auth (jetons / session API — `[détail technique à figer à la mise en prod]`). Pas d’analytics / pub identifié à ce stade. Pas de données bancaires. Pas de photo utilisateur, pas de géolocalisation.

### 5.6 Médias

Illustrations d’exercices dans l’app (assets). Droits d’auteur / modèles : `[à clarifier]`.

---

## 6. Finalités (telles que l’app les met en œuvre)

1. Compte athlète.
2. Personnaliser un profil d’objectif (genre, morphologie, niveaux).
3. Permettre l’entraînement (catalogue, live, solo).
4. Organiser des **saisons de challenge**, calculer un classement et une **éligibilité** de montée.
5. Conserver les logs comme preuve de tentative (règles live valide).

Pas de revente, pas de pub comportementale, pas de suivi nutritionnel/médical dans cette app V1.

---

## 7. Contenus juridiques à produire (mobile seulement)

1. Fiche store (description, public, données déclarées Apple / Google).
2. Conditions d’utilisation de **l’app**.
3. Politique de confidentialité **app / API**.
4. Disclaimer sport (pas un avis médical ; l’utilisateur s’estime apte).
5. Mineurs (pas de barrière d’âge).
6. **Règlement de challenge / palier** si le juriste le juge nécessaire (lot = accès à un palier plus exigeant, pas d’argent).
7. Permissions store (a priori aucune sensible en V1 : pas caméra, pas tracking pub, pas localisation) — à revérifier au build.
8. Hébergeur API ; durées de conservation `[à définir]`.
9. Lien éventuel futur avec le compte web : à traiter **quand** il existera, pas dans cette V1 comme un fait.

---

## 8. Hors périmètre de ce brief

Site web Laravel, espace coach, bilans santé, chat, eau, repas prescrits, pesée coach, pages de partage `/p/`, export CSV classements club.

Pas de caméra, GPS, wearable, IA, visio, paiement, publicité.
