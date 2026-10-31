# Roadmap.md — Ton Pote Musclé (web)

Uniquement le périmètre `Product.md`. Idées hors cœur : gelées.

Décisions : séance ≠ programme ; programme = compétition (points, classement, clôture) ; live **et** déclaré ; un programme actif à la fois.

## Phase 0 — Fondations

Auth membre + admin, layouts (`Design.md`), profil, pseudo, landing.

## Phase 1 — Catalogue

CRUD exercices et séances. Catalogue membre.

## Phase 2 — Faire une séance

Player live, formulaire déclarée, historique, anti-doublon.

## Phase 3 — Programmes

CRUD programme, inscription / abandon, dashboard (rang, points, prochaine séance).

## Phase 4 — Points, classement, partage

Barème, classement live + écart, clôture admin + CSV, partage.

**Fin de ce périmètre.** Pas de phase « idées » ensuite tant que ça n’est pas en production.

## Dépendances

```
Phase 0 → Phase 1 → Phase 2
                ↘ Phase 3 → Phase 4
```

## Risques

| Risque | Mitigation |
|--------|------------|
| Trop de concepts | Un objet daté : programme |
| Classement triché | Barème, 1 log/jour, plafond durée |
| Player web pénible | Tap targets, viewport étroit d’abord |
| Lime illisible | `Design.md` |
