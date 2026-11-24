# Ton Pote Musclé — Brief juridique : application web de suivi

**Objet.** Présenter **uniquement le site web** (outil coach / club et espace membre de suivi), ses fonctions et les données traitées, pour rédaction des mentions légales, CGU, politique de confidentialité, cookies, disclaimers santé, etc.

**Périmètre.** Ce brief **ne couvre pas** l’application mobile ni son API. Un document distinct existe : `Brief-juridique-app-mobile.md`.

**Nature.** Fiche produit factuelle. Pas un avis juridique, pas un projet de CGU. Cases `[à compléter]` : éditeur.

**Date.** 24 novembre 2026.  
**Statut.** En service (fonctionnalités ci-dessous).

---

## 1. Identité de l’éditeur (à compléter)

| Élément | Valeur |
|--------|--------|
| Nom commercial | Ton Pote Musclé |
| Application concernée | **Site web de suivi** (back-office coach + espace membre) |
| URL | `[https://…]` |
| Personne morale / entrepreneur | `[raison sociale, forme, capital]` |
| Siège | `[adresse]` |
| Immatriculation | `[SIRET / RCS / RNA]` |
| Directeur de la publication | `[nom]` |
| Contact | `[e-mail / téléphone]` |
| DPO (si désigné) | `[nom / e-mail]` |
| Hébergeur | `[nom, adresse, pays]` |
| Public visé | Coach / club et leurs membres. Adultes. **Contrôle d’âge à l’inscription : non implémenté.** |
| Territoire | France / `[UE ?]` |
| Langue | Français |
| Paiement | **Aucun** (pas de boutique, pas d’abonnement en ligne) |

**Lien avec le mobile.** Même marque, même éditeur pressenti. Produit **distinct** : le web sert le **suivi par le coach** ; le mobile sert l’**entraînement autonome** de l’athlète. Comptes et bases pourront être reliés plus tard ; aujourd’hui le juriste traite le web **comme un service à part**.

---

## 2. À quoi sert le site

Outil numérique de **coaching et de suivi** : le coach compose le catalogue, attribue un plan, lit les bilans, suit hydratation / repas / poids, échange par messages. Le membre exécute les séances (live ou déclarées), renseigne son suivi, voit son classement interne.

Ce n’est **pas** un dispositif médical, un réseau social, une marketplace, un service de paiement.

**Technique.** Application web Laravel. Espaces séparés : `/admin` (coach) et espace membre. Accueil visiteur = page de **connexion membre**.

---

## 3. Rôles

| Rôle | Accès | Rôle dans le suivi |
|------|--------|---------------------|
| **Administrateur / coach** | `/admin` | Crée catalogue et programmes, inscrit / donne un plan, lit bilans et suivi, messages, clôture classements |
| **Membre** | Espace connecté | Suit le plan **attribué**, s’entraîne, remplit profil et bilans, eau / pesée / repas, messages |
| **Non connecté** | URL publiques | Connexion. Fiche **programme ouvert**. Pages de **partage** à jeton (séance ou rang) |

Pas d’inscription libre à un programme public : le coach **attribue** le plan.

---

## 4. Fonctionnalités

### 4.1 Accès

- Inscription membre, connexion e-mail + mot de passe, option « se souvenir de moi ».
- Connexion admin distincte.
- Mot de passe hashé, session cookie serveur.
- Compte membre **désactivable** par le coach.
- Après inscription : **bilan de départ** obligatoire avant le tableau de bord.

### 4.2 Catalogue (coach)

- **Exercices** : consignes, zones, matériel, image / GIF / URL vidéo.
- **Séances** : exercices ordonnés (séries, reps ou durée, repos). Création possible depuis un programme.
- **Programmes** : période, calendrier (séance / repos), URL publique une fois **ouvert**.

### 4.3 Suivi coach → membre

- Fiche inscrit, plan donné (séance ou semaine), charges kg éventuelles.
- Bilans (questionnaires) ; réponses lues par le coach.
- Messages texte asynchrones (un fil par membre, pas de visio).
- Objectif d’hydratation (verres / jour) + historique coché par le membre.
- Plan repas prescrit (petit-déj / déjeuner / dîner, texte court) + check-in membre + note.
- Classement provisoire, **clôture / publication**, export CSV.

