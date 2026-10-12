<?php
// Base de données KWIZ (Sprint 1) - SQLite
// Le fichier kwiz.sqlite est créé automatiquement dans le dossier database/
// Utilisation : require_once __DIR__ . '/../config/db.php';  puis  $pdo

$dossier = __DIR__ . '/../database';
if (!is_dir($dossier)) {
    mkdir($dossier, 0777, true);
}

$pdo = new PDO("sqlite:$dossier/kwiz.sqlite", null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    nom          TEXT NOT NULL UNIQUE,
    courriel     TEXT NOT NULL UNIQUE,
    mot_de_passe TEXT NOT NULL,
    role         TEXT NOT NULL DEFAULT 'etudiant' CHECK (role IN ('enseignant', 'etudiant'))
)");

// Enseignant de test : prof / prof@test.com / Prof1234!
$pdo->prepare("INSERT OR IGNORE INTO users (nom, courriel, mot_de_passe, role)
               VALUES ('prof', 'prof@test.com', ?, 'enseignant')")
    ->execute([password_hash('Prof1234!', PASSWORD_DEFAULT)]);