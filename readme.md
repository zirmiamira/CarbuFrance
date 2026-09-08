# CarbuFrance — Présentation détaillée du projet

## Présentation générale

**CarbuFrance** est une application web développée dans le cadre de l’UE **Développement Web**. Le projet a pour objectif de faciliter la recherche et la comparaison des prix des carburants dans les stations-service de **France métropolitaine**.

L’idée principale est de proposer à l’utilisateur une interface simple permettant de trouver rapidement les stations-service proches de lui et de comparer leurs prix selon le type de carburant recherché.

L’application récupère notamment les informations relatives aux prix des carburants grâce à l’**API officielle des prix des carburants du gouvernement français**. Ces données sont ensuite traitées et affichées sous une forme compréhensible par l’utilisateur.

Le projet a été réalisé par :

- **Amira ZIRMI**
- **Maria MOKRANE**

## Accès au site

- URL du site :  
  https://zirmi.alwaysdata.net/
  https://mokrane2.alwaysdata.net/

# Objectifs du projet

Le projet répond à plusieurs objectifs:

- Permettre à un utilisateur de **trouver facilement une station-service et de comparer les prix des carburants**.

L'application permet également :

- de rechercher une station par **région** ;
- de rechercher par **département** ;
- de rechercher par **ville** ;
- de consulter les prix des différents carburants ;
- de rechercher les stations **autour de soi** ;
- d'appliquer différents filtres ;
- de consulter des statistiques concernant l'utilisation du site ;
- d'utiliser le site en **mode clair ou sombre**.

L'objectif est donc de centraliser plusieurs informations dans une seule application afin d'éviter à l'utilisateur de consulter plusieurs sources différentes.

## Technologies utilisées

- HTML5
- CSS3
- PHP 8
- Javascript

## APIs utilisées

- API des prix des carburants (gouvernement français)
- API de géolocalisation par IP
- API Ghibli (page technique)

## Fonctionnalités principales

- Recherche de stations par région, département et ville
- Affichage des prix des carburants
- Géolocalisation ("autour de moi")
- Filtres (carburants, services, horaires, rayon)
- Statistiques de consultation
- Mode clair / sombre

## Structure du projet

- `index.php` → Accueil
- `search.php` → Recherche
- `ville_prixcarburants.php` → Prix par ville
- `stations_departement.php` → Stations par département
- `stations_autour_de_moi.php` → Géolocalisation
- `statistics.php` → Statistiques
- `include/` → Fichiers communs (header, footer, fonctions)
- `data/` → Fichiers CSV

## Lancement du projet

1. Placer le projet dans un serveur local (ex : XAMPP, WAMP)
2. Démarrer Apache
3. Accéder à :
   http://localhost/CarbuFrance

## Données

- Fichiers CSV (régions, départements, villes)
- Fichiers de statistiques (visites)
- API externe pour les prix des carburants
