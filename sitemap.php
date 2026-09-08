<?php
declare(strict_types=1);
$title = "Plan du site – CarbuFrance";
include("./include/header.inc.php");
?>

        <main>
            <div class="box-title">
                <h1>Plan du site</h1>
            </div>

            <section>
                <h2>Pages du site</h2>
                <div class="stations-container">
                    <article class="card">
                        <h3>Accueil — <code>index.php</code></h3>
                        <ul>
                            <li>Bannière principale avec lien vers la recherche</li>
                            <li>Présentation du site</li>
                            <li>Les carburants disponibles</li>
                            <li>Les fonctionnalités</li>
                        </ul>
                        <a href="index.php" class="btn">Consulter</a>
                    </article>

                    <article class="card">
                        <h3>Recherche — <code>search.php</code></h3>
                        <ul>
                            <li>Carte interactive de France pour sélectionner une région</li>
                            <li>Choix du département</li>
                            <li>Choix de la ville et des carburants souhaités</li>
                        </ul>
                        <a href="search.php" class="btn">Consulter</a>
                    </article>

                    <article class="card">
                        <h3>Prix par ville — <code>ville_prixcarburants.php</code></h3>
                        <ul>
                            <li>Liste des stations dans la ville sélectionnée</li>
                            <li>Prix des carburants par station</li>
                            <li>Stations à proximité dans un rayon donné</li>
                        </ul>
                    </article>

                    <article class="card">
                        <h3>Stations par département — <code>stations_departement.php</code></h3>
                        <ul>
                            <li>Liste de toutes les stations du département</li>
                            <li>Prix, horaires et services filtrables</li>
                            <li>Pagination (100 stations par page)</li>
                        </ul>
                    </article>

                    <article class="card">
                        <h3>Autour de moi — <code>stations_autour_de_moi.php</code></h3>
                        <ul>
                            <li>Détection automatique de la position via l'adresse IP</li>
                            <li>Stations triées de la plus proche à la plus éloignée</li>
                            <li>Rayon ajustable, filtres carburants, horaires et services</li>
                        </ul>
                        <a href="stations_autour_de_moi.php" class="btn">Consulter</a>
                    </article>

                    <article class="card">
                        <h3>Statistiques — <code>statistics.php</code></h3>
                        <ul>
                            <li>Compteur global de visites</li>
                            <li>Graphique des 10 villes les plus consultées</li>
                            <li>Classement des 100 villes et départements les plus recherchés</li>
                        </ul>
                        <a href="statistics.php" class="btn">Consulter</a>
                    </article>

                    <article class="card">
                        <h3>Page technique — <code>tech.php</code></h3>
                        <ul>
                            <li>Film Studio Ghibli tiré aléatoirement depuis une API externe</li>
                            <li>Géolocalisation de l'utilisateur via l'API ipinfo.io</li>
                        </ul>
                        <a href="tech.php" class="btn">Consulter</a>
                    </article>

                    <article class="card">
                        <h3>Plan du site — <code>sitemap.php</code></h3>
                        <ul>
                            <li>Cette page</li>
                        </ul>
                    </article>
                </div>
            </section>

            <section>
                <h2>Fichiers inclus</h2>
                <div class="stations-container">
                    <article class="card">
                        <h3>En-tête — <code>include/header.inc.php</code></h3>
                        <ul>
                            <li>Navigation principale</li>
                            <li>Gestion du thème clair / sombre</li>
                            <li>Compteur de visites par session</li>
                        </ul>
                    </article>

                    <article class="card">
                        <h3>Pied de page — <code>include/footer.inc.php</code></h3>
                        <ul>
                            <li>Auteurs et université</li>
                            <li>Bouton retour en haut de page</li>
                        </ul>
                    </article>

                    <article class="card">
                        <h3>Fonctions — <code>include/function.inc.php</code></h3>
                        <ul>
                            <li>Lecture des fichiers CSV de référence</li>
                            <li>Appels à l'API gouvernementale des carburants</li>
                            <li>Géolocalisation par IP</li>
                            <li>Validation des paramètres et enregistrement des statistiques</li>
                        </ul>
                    </article>

                    <article class="card">
                        <h3>Carte de France — <code>include/map.inc.php</code></h3>
                        <ul>
                            <li>Carte SVG des 13 régions métropolitaines cliquables</li>
                        </ul>
                    </article>
                </div>
            </section>

            <section>
                <h2>Données</h2>
                <div class="stations-container">
                    <article class="card">
                        <h3>Référentiels géographiques — <code>data/</code></h3>
                        <ul>
                            <li><code>regions.csv</code> — 13 régions métropolitaines</li>
                            <li><code>departments.csv</code> — 96 départements</li>
                            <li><code>cities.csv</code> — plus de 10 000 communes avec coordonnées GPS</li>
                        </ul>
                    </article>

                    <article class="card">
                        <h3>Statistiques de consultation</h3>
                        <ul>
                            <li><code>villesvisite.csv</code> — nombre de consultations par ville</li>
                            <li><code>departementvisite.csv</code> — nombre de consultations par département</li>
                            <li><code>counter.txt</code> — compteur global de visites</li>
                        </ul>
                    </article>
                </div>
            </section>

        </main>
<?php include("./include/footer.inc.php"); ?>