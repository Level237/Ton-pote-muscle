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

**Programme** — créé par le coach, période + calendrier. Attribué aux membres (Donner une semaine), pas un self-join public pour l’instant.

**Inscription** — le coach connaît ses membres ; il crée le compte / l’inscrit et lui donne le plan.

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
- Donner une séance ou une semaine à un inscrit (plan privé coach → membre).
- Liste des inscrits.
- Classement provisoire, clôture / publication, export CSV.

### Membre

- Auth. Après inscription : **bilan de départ** obligatoire, puis dashboard.
- Espace **privé** : le membre voit le plan que le coach lui a donné. Le coach lit les réponses des bilans sur la fiche inscrit.
- Navigation : sidebar (grand écran) / tab bar (petit écran) — Accueil, Mes programmes, Bilans, Profil.
- Accueil : Accueil / Semaine / Progrès (séances du calendrier, à rattraper, stats).
- Mes programmes : programmes donnés par le coach.
- Bilans : questionnaires à remplir (santé, départ, mesure…).
- Profil (genre, objectif, niveaux, taille, poids, motivations) et modification.
- Séance live : Débuter depuis Accueil → détail (GIF animé s’il existe) → Commencez → player (timer, pause, skip).
- Séance déclarée — ensuite.
- Classement / points / partage — ensuite, une fois les logs en place.

### 6.1 Rejoindre un programme (plus tard)

Pas dans le flux actuel. Le coach attribue. Un self-join public pourra revenir si le club s’ouvre.

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

Le coach crée séances + programmes, donne un plan à ses membres. Le membre voit son dashboard, fera live et déclarée, gagnera des points. La déclarée n’exclut personne.

## 10. Gelé (ne pas implémenter maintenant)

Fil d’activité, podium semaine, mails « dépassé », duels, invitations, ligues, équipes, streak / recap, courbe de poids, premium payant, badges, nutrition, IA, hydratation, visio, wearables.
