# PhyloSanitas — Documentation du site

> Dernière mise à jour : 3 novembre 2026

Ce document présente le site PhyloSanitas : son contexte, toutes ses fonctionnalités (site public et administration), son fonctionnement technique et les points à surveiller. Il s'adresse à l'équipe éditoriale, aux administrateurs et aux développeurs.

---

## Sommaire

1. [Contexte](#1-contexte)
2. [Vue d'ensemble](#2-vue-densemble)
3. [Site public : contenus](#3-site-public--contenus)
4. [Site public : services santé](#4-site-public--services-santé)
5. [Fidélisation des lecteurs](#5-fidélisation-des-lecteurs)
6. [Administration](#6-administration)
7. [Rôles et droits](#7-rôles-et-droits)
8. [Référencement (SEO) et crédibilité médicale](#8-référencement-seo-et-crédibilité-médicale)
9. [Revenus : publicités et AdSense](#9-revenus--publicités-et-adsense)
10. [Application installable et notifications](#10-application-installable-pwa-et-notifications)
11. [Sécurité et protection anti-robots](#11-sécurité-et-protection-anti-robots)
12. [Technique](#12-technique)
13. [Mise en production](#13-mise-en-production)
14. [Contenus à valider par l'équipe médicale](#14-contenus-à-valider-par-léquipe-médicale)
15. [Tâches régulières](#15-tâches-régulières)
16. [Évolutions prévues](#16-évolutions-prévues)

---

## 1. Contexte

**PhyloSanitas** est un site d'information santé basé à **Abidjan (Côte d'Ivoire)**, dont les articles sont rédigés par un médecin. Slogan : *« Votre santé, notre priorité »*.

- **Public visé** : le grand public ivoirien et francophone d'Afrique de l'Ouest, souvent sur téléphone et parfois avec une connexion faible.
- **Objectifs** :
  - informer de façon fiable et accessible (prévention, maladies courantes, actualités santé) ;
  - fidéliser les lecteurs grâce à des services utiles (questions au médecin, outils, annuaire…) ;
  - générer des revenus (Google AdSense et espaces publicitaires vendus en direct).
- **Particularités** :
  - la santé est un sujet « sensible » pour Google (YMYL) : la crédibilité (auteur, relecture médicale, sources) pèse sur le référencement ;
  - le partage passe surtout par **WhatsApp** ;
  - le site doit rester léger et fonctionner hors connexion autant que possible.

---

## 2. Vue d'ensemble

| Domaine | Fonctionnalités |
|---|---|
| Contenus | Articles, catégories, sondages, actualités, « À la une », commentaires, recherche |
| Services santé (en ligne) | Pharmacies de garde, quiz vrai/faux, outils santé (IMC, besoin en eau, cycle menstruel), calendrier santé |
| Services santé (masqués, réactivables) | Le médecin répond, maladies de A à Z, annuaire santé public, dossiers, notifications |
| Communication | Infos flash, popups d'information, bandeau des journées santé, newsletter, notifications |
| Crédibilité | Pages auteur, « Relu par un médecin », sources, date de mise à jour, avertissement médical |
| Revenus | Google AdSense, publicités en direct avec statistiques de clics |
| Technique | Application installable (PWA), mode hors connexion, plan du site, données structurées, cache |

**Navigation du site** : le menu principal affiche les catégories, un menu déroulant **« Services santé »** et Contact. Sur téléphone, tout est dans le menu latéral. Les services sont aussi accessibles par :
- des **liens rapides** sous l'en-tête de chaque page de service ;
- un encadré **« Services santé »** dans la colonne de droite des articles et des listes ;
- la barre **« Thématiques »** de l'accueil ;
- le pied de page.

Les barres « Thématiques » et « Services » défilent horizontalement, avec des **flèches gauche / droite** qui n'apparaissent que lorsque tout ne tient pas sur une ligne. Le service de la page en cours est centré automatiquement.

### Modules masqués pour le moment

Plusieurs modules sont développés mais **masqués**. Leurs pages publiques renvoient une erreur 404 et ils n'apparaissent ni dans les menus, ni dans le plan du site. Le code et les données sont conservés.

| Module | Variable `.env` | Remarque |
|---|---|---|
| Dossiers | `MODULE_DOSSIERS` | Absent aussi de l'administration |
| Notifications | `MODULE_NOTIFICATIONS` | Aucune notification n'est envoyée |
| Le médecin répond | `MODULE_QUESTIONS` | Sera remplacé par un futur forum ; les boutons « Poser une question » mènent aux pharmacies de garde |
| Maladies de A à Z | `MODULE_MALADIES` | Absent aussi de l'administration ; les 13 fiches restent en base |
| Annuaire santé | `MODULE_ANNUAIRE` | L'administration reste active sous le nom « Pharmacies de garde » et ne gère **que les pharmacies** (garde, import) : hôpitaux, cliniques et numéros d'urgence y sont invisibles et inaccessibles, mais restent en base |

Pour réactiver un module, ajouter dans le fichier `.env` du serveur :

```
MODULE_DOSSIERS=true
MODULE_NOTIFICATIONS=true
MODULE_QUESTIONS=true
MODULE_MALADIES=true
MODULE_ANNUAIRE=true
```

puis lancer `php artisan config:clear` (ou `php artisan optimize`). Le réglage se trouve dans `config/sante.php` (clé `modules`).

---

## 3. Site public : contenus

### Accueil (`/`)
1. **Carrousel** (géré dans *Admin > Carrousel Hero*) ou bannière par défaut avec recherche.
2. **Thématiques** et **Services** : raccourcis vers les catégories et les services santé, avec flèches de défilement.
3. **Pharmacies de garde** : bandeau avec le nombre de pharmacies de garde, la date de fin de la garde, un aperçu de 3 pharmacies (bouton d'appel) et le lien vers la liste complète.
4. **À la une** : 1 article principal + 3 secondaires (4 places, gérées depuis la liste des articles).
5. Bandeau publicitaire.
6. **Services santé** : tuiles vers les services + une question de **quiz jouable directement** (tirée au hasard dans le dernier quiz en ligne).
7. **Articles récents**, newsletter, encadré **« Quand faut-il consulter ? »** (numéro d'urgence), puis **Journées santé** (les 3 prochaines dates du calendrier ; **Maladies fréquentes** si ce module est réactivé) et **Outils santé** ; barre latérale à droite.
8. **Prévention** : 4 bons réflexes illustrés (manger, bouger, se vacciner, se dépister).

### Articles (`/articles`, `/articles/{catégorie}`, `/article/{adresse}`)
- Liste paginée par catégorie.
- Page article :
  - temps de lecture, vues, commentaires ;
  - **auteur** et **« Relu par »** (voir §8) ;
  - date de mise à jour ;
  - **sources** ;
  - lien vers la source officielle ;
  - avertissement médical ;
  - partage (Facebook, WhatsApp, X, copie du lien) ;
  - **« Cet article vous a-t-il aidé ? »** ;
  - **navigation de dossier** (précédent / suivant) si l'article fait partie d'un dossier ;
  - bloc de fin de lecture (poser une question, s'abonner aux notifications) ;
  - commentaires ;
  - **« À lire aussi »** : la même catégorie en priorité.
- Les anciennes adresses (`/post?category=…`, `/post/detail?slug=…`) redirigent définitivement vers les nouvelles, ce qui conserve le référencement.

### Sondages
Un article de la catégorie « Sondage » affiche des options de vote et les résultats après le vote (un vote par visiteur).

### Autres pages
- Recherche (`/recherche`).
- Contact (`/contact`, messages reçus dans *Admin > Messages*).
- Politique de confidentialité (`/politique-de-confidentialite`, exigée par AdSense).
- Page hors connexion (`/hors-ligne`).

### Éléments communs à toutes les pages
- **Infos flash** : bandeau défilant ; il devient rouge pour une info urgente.
- **Bandeau des journées santé** : par exemple « Ce mois-ci : Novembre bleu ». Le visiteur peut le fermer.
- **Popup d'information** : une affiche de campagne, avec une fréquence réglable.
- Compteur de visites (optionnel), réseaux sociaux, coordonnées.
- Avertissement médical en pied de page.

---

## 4. Site public : services santé

La liste des services (nom, icône, texte) est centralisée dans `config/sante.php` (clé `services`).

### 4.0 Pharmacies de garde (`/pharmacies-de-garde`)
C'est le service consulté presque chaque jour : il est mis en avant partout.

**Où le trouver :**
- un bouton vert **« De garde »** dans l'en-tête de toutes les pages, avec le nombre de pharmacies (texte complet sur grand écran) ;
- le **bandeau de l'accueil**, sous les thématiques ;
- l'encadré **« De garde cette semaine »** en haut de la colonne de droite (sauf sur l'accueil) ;
- en premier dans le menu « Services santé », les liens rapides et le pied de page ;
- un **raccourci de l'application installée** (appui long sur l'icône) ;
- le plan du site, avec une priorité haute et une mise à jour quotidienne.

**La page :**
- titre avec les dates de la garde (« du lundi 2 novembre au dimanche 8 novembre »), utile pour Google ;
- choix de la ville, **mémorisé 30 jours** pour le visiteur ;
- pharmacies regroupées par quartier, avec une **recherche instantanée** par quartier ou par nom ;
- chaque fiche affiche un bouton d'appel et un lien Itinéraire ;
- bouton **« Partager la liste » sur WhatsApp**, qui envoie un message prêt avec la liste et les numéros ;
- s'il n'y a pas de garde enregistrée, un message invite à revenir et renvoie vers toutes les pharmacies de l'annuaire.

Les données viennent de l'annuaire. La liste du jour est gardée en cache 10 minutes et se met à jour dès qu'une garde est enregistrée. Elles peuvent être saisies à la main (voir §6) ou **importées depuis Google Drive**, automatiquement ou à la main, comme ci-dessous.

La page se présente en **tableau** : Abidjan d'abord, groupé par commune, puis les villes de l'intérieur. Une recherche instantanée accepte plusieurs mots (« cocody riviera »), et une liste déroulante permet de choisir la ville, avec une adresse propre à chaque ville (`?ville=Bouake`). Sur téléphone, chaque pharmacie devient une carte compacte avec un grand bouton d'appel.

#### Import depuis Google Drive (tâche planifiée Claude)

**Le principe :**
1. Une **tâche planifiée Claude**, reliée au Google Drive, récupère chaque semaine la liste des pharmacies de garde et dépose un fichier dans un **dossier Google Drive** (un nouveau fichier chaque semaine, ou toujours le même : les deux fonctionnent).
2. Le site lit ce dossier avec un **compte de service Google** (une clé JSON posée sur le serveur, accès en lecture seule). Le dossier reste **privé** : il est seulement partagé avec l'adresse du compte de service.
3. Le site regarde le dossier **au plus toutes les 6 heures**, au fil des visites, et importe le **fichier le plus récent** s'il est nouveau. Il n'y a besoin d'aucune tâche cron sur le serveur ; si une tâche cron `php artisan schedule:run` existe, la commande `php artisan gardes:import` tourne aussi toutes les 6 heures.
4. **À la main** : le bouton « Importer maintenant depuis Drive » force la lecture du dossier, et « Importer un fichier » publie un fichier envoyé depuis l'ordinateur (CSV, Excel, JSON). Un fichier envoyé à la main reste en ligne jusqu'à l'arrivée d'un nouveau fichier dans le dossier.

**Mise en place (une seule fois) : compte de service**, sans écran de consentement ni validation Google, et sans expiration :
1. Sur [console.cloud.google.com](https://console.cloud.google.com/), créer un projet (ex. « PhyloSanitas ») et, dans *API et services > Bibliothèque*, activer **Google Drive API**.
2. *IAM et administration > Comptes de service* : **Créer un compte de service** (ex. « phylosanitas-drive », aucun rôle nécessaire). Ouvrir ce compte, onglet *Clés > Ajouter une clé > Créer une clé > JSON* : un fichier `.json` est téléchargé.
3. Déposer ce fichier sur le serveur, dans `storage/app/private/` (dossier non versionné par git, et non publié sur le Web), par exemple `storage/app/private/google-drive.json`. **Ne jamais le mettre dans `public/` ni le commiter.**
4. Dans le `.env` du serveur :
   ```
   GOOGLE_DRIVE_FICHIER_IDENTIFIANTS=storage/app/private/google-drive.json
   GOOGLE_DRIVE_DOSSIER_ID=<ID du dossier, la fin du lien drive.google.com/drive/folders/…>
   ```
   puis `php artisan config:clear` (ou `config:cache`).
5. Dans Google Drive, **partager le dossier** avec l'adresse du compte de service (`…@….iam.gserviceaccount.com`, visible dans le fichier JSON, champ `client_email`, et dans l'administration), en **Lecteur**.
6. Dans *Admin > Pharmacies de garde > Import depuis Google Drive* : la case affiche « Drive relié par compte de service ». Activer « Publier automatiquement », enregistrer, puis « Importer maintenant » pour vérifier.

Le champ « Lien du dossier » de l'administration est facultatif : s'il est rempli, il remplace `GOOGLE_DRIVE_DOSSIER_ID`. Si la clé est supprimée dans Google Cloud ou si le dossier n'est plus partagé, l'import est refusé avec un message clair et la liste en ligne est conservée. La même clé peut servir en local et en ligne. Le script `deploy` exclut `storage/` et `.env` : la clé est à envoyer **une fois à la main** sur le serveur, par exemple `scp -P 65002 storage/app/private/google-drive.json u823812213@145.14.156.41:~/domains/phylosanitas.com/phylosanitas/storage/app/private/` (créer le dossier `private` s'il n'existe pas), et les deux lignes sont à ajouter au `.env` du serveur.

**Autre méthode (déconseillée) : connexion OAuth**, utilisée seulement si aucun compte de service n'est configuré. Dans l'administration, *Autre méthode : connexion OAuth* : créer un *ID client OAuth* « Application Web » avec l'URI de redirection affichée (ex. `https://phylosanitas.com/admin/annuaire/drive/callback` ; plusieurs URI possibles, dont `http://127.0.0.1:8000/…` en local), coller l'ID client et le code secret, puis « Connecter Google Drive ». L'écran de consentement doit être **publié** (statut « En production ») : sinon Google répond « Erreur 403 : access_denied » aux comptes non testeurs, et coupe l'accès au bout de 7 jours. Le jeton et le code secret sont **chiffrés** en base (clé `APP_KEY`).

**Autres liens acceptés** à la place d'un dossier : un fichier Drive, une feuille Google Sheets (l'onglet du lien est respecté), ou une feuille « publiée sur le Web » au format CSV. Un fichier partagé « Tous les utilisateurs disposant du lien » se lit même sans connecter Drive.

**Formats lus** : Google Sheets, Excel `.xlsx`, CSV (virgule, point-virgule ou tabulation), Google Docs contenant un tableau, JSON. Le fichier le plus récent du dossier qui a l'un de ces formats est choisi ; les autres (PDF, images…) sont ignorés.

**Contenu attendu** : une ligne d'en-têtes, dans n'importe quel ordre (majuscules et accents ignorés ; elle peut être précédée d'un titre), puis une ligne par pharmacie. Plusieurs tableaux à la suite (Abidjan puis intérieur) sont acceptés.

| ville | commune | pharmacie | telephone | debut | fin |
|---|---|---|---|---|---|
| Abidjan | Cocody | Pharmacie des Deux Plateaux | 27 22 41 00 01 | 2026-10-31 | 2026-11-06 |
| Bouaké | | Pharmacie de la Gare | 27 31 63 00 05 | 2026-10-31 | 2026-11-06 |

- `commune` : la commune ou le quartier pour Abidjan ; laisser vide pour les villes de l'intérieur.
- Dates au format `AAAA-MM-JJ`, `JJ/MM/AAAA` ou « 2 novembre 2026 ». Sans colonnes `debut`/`fin`, une colonne `periode` (« du … au … ») ou la période écrite **dans le nom du fichier ou dans le titre** au-dessus du tableau suffit (ex. « Pharmacies de garde du 2 au 8 novembre 2026 »).
- En-têtes acceptés aussi : `quartier`, `Commune / Secteur`, `nom`, `contact`, `tel`, `du`, `au`, `date de debut`…
- Colonnes facultatives : `Adresse / Repère` (affichée sous le nom), `Latitude` et `Longitude` (bouton d'itinéraire précis), `Itinéraire (Google Maps)` (utilisé seulement sans GPS ; les liens de recherche sont ignorés, le site les refait lui-même). Les autres colonnes (`Zone`, `Précision itinéraire`…) sont ignorées.
- Période dans le nom du fichier : `pharmacies_de_garde_CI_2026-10-31_au_11-06.csv` est compris comme « du 31 octobre au 6 novembre 2026 ».
- **Ne jamais mettre le nom des pharmaciens** : ce sont des données personnelles. Une colonne `Pharmacien(ne)` présente dans le fichier est ignorée à l'import.

**Sécurités** : la liste n'est **pas publiée**, et l'ancienne reste en ligne, si le dossier ou le fichier est inaccessible, s'il contient moins de 20 pharmacies valides, si la garde est déjà terminée, ou si elle commence dans plus de 7 jours. Le compte rendu du dernier import (date, fichier, nombre de pharmacies, période, erreur éventuelle) est affiché dans *Admin > Pharmacies de garde*. Un fichier refusé n'est pas relu automatiquement : il faut un nouveau fichier (ou cliquer « Importer maintenant » après correction).

**À l'import :**
- une pharmacie inconnue est ajoutée ;
- une pharmacie connue garde les informations saisies à la main (téléphone, adresse) ;
- les pharmacies absentes de la nouvelle liste ne sont plus de garde.

**Consigne type pour la tâche planifiée** (chaque samedi vers 8 h, puis une seconde fois vers 14 h par sécurité) :

> Ouvre la page de la liste des pharmacies de garde en Côte d'Ivoire. Récupère les deux tableaux (Abidjan et intérieur) et la période indiquée dans le titre (« du … au … »). Crée dans le dossier Google Drive « Pharmacies de garde » une feuille Google Sheets nommée « Pharmacies de garde du [début] au [fin] », avec une ligne d'en-tête `ville,commune,pharmacie,telephone,debut,fin`, puis une ligne par pharmacie. Pour Abidjan : ville = Abidjan, commune = la commune ou le quartier du tableau. Pour l'intérieur : ville = la ville du tableau, commune vide. Téléphone : uniquement le numéro, **sans le nom du pharmacien**. debut et fin : les dates de la période au format AAAA-MM-JJ. Ignore les lignes sans nom de pharmacie.

⚠️ Avant de reprendre automatiquement la liste d'un site tiers, obtenir son **autorisation**. Ce site se réserve les droits sur son contenu. Prévoir alors la mention de la source avec un lien sur la page.

### 4.1 Le médecin répond (`/le-medecin-repond`) — *masqué pour le moment, un forum est prévu (voir §2)*
Quand le module est masqué, les boutons « Poser une question » du site sont remplacés par des liens vers les pharmacies de garde.

- Le lecteur pose une question avec :
  - un prénom ou pseudo ;
  - une tranche d'âge (facultatif) ;
  - un email (facultatif, jamais publié) ;
  - son accord pour une publication anonyme.
- L'équipe répond dans l'administration, puis publie. La question et la réponse paraissent **sans le nom du lecteur**.
- Chaque réponse a sa propre page (`/le-medecin-repond/{adresse}`), avec un compteur de lectures et des données structurées « Questions-réponses » pour Google.
- On peut rechercher dans les réponses.
- Les boutons « Poser une question » du site (fin d'article, « Quand consulter ? ») mènent ici.

### 4.2 Quiz « Vrai ou faux ? » (`/quiz`)
- Chaque quiz est une série d'affirmations. Pour chacune, le lecteur répond « vrai » ou « faux », puis voit l'explication et, si prévu, un lien vers un article.
- En fin de partie :
  - score ;
  - récapitulatif ;
  - score moyen des participants ;
  - bouton **« Défier mes proches » sur WhatsApp** ;
  - bouton « Rejouer ».
- Le nombre de participants et le score moyen sont visibles dans l'administration.

### 4.3 Outils santé (`/outils-sante`)
Les calculs se font dans le navigateur : **rien n'est envoyé ni enregistré**.

| Outil | Adresse | Principe |
|---|---|---|
| IMC | `/outils-sante/imc` | Poids / taille², seuils de l'OMS, jauge, poids conseillé pour la taille (adultes) |
| Cycle menstruel | `/outils-sante/cycle` | Prochaines règles sur 3 cycles, ovulation (environ 14 jours avant les règles suivantes), période fertile (5 jours avant l'ovulation + le lendemain), jour et phase du cycle, calendrier coloré sur 2 mois. Rappel : ce n'est pas une méthode de contraception |
| Besoin en eau | `/outils-sante/hydratation` | Environ 30 ml/kg, plus un supplément selon l'activité, la chaleur, la grossesse ou l'allaitement |

Les seuils de l'IMC sont dans `config/sante.php`. Les outils **date d'accouchement** et **vaccins de l'enfant** sont masqués : pour les remettre, passer leur `'actif'` à `true` dans `config/sante.php` (clé `outils`).

### 4.4 Maladies de A à Z (`/maladies`) — *masqué pour le moment (voir §2)*
- Index alphabétique avec navigation par lettre et **filtre instantané** (fonctionne aussi avec les autres noms d'une maladie, sans tenir compte des accents).
- Chaque fiche contient :
  - un résumé ;
  - les symptômes ;
  - les causes et facteurs de risque ;
  - la prévention ;
  - le traitement ;
  - **« Quand consulter ? »**, mis en évidence ;
  - le médecin relecteur et la date de mise à jour ;
  - le partage ;
  - les **articles liés**, retrouvés automatiquement grâce aux mots-clés de la fiche.
- Données structurées « page médicale » pour Google.

### 4.5 Dossiers thématiques (`/dossiers`) — *masqué pour le moment (voir §2)*
- Un dossier regroupe plusieurs articles dans un ordre de lecture (par exemple « Tout comprendre sur l'hypertension »).
- La page du dossier présente un parcours numéroté, un bouton « Commencer la lecture » et le temps de lecture total.
- Dans chaque article du dossier, une barre de progression et des liens **précédent / suivant** guident le lecteur.

### 4.6 Annuaire santé (`/annuaire-sante`) — *page publique masquée pour le moment (voir §2)*
- **Numéros d'urgence** en haut de page : un appui lance l'appel.
- **Pharmacies de garde** de la période en cours, mises en avant.
- Filtres par nom ou quartier, type et ville. Pastilles par type.
- Chaque fiche affiche l'adresse, les horaires, un bouton d'appel et un lien **Itinéraire** (Google Maps).

### 4.7 Calendrier santé (`/calendrier-sante`)
- Les journées mondiales et les campagnes mensuelles de l'année en cours, classées en trois parties :
  1. **En ce moment** : la journée du jour et la campagne du mois (par exemple Novembre bleu) ;
  2. **À venir** : le reste de l'année, regroupé par mois, avec « Demain » ou « Dans 11 jours ». En fin d'année, quand il n'y a plus rien à venir, les premières journées de l'année suivante s'affichent ;
  3. **Déjà passées** : en fin de page, plus discrètes, avec la date de la prochaine édition.
- Le même calendrier alimente le **bandeau** affiché en haut du site :
  - le jour J ;
  - quelques jours avant (réglable) ;
  - tout le mois pour une campagne mensuelle.

### 4.8 Pages auteur (`/auteur/{adresse}`)
Photo, fonction, biographie, nombre d'articles écrits et relus, liste des articles. La page n'est visible que si le profil public du compte est activé.

---

## 5. Fidélisation des lecteurs

| Outil | Où | Fonctionnement |
|---|---|---|
| « Cet article vous a-t-il aidé ? » | Page article | Oui ou Non, un avis par visiteur et par article. Le pourcentage de « oui » s'affiche dans *Admin > Articles > Détail* |
| Notifications (*masquées pour le moment*) | Fin d'article et pied de page | « Recevoir les nouveaux articles » : une notification à chaque nouvel article, même site fermé (voir §10) |
| Partage WhatsApp | Articles, fiches, quiz, dossiers, réponses | Lien de partage prérempli |
| Newsletter | Accueil et barre latérale | Inscription par email, export dans l'administration |
| Commentaires | Articles | Publiés directement, modérables depuis le détail de l'article |

---

## 6. Administration

Accès : `/login`, puis `/admin`.

### Contenu
- **Tableau de bord** : statistiques d'articles, de visites et de commentaires.
- **Articles** :
  - création et modification avec éditeur riche ;
  - catégorie et image de couverture (versions WebP générées automatiquement) ;
  - résumé pour Google ;
  - publication ou brouillon, et « À la une » (4 places) ;
  - **auteur** et **« Relu par un médecin »** ;
  - **sources**, une par ligne ;
  - page de détail avec vues par jour, pays, commentaires et avis des lecteurs.
- **Sondages** : questions et options de réponse, résultats.
- **Catégories** (administrateurs).
- **Infos flash** : message, niveau (info, important, urgent), lien, période de diffusion.
- **Popups** : affiche, bouton, pages, fréquence, délai, période, statistiques d'affichage et de clics.

### Services lecteurs
- **Le médecin répond** :
  - trois onglets : *À traiter*, *Publiées*, *Sans suite* ;
  - pour chaque question : corriger le texte, donner un **titre court** (obligatoire pour publier), écrire la réponse, choisir le thème et le médecin ;
  - actions : « Enregistrer le brouillon » ou « Publier la réponse », puis « Retirer du site », « Classer sans suite » ou « Supprimer » ;
  - le menu affiche le nombre de questions en attente.
- **Quiz vrai / faux** : titre, introduction, affirmations (bonne réponse, explication, article lié), réordonnables. Les participants et le score moyen sont indiqués.
- **Maladies de A à Z** : fiches avec une rubrique par champ, une ligne par élément ; médecin relecteur ; mots-clés pour les articles liés ; publiée ou brouillon.
- **Dossiers** (*masqué pour le moment*) : titre, introduction, articles ajoutés puis réordonnés (au moins 2), position, en ligne ou masqué.
- **Journées santé** : nom, jour et mois (ou « Tout le mois »), message du bandeau, lien, couleur, nombre de jours d'annonce, actif. Le bandeau affiché en ce moment est indiqué en haut de la page.
- **Pharmacies de garde** (*Annuaire santé* quand le module est réactivé) :
  - fiches des pharmacies (tant que l'annuaire est masqué, pas de type à choisir : hôpitaux, cliniques et numéros d'urgence n'apparaissent pas) ; avec le module actif, fiches de tous les types ;
  - **import depuis Google Drive** (état de la liaison par compte de service, lien du dossier facultatif, import automatique ou immédiat) et **envoi d'un fichier** depuis l'ordinateur (voir §4.0) ;
  - **bloc « Pharmacies de garde »** : cocher les pharmacies, choisir les dates, enregistrer (option : retirer la garde des autres) ;
  - bouton pour retirer une garde ;
  - masquer un établissement fermé ;
  - le menu affiche le nombre de pharmacies de garde aujourd'hui.

### Audience et revenus (administrateurs)
- **Publicités** : bannière image ou code, emplacement, période, statistiques.
- **Messages** : messages de contact, avec les statuts non lu et traité.
- **Newsletter** : liste des abonnés et export.
- **Notifications** (*masqué pour le moment*) :
  - nombre d'appareils abonnés ;
  - envoi manuel (titre, texte, page à ouvrir) ;
  - réglage « Notifier chaque nouvel article » ;
  - historique avec le nombre d'appareils atteints.

### Administration (administrateurs)
- **Utilisateurs** : création, rôles, blocage.
- **Carrousel Hero** : diapositives de l'accueil.
- **Configuration** : nom du site, slogan, description, logo, adresse, téléphones, emails, WhatsApp, réseaux sociaux, horaires, compteur de visites, avertissement médical, numéro d'urgence.

### Mon profil
- Informations et mot de passe.
- Onglet **« Profil public »** : photo, fonction affichée, biographie, case « Professionnel de santé », case « Afficher ce profil sur le site ». Chaque utilisateur modifie le sien ; un administrateur peut modifier tous les profils.

---

## 7. Rôles et droits

| Fonction | Webmaster | Administrateur |
|---|---|---|
| Articles, sondages, infos flash, popups | ✅ | ✅ |
| Services lecteurs (questions, quiz, maladies, dossiers, journées, annuaire) | ✅ | ✅ |
| Son propre profil public | ✅ | ✅ |
| Profil public des autres comptes | ❌ | ✅ |
| Catégories, publicités, messages, newsletter, notifications | ❌ | ✅ |
| Utilisateurs, carrousel, configuration | ❌ | ✅ |

---

## 8. Référencement (SEO) et crédibilité médicale

- **Adresses lisibles** : `/article/…`, `/maladies/…`, `/dossiers/…`, `/le-medecin-repond/…`, etc.
- **Plan du site** `/sitemap.xml` : articles, catégories et toutes les pages des services (quiz, fiches, dossiers, réponses, outils, pages auteur). Il est recalculé au plus tard toutes les heures.
- **robots.txt** dynamique (administration, connexion et recherche exclues).
- **Balises** : titre, description, URL canonique, Open Graph et Twitter sur chaque page. Les pages de recherche et les pages 2 et suivantes sont en « noindex ».
- **Données structurées (schema.org)** :

| Page | Type de données |
|---|---|
| Article | Article, avec auteur, relecteur et sources |
| Fiche maladie | MedicalWebPage / MedicalCondition |
| Réponse du médecin | QAPage |
| Quiz | Quiz |
| Dossier | ItemList |
| Outil | WebApplication |
| Page auteur | ProfilePage |
| Partout | Fil d'Ariane |

- **Crédibilité (E-E-A-T)** : Google valorise les contenus santé dont on sait qui les a écrits et vérifiés. Pour en profiter :
  1. remplir le **profil public** de chaque rédacteur et de chaque médecin (photo, fonction, biographie), puis cocher « Afficher ce profil sur le site » ;
  2. renseigner **« Relu par »** et les **sources** sur chaque article.

  Sans profil public, l'article est attribué à la rédaction du site.

---

## 9. Revenus : publicités et AdSense

- **Google AdSense** :
  - le script et la balise de vérification sont intégrés en dur dans le `<head>` ;
  - le fichier `public/ads.txt` est présent ;
  - les blocs In-feed et In-article sont conformes aux codes fournis par Google.
- **Emplacements** :

| Emplacement | Où il apparaît |
|---|---|
| `leaderboard` | Accueil, sous « À la une » |
| `rectangle` | Barre latérale de toutes les pages |
| `infeed` | Dans les listes (articles, réponses du médecin) |
| `inarticle` | Après le 3e paragraphe d'un article |

- Une publicité vendue en direct (*Admin > Publicités*) remplace AdSense sur son emplacement pendant sa période. Les clics sont comptés via `/pub/{id}`.
- Les services santé augmentent le temps passé sur le site et le nombre de pages vues (quiz, parcours de dossier, fiches liées), ce qui fait progresser les revenus publicitaires.

---

## 10. Application installable (PWA) et notifications

### Application installable
- Manifeste `/manifest.webmanifest`, généré à partir de la configuration.
- Le **service worker** `public/sw.js` :
  - pages : réseau d'abord, copie locale si pas de connexion, sinon page « hors connexion » ;
  - fichiers CSS, JS, images et polices : servis depuis le cache puis mis à jour en arrière-plan ;
  - jamais en cache : administration, connexion, formulaires, publicités, `/push/…` ;
  - **changer `VERSION`** dans `sw.js` force la mise à jour chez tous les visiteurs (actuellement `ps-v2`).
- Une bannière « Installer PhyloSanitas » est proposée au visiteur.

### Notifications (Web Push) — *masquées pour le moment (voir §2)*
- **Sans bibliothèque externe** (`app/Services/WebPush.php`) :
  1. le serveur envoie une notification **vide**, signée avec la norme VAPID ;
  2. le service worker se réveille et lit la dernière notification sur `/push/derniere` ;
  3. il l'affiche ; un clic ouvre la bonne page.
- Ce choix évite d'ajouter une dépendance Composer qui exigerait une version récente de PHP sur le serveur.
- **Clés VAPID** : créées automatiquement au premier besoin et enregistrées dans les paramètres (`vapid_public`, `vapid_private`). **Ne pas les supprimer** : tous les abonnements seraient perdus.
- **Envoi automatique** à la première publication d'un article (sauf les sondages), une seule fois par article. Il se désactive dans *Admin > Notifications*.
- Les appareils désabonnés ou introuvables sont supprimés automatiquement.
- Compatibilité :
  - Android (Chrome, Edge, Firefox…) et ordinateurs : oui ;
  - iPhone : seulement si le site est **installé sur l'écran d'accueil** (iOS 16.4 et plus).
- Les notifications exigent le **HTTPS** en production.

---

## 11. Sécurité et protection anti-robots

- Les formulaires publics (commentaires, contact, newsletter, sondages, questions au médecin) sont protégés par :
  - un **champ piège** invisible (composant `<x-honeypot />`) ;
  - un **délai minimum** de 3 secondes entre l'affichage et l'envoi ;
  - le refus des messages bourrés de liens ;
  - une **limite d'envois** (5 par minute et 30 par heure par adresse IP).
- La connexion est limitée à 5 tentatives par minute.
- Les avis, le score des quiz et les abonnements aux notifications ont leur propre limite d'envois.
- Les réponses du médecin et les biographies sont affichées en texte échappé (pas de HTML injecté).
- Les emails des lecteurs ne sont jamais publiés.

---

## 12. Technique

- **Framework** : Laravel 9, PHP 8, MySQL.
- **Paquets principaux** :
  - spatie/laravel-medialibrary : images et conversions WebP ;
  - spatie/laravel-permission : rôles ;
  - cviebrock/eloquent-sluggable : adresses lisibles ;
  - eloquent-viewable : compteur de vues ;
  - sweet-alert : messages de l'administration ;
  - stevebauman/location : pays des lecteurs.
- **Front** : Bootstrap, Bootstrap Icons, Swiper, polices Source Serif 4 et Source Sans 3.

### Organisation des fichiers

| Élément | Emplacement |
|---|---|
| Gabarit public | `resources/views/site/layout.blade.php` |
| Gabarit d'administration | `resources/views/admin/layout.blade.php` |
| Styles du site | `public/assets_site/css/site.css`, `site-motion.css`, `site-services.css` (services santé) |
| Scripts du site | `public/assets_site/js/site.js`, `site-services.js` (menu Services, bandeau, avis, notifications, filtre A-Z, quiz de l'accueil) |
| Administration | `public/assets_admin/css/admin-ui.css`, `public/assets_admin/js/admin-ui.js` |
| Contenus fixes des services | `config/sante.php` (liste des services, outils, seuils IMC, calendrier vaccinal) |
| Envoi des notifications | `app/Services/WebPush.php` |
| Import des pharmacies de garde | `app/Services/GardesImport.php` (lecture des fichiers, contrôles, publication), `app/Services/GoogleDrive.php` (accès lecture seule : compte de service via `.env`, ou OAuth), réglages `drive` de `config/sante.php` |

### Contrôleurs des nouveaux modules

| Contrôleur | Rôle |
|---|---|
| `QuestionLecteurController` | Le médecin répond |
| `QuizController` | Quiz |
| `OutilController` | Outils santé |
| `MaladieController` | Maladies de A à Z |
| `DossierController` | Dossiers |
| `JourneeSanteController` | Calendrier et bandeau |
| `EtablissementController` | Annuaire |
| `EngagementController` | Avis sur les articles et notifications |
| `AuteurController` | Pages et profils auteur |

### Tables ajoutées le 3 novembre 2026

| Table | Contenu |
|---|---|
| `quizzes`, `quiz_questions` | Quiz et affirmations |
| `maladies` | Fiches maladies |
| `journees_sante` | Calendrier santé |
| `dossiers`, `dossier_post` | Dossiers et leurs articles |
| `questions_lecteurs` | Le médecin répond |
| `avis_articles` | « Cet article vous a-t-il aidé ? » |
| `etablissements` | Annuaire santé |
| `push_abonnes`, `push_messages` | Notifications |

Colonnes ajoutées :
- `users` : `slug`, `titre_pro`, `bio`, `photo`, `est_medecin`, `profil_public` ;
- `posts` : `reviewer_id`, `sources`.

### Cache
Les catégories, les listes d'articles, le détail d'un article (5 minutes), les paramètres, le popup, le bandeau des journées santé (1 heure), le quiz de l'accueil (10 minutes) et le plan du site (1 heure) sont en cache. Les modifications vident automatiquement le cache concerné.

### Point d'attention
Les variables `$category`, `$post_last`, `$post_popular`, `$sondage_front`, `$actualite_externe` et `$site` sont injectées dans toutes les vues `site.*` et `admin.*`. Ne pas réutiliser ces noms dans les contrôleurs ni dans les composants : ils seraient écrasés.

---

## 13. Mise en production

```bash
git pull
composer install --no-dev --optimize-autoloader   # aucune nouvelle dépendance n'a été ajoutée
php artisan migrate --force                       # crée les tables des services santé
php artisan optimize:clear && php artisan optimize
```

Les migrations du 3 novembre 2026 **n'ajoutent que des tables et des colonnes facultatives**. Elles installent aussi des **contenus par défaut**, pour que les pages soient remplies dès la mise en ligne :

| Service | Contenu fourni | Où le modifier |
|---|---|---|
| Outils santé | IMC, besoin en eau, cycle menstruel (grossesse et vaccins masqués) | `config/sante.php` |
| Calendrier santé | 13 journées et campagnes (cancer, tuberculose, paludisme, tabac, don de sang, hépatite, cœur, Octobre rose, santé mentale, Novembre bleu, diabète, sida…) | *Admin > Journées santé* |
| Maladies de A à Z (module masqué) | 13 fiches prêtes : AVC, asthme, choléra, dengue, diabète, drépanocytose, fièvre typhoïde, hépatite B, hypertension, obésité, paludisme, tuberculose, VIH / sida | *Admin > Maladies de A à Z* |
| Annuaire santé (page publique masquée) | 3 numéros d'urgence ; CHU de Cocody, Treichville, Yopougon, Angré et Bouaké, Hôpital Mère-Enfant de Bingerville, Institut de Cardiologie d'Abidjan, Institut Pasteur, CNTS, INHP | *Admin > Annuaire santé* |
| Quiz | « Idées reçues sur la santé » (8 affirmations), en ligne | *Admin > Quiz* |

Ces contenus ne sont jamais ajoutés en double, même si la migration est relancée.

Vérifications après la mise en ligne :
1. Le site est en HTTPS (indispensable pour l'application installable et, plus tard, pour les notifications).
2. Remplir le profil public des rédacteurs et des médecins.
3. Relier **Google Drive** : clé JSON du compte de service dans `storage/app/private/`, `GOOGLE_DRIVE_FICHIER_IDENTIFIANTS` et `GOOGLE_DRIVE_DOSSIER_ID` dans le `.env`, dossier partagé avec le compte de service (§4.0). Les hôpitaux de l'annuaire seront à compléter le jour où l'annuaire est réactivé.
4. Soumettre `/sitemap.xml` dans Google Search Console.
5. Le jour où les notifications sont réactivées : vérifier que *Admin > Notifications* n'affiche pas d'erreur de clés. Sinon, l'extension PHP OpenSSL du serveur ne sait pas créer de clés EC.

---

## 14. Contenus à valider par l'équipe médicale

⚠️ Ces contenus ont été pré-rédigés et sont **en ligne par défaut**. Ils **doivent être relus par un médecin** ; tout contenu peut être corrigé ou repassé en brouillon depuis l'administration.

- [ ] **Quiz « Idées reçues sur la santé »** (8 affirmations), dans *Admin > Quiz*.
- [ ] **Les 13 fiches maladies**, dans *Admin > Maladies de A à Z*. Indiquer le médecin relecteur sur chacune.
- [ ] **Hôpitaux et centres de l'annuaire** (quand l'annuaire sera réactivé) : vérifier les quartiers et ajouter les téléphones (*Admin > Annuaire santé*).
- [ ] **Calendrier vaccinal de l'enfant** (`config/sante.php`, clé `vaccins`). Il est indicatif, basé sur le PEV de Côte d'Ivoire ; à corriger si le programme national a évolué (vaccin antipaludique, hépatite B à la naissance, âge du 2e rougeole-rubéole…).
- [ ] **Numéros d'urgence** (quand l'annuaire sera réactivé) : SAMU 185, sapeurs-pompiers 180, police secours 170, dans *Admin > Annuaire santé*.
- [ ] **Textes des outils** (IMC, grossesse, hydratation) : repères généraux, avec un avertissement affiché sur chaque page.
- [ ] **Dates des journées santé** (*Admin > Journées santé*).

---

## 15. Tâches régulières

| Fréquence | Tâche |
|---|---|
| Chaque semaine (idéalement le lundi matin) | Vérifier que l'import des **pharmacies de garde** a réussi (*Admin > Pharmacies de garde*, compte rendu du dernier import), sinon les saisir avec le bloc « Pharmacies de garde ». C'est la page la plus consultée : une liste en retard fait perdre des visiteurs. Vérifier ensuite la page `/pharmacies-de-garde` |
| Plusieurs fois par semaine | Traiter les **questions des lecteurs** (badge rouge dans le menu) |
| Chaque mois | Ajouter une **fiche maladie** et un **quiz** ; créer un **dossier** quand un thème compte 3 articles ou plus |
| Avant chaque campagne | Vérifier la journée dans le calendrier ; ajouter un popup ou une info flash si besoin |
| À chaque article | Renseigner l'auteur, « Relu par » et les sources |
| Chaque trimestre | Relire les fiches maladies (la date de mise à jour s'affiche) et les numéros de l'annuaire |

---

## 16. Évolutions prévues

- **Espace étudiants / Thèses** (reporté) : résumés de thèses dans la catégorie « Thèses » et formulaire pour que les étudiants proposent la leur, avec validation dans l'administration.
- Pistes possibles :
  - envoi automatique de la newsletter ;
  - notification par email au lecteur quand sa question reçoit une réponse ;
  - carte interactive de l'annuaire ;
  - quiz par catégorie.
