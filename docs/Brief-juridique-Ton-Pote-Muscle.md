# Brief produit — Ton Pote Musclé

**Destinataire :** juriste / conseil (rédaction des mentions légales, CGU, politique de confidentialité, politique cookies, mentions App Store / Play Store, éventuel règlement de challenge).

**Objet :** décrire factuellement les applications et les traitements, pour que le juriste identifie les contenus juridiques **obligatoires** et les rédige. Ce document n’est **pas** un avis juridique et ne constitue **pas** les CGU ni la politique de confidentialité.

**Date du brief :** 24 novembre 2026  
**Statut produit :** web coach/membre déjà fonctionnel ; application mobile et API en cours de construction (périmètre mobile figé dans le Product.md mobile).

À compléter par l’éditeur avant envoi (ou avec le juriste) :

| Élément | Valeur |
|--------|--------|
| Dénomination sociale / forme | *[À COMPLÉTER]* |
| SIREN / RCS / TVA | *[À COMPLÉTER]* |
| Siège, pays d’établissement | *[À COMPLÉTER]* |
| Représentant légal | *[À COMPLÉTER]* |
| Contact (e-mail, téléphone) | *[À COMPLÉTER]* |
| Hébergeur (nom, adresse) | *[À COMPLÉTER]* |
| DPO / contact RGPD | *[À COMPLÉTER]* |
| Marques / nom commercial | Ton Pote Musclé (TPM) |
| Public visé (mineurs ?) | *[À COMPLÉTER — aujourd’hui pas de contrôle d’âge à l’inscription]* |
| Territoire (France / UE / autre) | *[À COMPLÉTER]* |
| URL web | *[À COMPLÉTER]* |
| Identifiants stores (quand publiés) | *[À COMPLÉTER]* |

---

## 1. Ce que c’est

**Ton Pote Musclé** est une plateforme d’entraînement physique (fitness / musculation / condition physique) destinée à un **club ou un coach** et à ses **athlètes / membres**.

Promesse produit : suivre un plan, faire des séances (en direct dans un player ou en les déclarant), éventuellement participer à un **classement** ou à un **challenge** daté.

Ce n’est **pas** :

- un dispositif médical, un diagnostic ou un traitement ;
- un réseau social ouvert (pas de fil d’activité public, pas de commentaires entre membres) ;
- un service de paiement / abonnement premium **dans l’état actuel du code** ;
- une preuve biométrique de l’effort (pas de caméra, GPS, montre, IA).

Le juriste devra typiquement prévoir un **avertissement santé** (effort physique, avis médical, repas / hydratation ≠ conseil diététique ou médical).

---

## 2. Architecture (trois surfaces)

| Surface | Dossier | Rôle | Public |
|---------|---------|------|--------|
| **Site web** | `tonpotemuscle-web` (Laravel) | Outil **coach / club** + espace **membre** | Coach (admin) et membres du club |
| **Application mobile** | `ton-pote-muscle-mobile` (Expo / React Native) | App **athlète** : s’entraîner, programmes solo, challenges | Athlète |
| **API** | `ton_pote_musclé_api` (FastAPI) | Backend prévu pour le mobile | Consommée par l’app, pas une UI |

Le **même univers métier** (exercice, séance, programme, live, log) existe des deux côtés, avec **deux façons d’entrer** :

- **Web :** le coach crée le catalogue et **donne** le plan à ses membres ; le membre s’entraîne dans l’espace privé.
- **Mobile :** l’athlète choisit un exercice à la carte, un programme **solo**, ou s’inscrit à une **saison de challenge**.

Le mobile **n’est pas** le back-office : pas de « Donner un plan », pas de gestion club dans l’app athlète (V1).

---

## 3. Rôles

| Rôle | Où | Droits principaux |
|------|----|-------------------|
| **Administrateur / coach** | Web `/admin` | Catalogue, programmes, inscrits, dons de plan, lecture des bilans, chat, hydratation, repas prescrits, classements, export CSV, désactivation de compte |
| **Membre (web)** | Web espace membre | Compte, bilan, plan reçu, live / déclarée, pesée, eau, repas, messages au coach, profil, classement / partage |
| **Athlète (mobile)** | App + API | Compte, onboarding objectifs, catalogue, live, programme solo, challenge, classement de sa cohorte |
| **Visiteur** | Web | Page de connexion ; fiche **programme public** si le coach l’ouvre ; pages de **partage** via lien à jeton (`/p/…`) |

Il n’y a pas, en V1 mobile, de compte « coach » dans l’app.

---

