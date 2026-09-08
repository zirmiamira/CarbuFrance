<?php


//-----------     Fonctions pour le csv    -------------


/**
 * Recherche dans un fichier CSV une ligne correspondant à une valeur donnée dans une colonne, 
 * puis retourne la valeur d’un autre champ de cette même ligne.
 * 
 * @param string $file Chemin du fichier CSV à analyser
 * @param int $columnIndex Index de la colonne où chercher la valeur
 * @param string $value Valeur recherchée dans le CSV
 * @param int $returnIndex Index de la colonne à retourner si trouvé
 * @return string|null Valeur trouvée ou null si absent
 * @author ZIRMI MOKRANE
 */
function findInCSV(string $file, int $columnIndex, string $value, int $returnIndex): ?string{
    $result = null;

    if (!file_exists($file) || !is_readable($file)) {
        return null;
    }

    $f = fopen($file, "r");

    if ($f !== false) {

        // lecture des en-têtes (on les ignore)
        fgetcsv($f, 0, ",", '"', "\\");

        $found = false;

        while (($row = fgetcsv($f, 0, ",", '"', "\\")) !== false && !$found) {

            if (isset($row[$columnIndex]) && $row[$columnIndex] == $value) {
                $result = $row[$returnIndex] ?? null;
                $found = true; // on stoppe la boucle proprement
            }
        }

        fclose($f);
    }

    return $result;
}
/**
 * Retourne le nom d’une région à partir de son code.
 *
 * @param string $code Code de la région
 * @return string|null Nom de la région ou null si introuvable
 * @author ZIRMI MOKRANE
 */
function getRegionNameByCode(string $code): ?string
{
    return findInCSV("data/regions.csv", 1, $code, 2);
}
/**
 * Retourne le nom d’un département à partir de son code.
 *
 * @param string $code Code du département
 * @return string|null Nom du département ou null si introuvable
 * @author ZIRMI MOKRANE
 */
function getDepartmentNameByCode(string $code): ?string
{
    return findInCSV("data/departments.csv", 2, $code, 3);
}
/**
 * Retourne le nom d’une ville à partir de son code INSEE.
 *
 * @param string $code Code de la ville
 * @return string|null Nom de la ville ou null si introuvable
 * @author ZIRMI MOKRANE
 */
function getCityNameByCode(string $code): ?string
{
    return findInCSV("data/cities.csv", 0, $code, 1);
}

//-----------     Fonctions utilise par search.php    ---------/

/**
 * Récupère une valeur depuis $_GET et vérifie qu’elle existe et n’est pas vide.
 *
 * @param string $key Nom du paramètre GET
 * @return string|null Valeur si présente et non vide, sinon null
 * @author ZIRMI MOKRANE
 */
function verifGET(string $key): ?string {
    if (isset($_GET[$key]) && !empty($_GET[$key])) {  
            return  $_GET[$key];
        
    }
    return null;
}

/**
 * Charge un fichier CSV et retourne son contenu sous forme de tableau.
 *
 * La première ligne (en-tête) est ignorée automatiquement.
 *
 * @param string $file Chemin du fichier CSV
 * @return array Liste des lignes du CSV (tableau indexé)
 * @author ZIRMI MOKRANE
 */
function chargerContenueCSV(string $file):array{
    $data = [];

    if (!file_exists($file) || !is_readable($file)) {
        return [];
    }

    if (($f = fopen($file, "r")) !== FALSE) {
        $headers = fgetcsv($f, 0, ",", '"', "\\"); // Lecture des en-têtes (non utilisés ici)

        while (($row = fgetcsv($f, 0, ",", '"', "\\")) !== FALSE) {
            $data[] = $row;
        }
        fclose($f);
    }

    return $data;
}


/**
 * Récupère la liste des régions depuis le fichier CSV.
 *
 * @return array Liste des régions
 * @author ZIRMI MOKRANE
 */
function getRegions():array{
    return chargerContenueCSV("data/regions.csv");
}


/**
 * Récupère les départements appartenant à une région donnée.
 *
 * @param string $regionCode Code de la région
 * @return array Liste des départements correspondants
 * @author ZIRMI MOKRANE
 */
