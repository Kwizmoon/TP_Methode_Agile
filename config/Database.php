<?php

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'Quiz';

try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Creation de la base de donnee si elle n'existe pas deja
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `$dbname`;");


    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);


    // Table users (id, nom, motdepasse, role)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL UNIQUE,
        mot_de_passe VARCHAR(255) NOT NULL,
        role ENUM('enseignant', 'etudiant') NOT NULL DEFAULT 'etudiant'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");


    // Table quiz (ID, titre, description, et référence à l'enseignant)
    $pdo->exec("CREATE TABLE IF NOT EXISTS quiz (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(100) NOT NULL UNIQUE,
        description TEXT NOT NULL,
        user_id INT NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");


    // Table questions (ID, texte de la question, et référence au quiz parent)
     $pdo->exec("CREATE TABLE IF NOT EXISTS questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        text TEXT NOT NULL,
        quiz_id INT NOT NULL,
        FOREIGN KEY(quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");


    // Table reponses (ID, texte de la réponse, indicateur de bonne/fausse réponse,
    // et référence à la question parente, maximum de 5 réponse par question).
    $pdo->exec("CREATE TABLE IF NOT EXISTS reponses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        text TEXT NOT NULL,
        est_vrai BOOLEAN NOT NULL DEFAULT 0,
        question_id INT NOT NULL,
        FOREIGN KEY(question_id) REFERENCES questions(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Hachage sécurisé du mot de passe
    $motDePasseClair = 'Farid123';
    $motDePasseHache = password_hash($motDePasseClair, PASSWORD_DEFAULT);

    // Insertion de l'enseignant (INSERT IGNORE évite les erreurs si l'utilisateur existe déjà)
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (nom, mot_de_passe, role) 
                        VALUES (:nom, :mot_de_passe, :role)");
    $stmt->execute([
        'nom' => 'Farid',
        'mot_de_passe' => $motDePasseHache,
        'role' => 'enseignant'
    ]);


} catch (PDOException $e) {
    exit('Connexion impossible : ' . $e->getMessage());
}



