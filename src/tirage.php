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
    $pdo = new PDO("mysql:host=db;dbname=dbBesian;charset=utf8", "root", "root");
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

    $sql = "INSERT INTO tirages (champ, valeur, date_tirage, heure_tirage)
            VALUES (:champ, :valeur, :date_tirage, :heure_tirage)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":champ" => $champTire,
        ":valeur" => $valeurTiree,
        ":date_tirage" => $date,
        ":heure_tirage" => $heure
    ]);

    echo "<h1>Le gagnant est : " . htmlspecialchars($valeurTiree) . "</h1>";
    echo "<h3>(Champ tiré : " . htmlspecialchars($champTire) . ")</h3>";

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