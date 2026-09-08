<?php
declare(strict_types=1);

$title = "Partie 1: Page tech";

include("./include/header.inc.php");
include("./include/function.inc.php");

?>
        <main id="partie1">
            <div class="box-title">
                <h1> Prise en main des API Web : JSON et XML </h1>
            </div>
            <section>
                <h2>Film mystère du jour</h2>
                
                <?php 
                    $film=getUnFilm();
                    // Vérification que le tableau n'est pas vide
                    if (!empty($film)) {
                        $titre = $film['title'] ?? "Titre non disponible";
                        $titrejpn = $film['original_title'] ?? "Titre japonais non disponible";
                        $annee = $film['release_date'] ?? "Date de sortie non disponible";
                        $description = $film['description'] ?? "Description non disponible";
                        $image = $film['image'] ?? "";

                        echo "<div class='film-container'>\n";
                        // pour l'image
                        if (!empty($image)) {
                            echo "\t\t\t\t<figure>\n";
                            echo "\t\t\t\t\t<img src=\"" . htmlspecialchars($image) . "\" alt=\"" . htmlspecialchars($titre) . "\"/>\n";                            
                            echo "\t\t\t\t\t<figcaption>Image du film : " . htmlspecialchars($titre) . "</figcaption>\n";
                            echo "\t\t\t\t</figure>\n";
                        }

                        // Liste des infos
                        echo "\t\t\t\t\t<ul>\n";
                        echo "\t\t\t\t\t\t<li><strong>Titre :</strong> " . htmlspecialchars($titre) . "</li>\n";
                        echo "\t\t\t\t\t\t<li><strong>Titre japonais :</strong> <span lang='ja'>" . htmlspecialchars($titrejpn) . "</span></li>\n";
                        echo "\t\t\t\t\t\t<li><strong>Année de sortie :</strong> " . htmlspecialchars($annee) . "</li>\n";
                        echo "\t\t\t\t\t\t<li><strong>Description :</strong> " . htmlspecialchars($description) . "</li>\n";
                        echo "\t\t\t\t\t</ul>\n";

                        
                        echo "\t\t\t\t</div>\n";
                    }else{
                        echo "<p>Malheureusement, nous n'avons pas pu récupérer les informations du film pour le moment. Veuillez réessayer plus tard.</p>\n";
                    }
                ?>
            </section>
            <section >
                <h2>Votre localisation approximative (JSON)</h2>
                <?php 
                    $geo = getGeoloc();

                        echo "<ul class='geo-list'>\n";
                        echo "\t\t\t\t\t<li><strong>IP :</strong> " . htmlspecialchars($geo['ip']) . "</li>\n";
                        echo "\t\t\t\t\t<li><strong>Ville :</strong> " . htmlspecialchars($geo['ville']) . "</li>\n";
                        echo "\t\t\t\t\t<li><strong>Région :</strong> " . htmlspecialchars($geo['region']) . "</li>\n";
                        echo "\t\t\t\t\t<li><strong>Pays :</strong> " . htmlspecialchars($geo['pays']) . "</li>\n";
                        echo "\t\t\t\t</ul>";
                ?>

            </section>
            <section >
                <h2>Votre localisation approximative (XML)</h2>
                <?php 
                    $geo = getGeolocXML();

                        echo "<ul class='geo-list'>\n";
                        echo "\t\t\t\t\t<li><strong>Ville :</strong> " . htmlspecialchars($geo['ville']) . "</li>\n";
                        echo "\t\t\t\t\t<li><strong>Région :</strong> " . htmlspecialchars($geo['region']) . "</li>\n";
                        echo "\t\t\t\t\t<li><strong>Pays :</strong> " . htmlspecialchars($geo['pays']) . "</li>\n";
                        echo "\t\t\t\t</ul>";
                ?>

            </section>
        </main>
<?php include("include/footer.inc.php"); ?>