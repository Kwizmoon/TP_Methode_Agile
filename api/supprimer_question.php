<?php

session_start();
require __DIR__ . '/../config/Database.php';

header("Content-Type: application/json; charset=utf-8");

// Accepte juste les requetes POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
    exit;
}

if (empty($_SESSION['authenticated']) || ($_SESSION['role'] ?? '') !== 'enseignant') {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Accès non autorisé"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$questionId = filter_var($data['question_id'] ?? null, FILTER_VALIDATE_INT);

if (!$questionId) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Question introuvable"]);
    exit;
}

// Supprime la question seulement si son quiz appartient a l'enseignant connecte
// (les reponses sont supprimees automatiquement grace au ON DELETE CASCADE)
$suppression = $pdo->prepare(
    "DELETE qs FROM questions qs
    INNER JOIN quiz q ON q.id = qs.quiz_id
    WHERE qs.id = :id AND q.user_id = :user_id"
);
$suppression->execute(['id' => $questionId, 'user_id' => $_SESSION['id']]);

if ($suppression->rowCount() === 0) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Question introuvable"]);
    exit;
}

echo json_encode(["success" => true]);
exit();