<?php

session_start();
require __DIR__ . '/../config/Database.php';

// Definit le format de reponse (format JSON)
header("Content-Type: application/json; charset=utf-8");

// Verifie la methode HTTP, accepte juste les requetes POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non autorisée"]);
    exit;
}

// Recuper les donnees envoyer en JSON et les transforment en tableau PHP
$data = json_decode(file_get_contents("php://input"), true);

$titre = trim($data['titre'] ?? "");
$description = trim($data['description'] ?? "");
$erreurs = [];
$champsValides = true;

// Validation du titre
if($titre === ""){
    $erreurs["titre"] = "Le titre est obligatoire";
    $champsValides = false;
}

// Validation de la description
if($motDePasse === ""){
    $erreurs["description"] = "La description est obligatoire";
    $champsValides = false;
}


// Insertion du quiz dans la bd
$insertionQuiz = $pdo->prepare(
    "INSERT IGNORE INTO quiz (titre, description, user_id)
    VALUES (:titre, :description, :user_id)"
);

$insertionQuiz->execute([
    'titre' => $titre,
    'description' => $description,
    'user_id' => $_SESSION['id']
]);

// Recuperer l'ID du quiz qui vient d'être creer
$quizId = $pdo->lastInsertId();

if(empty($erreurs)){

    $_SESSION['quiz_id'] = $quizId;

    echo json_encode([
        "success" => true,
        "redirect" => "ajouter_questions.php"
    ]);
    exit();

} else {

    http_response_code(400);
    echo json_encode([
        "success" => false, 
        "erreurs" => $erreurs
    ]);
    exit();
};