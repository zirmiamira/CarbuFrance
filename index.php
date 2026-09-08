<?php
declare(strict_types=1);
$title = "CarbuFrance – Prix des carburants en France";
include("./include/header.inc.php");
?>
        <!-- HERO BANNER -->
        <div class="hero">
            <a class="heroimg" href="search.php">
                <img src="./images/heroimg.png" alt="Rechercher par ville" />
            </a>
            <h1>France &#183; Carburants &#183; Prix</h1>
            <p class="hero-tagline">
                Trouvez les prix des carburants en temps réel dans toute la France métropolitaine.
                Comparez les stations près de chez vous et faites le plein au meilleur prix.
            </p>
            <div class="hero-stats">
                <span class="stat-item">13 régions</span>
                <span class="stat-item">96 départements</span>
                <span class="stat-item">+ 10 000 stations</span>
            </div>
        </div>
        <main id="main-hero">
            <!-- PRÉSENTATION -->
            <section class="presentation">
                <h2>À propos du site</h2>
                <p>
                    <strong>CarbuFrance</strong> est un site web développé dans le cadre du projet universitaire
                    de l'UE Développement Web (L2 Informatique – S4, 2025‑2026).
                    Il s'appuie sur les <strong>API ouvertes du gouvernement français</strong> pour vous fournir
                    les prix des carburants en France métropolitaine en temps réel.
                </p>
                <p>
                    Accédez facilement aux prix des carburants dans votre ville et découvrez les stations présentes dans chaque département. 
                    Que ce soit pour comparer ou simplement vous informer, vous obtenez des données fiables, rapidement et en toute simplicité.
                </p>
            </section>

            <!-- CARBURANTS DISPONIBLES -->
            <section class="carbu-band">
                <h2>Carburants disponibles</h2>
                <div class="carbu-tags">
                    <span class="carbu-tag">⛽ Gazole</span>
                    <span class="carbu-tag">🟢 SP95</span>
                    <span class="carbu-tag">🔵 SP98</span>
                    <span class="carbu-tag">🟡 E10</span>
                    <span class="carbu-tag">🌿 E85</span>
                    <span class="carbu-tag">♻️ GPLc</span>
                </div>
            </section>

            <!-- FONCTIONNALITÉS -->
            <section >
                <h2>Fonctionnalités</h2>
                <p>Accédez à toutes les fonctionnalités du site depuis les raccourcis ci-dessous.</p>
        
                <div class="features">
                    <a class="feature-card" href="search.php">
                        <div class="feature-icon">
                            <img src="./images/search.png" alt="Rechercher par ville" />
                        </div>
                        <h3>Stations et prix par département et ville</h3>
                        <p>Consultez les stations d’un département et découvrez les prix des carburants dans chaque ville.</p>
                    </a>
                    <a class="feature-card" href="stations_autour_de_moi.php">
                        <div class="feature-icon">
                            <img src="./images/autourdemoi.png" alt="Rechercher par ville" />
                        </div>
                        <h3>Autour de moi</h3>
                        <p>Géolocalisation automatique par adresse IP pour afficher les stations les plus proches.</p>
                    </a>
                    <a class="feature-card" href="statistics.php">
                        <div class="feature-icon">
                            <img src="./images/stats.png" alt="Rechercher par ville" />
                        </div>
                        <h3>Statistiques</h3>
                        <p>Visualisez les villes et départements les plus consultés sous forme de graphiques.</p>
                    </a>
                </div>
            </section>

        </main>

<?php include("./include/footer.inc.php"); ?>