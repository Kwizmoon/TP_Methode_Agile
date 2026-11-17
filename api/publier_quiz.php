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

// Seul un enseignant connecte peut publier
if (empty($_SESSION['authenticated']) || ($_SESSION['role'] ?? '') !== 'enseignant') {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Accès non autorisé"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$quizId = filter_var($data['quiz_id'] ?? null, FILTER_VALIDATE_INT);

if (!$quizId) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Quiz introuvable"]);
    exit;
}

// Verifie que le quiz appartient a l'enseignant connecte
$verif = $pdo->prepare("SELECT id FROM quiz WHERE id = :id AND user_id = :user_id");
$verif->execute(['id' => $quizId, 'user_id' => $_SESSION['id']]);

if (!$verif->fetch()) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Quiz introuvable"]);
    exit;
}

// Le statut du quiz passe a Publié
$publication = $pdo->prepare("UPDATE quiz SET statut = 'Publié' WHERE id = :id");
$publication->execute(['id' => $quizId]);

echo json_encode(["success" => true]);
exit();