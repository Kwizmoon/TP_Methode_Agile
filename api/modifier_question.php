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
$question = trim($data['question'] ?? "");
$reponses = $data['reponses'] ?? [];
$erreurs = [];

// Validation de la question
if ($question === "") {
    $erreurs["question"] = "La question est obligatoire.";
}

// Validation des reponses (2 a 5, aucune vide, une seule bonne reponse)
if (!is_array($reponses) || count($reponses) < 2) {
    $erreurs["reponses"] = "Minimum de 2 réponses.";
} elseif (count($reponses) > 5) {
    $erreurs["reponses"] = "Maximum de 5 réponses.";
} else {
    $nombreBonnesReponses = 0;

    foreach ($reponses as $rep) {
        if (trim($rep['texte'] ?? '') === "") {
            $erreurs["reponses"] = "Une réponse ne peut pas être vide.";
        }
        if (filter_var($rep['correct'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $nombreBonnesReponses++;
        }
    }

    if (!isset($erreurs["reponses"]) && $nombreBonnesReponses !== 1) {
        $erreurs["reponses"] = "Vous devez sélectionner une bonne réponse.";
    }
}

if (!$questionId) {
    $erreurs["global"] = "Question introuvable.";
}

if (!empty($erreurs)) {
    http_response_code(400);
    echo json_encode(["success" => false, "erreurs" => $erreurs]);
    exit;
}

// Verifie que la question appartient a un quiz de l'enseignant connecte
$verif = $pdo->prepare(
    "SELECT qs.quiz_id
    FROM questions qs
    INNER JOIN quiz q ON q.id = qs.quiz_id
    WHERE qs.id = :id AND q.user_id = :user_id"
);
$verif->execute(['id' => $questionId, 'user_id' => $_SESSION['id']]);
$existante = $verif->fetch();

if (!$existante) {
    http_response_code(404);
    echo json_encode(["success" => false, "erreurs" => ["global" => "Question introuvable."]]);
    exit;
}

// Mise a jour de la question et de ses reponses (tout ou rien)
try {
    $pdo->beginTransaction();

    $majQuestion = $pdo->prepare("UPDATE questions SET text = :text WHERE id = :id");
    $majQuestion->execute(['text' => $question, 'id' => $questionId]);

    // On remplace les anciennes reponses par les nouvelles
    $suppReponses = $pdo->prepare("DELETE FROM reponses WHERE question_id = :question_id");
    $suppReponses->execute(['question_id' => $questionId]);

    $insertReponse = $pdo->prepare(
        "INSERT INTO reponses (text, est_vrai, question_id)
        VALUES (:text, :est_vrai, :question_id)"
    );

    foreach ($reponses as $rep) {
        $insertReponse->execute([
            'text' => trim($rep['texte']),
            'est_vrai' => filter_var($rep['correct'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'question_id' => $questionId
        ]);
    }

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["success" => false, "erreurs" => ["global" => "Erreur lors de la modification."]]);
    exit;
}

echo json_encode([
    "success" => true,
    "redirect" => "quiz_details.php?id=" . $existante['quiz_id']
]);
exit();