function getDepartmentsByRegion(string $regionCode):array{
    $departments = chargerContenueCSV("data/departments.csv");
    $result = [];

    foreach ($departments as $dep) {
        if ($dep[1] == $regionCode) {
            $result[] = $dep;
        }
    }

    return $result;
}

/**
 * Récupère les villes appartenant à un département donné.
 *
 * @param string $depCode Code du département
 * @return array Liste des villes correspondantes
 * @author ZIRMI MOKRANE
 */
function getCitiesByDepartment(string $depCode):array{
    $cities = chargerContenueCSV("data/cities.csv");
    $result = [];

        foreach ($cities as $city) {
            // code département
            $cityDepCode = $city[1];
            if ($cityDepCode == $depCode) {
                if (!in_array($city[4], $result)) {
                    $result[] = $city[4];
                }
            }
        }
    

    return $result;
}


/**
 * Vérifie si un code région existe dans le fichier CSV.
 *
 * Recherche dynamique de la colonne "code" dans l’en-tête du fichier.
 *
 * @param string $codeRecherche Code de la région
 * @return bool True si trouvé, sinon false
 * @author ZIRMI MOKRANE
 */
function checkcodeRegion(string $codeRecherche): bool
{
    $file = "data/regions.csv";

    if (!is_readable($file)) return false;

    $f = fopen($file, "r");
    if (!$f) return false;

    // lecture header
    $header = fgetcsv($f, 0, ",", '"', "\\");
    if ($header === false) {
        fclose($f);
        return false;
    }

    // récupérer index de la colonne "code"
    $index = array_search("code", $header, true);

    if ($index === false) {
        fclose($f);
        return false;
    }

    $codeRecherche = trim($codeRecherche);

    while (($row = fgetcsv($f, 0, ",", '"', "\\")) !== false) {

        // accès direct à la colonne + trim
        if (isset($row[$index]) && trim($row[$index]) === $codeRecherche) {
            fclose($f);
            return true;
        }
    }

    fclose($f);
    return false;
}

/**
/**
 * Vérifie si un département appartient à une région donnée.
 *
 * Parcourt le fichier CSV des départements et vérifie la correspondance
 * entre code département et code région.
 *
 * @param string $dep Code du département
 * @param string $region Code de la région
 * @return bool True si correspondance trouvée
 * @author ZIRMI MOKRANE
 */
function checkDepartementRegion(string $dep, string $region): bool {

    $file = "data/departments.csv";
    if (!file_exists($file)) return false;

    $f = fopen($file, "r");
    if (!$f) return false;

    fgetcsv($f, 0, ",", '"', "\\"); // skip header

    while (($row = fgetcsv($f, 0, ",", '"', "\\")) !== false) {
        if ($row[2] === $dep && $row[1] === $region) {
            fclose($f);
            return true;
        }
    }

    fclose($f);
    return false;
}



/**
 * Vérifie si une ville appartient à un département donné.
 *
 * Compare le nom de la ville (insensible à la casse)
 * avec les entrées du CSV filtrées par département.
 *
 * @param string $ville Nom de la ville 
 * @param string $dep   Code du département 
 *
 * @return bool
 *  - true  : si la ville appartient au département
 *  - false : sinon ou en cas d'erreur
 * @author ZIRMI MOKRANE
 */
function checkVilleDepartement(string $ville, string $dep): bool {

    $file = "data/cities.csv";
    if (!file_exists($file)) return false;

    $f = fopen($file, "r");
    if (!$f) return false;

    $ville = strtolower($ville);

    fgetcsv($f, 0, ",", '"', "\\"); // skip header

    while (($row = fgetcsv($f, 0, ",", '"', "\\")) !== false) {

        $name = strtolower($row[4]); // nom ville
        $codeDep = $row[1];

        if ($name === $ville && $codeDep === $dep) {
            fclose($f);
            return true;
        }
    }

    fclose($f);
    return false;
}

//-----------       Fonction utilise par ville_prixcarburants.php  ------------

/**
 * Récupère les stations-service d’une ville via l’API gouvernementale.
 *
 * Effectue une requête HTTP vers l’API officielle des carburants
 * et retourne les stations correspondant à la ville demandée.
 *
 * @param string $region Code de la région
 * @param string $dep Code du département
 * @param string $ville Nom de la ville
 * @return array Tableau contenant :
 *               - total (int) nombre de stations
 *               - stations (array) liste des stations
 * @author ZIRMI MOKRANE
 */
