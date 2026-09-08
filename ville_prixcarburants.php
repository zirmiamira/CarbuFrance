<?php
declare(strict_types=1);

$title = "Prix carburants ville";

include("./include/header.inc.php");
include("./include/function.inc.php");

$region = verifGET("region");
$departement = verifGET("departement");
$ville = verifGET("ville");

$region = str_replace(' ', '', $region ?? '');
$departement = str_replace(' ', '', $departement ?? '');
$ville = trim($ville ?? '');

$bonneLoc = $region !== '' && $departement !== '' && $ville !== '';
if ($bonneLoc) {
    $bonneLoc = checkDepartementRegion($departement, $region) && checkVilleDepartement($ville, $departement);
}

$allowedCarburants = ["gazole", "sp95", "sp98", "e10", "e85", "gplc"];
$carburants = $allowedCarburants;

if (isset($_GET["carburants"]) && !empty($_GET["carburants"]) && is_array($_GET["carburants"])) {
    $carburants = array_values(array_intersect($_GET["carburants"], $allowedCarburants));
}

if (empty($carburants)) {
    $carburants = $allowedCarburants;
}

$datePrix = isset($_GET["date_prix"]) && $_GET["date_prix"] === "1";
$dispo = isset($_GET["dispo"]) && $_GET["dispo"] === "1";
$services = isset($_GET["services"]) && $_GET["services"] === "1";

$rayon = 5;

