<?php
declare(strict_types=1);

$title = "Stations autour de moi";

include("./include/header.inc.php");
include("./include/function.inc.php");

/* GEOLOCALISATION  */

$ip = getUserIP();
$coords = getCoordsFromIP($ip);
$is_coords=false;
if (!empty($coords)) {
    $is_coords=true;
    $lat = $coords['lat'];
    $lng = $coords['lng'];
}

/* FILTRES */

$allowedCarburants = ["gazole", "sp95", "sp98", "e10", "e85", "gplc"];

$carburants = [];

if (!empty($_GET["carburants"]) && is_array($_GET["carburants"])) {
    $carburants = array_values(array_intersect($_GET["carburants"], $allowedCarburants));
}

if(empty($carburants)){
    $carburants = ["gazole", "sp95", "sp98", "e10", "e85", "gplc"];
}

$showHoraires = isset($_GET["horaires"]) && $_GET["horaires"] === "on";
$showServices = isset($_GET["services"]) && $_GET["services"] === "on" ;


$rayon = 20;

if (!empty($_GET["rayon"]) && is_numeric($_GET["rayon"])) {
    $rayon = max(1, min(40, (float)$_GET["rayon"]));
}

/* PAGINATION */
$page = (isset($_GET['page']) && (int)$_GET['page'] > 0) ? (int)$_GET['page'] : 1;
$limit = 100;
$offset = ($page - 1) * $limit;

/* API STATIONS */
if($is_coords){
    $data = getStationsAutourDeMoi($lat, $lng, $carburants, $showServices, $rayon,  $offset);
    $stations = $data['stations'] ?? [];
    $totalStations = $data['total'] ?? 0;
    $maxPage = (int) ceil($totalStations / $limit);
}


?>
        <main>
            <div class="box-title">
                <h1> Stations autour de vous ( à <?=$rayon ?>km )</h1>
            </div>
            <aside class="filters-panel">
                <h2 class="filters-title">Filtres</h2>
                <form method="GET" class="filters-form">
                    <input type="hidden" name="page" value="<?= $page ?>" />
                  <div class="filtre-autour">
                        
                        <div class="form-group-inline2">
                            <h3 class="yesville-autour">Carburants à afficher</h3>
                            <?php foreach ($allowedCarburants as $c): ?>
                                <label class="checkbox-item"> 
                                    <input type="checkbox" name="carburants[]" value="<?= $c ?>"
                                        <?= in_array($c, $carburants) ? 'checked="checked"' : '' ?> />
                                    <?= strtoupper($c) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>  
                    </div>
                    <div class="form-group-inline1">
                            <label for="rayon"><strong>Choisir un rayon (km)</strong>
                                <input id="rayon" type="number" name="rayon" value="<?= $rayon ?>" min="1" max="40" />
                            </label>
                            <label><strong>Voir les services</strong>
                                <input type="checkbox" name="services" <?= $showServices ? 'checked="checked"' : '' ?> />
                            </label>
                             <label><strong>Afficher les horaires</strong>
                                <input type="checkbox" name="horaires" <?= $showHoraires ? 'checked=checked' : '' ?> />
                            </label>
                        </div>
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </form>
            </aside>

            <!-- STATIONS -->
            <section class="stations-section">
                <?php if($is_coords): ?>
                <h2>
                    Votre position détectée : 
                        <?= htmlspecialchars($coords['city'] ?? 'Localisation inconnue') ?>
                    <?= !empty($coords['region_name']) 
                        ? ', ' . htmlspecialchars($coords['region_name']) 
                        : '' ?>
                </h2>
                <?php if (empty($stations)): ?>
                <h3 class="noville"> <?= "Aucune station trouvée autour de vous dans un rayon de ". $rayon." km" ?> </h3>
                <?php else: ?>
                    <h3 class="yesville"> <?= $totalStations ?>  station(s) trouvée(s), organisées de la plus proche à la plus éloignée</h3>
                    <div class="stations-container">
                        <?php foreach ($stations as $s): ?>
                                 <?php
                                   $adresse = ($s["adresse"] ?? "").", ".($s["ville"] ?? "");
                                   $urlMaps = "https://www.google.com/maps/search/?api=1&query=" . urlencode($adresse);
                                ?>
                             
                            <div class="station-card">



                                <a href="<?= htmlspecialchars($urlMaps) ?>" target="_blank" class="station-link"><h3><?= htmlspecialchars($s['adresse'] . " - " . $s['ville']) ?></h3> </a>
                                <!-- PRIX -->
                                     <?php $prix = formatStation($s, $carburants);     ?>                              
                                        <h4> Les prix des carburants</h4>
                                        <div class="prix-box">
                                            <?php foreach ($prix as $k => $v): ?>
                                                <span class="prix-item">
                                                    <strong><?= strtoupper(htmlspecialchars($k)) ?></strong> : <?= htmlspecialchars((string)$v) ?> €
                                                </span>
                                            <?php endforeach; ?>
                                        </div>

                                <!-- HORAIRES -->
                                <?php if ($showHoraires): 
                                    $horaires = parseHoraires($s);?>                              
                                    <h4> Les horaires de la station</h4>
                                    <p class="open24"> Automate-24-24: <?= $horaires['auto_24_24'] ?? "Non"?></p>
                                    <?php $jours=$horaires['jours'];
                                    if(!empty($jours)) :?>
                                    <table class="horaires-table">
                                        <caption>Les horaires par jour</caption>
                                        <tr>
                                            <th>Jour</th>
                                            <th>Ouverture</th>
                                            <th>Fermeture</th>
                                            <th>État</th>
                                        </tr>
                                        <?php foreach ($jours as $j): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($j['jour']) ?></td>
                                                <td><?= htmlspecialchars($j['ouverture'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($j['fermeture'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($j['ferme'] ?? '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                    <?php endif; ?>


                                <?php endif; ?>

                                <!-- SERVICES -->
                                <?php if ($showServices && !empty($s['services_service'])): ?>
                                    <div class="services-box">
                                        <h4>Services</h4>
                                        <ul class="services-list">
                                            <?php $services = $s['services_service'];?>
                                            <?php foreach ($services as $service): ?>
                                                <li><?= htmlspecialchars($service) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                            </div>

                        <?php endforeach; ?>
                     </div>
                    <!-- PAGINATION -->
                    <?php if ($maxPage > 1): ?>
                    <div class="pagination">

                    <?php for ($i = 1; $i <= $maxPage; $i++): ?>

                        <?php
                        $params = [
                            'page' => $i,
                            'rayon' => $rayon,
                        ];

                        foreach ($carburants as $c) {
                            $params['carburants'][] = $c;
                        }

                        if ($showHoraires) {
                            $params['horaires'] = 'on';
                        }

                        if ($showServices) {
                            $params['services'] = 'on';
                        }

                        $url = '?' . http_build_query($params);
                        ?>

                        <a href="<?= htmlspecialchars($url) ?>"
                        class="<?= $i === $page ? 'active-page' : '' ?>">
                            <?= $i ?>
                        </a>

                    <?php endfor; ?>

                    </div>
                    <?php endif; ?>

                <?php endif; ?>
                <?php else:?>
                    <h2 class='noville station-card'>Impossible d’afficher les stations : votre localisation n’a pas pu être détectée.</h2>
                <?php endif; ?>
            </section>
        </main>

<?php include("./include/footer.inc.php"); ?>