function getStationsByVille(string $region, string $dep, string $ville):array{
    $region = str_replace(' ', '', $region);
    $dep = str_replace(' ', '', $dep);
    $ville = str_replace(' ', '', $ville);
    $url = "https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/prix-des-carburants-en-france-flux-instantane-v2/records?"
        . "where=" . urlencode(
                'code_region="' . $region . '" AND code_departement="' . $dep . '" AND ville="' . $ville . '"'
        )
        . "&limit=100";
    // Appel API
    $json = @file_get_contents($url);
    if ($json === false) {
        return [
            "total" => 0,
            "stations" => []
        ];
    }
    // Decode JSON
    $data = json_decode($json, true);

    if (!is_array($data)) {
        return [
            "total" => 0,
            "stations" => []
        ];
    }

    return [
        "total" => (int)($data["total_count"] ?? 0),
        "stations" => $data["results"] ?? []
    ];

}


/**
 * Transforme une station brute issue de l’API en structure simplifiée.
 *
 * Extrait uniquement les informations utiles pour l’affichage :
 * - adresse
 * - prix des carburants
 * - dates de mise à jour
 * - services disponibles
 *
 * @param array $station Données API
 * @return array Station formatée
 * @author ZIRMI MOKRANE
 */
function orgStationVille(array $station): array
{
    // Adresse
    $adresse = ($station["adresse"] ?? "") . ", " . ($station["cp"] ?? "");

    // Carburants
    $carburants = ["gazole", "sp95", "sp98", "e10", "e85", "gplc"];
    $mapping = [
        "gazole" => "Gazole",
        "sp95" => "SP95",
        "sp98" => "SP98",
        "e10" => "E10",
        "e85" => "E85",
        "gplc" => "GPLc"
    ];

    $prix = [];
    $datePrix = [];
    $dispo = [];

    $disponibles = $station["carburants_disponibles"] ?? [];

    foreach ($carburants as $c) {

        // PRIX
        $p = $station[$c . "_prix"] ?? "";
        if(!empty($p)){
             $prix[$c] = $p ;
             $datePrix[$c] = $station[$c . "_maj"] ?? "";
        }
    }

    $horaires = [];
    $rawHoraires = $station["horaires"] ?? null;
    if ($rawHoraires) {
        $decoded = json_decode($rawHoraires, true);
        if (is_array($decoded)) {
            // automate 24/24
            $horaires["automate_24_24"] = !empty($decoded["@automate-24-24"]) ? "Oui" : "Non";
            // jours
            $jours = [];
            foreach (($decoded["jour"] ?? []) as $j) {
                $nomJour = $j["@nom"] ?? "";
                $plages[] = [
                    "ouverture" => $j["@ouverture"] ?? "",
                    "fermeture" => $j["@fermeture"] ?? ""
                ];
                $jours[] = [
                    "jour" => $nomJour,
                    "ferme" => !empty($j["@ferme"]),
                    "horaires" => $plages
                ];
            }
            $horaires["jours"] = $jours;
        }
    }

    // SERVICES
    $services = $station["services_service"] ?? [];

return [
    "adresse" => $adresse,
    "prix" => $prix,
    "date_prix" => $datePrix,
    "services" => $services
];
}

/**
 * Transforme une station brute issue de l’API en structure normalisée.
 *
 * Cette fonction nettoie et organise les données pour l’affichage :
 * - prix carburants
 * - dates de mise à jour
 * - services disponibles
 *
 * @param array $station Données brutes d’une station API
 * @return array Station structurée pour affichage
 * @author ZIRMI MOKRANE
 */
function orgStationsVille(array $stations): array
{
    $result = [];

    foreach ($stations as $station) {
        $result[] = orgStationVille($station);
    }
    return $result;
}

/**
 * Calcule le centre géographique d’une ville à partir du fichier CSV.
 *
 * Moyenne des coordonnées GPS de toutes les entrées correspondantes.
 *
 * @param string $ville Nom de la ville
 * @return array Coordonnées ["lat" => float, "lng" => float]
 * @author ZIRMI MOKRANE
 */