## 4. Site web — fonctionnalités

### 4.1 Accès et comptes

- Inscription membre (e-mail, mot de passe, nom / identifiants).
- Connexion membre et connexion admin **séparées**.
- Session cookie (framework Laravel). Option « se souvenir de moi ».
- Compte désactivable par l’admin (le membre ne peut plus se connecter).
- Profil admin (e-mail, mot de passe).
- Après inscription membre : **bilan de départ obligatoire** avant le tableau de bord.

### 4.2 Catalogue (admin)

- **Exercices :** titre, description, consignes, zones du corps, matériel, muscles, image, GIF, URL vidéo.
- **Séances :** titre, niveau, exercices ordonnés (séries, répétitions ou durée, repos), visuel. Création possible **depuis** un programme (tiroir).
- **Programmes :** titre, dates, niveau, statut (brouillon / ouvert / clôturé), calendrier jour par jour (séance ou repos), visuel. URL publique une fois ouvert.

L’admin peut créer un exercice **pendant** la composition d’une séance, et une séance **pendant** la composition d’un programme. Tout va dans le **catalogue** (objets réutilisables).

### 4.3 Inscrits et « Donner »

- Liste des membres, fiche (coordonnées, profil, bilans, plan).
- Le coach **attribue** une séance ou une semaine de programme à un membre (plan privé). Pas d’auto-inscription publique au programme pour l’instant (prévu « plus tard »).
- Charges (kg) possibles sur les exercices si le matériel le justifie ; affichage pendant le live.
- Désactivation / réactivation du compte.

### 4.4 Espace membre — entraînement

- Tableau de bord (accueil, semaine, progrès).
- Plan reçu du coach.
- **Séance live :** player plein écran, timer, pause, skip, enchaînement travail / repos ; charges kg si renseignées.
- **Séance déclarée :** le membre indique que c’est fait (date, durée, ressenti) — prévu / en place selon les flux.
- Historique / logs : une séance par jour et par exercice de calendrier (anti-doublon).
- Recap / feedback après séance.

### 4.5 Points et classement (web)

Barème **par inscription à un programme** (une séance du calendrier paie une fois) :

| Action | Points |
|--------|--------|
| Live le jour J | 10 |
| Déclarée le jour J | 6 |
| Live J±1 | 7 |
| Déclarée J±1 | 4 |
| Hors fenêtre / abandon | 0 |
| Live sans skip | +2 |

- Classement provisoire visible côté admin (et logique membre prévue).
- Clôture / publication par l’admin ; **snapshot** figé ; export CSV.
- **Partage** : page publique avec jeton (`/p/{token}`) — extrait d’une séance terminée (prénom/pseudo, titre, minutes, live ou déclarée, points) **ou** du rang de fin de programme.

À signaler au juriste : un **classement** et un **partage public** (lien) existent ; ce n’est pas un tirage au sort, mais cela peut appeler un encadrement (jeu, image, droit au nom).

### 4.6 Bilans (questionnaires)

Le coach crée des questionnaires. Trois modèles seedés :

1. **Bilan de santé** — blessure / douleur, précisions (zone, consignes médicales), énergie, sommeil, **suivi médical ou traitement en cours**.
2. **Bilan de départ** — objectif, disponibilités, lieu, matériel, résultat visé, motivation.
3. **Bilan de mesure** — poids, tours (taille, hanches, poitrine, bras, cuisse), **âge**, taille.

Les réponses sont lues par le **coach** sur la fiche membre. C’est le traitement le plus sensible (données de santé / mesures corporelles).

### 4.7 Suivi quotidien (web)

- **Pesée :** log de poids (kg) par semaine.
- **Hydratation :** objectif en verres ; logs par jour.
- **Repas :** le coach prescrit petit-déjeuner / déjeuner / dîner (texte court) ; le membre **coche** s’il a suivi (check-in) et peut laisser une note courte.

### 4.8 Messagerie coach ↔ membre

- Fil **texte asynchrone** (pas de visio, pas de websocket).
- Un fil par membre avec le coach.
- Badge non-lu au chargement de page.

### 4.9 Pages potentiellement publiques

- Connexion / inscription.
- Fiche programme si statut « ouvert ».
- Pages de partage `/p/…` (séance ou rang).

Pas de mentions légales / CGU / cookies **dans le code actuel**.

---

## 5. Application mobile — fonctionnalités (périmètre visé)

### 5.1 Parcours

