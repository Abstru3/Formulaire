<?php

/**
 * Gestionnaire de migrations pour la base de données
 * Usage: php migrate.php
 */

$host = 'db';
$dbname = 'formulaire';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);

    // Créer la base de données si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");

    // Créer la table de suivi des migrations
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL UNIQUE,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Récupérer les migrations déjà exécutées
    $stmt = $pdo->query("SELECT filename FROM migrations");
    $executed = [];
    while ($row = $stmt->fetch()) {
        $executed[] = $row['filename'];
    }

    // Exécuter les nouvelles migrations
    $migrationsDir = __DIR__ . '/db/migrations';
    if (is_dir($migrationsDir)) {
        $files = array_merge(
            glob($migrationsDir . '/*.sql') ?: [],
            glob($migrationsDir . '/*.json') ?: []
        );
        sort($files);

        foreach ($files as $file) {
            $filename = basename($file);

            if (!in_array($filename, $executed)) {
                echo "Exécution : $filename...\n";

                $content = file_get_contents($file);
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                if ($ext === 'json') {
                    // Traiter les fichiers JSON
                    $data = json_decode($content, true);

                    if ($data) {
                        // Si c'est un tableau de tirages
                        if (isset($data[0]) && is_array($data[0])) {
                            foreach ($data as $tirage) {
                                if (isset($tirage['valeur'])) {
                                    $sql = "INSERT INTO tirages (valeur, date_tirage, heure_tirage) 
                                            VALUES (:valeur, :date_tirage, :heure_tirage)";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute([
                                        ":valeur" => $tirage['valeur'],
                                        ":date_tirage" => $tirage['date_tirage'],
                                        ":heure_tirage" => $tirage['heure_tirage']
                                    ]);
                                }
                            }
                        } elseif (isset($data['valeur'])) {
                            // Tirage unique
                            $sql = "INSERT INTO tirages (valeur, date_tirage, heure_tirage) 
                                    VALUES (:valeur, :date_tirage, :heure_tirage)";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([
                                ":valeur" => $data['valeur'],
                                ":date_tirage" => $data['date_tirage'],
                                ":heure_tirage" => $data['heure_tirage']
                            ]);
                        }
                    }
                } else {
                    // Exécuter comme SQL normal (pour les migrations de schéma)
                    $pdo->exec($content);
                }

                $insert = $pdo->prepare("INSERT INTO migrations (filename) VALUES (?)");
                $insert->execute([$filename]);

                echo "✓ $filename exécutée\n";
            }
        }
        echo "\nToutes les migrations sont à jour !\n";
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
