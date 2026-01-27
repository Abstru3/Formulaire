-- Migration 001 : Création des tables initiales
-- Date : 2026-01-27

CREATE TABLE IF NOT EXISTS tirages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    valeur VARCHAR(255) NOT NULL,
    date_tirage DATE NOT NULL,
    heure_tirage TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