- Écrans d’accueil / onboarding visuel.
- Parcours **objectif** : genre, taille, poids, niveau d’activité, niveau d’entraînement, motivations, objectif.
- Auth (prévue).
- Accueil, découverte, rapport, réglages (structure d’app en place).
- Catalogue d’exercices (visuels fond noir), séances, programmes, **challenges**.
- Player live (timer, pause, skip) — mêmes garde-fous que le web, une fois branché.

### 5.2 Trois modes d’entraînement (vision mobile figée)

| Mode | Description | Classement |
|------|-------------|------------|
| **Exercice à la carte** | Un mouvement, sans date, sans groupe | Non |
| **Programme solo** | Calendrier avec une fin ; l’athlète commence quand il veut | Non |
| **Challenge (saison)** | Programme + dates + cohorte ; même calendrier pour tous | Oui, à la clôture |

**Paliers de challenge (V1) :** Base (ouvert) → Relève (top 5 éligibles d’une saison Base) → Cime (idem depuis Relève). Pas de relégation. Une saison à la fois par palier.

### 5.3 Live « valide » (challenge mobile — plus strict que le web)

Seuls les **lives valides** donnent des points de challenge. Une **déclarée** = **0 point** challenge.

Conditions (résumé) : durée player ≥ 60 % du temps prévu ; skips ≤ 30 % des exercices ; pas de durée absurde ; un log par séance et par jour.

Barème challenge : live valide J = 10 ; J±1 = 7 ; sans skip = +2.

**Éligibilité top 5 :** ≥ 70 % des séances en live valide **et** au moins 1 live valide la dernière semaine. Un athlète 100 % déclarée est inéligible à la montée.

Classement de **sa** cohorte seulement (points, ratio lives, badge live vs déclaré). Snapshot à la date de fin.

### 5.4 Hors mobile V1 (reste sur le web)

Coach, dons, fiche membre, chat, hydratation, repas prescrits, bilans, « partager à un utilisateur ».

Pas de : caméra, IA, wearable, GPS, premium, équipes, duels, fil d’activité.

---

## 6. Inventaire des données (pour le RGPD)

Catégories observées dans le code. Finalités indiquées de façon opérationnelle ; bases légales à poser par le juriste.

### 6.1 Compte et identification

- Nom, identifiant / pseudo, e-mail, téléphone, mot de passe (hashé).
- Rôle (admin / membre), dates, désactivation.
- Sessions, jetons de réinitialisation de mot de passe.

### 6.2 Profil sportif

- Genre, taille (cm), poids (kg), niveau d’activité, niveau sportif, objectif, motivations, matériel (oui/non + notes).
- Objectif d’hydratation (nombre de verres).

### 6.3 Données corporelles / santé (attention particulière)

- Questionnaires **bilan de santé** (blessure, consignes médicales, traitement).
- Mensurations, âge, poids (bilan + logs de pesée).
- Sommeil, énergie (auto-déclaration).
- Repas prescrits et check-in alimentaire.
- Hydratation quotidienne.

Ces éléments peuvent être qualifiés de **données de santé** ou données particulièrement sensibles. Le juriste tranchera (art. 9 RGPD, responsabilité du coach, hébergement, durée de conservation, accès strict coach).

### 6.4 Activité d’entraînement

- Logs de séances : date, mode live/déclaré, horodatages, durée réelle, skips, abandon, ressenti, notes, points, rattachement programme.
- Détail par exercice du live (dont charge kg le cas échéant).
- Inscriptions à des programmes / saisons, calendrier suivi.

### 6.5 Social / visibilité

- Classement (rang, points, ratio de lives) dans une cohorte ou un programme.
- Pages de partage **publiques** (pseudo/prénom, titre de séance ou rang, points).
- Fiche programme publique (titre, calendrier, noms de séances).

### 6.6 Communications

- Messages texte coach ↔ membre (contenu libre : peut contenir de la santé si l’utilisateur l’écrit).

### 6.7 Contenus éditoriaux (catalogue)

- Textes, images, GIF, liens vidéo d’exercices — **créés par le coach / l’éditeur**, pas des photos des membres (sauf si un visuel programme uploadé le serait).

### 6.8 Technique

- Cookie de **session** (authentification).
- Pas de module analytics / pub identifié dans le code actuel.
- Fichiers médias stockés sur disque « public » de l’application (URL d’accès fichier).
- API mobile : e-mail, téléphone, pseudo, genre, poids, activité, motivations, flags actif / premium (le premium n’est pas un parcours de paiement dans le web actuel).

### 6.9 Non collecté (V1)

Géolocalisation, contacts du téléphone, caméra, micro, capteurs santé constructeur, paiement, pièce d’identité.

