<?php
// Base de données KWIZ (Sprint 1) - SQLite
// Le fichier kwiz.sqlite est créé automatiquement dans le dossier database/
// Utilisation : require_once __DIR__ . '/../config/db.php';  puis  $pdo

// __DIR__ retourne le path absolue du directory que le script courant est dedans
// .. Rentre dans le niveau suivant du directory et va dans le fichier database
$dossier = __DIR__ . '/../database';

// Check si le folder n'existe pas deja en tant que directory (is_dir)
// 0777 Garantie full read, write, et execute permissions au folder
if (!is_dir($dossier)) {
    mkdir($dossier, 0777, true);
}

$pdo = new PDO("sqlite:$dossier/kwiz.sqlite", null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Table user (id, nom, motdepasse, role)
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    nom          TEXT NOT NULL UNIQUE,
    mot_de_passe TEXT NOT NULL,
    role         TEXT NOT NULL DEFAULT 'etudiant' CHECK (role IN ('enseignant', 'etudiant'))
)");

// Insertion de l'enseignant
$pdo->prepare("INSERT OR IGNORE INTO users (nom, mot_de_passe, role)
               VALUES ( :nom, :mot_de_passe , :role )")
    ->execute([
        'nom' => 'Farid',
        'mot_de_passe' => 'Farid123',
        'role' => 'enseignant'
    ]);