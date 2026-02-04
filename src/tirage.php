<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Résultat du tirage</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="./icon.jpg" type="image/x-icon">
</head>

<body>

    <?php
    try {
        $pdo = new PDO("mysql:host=db;dbname=formulaire;charset=utf8", "root", "root");
    } catch (Exception $e) {
        die("<h1>Erreur connexion BDD</h1>");
    }

    $champs = [];

    for ($i = 1; $i <= 10; $i++) {
        if (!empty($_POST['c' . $i])) {
            $champs['c' . $i] = $_POST['c' . $i];
        }
    }

    if (count($champs) > 0) {

        $champTire = array_rand($champs);
        $valeurTiree = $champs[$champTire];

        $date = date("Y-m-d");
        $heure = date("H:i:s");

        try {
            $sql = "INSERT INTO tirages (valeur, date_tirage, heure_tirage) 
                    VALUES (:valeur, :date_tirage, :heure_tirage)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":valeur" => $valeurTiree,
                ":date_tirage" => $date,
                ":heure_tirage" => $heure
            ]);
        } catch (PDOException $e) {
            error_log("Database insertion error: " . $e->getMessage());
        }

        $migrationsDir = __DIR__ . "/../db/migrations";

        // Créer le dossier s'il n'existe pas
        if (!is_dir($migrationsDir)) {
            mkdir($migrationsDir, 0755, true);
        }

        // Récupérer les migrations exécutées
        $stmt = $pdo->query("SELECT filename FROM migrations");
        $executed = [];
        while ($row = $stmt->fetch()) {
            $executed[] = $row['filename'];
        }

        // Trouver le fichier de migration en cours (non exécuté)
        $files = array_merge(
            glob($migrationsDir . '/*.json') ?: [],
            glob($migrationsDir . '/*.sql') ?: []
        );
        sort($files);
        $currentMigration = null;

        foreach (array_reverse($files) as $file) {
            $filename = basename($file);
            if (!in_array($filename, $executed) && pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $currentMigration = $file;
                break;
            }
        }

        // Si pas de migration en cours, en créer une nouvelle
        if (!$currentMigration) {
            $counterFile = $migrationsDir . "/.counter";
            $counter = file_exists($counterFile) ? intval(file_get_contents($counterFile)) : 0;
            $migrationNumber = 1000 + $counter + 1;
            file_put_contents($counterFile, $counter + 1);

            $timestamp = date("YmdHis");
            $currentMigration = $migrationsDir . "/" . str_pad($migrationNumber, 3, "0", STR_PAD_LEFT) . "_tirage_$timestamp.json";
        }

        // Lire le fichier de migration actuel
        $migrationData = [];
        if (file_exists($currentMigration)) {
            $content = file_get_contents($currentMigration);
            $migrationData = json_decode($content, true) ?: [];
        }

        // Ajouter les nouvelles données
        $migrationData[] = [
            "valeur" => $valeurTiree,
            "date_tirage" => $date,
            "heure_tirage" => $heure
        ];

        // Sauvegarder le fichier de migration
        file_put_contents($currentMigration, json_encode($migrationData, JSON_PRETTY_PRINT));

        echo "<h1>Le gagnant est : " . htmlspecialchars($valeurTiree) . "</h1>";
        echo "<h3>(Champ tiré : " . htmlspecialchars($champTire) . ")</h3>";
        echo "<p style='color: green;'>✓ Tirage ajouté à la base de données et à la migration</p>";
    } else {
        echo "<h1>Aucun participant</h1>";
    }

    echo '<br><a href="index.html">Retour</a>';
    ?>

</body>

</html>


<!-- 
Créé par Besjan
Récupéré John 
-->