---

## 7. Destinataires et flux

| Qui voit quoi | Détail |
|---------------|--------|
| Le membre / athlète | Son compte, son plan, ses logs, ses bilans, son fil de messages |
| Le coach (admin du club) | Tous les inscrits du club, plans donnés, réponses aux bilans, pesée / eau / repas, messages, classements, export CSV |
| Les autres inscrits du même programme / de la même saison | Classement (rang, points, pastilles live/déclaré) — pas le détail santé |
| Public (lien) | Partage séance ou rang ; éventuellement fiche programme ouverte |
| Prestataires | *[Hébergeur, e-mail transactionnel, stores — à déclarer]* |

Pas de revente de données prévue dans le produit. Pas de SSO tiers (Google / Apple) dans le code web actuel.

Les données du **web** et celles de l’**API mobile** sont aujourd’hui deux stacks. Le juriste doit savoir si un **seul responsable de traitement** opère les deux, et si les bases seront unifiées.

---

## 8. Mineurs, santé, nutrition, challenge

Points à faire trancher (le produit ne les tranche pas) :

1. **Âge minimum** et contrôle (inscription libre côté membre aujourd’hui).
2. **Avertissement effort physique** (contre-indications, urgence, pas un substitut médical).
3. **Bilans de santé et traitements** : consentement explicite ? questionnaire réservé au coach diplômé ? conservation ?
4. **Repas prescrits** : coaching nutritionnel vs information générale ; responsabilité si régime.
5. **Classement / challenge / paliers** : simple animation interne au club, ou « jeu-concours » (lots, palier = nouveau programme plus dur, pas d’argent dans le produit).
6. **Partage public** : droit à l’image / au nom ; possibilité de désactiver le partage.
7. **Contenu UGC** : messages et notes libres (modération, signalement — non implémentés).
8. **Stores** : politiques Apple / Google (santé, comptes enfants, suppression de compte).

---

## 9. Contenus juridiques que le juriste devra typiquement produire

Liste d’aide, **non exhaustive**, pour orienter la livraison :

| Document | Surfaces |
|----------|----------|
| Mentions légales (LCEN) | Site web ; éventuellement store « mentions éditeur » |
| Conditions générales d’utilisation | Web membre, web admin, app mobile (éventuellement CGU distinctes coach vs athlète) |
| Politique de confidentialité (RGPD) | Web + app + API — un document ou deux si responsables distincts |
| Politique cookies / bandeau | Web (session a minima ; pas de traceurs pub identifiés) |
| Information art. 13/14 + droits des personnes | Accès, rectif, effacement, portabilité, opposition — **l’app n’expose pas encore** un parcours « supprimer mon compte » côté membre (l’admin peut désactiver) |
| Clause / écran santé | Inscription, premier bilan, stores |
| Règlement challenge / classement | Si le juriste estime qu’il en faut un |
| Contrat / DPA hébergeur | Hors UI |
| Conditions App Store / Play Store | Fiches stores, suppression compte, données santé |

Ce brief **ne rédige pas** ces textes : il donne la matière factuelle.

---

## 10. Synthèse une page (pour transfert rapide)

**Ton Pote Musclé** = plateforme de coaching sportif.

- **Coach** sur le **web** : crée exercices / séances / programmes, inscrit ses membres, leur donne un plan, lit les bilans (dont santé), suit poids / eau / repas, discute par messages, clôture des classements, exporte un CSV.
- **Membre web** : s’entraîne en live ou déclare, gagne des points, peut partager un résultat via un lien public.
- **Athlète mobile** (en construction) : s’entraîne seul (exercice, programme) ou en **challenge** saisonnier avec classement ; la déclarée ne fait **pas** monter au palier.

Données : identité, profil sportif, **mesures corporelles**, **questionnaires de santé**, logs d’effort, messages, éventuellement classement visible aux pairs et extraits publics.

Pas de paiement, pas de géoloc, pas de caméra. Cookies de session. Contrôle d’âge et mentions légales **à créer**.

---

## 11. Annexes techniques (si le juriste les demande)

- Stack web : Laravel, sessions serveur, stockage fichiers `public` (exercices, visuels programmes).
- Stack mobile : Expo Router / React Native.
- Stack API : FastAPI, utilisateur (e-mail, téléphone, genre, poids, etc.).
- Pas d’outil analytics intégré identifié à la date du brief.

---

*Document interne destiné au conseil juridique. Toute qualification (responsable de traitement, bases légales, données de santé, jeu, mineurs) appartient au juriste.*
