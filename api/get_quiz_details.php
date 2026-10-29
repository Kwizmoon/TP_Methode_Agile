<?php

session_start();
require __DIR__ . '/../config/Database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);

    exit();
}

$quizId = $_GET['id'] ?? null;

if (!$quizId) {
    echo json_encode([
        'success' => false,
        'message' => 'Quiz introuvable'
    ]);

    exit();
}

$quizQuery = $pdo->prepare(
    "SELECT id, titre, description
     FROM quiz
     WHERE id = :id"
);

$quizQuery->execute([
    'id' => $quizId
]);

$quiz = $quizQuery->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    echo json_encode([
        'success' => false,
        'message' => 'Quiz introuvable'
    ]);

    exit();
}

$questionQuery = $pdo->prepare(
    "SELECT id, text
     FROM questions
     WHERE quiz_id = :quiz_id"
);

$questionQuery->execute([
    'quiz_id' => $quizId
]);

$questions = $questionQuery->fetchAll(PDO::FETCH_ASSOC);

foreach ($questions as &$question) {

    $reponseQuery = $pdo->prepare(
        "SELECT id, text, est_vrai
         FROM reponses
         WHERE question_id = :question_id"
    );

    $reponseQuery->execute([
        'question_id' => $question['id']
    ]);

    $question['reponses'] = $reponseQuery->fetchAll(PDO::FETCH_ASSOC);
}

$quiz['questions'] = $questions;

echo json_encode([
    'success' => true,
    'quiz' => $quiz
]);

exit();