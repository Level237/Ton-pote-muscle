# Design.md — Ton Pote Musclé (web)

Système visuel de cette application Laravel.

## 1. Marque

- **Nom :** Ton Pote Musclé  
- **Monogramme :** TPM  
- **Logo :** [`public/assets/img/logo.webp`](../public/assets/img/logo.webp)  
- **URL :** `/assets/img/logo.webp`  
- Fond du fichier : **noir**, glyphe **jaune-lime**. Ne jamais poser le logo sur un fond jaune.

| Contexte | Traitement |
|----------|------------|
| Header sombre, splash, footer dark | Logo tel quel |
| Header clair | Pastille noire **ou** monochrome noir — pas le webp brut sur blanc |
| Favicon | Monogramme, fond `#0a0a0a` |
| Taille header | 32–40 px de haut |
| Respiration | Marge ≥ 25 % de la hauteur du glyphe |

## 2. Couleur

### Primaire

| Token | Valeur | Rôle |
|-------|--------|------|
| `--color-primary` | `#e6f419` | Accent, CTAs, badges, focus, progress |
| `--color-primary-hover` | `#c9d616` | Hover (assombrir, ne pas éclaircir) |
| `--color-primary-muted` | `#e6f41933` | Chips, sélection |

Texte sur primaire = **noir**. `#e6f419` n’est pas une couleur de texte sur blanc.

### Neutres

Espace **membre** et **admin** : sombre + lime.

| Token | Valeur | Usage |
|-------|--------|------|
| `--color-bg` | `#0a0a0a` | Fond membre |
| `--color-bg-elevated` | `#141414` | Cards, header |
| `--color-bg-sunken` | `#000000` | Hero, overlay |
| `--color-border` | `#2a2a2a` | Séparateurs |
| `--color-text` | `#f5f5f5` | Titres, body |
| `--color-text-muted` | `#9a9a9a` | Meta |
| `--color-text-on-primary` | `#0a0a0a` | Bouton / badge lime |
| `--color-admin-bg` | `#111111` | Fond back-office |
| `--color-admin-surface` | `#181818` | Sidebar, cartes admin |
| `--color-admin-text` | `#f2f2f0` | Texte admin |

### Sémantique

| Token | Valeur | Usage |
|-------|--------|------|
| `--color-success` | `#3dd68c` | Séance terminée, inscrit |
| `--color-warning` | `#f5a524` | Programme bientôt fini |
| `--color-danger` | `#f31260` | Abandon, erreur |
| `--color-info` | `#5dade2` | Info |
| `--color-live` | `#e6f419` | Badge live |
| `--color-declared` | `#9a9a9a` | Badge déclarée |

Contraste : lime + texte noir OK ; lime 14 px sur blanc **interdit** ; overlay photo `rgba(0,0,0,0.45)` min.

## 3. Typographie

| Rôle | Famille | Poids | Notes |
|------|---------|-------|--------|
| Display / hero / CTA | `Barlow Condensed` | 600–700 | Même famille que l’UI, plus compacte |
| UI / body | `Barlow` | 400–600 | Pas d’uppercase sur les paragraphes |
| Chiffres (timer, rang) | `tabular-nums` | 700 | |

Échelle (base 16 px) : Display 40–48 / H1 28–32 / H2 22–24 / H3 18 / Body 16 / Meta 13–14 / Bouton 14–16 bold.

Énergie display (titres très gras, compact) sur landing et hero, pas dans les formulaires admin.

## 4. Forme et espace

- Cards média : rayon **20–24 px**. Boutons : **10–12 px**. Chips : pill.  
- Grille 8 px. Padding page : 24 (viewport étroit) / 32–48 (desktop).  
- Contenu membre max 1120 px, admin max 1280 px.  
- Accent **coin lime** sur hero / card programme seulement, pas sur les tableaux.

## 5. Composants

**Boutons :** primary lime (texte noir) ; inverse noir sur photo ; ghost bordure `#2a2a2a` ; danger. Hover −8–12 %, focus ring 2 px `#e6f419`, disabled 40 %, hauteur min 44 px.

**Cards :** fond `#141414`. Programme / séance : image, overlay, titre, durée, play (cercle noir, icône lime).

**Nav membre :** header sticky sombre, logo, Séances, Programmes, Classements, Profil, CTA « S’entraîner ».  
**Nav admin :** sidebar sombre, item actif fond lime 15 % + texte lime.

**Forms :** label au-dessus. Inputs fond `#111111`, focus lime. Erreurs en texte, pas seulement en couleur.

**Badges :** `LIVE` lime/noir ; `DÉCLARÉE` gris ; `PREMIUM` outline lime ; podium lime / argent / bronze.

**Séance live :** plein viewport, fond noir, timer très grand, barre d’exercices, CTA « Terminé / suivant », Pause / Skip. Repos : fond lime, texte noir.

**Classement :** tableau, rang sticky, ligne du membre connecté en lime muted, podium top 3 au-dessus.

## 6. Motion

150–200 ms ease-out. Timer linéaire. Peu de célébration (fin de programme / 1er rang). Respecter `prefers-reduced-motion`.

## 7. Images

Photos : overlay sombre obligatoire. GIF / vidéo exercice : ne pas recadrer le mouvement. Assets dans `public/assets/`.

## 8. Accessibilité

Focus clavier partout (player inclus). Timer : `aria-live` polite, pas à chaque seconde. Unités kg / cm explicites. Rang : ne pas reposer que sur la couleur.

## 9. Pages V1

**Public :** landing, connexion, inscription.  
**Membre :** dashboard, catalogue, player live, log déclaré, programmes, classement, profil, partage.  
**Admin :** login, dashboard, exercices, séances, programmes, inscrits, génération classement.

## 10. Tokens Tailwind

Dans `resources/css/app.css` `@theme` :

```css
--color-primary: #e6f419;
--color-bg: #0a0a0a;
--color-surface: #141414;
--color-text: #f5f5f5;
--color-muted: #9a9a9a;
```

Toujours `#e6f419`, jamais un `lime-*` Tailwind de substitution.
