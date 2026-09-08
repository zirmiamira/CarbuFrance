<?php
declare(strict_types=1);

$title="Les statistiques";
include("./include/header.inc.php");
include("./include/function.inc.php");


// DATA

$villes = getStats(0);
$departements = getStats(0, "./departementvisite.csv");

// compteur global
$filename = "./counter.txt";
$compteur = file_exists($filename) ? (int)file_get_contents($filename) : 0;

// dérivées
$villesUniques = count($villes);
$plusConsultee = !empty($villes) ? array_key_first($villes) : "—";

// TOP
$topVilles = array_slice($villes, 0, 100, true);
$topVilles10 = array_slice($villes, 0, 10, true);
$topDeps = array_slice($departements, 0, 100, true);
$maxVille = !empty($topVilles) ? max($topVilles) : 1;
$maxDep   = !empty($topDeps) ? max($topDeps) : 1;
$totalVilles = array_sum($topVilles);
$totalDeps = array_sum($topDeps);

// chart
$villesLabels = array_keys($topVilles10);
$villesData = array_values($topVilles10);


//  COOKIES dernière visite

$lastCity   = $_COOKIE['last_city'] ?? null;
$lastDep    = $_COOKIE['last_dep'] ?? null;
$lastRegion = $_COOKIE['last_region'] ?? null;
?>

         <main>
            <div class="box-title">
                <h1>
                    Statistiques de consultation
                </h1>
            </div>
            <!--   DERNIÈRE VISITE  -->
            <?php if ($lastCity || $lastDep || $lastRegion): ?>
            <section class="last-visit" aria-label="Dernière recherche utilisateur">
                <h2>Votre dernière recherche</h2>
                <p>
                     Dernière recherche :
                    <?php if ($lastCity): ?>
                        <strong><?= htmlspecialchars($lastCity) ?></strong>
                    <?php endif; ?>

                    <?php if ($lastDep): ?>
                        (<?= htmlspecialchars($lastDep) ?>)
                    <?php endif; ?>

                    <?php if ($lastRegion): ?>
                        — <?= htmlspecialchars($lastRegion) ?>
                    <?php endif; ?>
                </p>
            </section>
            <?php endif; ?>


            <!--   3 STATS  -->
            <section >

                <h2>Résumé des statistiques</h2>
                <div class="stats-overview">
                    <article class="stat-card stat-orange">
                        <h2>Total visiteurs</h2>
                        <p><?= $compteur ?></p>
                    </article>

                    <article class="stat-card stat-blue">
                        <h2>Villes visitees</h2>
                        <p><?= $villesUniques ?></p>
                    </article>

                    <article class="stat-card stat-green">
                        <h2>Ville la plus consultée</h2>
                        <p><?= htmlspecialchars($plusConsultee) ?></p>
                    </article>

                </div>
            </section>


            <!--  HISTOGRAMME  -->
            <section class="chart-section">
                <h2>Top 10 villes consultées</h2>
                <div  class="charts">
                    <canvas id="chartVilles"></canvas>
                </div>
            </section>

            <!--  TABLEAUX  -->
            <section >
                <h2>Classements</h2>
                 <div class="ranking-grid">
                 <!-- VILLES -->
                    <article class="card">

                        <h3>Les 100 villes les plus recherchées</h3>
                        <?php foreach ($topVilles as $nom => $count): ?>
                            <div class="row">

                                <div class="row-top">
                                    <span><?= htmlspecialchars($nom) ?></span>
                                    <span><?= $count ?> visites (<?= $totalVilles > 0 ? round(($count / $totalVilles) * 100, 1) : 0 ?>%)</span>
                                </div>
                                <div class="bar">
                                    <div class="bar-fill" style="width: <?= $totalVilles > 0 ? ($count / $totalVilles) * 100 : 0 ?>%"></div>
                                </div>

                            </div>
                        <?php endforeach; ?>

                    </article>
                    <!-- DÉPARTEMENTS -->
                    <article class="card">

                        <h3>Les 100 départements les plus recherchés</h3>

                        <?php foreach ($topDeps as $dep => $count): ?>
                            <div class="row">
                                <div class="row-top">
                                    <span><?= htmlspecialchars($dep) ?></span>
                                    <span>
                                        <?= $count ?> visites (<?= $totalDeps > 0 ? round(($count / $totalDeps) * 100, 1) : 0 ?>%)
                                    </span>
                                </div>
                                <div class="bar">
                                    <div class="bar-fill dep" style="width: <?= $totalDeps > 0 ? ($count / $totalDeps) * 100 : 0 ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </article>
                 </div>
            </section>

         </main>

        <!--  CHART JS  -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const villesLabels = <?= json_encode($villesLabels) ?>;
            const villesData = <?= json_encode($villesData) ?>;

            new Chart(document.getElementById('chartVilles'), {
                type: 'bar',
                data: {
                    labels: villesLabels,
                    datasets: [{
                        data: villesData,
                        backgroundColor: '#3b83f6'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false } 
                    }
                }
            });
        </script>
<?php include("include/footer.inc.php"); ?>