### 4.4 Espace membre

- Tableau de bord (accueil, semaine, progrès).
- Mes programmes (ceux donnés par le coach).
- Bilans, profil, live (timer, pause, skip, kg), séance **déclarée**.
- Onglets suivi : **eau**, **pesée**, **repas**.
- Messages avec le coach.
- Partage d’une séance ou d’un rang via URL `/p/{jeton}`.

### 4.5 Points (par inscription à un programme web)

Une séance du calendrier paie **une fois**.

| Action | Points |
|--------|--------|
| Live le jour J | 10 |
| Déclarée le jour J | 6 |
| Live J±1 | 7 |
| Déclarée J±1 | 4 |
| Hors fenêtre / abandon | 0 |
| Live sans skip | +2 |

Après clôture : classement figé. Pas de gain financier.

---

## 5. Données personnelles

### 5.1 Compte

Nom, pseudo, e-mail, mot de passe (hash), téléphone optionnel, rôle, désactivation, sessions, jetons mot de passe.

### 5.2 Profil et morphologie

Genre, taille, poids, niveaux d’activité et sportif, objectif, motivations, matériel, objectif d’hydratation (verres / jour).

### 5.3 Santé ou assimilable (attention)

**Bilan de santé** (exemples prévus) : blessure / douleur ; précisions (zone, consignes médicales) ; énergie ; sommeil ; **suivi médical ou traitement** (texte libre).

**Bilan de mesure** : poids, mensurations, âge, taille.

**Bilan de départ** : objectifs, disponibilités, lieu, matériel.

Les questionnaires sont **paramétrables** par le coach (d’autres questions santé possibles).

Aussi : pesées hebdomadaires ; verres d’eau ; plan repas + check-in ; kg en séance.

**Destinataires.** Membre + **son coach**. Pas de publication de ces éléments.

### 5.4 Activité

Logs live / déclarée (dates, durées, skips, ressenti, notes, points), plan attribué, inscriptions, rang, snapshot.

### 5.5 Messages

Texte, auteur, lu, date. Pas de pièces jointes.

### 5.6 Médias catalogue

Images / GIF d’exercices et visuels programmes (stockage disque applicatif). Pas de photo de l’athlète, pas de caméra.

### 5.7 Visibilité

| Donnée | Qui voit |
|--------|----------|
| Plan, bilans, poids, eau, repas, messages | Membre + coach |
| Classement du programme | Inscrits de ce programme |
| Programme **ouvert** | Quiconque a l’URL |
| Partage `/p/{jeton}` | Quiconque a le lien |

### 5.8 Technique

Cookie de **session**. IP pour limitation des tentatives de login. Pas d’analytics / pub / pixel identifié. Pas de données bancaires.

---

## 6. Finalités (telles que le site les met en œuvre)

1. Comptes coach et membre.
2. Composer et **attribuer** un plan.
3. Exécuter les séances et en garder la preuve.
4. Points et classement interne au programme.
5. **Suivi** coach (bilans, poids, eau, repas, messages).
6. Partage d’un résultat par lien.

Pas de revente, pas de pub comportementale.

---

## 7. Contenus juridiques à produire (web seulement)

1. Mentions légales (site).
2. CGU du **site de suivi** (rôles coach / membre, plan attribué, messages, désactivation).
3. Politique de confidentialité **web** (dont données de santé / mensurations, accès coach).
4. Disclaimer sport / santé / nutrition (pas un acte médical ; repas = consignes de suivi).
5. Mineurs (pas de barrière d’âge aujourd’hui).
6. Cookies (session a priori).
7. Classement interne : besoin ou non d’un règlement de challenge (pas de lot financier).
8. Lien de partage public.
9. Droits sur les visuels d’exercices.
10. Sous-traitant hébergeur ; durées de conservation `[à définir]`.

---

## 8. Hors périmètre de ce brief

Application mobile, stores, API FastAPI, challenges à paliers, programme solo self-join, player mobile.

Pas de caméra, GPS, wearable, IA, visio, paiement, publicité.
