# Product.md — Ton Pote Musclé (web)

Référence produit de **l’application web** Laravel (`tonpotemuscle-web`). Périmètre **figé** : ce qui est décrit ici. Les idées hors de ce document sont gelées (voir fin de fichier).

## 1. Vision

Site d’entraînement : séances, programmes datés, **compétition entre inscrits** (points + classement).

Promesse : **ton corps, ton rythme** — les autres inscrits du programme empêchent de lâcher.

## 2. Problème

- Une **séance** n’est pas un **programme**.
- Beaucoup s’entraînent sans ouvrir le site : il faut un mode **déclaré**, pas seulement le live.

## 3. Utilisateurs

| Rôle | Besoin |
|------|--------|
| **Admin** | Catalogue, programmes, inscrits, classement |
| **Membre** | Profil, séances, programme, points, partage |

Espaces séparés : `/admin` vs espace membre.

## 4. Concepts

**Exercice** — consignes, média, zones, matériel.

**Séance** — réutilisable, sans dates. Exercices ordonnés (reps ou durée, repos). Seule (à la carte) ou placée dans un programme.

**Programme** — **public**. Période (début / fin), calendrier de séances. Rejoindre = entrer en compétition (pseudo au classement). Points pendant la période, classement live, snapshot à la clôture.

**Inscription** — un programme actif à la fois (V1).

**Log** — preuve qu’une séance a été faite (liée ou non à un programme).

## 5. Live et déclarée

Les deux.

| | Live | Déclarée |
|---|------|----------|
| | Timer, horodatage par exercice | Date, durée, ressenti, certification |
| Points | Plus élevés | Moins élevés |

Garde-fous : 1 log par séance et par jour ; durée déclarée bornée ; badges `live` / `déclarée`. Pas de points pour la vitesse ni pour les minutes déclarées.

## 6. Périmètre (on implémente ça)

### Admin

- Auth.
- CRUD exercices, séances, programmes (période + calendrier).
- Diffuser un programme : copier l’**URL publique** de la fiche (`/programmes/{uuid}`) et l’envoyer. Pas de générateur de lien, pas de token.
- Liste des inscrits.
- Classement provisoire, clôture / publication, export CSV.

### Membre

- Auth, profil (genre, objectif, niveaux, taille, poids, motivations) et modification de ces paramètres.
- Pseudo visible au classement (pas l’e-mail).
- Catalogue séances / programmes (fiches publiques).
- **Rejoindre un programme** (voir §6.1) / abandonner.
- Inscription compte seulement si besoin, après le clic Rejoindre.
- Séance live ou déclarée.
- Dashboard : programme actif, rang, points, prochaine séance, derniers logs.
- Classement live des inscrits + écart au suivant.
- Partage d’un résumé (séance ou rang).

### 6.1 Parcours « Rejoindre le programme »

La fiche est lisible **sans compte**. Un seul CTA : **Rejoindre le programme**.

```
Fiche publique  →  Rejoindre  →  (si besoin) créer un compte ou se connecter  →  retour sur le programme, inscrit
```

| État | Après « Rejoindre » |
|------|---------------------|
| Visiteur | Création de compte (lien « j’ai déjà un compte » → connexion). Intention `programme` conservée. |
| Membre connecté | Inscription immédiate, on reste sur la fiche (déjà dedans). |
| Compte créé / connecté | Retour **sur ce programme**, déjà **inscrit** (pas un 2e clic Rejoindre). |

Le profil (taille, poids, etc.) se complète après, depuis le compte — on ne bloque pas le join avec un onboarding long.

Programme `clos` ou `draft` : pas de CTA Rejoindre.

## 7. Points

Solde **par inscription**. Une séance du calendrier paie **une fois**.

| Action | Points |
|--------|--------|
| Live le jour J | **10** |
| Déclarée le jour J | **6** |
| Live J±1 | **7** |
| Déclarée J±1 | **4** |
| Hors fenêtre, hors calendrier, live abandonné | **0** |
| Live sans skip | **+2** |

Égalité : total de points, puis nombre de lives, puis date d’inscription.

Admin clôt → snapshot, plus de points.

## 8. Technique

Laravel 13, Vite, Tailwind 4. Tout le métier dans cette app.

## 9. Succès

Admin crée séance + programme. Membre s’inscrit, fait live et déclarée, gagne des points, voit son rang. Classement figé à la fin. La déclarée n’exclut personne.

## 10. Gelé (ne pas implémenter maintenant)

Fil d’activité, podium semaine, mails « dépassé », duels, invitations, ligues, équipes, streak / recap, courbe de poids, premium payant, badges, nutrition, IA, hydratation, visio, wearables.
