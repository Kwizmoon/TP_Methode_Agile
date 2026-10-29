<?php

session_start();
require __DIR__ . '/../config/Database.php';

// Definit le format de reponse (format JSON)
header("Content-Type: application/json; charset=utf-8");

// Verifie la methode HTTP, accepte juste les requetes GET
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non autorisée"]);
    exit;
}

// Verifie que l'utilisateur est connecte et qu'il est enseignant
if (empty($_SESSION['authenticated']) || ($_SESSION['role'] ?? '') !== 'enseignant') {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Accès non autorisé"
    ]);
    exit;
}

// Recupere les quiz de l'enseignant connecte, avec leur nombre de questions
$recupQuiz = $pdo->prepare(
    "SELECT q.id, q.titre, q.description, COUNT(qs.id) AS nb_questions
    FROM quiz q
    LEFT JOIN questions qs ON qs.quiz_id = q.id
    WHERE q.user_id = :user_id
    GROUP BY q.id, q.titre, q.description
    ORDER BY q.id DESC"
);

$recupQuiz->execute([
    'user_id' => $_SESSION['id']
]);

$quiz = $recupQuiz->fetchAll();

echo json_encode([
    "success" => true,
    "quiz" => $quiz
]);
exit();
