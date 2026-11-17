<?php

session_start();
require __DIR__ . '/../config/Database.php';

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
    exit;
}

if (empty($_SESSION['authenticated']) || ($_SESSION['role'] ?? '') !== 'enseignant') {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Accès non autorisé"]);
    exit;
}

$questionId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$questionId) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Question introuvable"]);
    exit;
}

// Recupere la question, seulement si son quiz appartient a l'enseignant connecte
$recupQuestion = $pdo->prepare(
    "SELECT qs.id, qs.text, qs.quiz_id
    FROM questions qs
    INNER JOIN quiz q ON q.id = qs.quiz_id
    WHERE qs.id = :id AND q.user_id = :user_id"
);
$recupQuestion->execute(['id' => $questionId, 'user_id' => $_SESSION['id']]);
$question = $recupQuestion->fetch();

if (!$question) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Question introuvable"]);
    exit;
}

$recupReponses = $pdo->prepare("SELECT id, text, est_vrai FROM reponses WHERE question_id = :question_id ORDER BY id");
$recupReponses->execute(['question_id' => $questionId]);
$question['reponses'] = $recupReponses->fetchAll();

echo json_encode(["success" => true, "question" => $question]);
exit();