if (isset($_GET["rayon"]) && !empty($_GET["rayon"]) && is_numeric($_GET["rayon"])) {
    $rayon = (float)$_GET["rayon"];

    if ($rayon < 1) {
        $rayon = 1;
    }

    if ($rayon > 10) {
        $rayon = 10;
    }
}
if($bonneLoc){
    $result = getStationsByVille($region, $departement, $ville);
    $nbr = $result["total"];
    $stations = $result["stations"];
    $nomdep = getDepartmentNameByCode($departement) ?? "";

    enregistrerVille($ville, $nomdep, $region);
    enregistrerDepartement($nomdep, $region);

    setcookie("last_city", $ville, time() + 60 * 60 * 24 * 30, "/");
    setcookie("last_dep", $departement, time() + 60 * 60 * 24 * 30, "/");
    setcookie("last_region", $region, time() + 60 * 60 * 24 * 30, "/");
}
?>
         <main id="prixville-main">
            <aside>  
                <nav>
                    <h2>Voir les prix</h2>
                    <ul>
                        <li><a href="#enville">En ville</a></li>
                        <li><a href="#acote">À proximité</a></li>
                    </ul>
                </nav>
            </aside>
            <div class="box-title">
                <h1>
                    Prix des carburants en ville et autour
                </h1>
            </div>
            

            <section id="enville">

            <?php
            if ($bonneLoc) {

                echo "\t<h2>À " . htmlspecialchars((string)$ville) . " (" . htmlspecialchars((string)$departement) . ", " . htmlspecialchars((string)$region) . ")</h2>\n";

                if ($nbr === 0 || empty($stations)) {
                    echo "\t<h3 class=\"noville station-card\">Aucune station trouvée dans cette ville !</h3>\n";
                } else {

                    echo "\t<h3 class=\"yesville\">" . $nbr . " station(s) trouvée(s)</h3>\n";

                    $stations = orgStationsVille($stations);

                    echo "\t<div class='stations-container'>\n";

                    foreach ($stations as $station) {
                         $adresse = ($station["adresse"] ?? "").",".($station["ville"] ?? "");
                        $urlMaps = "https://www.google.com/maps/search/?api=1&query=" . urlencode($adresse);

                        echo "\t\t<ul class='station-card'>\n";

                        // Adresse
                        echo "\t\t\t<li><a href='" . htmlspecialchars($urlMaps) . "' target='_blank' class='station-link'><h3>" . htmlspecialchars($station["adresse"]) . "</h3></a></li>\n";

                        echo "\t\t\t<li><h4>Prix des carburants</h4></li>\n";

                        $arr = $station["prix"];
                        $arr1 = array_keys($arr);
                        $arr1 = is_array($arr1) ? $arr1 : [];
                        $carburants = is_array($carburants) ? $carburants : [];
                        $carbs = array_intersect($carburants, $arr1);
                        echo "\t\t\t<li>\n";
                        if(!empty( $carbs)){
                            
                            echo "\t\t\t\t<table class='station-table'>\n";
                            echo "\t\t\t\t\t<caption>Carburants Prix</caption>\n";
                            // HEADER
                            echo "\t\t\t\t\t<thead><tr>";
                            echo "<th>Carburant</th>";
                            echo "<th>Prix</th>";

                            if ($datePrix) {
                                echo "<th>Date Heure</th>";
                            }
                            

                            echo "\t\t\t\t\t</tr></thead>\n";
                            echo "\t\t\t\t\t<tbody>\n";

                            // LIGNES
                            foreach ($carbs as $carb) {

                                echo "\t\t\t\t\t\t<tr>\n";

                                // carburant
                                echo "\t\t\t\t\t\t\t<td><strong>" . strtoupper($carb) . "</strong></td>\n";

                                // prix
                                $prix = $station["prix"][$carb] ?? "";
                                echo "\t\t\t\t\t\t\t<td>" . ($prix !== "" ? htmlspecialchars((string)$prix) . " €" : "-") . "</td>\n";

                                // date
                                if ($datePrix) {

                                    $date = $station["date_prix"][$carb] ?? "";

                                    if ($date !== "" && $date !== null) {
                                        $parts = explode("T", $date);
                                        $datePart = $parts[0];
                                        $timePart = substr($parts[1], 0, 5);

                                        $d = explode("-", $datePart);
                                        $cleanDate = $d[2] . "/" . $d[1] . "/" . $d[0] . " " . $timePart;
                                    } else {
                                        $cleanDate = "-";
                                    }

                                    echo "\t\t\t\t\t\t\t<td>" . htmlspecialchars($cleanDate) . "</td>\n";
                                }

                                echo "\t\t\t\t\t\t</tr>\n";
                            }

                            echo "\t\t\t\t\t</tbody>\n";
                            echo "\t\t\t\t</table>\n";
                            
                        }else{
                           echo "Aucune information liée au carburant(s)";
                        }
                       echo "\t\t\t</li>\n";

                        // SERVICES
                        if ($services) {

                            if (!empty($station["services"])) {

                                echo "\t\t\t<li><h4>Services disponibles</h4></li>\n";
                                echo "\t\t\t<li>\n";
                                echo "\t\t\t\t<ul class='services-list'>\n";

                                foreach ($station["services"] as $s) {
                                    echo "\t\t\t\t\t<li>" . htmlspecialchars((string)$s) . "</li>\n";
                                }

                                echo "\t\t\t\t</ul>\n";
                                echo "\t\t\t</li>\n";

                            } else {
                                echo "\t\t\t<li><h4>Aucun services disponibles</h4></li>\n";
                            }
                        }
                        echo "\t\t</ul>\n";
                    }

                    echo "\t</div>\n";
                }

            } else {
                echo "\t<h2 class='noville station-card'>Impossible d’afficher les stations : localisation incomplète ou incorrecte</h2>\n";
            }
            ?>

            </section>

            <?php
            if ($bonneLoc) {

                echo "<section id=\"acote\">\n";

                echo "\t<h2>À proximité (" . $rayon . " km) autour de " . htmlspecialchars((string)$ville) . " (" . htmlspecialchars((string)$departement) . ", " . htmlspecialchars((string)$region) . ")</h2>\n";

                $centre = getCentreVille($ville);

                if (empty($centre) || empty(getStationsProches($ville, $centre["lat"], $centre["lng"],$carburants, $services, $rayon))) {

                    echo "\t<h3 class=\"noville\">Aucune station trouvée proche de cette ville !</h3>\n";

                } else {

                    $proche = getStationsProches($ville, $centre["lat"], $centre["lng"], $carburants, $services ,$rayon);
                    $nbrproche = $proche["total"];

                    echo "\t<h3 class=\"yesville\">" . $nbrproche . " station(s) trouvée(s), organisées de la plus proche à la plus éloignée</h3>\n";

                    if (!empty($proche["stations"])) {

                        $stationspro = orgStationsVille($proche["stations"]);

                        echo "\t<div class='stations-container'>\n";

                        foreach ($stationspro as $station) {
                             $adresse = ($station["adresse"] ?? "").",".($station["ville"] ?? "");
                            $urlMaps = "https://www.google.com/maps/search/?api=1&query=" . urlencode($adresse);
                            echo "\t\t<ul class='station-card villeproche'>\n";

                            // adresse
                            echo "\t\t\t<li><a href='" . htmlspecialchars($urlMaps) . "' target='_blank' class='station-link'><h3 class=\"h3proche\">" . htmlspecialchars($station["adresse"]) . "</h3></a></li>\n";

                            echo "\t\t\t<li><h4>Prix des carburants</h4></li>\n";


                            $arr = $station["prix"];
                            $arr1 = array_keys($arr);
                            $arr1 = is_array($arr1) ? $arr1 : [];
                            $carburants = is_array($carburants) ? $carburants : [];
                            $carbs=array_intersect($carburants, $arr1);
                            echo "\t\t\t<li>\n";
                            if(!empty( $carbs)){
                            
                            echo "\t\t\t\t<table class='station-table'>\n";
                            echo "\t\t\t\t\t<caption>Carburants Prix</caption>\n";
                            // HEADER
                            echo "\t\t\t\t\t<thead><tr>";
                            echo "<th>Carburant</th>";
                            echo "<th>Prix</th>";

                            if ($datePrix) {
                                echo "<th>Date Heure</th>";
                            }
                            

                            echo "\t\t\t\t\t</tr></thead>\n";
                            echo "\t\t\t\t\t<tbody>\n";

                            // LIGNES
                            foreach ($carbs as $carb) {

                                echo "\t\t\t\t\t\t<tr>\n";

                                // carburant
                                echo "\t\t\t\t\t\t\t<td><strong>" . strtoupper($carb) . "</strong></td>\n";

                                // prix
                                $prix = $station["prix"][$carb] ?? "";
                                echo "\t\t\t\t\t\t\t<td>" . ($prix !== "" ? htmlspecialchars((string)$prix) . " €" : "-") . "</td>\n";

                                // date
                                if ($datePrix) {

                                    $date = $station["date_prix"][$carb] ?? "";

                                    if ($date !== "" && $date !== null) {
                                        $parts = explode("T", $date);
                                        $datePart = $parts[0];
                                        $timePart = substr($parts[1], 0, 5);

                                        $d = explode("-", $datePart);
                                        $cleanDate = $d[2] . "/" . $d[1] . "/" . $d[0] . " " . $timePart;
                                    } else {
                                        $cleanDate = "-";
                                    }

                                    echo "\t\t\t\t\t\t\t<td>" . htmlspecialchars($cleanDate) . "</td>\n";
                                }

                
                                echo "\t\t\t\t\t\t</tr>\n";
                            }

                            echo "\t\t\t\t\t</tbody>\n";
                            echo "\t\t\t\t</table>\n";
                            
                            }else{
                            echo "Aucune information liée au carburant(s)";
                            }
                            echo "\t\t\t</li>\n";


                            if ($services) {

                                if (!empty($station["services"])) {

                                    echo "\t\t\t<li><h4>Services disponibles</h4></li>\n";
                                    echo "\t\t\t<li>\n";
                                    echo "\t\t\t\t<ul class='services-list'>\n";

                                    foreach ($station["services"] as $s) {
                                        echo "\t\t\t\t\t<li>" . htmlspecialchars((string)$s) . "</li>\n";
                                    }

                                    echo "\t\t\t\t</ul>\n";
                                    echo "\t\t\t</li>\n";

                                } else {
                                    echo "\t\t\t<li><h4>Aucun services disponibles</h4></li>\n";
                                }
                            }

                            echo "\t\t</ul>\n";
                        }

                        echo "\t</div>\n";
                    }
                }

                echo "</section>\n";
            }
?>
         </main>

<?php include("include/footer.inc.php"); ?>