function getCentreVille(string $ville): array {

    $file = "data/cities.csv";

    if (!file_exists($file) || !is_readable($file)) {
        return ["lat" => 0, "lng" => 0];
    }

    $f = fopen($file, "r");
    if (!$f) {
        return ["lat" => 0, "lng" => 0];
    }

    // normalisation : minuscule + suppression espaces
    $ville = strtolower(str_replace(" ", "", $ville));

    $latTotal = 0;
    $lngTotal = 0;
    $count = 0;

    fgetcsv($f, 0, ",", '"', "\\");

    while (($row = fgetcsv($f, 0, ",", '"', "\\")) !== false) {

        // même normalisation sur les données CSV
        $name = strtolower(str_replace(" ", "", $row[4]));

        if ($name !== $ville) {
            continue;
        }

        $latTotal += (float)$row[6];
        $lngTotal += (float)$row[7];
        $count++;
    }

    fclose($f);

    if ($count === 0) {
        return ["lat" => 0, "lng" => 0];
    }

    return [
        "lat" => $latTotal / $count,
        "lng" => $lngTotal / $count
    ];
}

/**
 * Récupère les stations-service proches d’un point GPS.
 *
 * Applique un filtre géographique et optionnellement des filtres
 * sur les carburants et services.
 *
 * @param string $ville Ville de référence
 * @param float $lat Latitude
 * @param float $lng Longitude
 * @param array $carbs Carburants sélectionnés
 * @param bool $services Filtrer par services
 * @param float $rayon Rayon en km
 * @return array Stations proches + total
 * @author ZIRMI MOKRANE
 */
function getStationsProches(string $ville, float $lat, float $lng, array $carbs, bool $services, float $rayon = 10): array {

    // Filtres de base
    $filters = [];
    $filters[] = 'NOT ville="' . $ville . '"';
    $filters[] = 'within_distance(geom, geom\'POINT(' . $lng . ' ' . $lat . ')\', ' . $rayon . 'km)';

    // Mapping carburants → champs API
    $map = [
        'gazole' => 'gazole_prix',
        'sp95'   => 'sp95_prix',
        'sp98'   => 'sp98_prix',
        'e10'    => 'e10_prix',
        'e85'    => 'e85_prix',
        'gplc'   => 'gplc_prix'
    ];

    // Bloc OR global
    $orFilters = [];

    // Carburants
    foreach ($carbs as $carb) {
        if (isset($map[$carb])) {
            $orFilters[] = $map[$carb] . ' is not null';
        }
    }

    // Services
    if ($services) {
        $orFilters[] = 'services_service is not null';
    }

    // Ajout du bloc OR si nécessaire
    if (!empty($orFilters)) {
        $filters[] = '(' . implode(' OR ', $orFilters) . ')';
    }

    // Construction WHERE
    $where = implode(' AND ', $filters);

    // URL API
    $url = "https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/prix-des-carburants-en-france-flux-instantane-v2/records"
        . "?select=*%2Cdistance(geom%2C%20geom%27POINT(".$lng."%20".$lat.")%27)%20as%20dist"
        . "&where=" . urlencode($where)
        . "&order_by=dist%20ASC"
        . "&limit=100";

    // Appel API
    $response = @file_get_contents($url);

    if ($response === false) {
        return [
            "total" => 0,
            "stations" => []
        ];
    }

    // Décodage JSON
    $data = json_decode($response, true);

    return [
        "total" => (int)($data["total_count"] ?? 0),
        "stations" => $data["results"] ?? []
    ];
}

//-----------      Fonction utilise pour autour de moi     --------------
/**
 * Récupère les stations-service proches d’un point GPS.
 *
 * Applique un filtre géographique et optionnellement des filtres
 * sur les carburants et services.
 *
 * @param string $ville Ville de référence
 * @param float $lat Latitude
 * @param float $lng Longitude
 * @param array $carbs Carburants sélectionnés
 * @param bool $services Filtrer par services
 * @param float $rayon Rayon en km
 * @return array Stations proches + total
 * @author ZIRMI MOKRANE
 */
