<?php
declare(strict_types= 1);
$title="Stations et carburants";

include("./include/header.inc.php");
include("./include/function.inc.php");

$lastCity = $_COOKIE['last_city'] ?? null;
$lastDep = $_COOKIE['last_dep'] ?? null;
$lastRegion = $_COOKIE['last_region'] ?? null;

$region = verifGET('region') ?? $lastRegion;
$dep    = verifGET('departement') ?? $lastDep;
$city   = verifGET('ville') ?? $lastCity;
?>
        <main>
            <div class="box-title">
                <h1>Rechercher stations et prix carburants en France</h1>
            </div>
            <section id="recherche">
                <h2>Choisissez votre région, département puis ville en quelques clics</h2>
                <p class="intro-recherche">
                    Trouvez rapidement la station-service idéale dans votre région. Affinez par département ou ville, 
                    consultez les prix en temps réel et comparez facilement les offres autour de vous pour faire le meilleur choix.
                </p>

                    <div class="recherche-layout">
                    <?php if ($region !== null &&  checkcodeRegion($region)): 
                        $regionName = getRegionNameByCode($region);?>

                    
                    <div class="col-left">
                    <h3><?= $regionName !== null ?  "Vous avez déjà choisi la région: ".htmlspecialchars((string)$regionName) : "Aucune région avec le code ".htmlspecialchars((string)$region) ?> </h3>
                    <!--  Puis choix département -->
                    <h3>Choisissez votre département</h3>
                        <div class="dept-grid">
<?php $deps = getDepartmentsByRegion($region);
foreach ($deps as $depItem): ?>
                            <a class="dept-card <?= ($dep !== null && $dep == $depItem[2]) ? 'active' : '' ?>"
                                href="?region=<?= htmlspecialchars((string)$region) ?>&amp;departement=<?= htmlspecialchars((string)$depItem[2]) ?>#recherche">
                                <?= htmlspecialchars((string)$depItem[3]) ?>
                            </a>
<?php endforeach; ?>
                        </div>


<?php if ( $dep !== null && checkDepartementRegion($dep, $region)): ?>
                        <!-- Bouton pour afficher les stations d'un departement -->
                        <form method="GET" action="stations_departement.php">
                            <input type="hidden" name="region" value="<?= htmlspecialchars((string)$region) ?>" />
                            <input type="hidden" name="departement" value="<?= htmlspecialchars((string)$dep) ?>" />

                            <button type="submit" class="btn">
                                Voir stations du département
                            </button>
                        </form>

                    <?php   $cities =getCitiesByDepartment($dep); ?>
                        <!--  Enfin le choix de la ville -->
                        <h3>Choisissez votre ville</h3>
                    <?php if (empty($cities)): ?>
                            <h3>Aucune ville trouvée pour ce département.</h3>
                        <?php else: ?>
<form method="GET" action="ville_prixcarburants.php">
                            <input type="hidden" name="region" value="<?= htmlspecialchars((string)$region) ?>" />
                            <input type="hidden" name="departement" value="<?= htmlspecialchars((string)$dep) ?>" />

                            <select name="ville" required="required">
                                <option value="" <?= empty($city) ? 'selected="selected"' : '' ?>> -- Choisissez une ville -- </option>

                                <?php foreach ($cities as $c): 
                                    $nom = htmlspecialchars((string)$c);

                                    // sécurité : on ne sélectionne que si la ville existe dans la liste
                                    $isSelected = (!empty($city) && $city === $c);?>
<option value="<?= $nom ?>" <?= $isSelected ? 'selected="selected"' : '' ?>><?= $nom ?></option>
                                <?php endforeach; ?>
                            </select>

                            <h4>Carburants</h4>
                            <div class="carburants-group">
                                <label><input type="checkbox" name="carburants[]" value="gazole"/> Gazole</label>
                                <label><input type="checkbox" name="carburants[]" value="sp95"/> SP95</label>
                                <label><input type="checkbox" name="carburants[]" value="sp98"/> SP98</label>
                                <label><input type="checkbox" name="carburants[]" value="e10"/> E10</label>
                                <label><input type="checkbox" name="carburants[]" value="e85"/> E85</label>
                                <label><input type="checkbox" name="carburants[]" value="gplc"/> GPLc</label>
                            </div>
                            <h4>Options</h4>
                            <div class="options-group">
                                <label>
                                    <input type="checkbox" name="date_prix" value="1" />
                                    Voir la date des prix
                                </label>

                                <label>
                                    <input type="checkbox" name="services" value="1" />
                                    Voir les services
                                </label>

                                <label>
                                     Rayon de recherche autour de votre ville (en km):
                                    <input type="number" name="rayon" min="1" max="10" step="0.5" value="5" />
                                </label>
                                
                             </div>
                            <!-- Bouton pour afficher les stations d'une ville -->
                            <button type="submit" class="btn">
                                Prix des carburants dans cette ville
                            </button>
                        </form>
<?php endif; ?>
<?php endif; ?>
                        </div>
<?php endif; ?>
                    <div class="col-right">
                        <h3>Choisissez une région sur la carte pour commencer</h3>
                        <!--  La Carte d'abord -->
                <?php include("include/map.inc.php"); ?>
                    </div>
                </div>
            </section>
         </main>
<?php include("include/footer.inc.php"); ?>