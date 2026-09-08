<?php
declare(strict_types=1);

$title = "Stations departement";

include("./include/header.inc.php");
include("./include/function.inc.php");


$region = verifGET("region") ?? "";
$departement = verifGET("departement") ?? "";

$region = str_replace(' ', '', $region ?? '');
$departement = str_replace(' ', '', $departement ?? '');

$bonneLoc = $region !== '' && $departement !== '' && checkDepartementRegion($departement, $region);


/* FILTRES */

$allowedCarburants = ["gazole", "sp95", "sp98", "e10", "e85", "gplc"];

$carburants = ["gazole", "sp95", "sp98", "e10", "e85", "gplc"];


if (!empty($_GET["carburants"]) && is_array($_GET["carburants"])) {
    $carburants = array_values(array_intersect($_GET["carburants"], $allowedCarburants));
}
if(empty($carburants)){
    $carburants = ["gazole", "sp95", "sp98", "e10", "e85", "gplc"];
}

$showHoraires = isset($_GET["horaires"]) && $_GET["horaires"] === "on";
$showServices = isset($_GET["services"]) && $_GET["services"] === "on" ;

/* PAGINATION */
$page = (isset($_GET['page']) && (int)$_GET['page'] > 0) ? (int)$_GET['page'] : 1;
$limit = 100;
$offset = ($page - 1) * $limit;

/* API STATIONS */
if($bonneLoc){

    $data = getStationsByDepartment( $region, $departement, $carburants, $showServices, $offset);
    $stations = $data['stations'] ?? [];
    $totalStations = $data['total'] ?? 0;
    $maxPage = (int) ceil($totalStations / $limit);
    $nomdep = getDepartmentNameByCode($departement) ?? "";
    enregistrerDepartement($nomdep, $region);
    setcookie("last_dep", $departement, time() + 60 * 60 * 24 * 30, "/");
    setcookie("last_region", $region, time() + 60 * 60 * 24 * 30, "/");
    
}


?>
        <main>
            <div class="box-title">
                <h1>Stations-service dans le département</h1>
            </div>
            <aside class="filters-panel">
                <h2 class="filters-title">Filtres</h2>
                <form method="GET" class="filters-form">
                     <input type="hidden" name="region" value="<?= htmlspecialchars((string)$region) ?>" />
                     <input type="hidden" name="departement" value="<?= htmlspecialchars((string)$departement) ?>" />
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
                        <div class="form-group-inline1">
                            <label><strong>Voir les services</strong>
                                <input type="checkbox" name="services" <?= $showServices ? 'checked="checked"' : '' ?> />
                            </label>
                            <label><strong>Afficher les horaires</strong>
                                <input type="checkbox" name="horaires" <?= $showHoraires ? 'checked="checked"' : '' ?> />
                            </label>
                        </div> 
                    </div>
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </form>
            </aside>

            <!-- STATIONS -->
            <section class="stations-section">
                <?php if($bonneLoc): ?>
                <h2>Stations trouvées dans le département <?= htmlspecialchars($nomdep."(".$departement.")") ?> (région <?= htmlspecialchars($region) ?>)</h2>
                <?php if (empty($stations)): ?>
                <h3 class="noville">"Aucune station trouvée" </h3>
                <?php else: ?>
                    <h3 class="yesville"> <?= $totalStations ?>  station(s) trouvée(s)</h3>
                    <div class="stations-container">
                        <?php foreach ($stations as $s): ?>
                            <div class="station-card">
                                <?php
                                   $adresse = ($s["adresse"] ?? "").", ".($s["ville"] ?? "");
                                   $urlMaps = "https://www.google.com/maps/search/?api=1&query=" . urlencode($adresse);
?>
                                <a href="<?= htmlspecialchars($urlMaps) ?>" target="_blank" class="station-link"><h3><?= htmlspecialchars($s['adresse'] . " - " . $s['ville']) ?></h3> </a>
                                <!-- PRIX -->
                                <?php  $prix = formatStation($s, $carburants); 
                                if (!empty($prix)): ?>                                  
                                    <h4> Les prix des carburants</h4>
                                    <div class="prix-box">
                                        <?php foreach ($prix as $k => $v): ?>
                                            <span class="prix-item">
                                                <strong><?= strtoupper(htmlspecialchars($k)) ?></strong> : <?= htmlspecialchars((string)$v) ?> €
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

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
                                                <td><?= $j['ferme'] ?></td>
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
                            'region' => $region,
                            'departement' => $departement,
                            'page' => $i,
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
                    <h2 class='station-card noville '>Impossible d’afficher les stations : localisation incomplète ou incorrecte.</h2>
                <?php endif; ?>
            </section>
        </main>

<?php include("./include/footer.inc.php"); ?>