function getStationsAutourDeMoi( float $lat, float $lng, array $carbs = [], bool $services = false, float $rayon = 15, int $offset = 0 ): array {

    $filters = [];
    // Filtre géographique
    $filters[] = "within_distance(geom, geom'POINT($lng $lat)', ".$rayon."km)";
    // Mapping carburants
    $map = [
        'gazole' => 'gazole_prix',
        'sp95'   => 'sp95_prix',
        'sp98'   => 'sp98_prix',
        'e10'    => 'e10_prix',
        'e85'    => 'e85_prix',
        'gplc'   => 'gplc_prix'
    ];

    // Bloc OR global
    $orFilters = [];

    // Carburants
    foreach ($carbs as $carb) {
        if (isset($map[$carb])) {
            $orFilters[] = $map[$carb] . ' is not null';
        }
    }

    // Services
    if ($services) {
        $orFilters[] = 'services_service is not null';
    }

    // Ajout du bloc OR si nécessaire
    if (!empty($orFilters)) {
        $filters[] = '(' . implode(' OR ', $orFilters) . ')';
    }

    // Construction WHERE
    $where = implode(' AND ', $filters);

    // URL API
    $url = "https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/prix-des-carburants-en-france-flux-instantane-v2/records"
        . "?select=*%2Cdistance(geom%2C%20geom%27POINT(".$lng."%20".$lat.")%27)%20as%20dist"
        . "&where=" . urlencode($where)
        . "&order_by=dist%20ASC"
        . "&limit=100"
        . "&offset=".$offset;

    $response = file_get_contents($url);

    if ($response === false) {
        echo"<p> ".$url."  </p>";
        return [
            "total" => 0,
            "stations" => []
        ];
    }

    $data = json_decode($response, true);

    return [
        "total" => (int)($data["total_count"] ?? 0),
        "stations" => $data["results"] ?? []
    ];
}

/**
 * Récupère l’adresse IP de l’utilisateur.
 *
 * Gère les cas de proxy (HTTP_CLIENT_IP / HTTP_X_FORWARDED_FOR).
 *
 * @return string Adresse IP détectée
 */
