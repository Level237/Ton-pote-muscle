# Backlog.md — Ton Pote Musclé (web)

Périmètre **actuel uniquement** (`Product.md`). Statuts : `todo` | `doing` | `done`.

---

## Fondations

| ID | Rôle | Story |
|----|------|-------|
| AUTH-01 | Membre | Inscription e-mail + mot de passe. |
| AUTH-02 | Membre | Connexion / déconnexion. |
| AUTH-03 | Admin | Connexion espace admin. |
| AUTH-04 | Système | Routes admin interdites aux membres. |
| DS-01 | Tous | `#e6f419`, fond membre sombre, logo `/assets/img/logo.webp`. |
| PROF-01 | Membre | Genre, taille, poids, objectif, niveaux, motivations. |
| PROF-02 | Membre | Modification de ces paramètres. |
| NAV-01 | Membre | Sidebar desktop + tab bar mobile : Accueil, Mes programmes, Bilans, Profil. |
| BIL-01 | Membre | Liste des bilans + réponses (une soumission par bilan, modifiable). |
| BIL-02 | Membre | Bilan de départ juste après l’inscription. |
| BIL-03 | Admin | Résultats des bilans sur la fiche inscrit. |
| PR-09 | Membre | Pseudo visible au classement (pas l’e-mail). |

## Catalogue

| ID | Rôle | Story |
|----|------|-------|
| EX-01 | Admin | CRUD exercice. |
| EX-02 | Admin | Image d’exercice. |
| SE-01 | Admin | CRUD séance + ordre des exercices. |
| SE-02 | Membre | Catalogue + fiche séance. |

## Réalisation

| ID | Rôle | Story |
|----|------|-------|
| LIVE-01 | Membre | Live : timer, repos, horodatage par exercice. |
| LIVE-02 | Membre | Pause, skip, terminer. |
| LIVE-03 | Membre | Ressenti 1–5 + note. |
| DEC-01 | Membre | Déclarer une séance déjà faite. |
| DEC-02 | Membre | Certification. |
| LOG-01 | Membre | Historique, badges live / déclarée. |
| LOG-02 | Système | Pas deux logs de la même séance le même jour. |

## Programmes

| ID | Rôle | Story |
|----|------|-------|
| PR-01 | Admin | Programme : titre, période, calendrier. |
| PR-02 | Admin | Attacher des séances aux jours. |
| PR-03 | Membre | Fiche programme publique + CTA Rejoindre — **plus tard**. |
| PR-04 | Membre | Rejoindre self-serve — **plus tard**. |
| PR-04b | Système | Intention programme pendant auth — **plus tard**. |
| PR-05 | Membre | Abandonner (statut, pas delete) — **plus tard**. |
| PR-06 | Membre | Dashboard privé : Accueil / Semaine / Progrès (plan du coach). |
| PR-07 | Système | Fin → clos, plus d’inscription ni de points. |

## Points et classement

| ID | Rôle | Story |
|----|------|-------|
| PTS-01 | Système | Barème Product.md (idempotent). |
| PTS-02 | Membre | Points gagnés après la séance (base + bonus sans skip). |
| PTS-03 | Membre | Classement live (pseudo, pts, rang). |
| PTS-04 | Membre | Écart au rang supérieur. |
| RK-01 | Système | Rang = somme des points. |
| RK-02 | Admin | Classement provisoire. |
| RK-03 | Admin | Clôture / publication. |
| RK-04 | Admin | Export CSV. |
| RK-05 | Membre | Classement officiel après clôture. |
| RK-06 | Système | Snapshot figé. |

## Partage

| ID | Rôle | Story |
|----|------|-------|
| SH-01 | Membre | Partager un résumé de séance. |
| SH-02 | Membre | Partager le rang de fin de programme. |

---

## Ordre

1. AUTH, DS-01, PROF, PR-09  
2. EX, SE  
3. LIVE, DEC, LOG  
4. PR  
5. PTS, RK, SH  

## Gelé

Tout le reste (fil d’activité, podium semaine, mails, duels, invitations, ligues, équipes, streak, recap, courbe de poids, premium, badges, etc.) : **pas dans ce backlog**.
