<?php
$champs = [];

for ($i = 1; $i <= 10; $i++) {
    if (!empty($_POST['c' . $i])) {
        $champs[] = $_POST['c' . $i];
    }
}

if (count($champs) > 0) {
    $gagnant = $champs[array_rand($champs)];
    echo "<h1>Le gagnant est : " . htmlspecialchars($gagnant) . "</h1>";
} else {
    echo "<h1>Aucun participant</h1>";
}

echo '<br><a href="index.html">Retour</a>';
?><?php