function getUserIP(): string {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

/**
 * Récupère les coordonnées géographiques à partir d’une adresse IP.
 *
 * Utilise une API externe (ip2location) pour obtenir latitude et longitude.
 *
 * @param string $ip Adresse IP
 * @return array Tableau contenant lat, lng, ville et région (ou vide si échec)
 */
function getCoordsFromIP(string $ip): array {
    $apiKey = "56CA1735813DEE9E17FD50FFA3DB6246"; 

    $url = "https://api.ip2location.io/?key=".$apiKey."&ip=" . $ip;

    $response = @file_get_contents($url);
    if ($response === false) {
        return [];
    }

    $data = json_decode($response, true);
    if (empty($data['latitude']) || empty($data['longitude'])) {
        return [];
    }

    return [
        'lat' => (float)$data['latitude'],
        'lng' => (float)$data['longitude'],
        'city' => $data['city_name'] ?? null,
        'region' => $data['region_name'] ?? null,
        'pays' => $data['country_name'] ?? null
    ];
}

/**
 * Extrait les prix des carburants sélectionnés pour une station.
 *
 * @param array $station Données station
 * @param array $selectedCarbs Liste des carburants
 * @return array Prix filtrés
 * @author ZIRMI MOKRANE
 */
function formatStation(array $station, array $selectedCarbs):array {
    $prix = [];
    foreach ($selectedCarbs as $carb) {
        $key = strtolower($carb) . "_prix";

        if (!empty($station[$key])) {
            $prix[$carb] = $station[$key];
        }
    }
    return $prix;
}

/**
 * Transforme les horaires JSON d’une station en structure exploitable.
 *
 * @param array $station Données station
 * @return array Horaires normalisés (jours + statut 24h/24)
 * @author ZIRMI MOKRANE
 */
function parseHoraires($station) {

    $result = [
        "auto_24_24" => $station['horaires_automate_24_24'] ?? "Non",
        "jours" => []
    ];

    // 👉 Cas spécial : automate 24/24
    if (($station['horaires_automate_24_24'] ?? null) === "Oui") {
        return [
            "auto_24_24" => "Oui",
            "jours" => null
        ];
    }

    if (empty($station['horaires'])) {
        return $result;
    }

    $horaires = json_decode($station['horaires'], true);

    if (!isset($horaires['jour']) || !is_array($horaires['jour'])) {
        return $result;
    }

    foreach ($horaires['jour'] as $j) {

        $result['jours'][] = [
            "jour" => $j['@nom'] ?? '',
            "ouverture" => $j['horaire']['@ouverture'] ?? null,
            "fermeture" => $j['horaire']['@fermeture'] ?? null,
            "ferme" => empty($j['@ferme']) ? "Ouvert" : "Fermé"
        ];
    }

    return $result;
}

//-----------       Fonction utilise par stations_departement.php  ------------

/**
 * Récupère la liste des stations-service d’un département via l’API gouvernementale.
 *
 * @param string $codeDep Code du département (ex: "95")
 * @return array Tableau contenant :
 *               - total : nombre total de stations trouvées
 *               - stations : liste des stations 
 * @author ZIRMI MOKRANE
 */
function getStationsByDepartment(string $region, string $dep, array $carbs, bool $services, int $offset): array {

    $filters = [];
    $filters[] = 'code_region="' . $region . '"';
    $filters[] = 'code_departement="' . $dep . '"';

    $map = [
        'gazole' => 'gazole_prix',
        'sp95'   => 'sp95_prix',
        'sp98'   => 'sp98_prix',
        'e10'    => 'e10_prix',
        'e85'    => 'e85_prix',
        'gplc'   => 'gplc_prix'
    ];

    // Carburants
    $carbFilters = [];
    foreach ($carbs as $carb) {
        if (isset($map[$carb])) {
            $carbFilters[] = $map[$carb] . ' is not null';
        }
    }

    // Bloc OR global
    $orFilters = [];

    if (!empty($carbFilters)) {
        $orFilters[] = implode(' OR ', $carbFilters);
    }

    if ($services) {
        // condition "a des services"
        $orFilters[] = 'services_service is not null';
    }

    // On ajoute seulement si on a quelque chose
    if (!empty($orFilters)) {
        $filters[] = '(' . implode(' OR ', $orFilters) . ')';
    }

    $where = implode(' AND ', $filters);

    $url = "https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/prix-des-carburants-en-france-flux-instantane-v2/records"
         . "?where=" . urlencode($where)
         . "&limit=100&offset=" . $offset;

    $json = @file_get_contents($url);

    if ($json === false) {
        return ["total" => 0, "stations" => []];
    }

    $data = json_decode($json, true);

    return [
        "total" => (int)($data["total_count"] ?? 0),
        "stations" => $data["results"] ?? []
    ];
}
//------------      statistics    -----------
/**
 * Enregistre une consultation de ville dans un fichier CSV.
 *
 * Ajoute automatiquement un horodatage à chaque entrée.
 * Crée le fichier avec en-tête s’il n’existe pas encore.
 *
 * @param string $ville Nom de la ville consultée
 * @param string $departement Nom du département
 * @param string $region Nom de la région
 * @param string $fichier Chemin du fichier CSV (optionnel)
 * @return void
 * @author ZIRMI MOKRANE
 */
function enregistrerVille(string $ville, string $departement, string $region, string $fichier = "./villesvisite.csv") {
    // Vérifier si le fichier existe déjà
    $nouveauFichier = !file_exists($fichier);
    
    // Ouvrir en mode ajout
    $file = fopen($fichier, "a");
    if ($file) {
        // Si le fichier vient d'être créé → ajouter l'en-tête
        if ($nouveauFichier) {
            fputcsv($file, ["Ville", "Département", "Région", "Horodatage"], ";", '"', "\\");
        }
        
        // Horodatage actuel
        $horodatage = date("Y-m-d H:i:s");
        
        // Ajouter la ligne
        fputcsv($file, [$ville, $departement, $region, $horodatage], ";", '"', "\\");
        fclose($file);
    } 
}

/**
 * Enregistre une consultation de département dans un fichier CSV.
 *
 * Ajoute automatiquement un horodatage et crée le fichier si nécessaire.
 *
 * @param string $nomdep Nom du département
 * @param string $region Nom de la région
 * @param string $fichier Chemin du fichier CSV
 * @return void
 * @author ZIRMI MOKRANE
 */
function enregistrerDepartement(string $nomdep, string $region, string $fichier = "./departementvisite.csv"){    // Vérifier si le fichier existe déjà
    $nouveauFichier = !file_exists($fichier);
    
    // Ouvrir en mode ajout
    $file = fopen($fichier, "a");
    if ($file) {
        // Si le fichier vient d'être créé → ajouter l'en-tête
        if ($nouveauFichier) {
            fputcsv($file, ["Département", "Region" , "Horodatage"],  ";", '"', "\\");
        }
        
        // Horodatage actuel
        $horodatage = date("Y-m-d H:i:s");
        
        // Ajouter la ligne
        fputcsv($file, [$nomdep, $region,$horodatage], ";", '"', "\\");
        fclose($file);
    } 
}

/**
 * Compte les occurrences des valeurs présentes dans une colonne d’un fichier CSV.
 *
 * Cette fonction permet de générer des statistiques de consultation
 * (ex: nombre de visites par ville ou département).
 *
 * @param int $index Index de la colonne à analyser
 * @param string $fichier Chemin du fichier CSV
 * @return array Tableau associatif trié par fréquence décroissante
 */

function getStats(int $index, $fichier = "villesvisite.csv"):array { 
    $stats = [];

    if (!file_exists($fichier)) return $stats; 

    if (($file = fopen($fichier, "r")) !== false) {
        fgetcsv($file, 0, ";", '"', "\\"); // skip header

        while (($data = fgetcsv($file, 0, ";", '"', "\\")) !== false) {
            if (isset($data[$index])) {
                $cle = $data[$index];
                $stats[$cle] = ($stats[$cle] ?? 0) + 1;
            }
            
        }

        fclose($file);
    }

    arsort($stats);
    return $stats;
}

//-------------------    page  tech   ----------------------
/**
 * Récupère un film aléatoire depuis l’API Studio Ghibli.
 *
 * @return array Tableau contenant les informations du film sélectionné
 * @author ZIRMI MOKRANE
 */
function getUnFilm(): array {
    $url = "https://ghibliapi.vercel.app/films";
    $resultat = @file_get_contents($url);

    if ($resultat === false) {
        return []; // API inaccessible
    }

    $tab = json_decode($resultat, true);

    if ($tab === null || !is_array($tab) || count($tab) === 0) {
        return []; // JSON invalide ou vide
    }

    $nbr = rand(0, count($tab) - 1);

    return $tab[$nbr];
}


/**
 * Récupère la géolocalisation d’un utilisateur à partir de son IP (version JSON).
 *
 * @return array Données de localisation : IP, ville, région, pays
 * @author ZIRMI MOKRANE
 */
function getGeoloc(): array {

    $ip = getUserIP() ?? 'inconnue';

    if ($ip !== 'inconnue') {
        $data = getCoordsFromIP($ip);

        return [
            'ip'     => $ip,
            'ville'  => $data['city'] ?? 'non disponible',
            'region' => $data['region'] ?? 'non disponible',
            'pays'   => $data['pays'] ?? 'non disponible'
        ];
    }

    return [
        'ip'     => $ip,
        'ville'  => 'non disponible',
        'region' => 'non disponible',
        'pays'   => 'non disponible'
    ];
}


/**
 * Récupère la géolocalisation via API XML (whatismyip).
 *
 * Analyse la réponse XML et extrait les informations de localisation.
 *
 * @return array Données : ville, région, pays
 * @author ZIRMI MOKRANE
 */
function getGeolocXML(): array {

    $ip = getUserIP() ?? '';

    if (empty($ip)) {
        return [
            'ville' => 'non disponible',
            'region' => 'non disponible',
            'pays' => 'non disponible'
        ];
    }

    $key = "a091678a81b7a8e83b95fe6ee9766a52";
    $url = "https://api.whatismyip.com/ip-address-lookup.php?key=$key&input=$ip&output=xml";

    $resultat = @file_get_contents($url);

    if ($resultat === false) {
        return [
            'ville' => 'API inaccessible',
            'region' => 'API inaccessible',
            'pays' => 'API inaccessible'
        ];
    }

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($resultat);

    if ($xml === false || !isset($xml->server_data)) {
        return [
            'ville' => 'XML invalide',
            'region' => 'XML invalide',
            'pays' => 'XML invalide'
        ];
    }

    $data = $xml->server_data;

    return [
        'ville' => (string) $data->city,
        'region' => (string) $data->region,
        'pays' => (string) $data->country
    ];
}

?>