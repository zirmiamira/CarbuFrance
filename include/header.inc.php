<?php
// save nbr of visitors

$style = "styles.css";
$theme = "light";
$params = $_GET; // on récupère tous les paramètres


/* Si paramètre dans l’URL */
if (isset($_GET["style"]) && !empty($_GET["style"])) {

    if ($_GET["style"] === "dark") {
        $style = "stylesdark.css";
        $theme = "dark";
    } elseif ($_GET["style"] === "light") {
        $style = "styles.css";
        $theme = "light";
    }

    setcookie("theme", $theme, time() + (30 * 24 * 60 * 60), "/");
}/* Sinon on lit le cookie */
elseif (isset($_COOKIE["theme"]) && !empty($_COOKIE["theme"])) {

    if ($_COOKIE["theme"] === "dark") {
        $style = "stylesdark.css";
        $theme = "dark";
    } elseif ($_COOKIE["theme"] === "light") {
        $style = "styles.css";
        $theme = "light";
    } else {
        setcookie("theme", "", time() - 3600, "/");
    }
}else{/* Par defaut */
    setcookie("theme", $theme, time() + (30 * 24 * 60 * 60), "/");
}

$darkParams = $params;
$darkParams['style'] = 'dark';
$darkUrl = '?' . http_build_query($darkParams);

$lightParams = $params;
$lightParams['style'] = 'light';
$lightUrl = '?' . http_build_query($lightParams);


session_start();

if (!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    $file = "./counter.txt";

    // Si le fichier n'existe pas, on le crée avec 0
    if (!file_exists($file)) {
        file_put_contents($file, "0");
    }

    $count = (int)file_get_contents($file);
    $count++;


    file_put_contents($file, $count);
}
?>
<!DOCTYPE html>
<html lang = "fr" >
    <head>
        <meta charset= "UTF-8" />
        <meta name="description" content ="Prix des carburants"/>
        <title><?= htmlspecialchars($title) ?></title>
        <link rel="icon" type="image/png" href="./images/favicon.png"/> 
        <link rel="stylesheet" href="<?= $style ?>"/>
    </head>
    <body>
        <header>
            <nav id="NavPrincipale">

                <div class="nav-left">
                    <a href="index.php"><img src="./images/logo.png" alt="logo" /></a>
                </div>

                <ul class="nav-center">
                    <li><a href="search.php">Stations carburant</a></li>
                    <li><a href="statistics.php">Statistique</a></li>
                    <li><a href="stations_autour_de_moi.php">Autour de moi</a></li>
                </ul>

                <div class="nav-right">
                    <?php if ($theme === "light") : ?>
<a href="<?= htmlspecialchars($darkUrl) ?>">
                        <img src="images/moon.png" alt="Dark mode" />
                    </a>
                <?php else : ?>
<a href="<?= htmlspecialchars($lightUrl) ?>">
                        <img src="images/sun.png" alt="Light mode" />
                    </a>
                <?php endif; ?>
</div>

            </nav>
        </header>

       
